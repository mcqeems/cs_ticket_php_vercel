<?php
namespace App\Controllers;

use App\Models\HelpTopicModel;
use App\Models\DepartmentModel;

class HelpTopicsController
{
	private $model;
	private $departmentModel;

	public function __construct()
	{
		$this->model = new HelpTopicModel();
		$this->departmentModel = new DepartmentModel();
	}

	public function index()
	{
		$topics = $this->model->getAll(false); // Get all including inactive

		// Load the view
		$viewPath = __DIR__ . '/../Views/help_topics_list.php';
		include $viewPath;
	}

	public function detail($topicId)
	{
		$topic = $this->model->getById($topicId);

		if (!$topic) {
			$_SESSION['error'] = 'Help topic not found.';
			header('Location: index.php?action=help_topics');
			exit;
		}

		// Get statistics
		$stats = $this->model->getStatistics($topicId);

		// Get department info if available
		$department = null;
		if (isset($topic['department_id'])) {
			$department = $this->departmentModel->getById((string) $topic['department_id']);
		}

		// Load the view
		$viewPath = __DIR__ . '/../Views/help_topic_detail.php';
		include $viewPath;
	}

	public function create()
	{
		// Get all active departments for the form
		$departments = $this->departmentModel->getAll(true);

		// Show create form
		$viewPath = __DIR__ . '/../Views/help_topic_form.php';
		include $viewPath;
	}

	public function store()
	{
		// Validate input
		$errors = [];

		if (empty($_POST['topic_name'])) {
			$errors[] = 'Help topic name is required';
		} else {
			// Check if topic name already exists
			$existing = $this->model->findByTopicName($_POST['topic_name']);
			if ($existing) {
				$errors[] = 'Help topic name already exists';
			}
		}

		if (empty($_POST['department_id'])) {
			$errors[] = 'Department is required';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=help_topic_create');
			exit;
		}

		// Create help topic
		try {
			$this->model->create($_POST);
			$_SESSION['success'] = 'Help topic created successfully!';
			header('Location: index.php?action=help_topics');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error creating help topic: ' . $e->getMessage();
			header('Location: index.php?action=help_topic_create');
		}
		exit;
	}

	public function edit($topicId)
	{
		$topic = $this->model->getById($topicId);

		if (!$topic) {
			$_SESSION['error'] = 'Help topic not found.';
			header('Location: index.php?action=help_topics');
			exit;
		}

		// Get all active departments for the form
		$departments = $this->departmentModel->getAll(true);

		// Load edit form
		$viewPath = __DIR__ . '/../Views/help_topic_form.php';
		include $viewPath;
	}

	public function updateTopic()
	{
		$topicId = $_POST['topic_id'] ?? null;

		if (!$topicId) {
			$_SESSION['error'] = 'Help topic ID is required';
			header('Location: index.php?action=help_topics');
			exit;
		}

		// Validate input
		$errors = [];

		if (empty($_POST['topic_name'])) {
			$errors[] = 'Help topic name is required';
		} else {
			// Check if topic name exists for other topics
			$existing = $this->model->findByTopicName($_POST['topic_name']);
			if ($existing && (string) $existing['_id'] !== $topicId) {
				$errors[] = 'Help topic name already exists';
			}
		}

		if (empty($_POST['department_id'])) {
			$errors[] = 'Department is required';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=help_topic_edit&id=' . $topicId);
			exit;
		}

		// Update help topic
		try {
			$this->model->update($topicId, $_POST);
			$_SESSION['success'] = 'Help topic updated successfully!';
			header('Location: index.php?action=help_topics');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error updating help topic: ' . $e->getMessage();
			header('Location: index.php?action=help_topic_edit&id=' . $topicId);
		}
		exit;
	}

	public function deleteTopic()
	{
		$topicId = $_GET['id'] ?? null;

		if (!$topicId) {
			$_SESSION['error'] = 'Help topic ID is required';
			header('Location: index.php?action=help_topics');
			exit;
		}

		// Check if help topic has tickets
		$stats = $this->model->getStatistics($topicId);
		if ($stats['total_tickets'] > 0) {
			$_SESSION['error'] = 'Cannot delete help topic with existing tickets. Please reassign tickets first.';
			header('Location: index.php?action=help_topics');
			exit;
		}

		try {
			$this->model->delete($topicId);
			$_SESSION['success'] = 'Help topic deleted successfully!';
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error deleting help topic: ' . $e->getMessage();
		}

		header('Location: index.php?action=help_topics');
		exit;
	}
}
?>