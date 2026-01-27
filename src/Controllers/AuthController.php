<?php
namespace App\Controllers;

use App\Models\AuthModel;

class AuthController
{
	private $model;

	public function __construct()
	{
		$this->model = new AuthModel();
	}

	/**
	 * Show login form
	 */
	public function showLogin()
	{
		// If already logged in, redirect to dashboard
		if (isset($_SESSION['user_id'])) {
			header('Location: index.php?action=dashboard');
			exit;
		}

		$viewPath = __DIR__ . '/../Views/login.php';
		include $viewPath;
	}

	/**
	 * Process login
	 */
	public function login()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?action=login');
			exit;
		}

		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';

		if (empty($email) || empty($password)) {
			$_SESSION['error'] = 'Email and password are required';
			header('Location: index.php?action=login');
			exit;
		}

		$user = $this->model->login($email, $password);

		if ($user) {
			// Set session variables
			$_SESSION['user_id'] = (string) $user['_id'];
			$_SESSION['user_name'] = $user['name'];
			$_SESSION['user_email'] = $user['email'];
			$_SESSION['user_role'] = $user['role'];
			$_SESSION['department_id'] = isset($user['department_id']) ? (string) $user['department_id'] : null;

			// Redirect based on role
			header('Location: index.php?action=dashboard');
			exit;
		} else {
			$_SESSION['error'] = 'Invalid email or password';
			header('Location: index.php?action=login');
			exit;
		}
	}

	/**
	 * Show registration form
	 */
	public function showRegister()
	{
		if (isset($_SESSION['user_id'])) {
			header('Location: index.php?action=dashboard');
			exit;
		}

		$viewPath = __DIR__ . '/../Views/register.php';
		include $viewPath;
	}

	/**
	 * Process registration
	 */
	public function register()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: index.php?action=register');
			exit;
		}

		$name = $_POST['name'] ?? '';
		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';
		$confirmPassword = $_POST['confirm_password'] ?? '';
		$phone = $_POST['phone'] ?? '';

		// Validation
		if (empty($name) || empty($email) || empty($password)) {
			$_SESSION['error'] = 'Name, email, and password are required';
			header('Location: index.php?action=register');
			exit;
		}

		if ($password !== $confirmPassword) {
			$_SESSION['error'] = 'Passwords do not match';
			header('Location: index.php?action=register');
			exit;
		}

		if (strlen($password) < 6) {
			$_SESSION['error'] = 'Password must be at least 6 characters';
			header('Location: index.php?action=register');
			exit;
		}

		$result = $this->model->register([
			'name' => $name,
			'email' => $email,
			'password' => $password,
			'phone' => $phone,
			'role' => 'client' // Default role for registration
		]);

		if ($result['success']) {
			$_SESSION['success'] = 'Registration successful! Please login.';
			header('Location: index.php?action=login');
			exit;
		} else {
			$_SESSION['error'] = $result['message'];
			header('Location: index.php?action=register');
			exit;
		}
	}

	/**
	 * Logout
	 */
	public function logout()
	{
		session_destroy();
		header('Location: index.php?action=login');
		exit;
	}

	/**
	 * Check if user is logged in
	 */
	public static function isLoggedIn()
	{
		return isset($_SESSION['user_id']);
	}

	/**
	 * Check if user has specific role
	 */
	public static function hasRole($role)
	{
		return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
	}

	/**
	 * Require login (redirect to login if not authenticated)
	 */
	public static function requireLogin()
	{
		if (!self::isLoggedIn()) {
			header('Location: index.php?action=login');
			exit;
		}
	}

	/**
	 * Require specific role
	 */
	public static function requireRole($role)
	{
		self::requireLogin();

		if (!self::hasRole($role)) {
			$_SESSION['error'] = 'Access denied. Insufficient permissions.';
			header('Location: index.php?action=dashboard');
			exit;
		}
	}
}
?>