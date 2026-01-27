<?php
namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\DepartmentModel;
use App\Models\HelpTopicModel;

class TicketController
{
	private $model;

	public function __construct()
	{
		$this->model = new TicketModel();
	}

	/**
	 * Show ticket list
	 */
	public function index()
	{
		AuthController::requireLogin();

		$role = $_SESSION['user_role'];
		$userId = $_SESSION['user_id'];

		$filters = [];

		// Apply filters based on role
		if ($role === 'client') {
			$filters['user_id'] = $userId;
		} elseif ($role === 'support_agent') {
			// Check if viewing assigned tickets or all
			$filter = $_GET['filter'] ?? 'assigned';
			if ($filter === 'assigned') {
				$filters['assigned_to'] = $userId;
			}
		}

		// Apply additional filters from GET parameters
		if (isset($_GET['status']) && !empty($_GET['status'])) {
			$filters['status'] = $_GET['status'];
		}

		$tickets = $this->model->getTickets($filters);
		$stats = $this->model->getStatistics($role === 'client' ? ['user_id' => $userId] : []);

		$viewPath = __DIR__ . '/../Views/ticket_list.php';
		include $viewPath;
	}

	/**
	 * Show create ticket form
	 */
	public function create()
	{
		AuthController::requireLogin();

		$departmentModel = new DepartmentModel();
		$departments = $departmentModel->getAll();

		$viewPath = __DIR__ . '/../Views/ticket_create.php';
		include $viewPath;
	}

	/**
	 * Store new ticket
	 */
	public function store()
	{
		AuthController::requireLogin();

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?action=ticket_create');
			exit;
		}

		$department_id = $_POST['department_id'] ?? '';
		$help_topic_id = $_POST['help_topic_id'] ?? '';
		$subject = $_POST['subject'] ?? '';
		$message = $_POST['message'] ?? '';
		$priority = $_POST['priority'] ?? 'normal';

		// Validation
		if (empty($department_id) || empty($help_topic_id) || empty($subject) || empty($message)) {
			$_SESSION['error'] = 'All fields are required';
			header('Location: index.php?action=ticket_create');
			exit;
		}

		$result = $this->model->create([
			'user_id' => $_SESSION['user_id'],
			'department_id' => $department_id,
			'help_topic_id' => $help_topic_id,
			'subject' => $subject,
			'message' => $message,
			'priority' => $priority
		]);

		if ($result['success']) {
			$_SESSION['success'] = 'Ticket created successfully! Ticket #: ' . $result['ticket_number'];
			header('Location: index.php?action=ticket_detail&id=' . $result['ticket_id']);
			exit;
		} else {
			$_SESSION['error'] = $result['message'];
			header('Location: index.php?action=ticket_create');
			exit;
		}
	}

	/**
	 * Show ticket detail
	 */
	public function detail()
	{
		AuthController::requireLogin();

		$ticketId = $_GET['id'] ?? null;

		if (!$ticketId) {
			$_SESSION['error'] = 'Invalid ticket ID';
			header('Location: index.php?action=tickets');
			exit;
		}

		$ticket = $this->model->getTicketById($ticketId);

		if (!$ticket) {
			$_SESSION['error'] = 'Ticket not found';
			header('Location: index.php?action=tickets');
			exit;
		}

		// Check permissions - clients can only view their own tickets
		$role = $_SESSION['user_role'];
		$userId = $_SESSION['user_id'];

		if ($role === 'client' && (string) $ticket->user['_id'] !== $userId) {
			$_SESSION['error'] = 'Access denied';
			header('Location: index.php?action=tickets');
			exit;
		}

		$replies = $this->model->getReplies($ticketId);
		$history = $this->model->getHistory($ticketId);

		// Get agents for assignment (admin/agent only)
		$agents = [];
		if ($role !== 'client') {
			$departmentModel = new DepartmentModel();
			$agents = $departmentModel->getAgentsByDepartment($ticket->department['_id']);
		}

		$viewPath = __DIR__ . '/../Views/ticket_detail.php';
		include $viewPath;
	}

	/**
	 * Add reply to ticket
	 */
	public function addReply()
	{
		AuthController::requireLogin();

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?action=tickets');
			exit;
		}

		$ticketId = $_POST['ticket_id'] ?? '';
		$message = $_POST['message'] ?? '';
		$isInternal = isset($_POST['is_internal']) && $_POST['is_internal'] === '1';

		if (empty($ticketId) || empty($message)) {
			$_SESSION['error'] = 'Message is required';
			header('Location: index.php?action=ticket_detail&id=' . $ticketId);
			exit;
		}

		// Only agents/admins can add internal notes
		$role = $_SESSION['user_role'];
		if ($isInternal && $role === 'client') {
			$isInternal = false;
		}

		$success = $this->model->addReply($ticketId, $_SESSION['user_id'], $message, $isInternal);

		if ($success) {
			$_SESSION['success'] = 'Reply added successfully';
		} else {
			$_SESSION['error'] = 'Failed to add reply';
		}

		header('Location: index.php?action=ticket_detail&id=' . $ticketId);
		exit;
	}

	/**
	 * Update ticket status
	 */
	public function updateStatus()
	{
		AuthController::requireLogin();

		// Only agents and admins can update status
		$role = $_SESSION['user_role'];
		if ($role === 'client') {
			$_SESSION['error'] = 'Access denied';
			header('Location: index.php?action=tickets');
			exit;
		}

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?action=tickets');
			exit;
		}

		$ticketId = $_POST['ticket_id'] ?? '';
		$status = $_POST['status'] ?? '';

		if (empty($ticketId) || empty($status)) {
			$_SESSION['error'] = 'Invalid request';
			header('Location: index.php?action=tickets');
			exit;
		}

		$success = $this->model->updateStatus($ticketId, $status, $_SESSION['user_id']);

		if ($success) {
			$_SESSION['success'] = 'Ticket status updated successfully';
		} else {
			$_SESSION['error'] = 'Failed to update status';
		}

		header('Location: index.php?action=ticket_detail&id=' . $ticketId);
		exit;
	}

	/**
	 * Assign ticket to agent
	 */
	public function assign()
	{
		AuthController::requireLogin();

		// Only agents and admins can assign tickets
		$role = $_SESSION['user_role'];
		if ($role === 'client') {
			$_SESSION['error'] = 'Access denied';
			header('Location: index.php?action=tickets');
			exit;
		}

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?action=tickets');
			exit;
		}

		$ticketId = $_POST['ticket_id'] ?? '';
		$agentId = $_POST['agent_id'] ?? '';

		if (empty($ticketId) || empty($agentId)) {
			$_SESSION['error'] = 'Invalid request';
			header('Location: index.php?action=tickets');
			exit;
		}

		$success = $this->model->assignToAgent($ticketId, $agentId, $_SESSION['user_id']);

		if ($success) {
			$_SESSION['success'] = 'Ticket assigned successfully';
		} else {
			$_SESSION['error'] = 'Failed to assign ticket';
		}

		header('Location: index.php?action=ticket_detail&id=' . $ticketId);
		exit;
	}
}
?>