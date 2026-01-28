<?php
namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class DashboardController
{
	private $ticketModel;
	private $userModel;
	private $departmentModel;

	public function __construct()
	{
		$this->ticketModel = new TicketModel();
		$this->userModel = new UserModel();
		$this->departmentModel = new DepartmentModel();
	}

	public function index()
	{
		$role = $_SESSION['user_role'] ?? 'client';

		// Load different dashboard based on role
		switch ($role) {
			case 'admin':
				$this->adminDashboard();
				break;
			case 'support_agent':
				$this->agentDashboard();
				break;
			case 'client':
			default:
				$this->clientDashboard();
				break;
		}
	}

	private function adminDashboard()
	{
		// Get statistics
		$stats = $this->ticketModel->getStatistics();

		// Get recent tickets (last 5)
		$recentTickets = $this->ticketModel->getTickets(['limit' => 5]);

		// Get active agents
		$activeAgents = $this->userModel->getUsersByRole('support_agent', 5);

		// Get all departments and create a lookup map
		$departments = $this->departmentModel->getAll(false);
		$departmentMap = [];
		foreach ($departments as $dept) {
			$departmentMap[(string) $dept['_id']] = $dept['name'];
		}

		$viewPath = __DIR__ . '/../Views/dashboard_admin.php';
		include $viewPath;
	}

	private function agentDashboard()
	{
		$userId = $_SESSION['user_id'];

		// Get agent statistics
		$stats = $this->ticketModel->getAgentStatistics($userId);

		// Get assigned tickets
		$assignedTickets = $this->ticketModel->getTickets(['assigned_to' => $userId, 'limit' => 10]);

		$viewPath = __DIR__ . '/../Views/dashboard_agent.php';
		include $viewPath;
	}

	private function clientDashboard()
	{
		$userId = $_SESSION['user_id'];

		// Get client statistics
		$stats = $this->ticketModel->getClientStatistics($userId);

		// Get user's tickets
		$myTickets = $this->ticketModel->getTickets(['user_id' => $userId, 'limit' => 10]);

		$viewPath = __DIR__ . '/../Views/dashboard_client.php';
		include $viewPath;
	}
}
?>