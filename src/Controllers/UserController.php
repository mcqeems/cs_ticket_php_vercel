<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\TicketModel;

class UserController
{
	private $model;
	private $ticketModel;

	public function __construct()
	{
		$this->model = new UserModel();
		$this->ticketModel = new TicketModel();
	}

	public function index()
	{
		$users = $this->model->getData();

		// Load the view
		$viewPath = __DIR__ . '/../Views/users_list.php';
		include $viewPath;
	}

	public function detail($userId)
	{
		$user = $this->model->findById($userId);

		if (!$user) {
			$_SESSION['error'] = 'User not found.';
			header('Location: index.php?action=users');
			exit;
		}

		// Get ticket statistics based on user role
		if ($user['role'] === 'client') {
			// For clients, get tickets they created (user_id)
			$ticketStats = $this->ticketModel->getClientStatistics($userId);
		} else {
			// For support agents and admins, get tickets assigned to them
			$ticketStats = $this->ticketModel->getStatistics(['assigned_to' => $userId]);
		}

		// Get recent activity for this user
		$recentActivity = $this->ticketModel->getUserRecentActivity($userId, 10);

		// Load the user detail view
		$viewPath = __DIR__ . '/../Views/user_detail.php';
		include $viewPath;
	}

	public function create()
	{
		// Show create form
		$viewPath = __DIR__ . '/../Views/user_form.php';
		include $viewPath;
	}

	public function store()
	{
		// Validate input
		$errors = [];

		if (empty($_POST['name'])) {
			$errors[] = 'Name is required';
		}

		if (empty($_POST['email'])) {
			$errors[] = 'Email is required';
		} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Invalid email format';
		} else {
			// Check if email already exists
			$existingUser = $this->model->findByEmail($_POST['email']);
			if ($existingUser) {
				$errors[] = 'Email already exists';
			}
		}

		if (empty($_POST['password'])) {
			$errors[] = 'Password is required';
		} elseif (strlen($_POST['password']) < 6) {
			$errors[] = 'Password must be at least 6 characters';
		}

		if (empty($_POST['role'])) {
			$errors[] = 'Role is required';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=user_create');
			exit;
		}

		// Create user
		try {
			$this->model->create($_POST);
			$_SESSION['success'] = 'User created successfully!';
			header('Location: index.php?action=users');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error creating user: ' . $e->getMessage();
			header('Location: index.php?action=user_create');
		}
		exit;
	}

	public function edit($userId)
	{
		$user = $this->model->findById($userId);

		if (!$user) {
			$_SESSION['error'] = 'User not found.';
			header('Location: index.php?action=users');
			exit;
		}

		// Load edit form
		$viewPath = __DIR__ . '/../Views/user_form.php';
		include $viewPath;
	}

	public function updateUser()
	{
		$userId = $_POST['user_id'] ?? null;

		if (!$userId) {
			$_SESSION['error'] = 'User ID is required';
			header('Location: index.php?action=users');
			exit;
		}

		// Validate input
		$errors = [];

		if (empty($_POST['name'])) {
			$errors[] = 'Name is required';
		}

		if (empty($_POST['email'])) {
			$errors[] = 'Email is required';
		} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Invalid email format';
		} else {
			// Check if email exists for other users
			$existingUser = $this->model->findByEmail($_POST['email']);
			if ($existingUser && (string) $existingUser['_id'] !== $userId) {
				$errors[] = 'Email already exists';
			}
		}

		// Password is optional on update
		if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
			$errors[] = 'Password must be at least 6 characters';
		}

		if (empty($_POST['role'])) {
			$errors[] = 'Role is required';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=user_edit&id=' . $userId);
			exit;
		}

		// Update user
		try {
			$this->model->update($userId, $_POST);
			$_SESSION['success'] = 'User updated successfully!';
			header('Location: index.php?action=users');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error updating user: ' . $e->getMessage();
			header('Location: index.php?action=user_edit&id=' . $userId);
		}
		exit;
	}

	public function deleteUser()
	{
		$userId = $_GET['id'] ?? null;

		if (!$userId) {
			$_SESSION['error'] = 'User ID is required';
			header('Location: index.php?action=users');
			exit;
		}

		// Prevent deleting yourself
		if ($userId === $_SESSION['user_id']) {
			$_SESSION['error'] = 'You cannot delete your own account';
			header('Location: index.php?action=users');
			exit;
		}

		try {
			$this->model->delete($userId);
			$_SESSION['success'] = 'User deleted successfully!';
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error deleting user: ' . $e->getMessage();
		}

		header('Location: index.php?action=users');
		exit;
	}
}
?>