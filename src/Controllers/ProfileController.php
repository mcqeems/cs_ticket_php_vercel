<?php
namespace App\Controllers;

use App\Models\UserModel;

class ProfileController
{
	private $userModel;

	public function __construct()
	{
		$this->userModel = new UserModel();
	}

	public function index()
	{
		$userId = $_SESSION['user_id'];
		$user = $this->userModel->findById($userId);

		if (!$user) {
			$_SESSION['error'] = 'User not found.';
			header('Location: index.php?action=dashboard');
			exit;
		}

		// Load the view
		$viewPath = __DIR__ . '/../Views/profile.php';
		include $viewPath;
	}

	public function update()
	{
		$userId = $_SESSION['user_id'];
		$user = $this->userModel->findById($userId);

		if (!$user) {
			$_SESSION['error'] = 'User not found.';
			header('Location: index.php?action=dashboard');
			exit;
		}

		// Validate input
		$errors = [];

		if (empty($_POST['name'])) {
			$errors[] = 'Name is required';
		}

		if (empty($_POST['email'])) {
			$errors[] = 'Email is required';
		} else {
			// Check if email already exists for another user
			$existingUser = $this->userModel->findByEmail($_POST['email']);
			if ($existingUser && $existingUser['_id'] != $user['_id']) {
				$errors[] = 'Email already exists';
			}
		}

		// Validate password if provided
		if (!empty($_POST['current_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
			if (empty($_POST['current_password'])) {
				$errors[] = 'Current password is required to change password';
			} elseif (!password_verify($_POST['current_password'], $user['password'])) {
				$errors[] = 'Current password is incorrect';
			}

			if (empty($_POST['new_password'])) {
				$errors[] = 'New password is required';
			} elseif (strlen($_POST['new_password']) < 6) {
				$errors[] = 'New password must be at least 6 characters';
			}

			if (empty($_POST['confirm_password'])) {
				$errors[] = 'Please confirm your new password';
			} elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
				$errors[] = 'New passwords do not match';
			}
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=profile');
			exit;
		}

		// Update user
		try {
			$updateData = [
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'role' => $user['role'] // Keep existing role
			];

			// Add password if changing
			if (!empty($_POST['new_password'])) {
				$updateData['password'] = $_POST['new_password'];
			}

			// Add department if exists
			if (isset($user['department'])) {
				$updateData['department'] = $user['department'];
			}

			$this->userModel->update($userId, $updateData);

			// Update session with new name
			$_SESSION['user_name'] = $_POST['name'];

			$_SESSION['success'] = 'Profile updated successfully!';
			header('Location: index.php?action=profile');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error updating profile: ' . $e->getMessage();
			header('Location: index.php?action=profile');
		}
		exit;
	}
}
?>