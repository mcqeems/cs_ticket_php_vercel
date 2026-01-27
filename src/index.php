<?php
session_start();
require 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\TicketController;
use App\Controllers\ProfileController;
use App\Controllers\KnowledgeBaseController;
use App\Models\HelpTopicModel;

// Determine the route
$action = $_GET['action'] ?? 'dashboard';

// Public routes (no authentication required)
$publicRoutes = ['login', 'register',];

// API routes (return JSON, no HTML layout)
$apiRoutes = ['api_help_topics'];

// Check if route is public or requires authentication
if (!in_array($action, $publicRoutes) && !in_array($action, $apiRoutes)) {
	AuthController::requireLogin();
}

// Handle API routes separately (no layout)
if (in_array($action, $apiRoutes)) {
	header('Content-Type: application/json');

	switch ($action) {
		case 'api_help_topics':
			AuthController::requireLogin();
			$departmentId = $_GET['department_id'] ?? null;
			if ($departmentId) {
				$helpTopicModel = new HelpTopicModel();
				$topics = $helpTopicModel->getByDepartment($departmentId);
				echo json_encode(['success' => true, 'topics' => $topics]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Department ID required']);
			}
			exit;
	}
}

// Start output buffering to capture view content
ob_start();

// Route handling
switch ($action) {
	// Authentication routes
	case 'login':
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$authController = new AuthController();
			$authController->login();
		} else {
			$authController = new AuthController();
			$authController->showLogin();
		}
		break;

	case 'register':
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$authController = new AuthController();
			$authController->register();
		} else {
			$authController = new AuthController();
			$authController->showRegister();
		}
		break;

	case 'logout':
		$authController = new AuthController();
		$authController->logout();
		break;
	// Ticket Management
	case 'tickets':
		$ticketController = new TicketController();
		$ticketController->index();
		break;

	case 'ticket_create':
		$ticketController = new TicketController();
		$ticketController->create();
		break;

	case 'ticket_store':
		$ticketController = new TicketController();
		$ticketController->store();
		break;

	case 'ticket_detail':
		$ticketController = new TicketController();
		$ticketController->detail();
		break;

	case 'ticket_reply':
		$ticketController = new TicketController();
		$ticketController->addReply();
		break;

	case 'ticket_update_status':
		$ticketController = new TicketController();
		$ticketController->updateStatus();
		break;

	case 'ticket_assign':
		$ticketController = new TicketController();
		$ticketController->assign();
		break;

	// Profile Management
	case 'profile':
		$profileController = new ProfileController();
		$profileController->index();
		break;

	case 'profile_update':
		$profileController = new ProfileController();
		$profileController->update();
		break;

	// Knowledge Base
	case 'knowledge_base':
		$kbController = new KnowledgeBaseController();
		$kbController->index();
		break;

	case 'kb_view':
		$articleId = $_GET['id'] ?? null;
		$kbController = new KnowledgeBaseController();
		$kbController->view($articleId);
		break;

	case 'kb_create':
		$kbController = new KnowledgeBaseController();
		$kbController->create();
		break;

	case 'kb_store':
		$kbController = new KnowledgeBaseController();
		$kbController->store();
		break;

	case 'kb_edit':
		$articleId = $_GET['id'] ?? null;
		$kbController = new KnowledgeBaseController();
		$kbController->edit($articleId);
		break;

	case 'kb_update':
		$kbController = new KnowledgeBaseController();
		$kbController->update();
		break;

	case 'kb_delete':
		$kbController = new KnowledgeBaseController();
		$kbController->delete();
		break;

	// Dashboard
	case 'dashboard':
	case 'index':
		$dashboardController = new DashboardController();
		$dashboardController->index();
		break;

	// Admin only - User management
	case 'users':
		AuthController::requireRole('admin');
		$userController = new UserController();
		$userController->index();
		break;

	case 'user_detail':
		AuthController::requireRole('admin');
		$userId = $_GET['id'] ?? null;
		$userController = new UserController();
		$userController->detail($userId);
		break;

	case 'user_create':
		AuthController::requireRole('admin');
		$userController = new UserController();
		$userController->create();
		break;

	case 'user_store':
		AuthController::requireRole('admin');
		$userController = new UserController();
		$userController->store();
		break;

	case 'user_edit':
		AuthController::requireRole('admin');
		$userId = $_GET['id'] ?? null;
		$userController = new UserController();
		$userController->edit($userId);
		break;

	case 'user_update':
		AuthController::requireRole('admin');
		$userController = new UserController();
		$userController->updateUser();
		break;

	case 'user_delete':
		AuthController::requireRole('admin');
		$userController = new UserController();
		$userController->deleteUser();
		break;

	// Admin only - Department management
	case 'departments':
		AuthController::requireRole('admin');
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->index();
		break;

	case 'department_detail':
		AuthController::requireRole('admin');
		$deptId = $_GET['id'] ?? null;
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->detail($deptId);
		break;

	case 'department_create':
		AuthController::requireRole('admin');
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->create();
		break;

	case 'department_store':
		AuthController::requireRole('admin');
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->store();
		break;

	case 'department_edit':
		AuthController::requireRole('admin');
		$deptId = $_GET['id'] ?? null;
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->edit($deptId);
		break;

	case 'department_update':
		AuthController::requireRole('admin');
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->updateDepartment();
		break;

	case 'department_delete':
		AuthController::requireRole('admin');
		$departmentController = new \App\Controllers\DepartmentController();
		$departmentController->deleteDepartment();
		break;

	default:
		$dashboardController = new DashboardController();
		$dashboardController->index();
		break;
}

// Capture the view content
$content = ob_get_clean();

// For login/register pages, use different layout
$isAuthPage = in_array($action, $publicRoutes);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CS Ticket - Customer Support System</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
	<style>
		body {
			background-color: #f8f9fa;
		}

		.navbar-brand {
			font-weight: bold;
		}

		.content-wrapper {
			min-height: calc(100vh - 200px);
		}
	</style>
</head>

<body>
	<?php if (!$isAuthPage): ?>
		<!-- Navigation -->
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php?action=dashboard">
					<i class="bi bi-ticket-perforated"></i> CS Ticket System
				</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav me-auto">
						<li class="nav-item">
							<a class="nav-link" href="index.php?action=dashboard">
								<i class="bi bi-speedometer2"></i> Dashboard
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="index.php?action=tickets">
								<i class="bi bi-ticket"></i> Tickets
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="index.php?action=knowledge_base">
								<i class="bi bi-book"></i> Knowledge Base
							</a>
						</li>
						<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
									<i class="bi bi-gear"></i> Admin
								</a>
								<ul class="dropdown-menu">
									<li><a class="dropdown-item" href="index.php?action=users">Users</a></li>
									<li><a class="dropdown-item" href="index.php?action=departments">Departments</a></li>
									<li><a class="dropdown-item" href="index.php?action=help_topics">Help Topics</a></li>
								</ul>
							</li>
						<?php endif; ?>
					</ul>
					<ul class="navbar-nav">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
								<i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-end">
								<li><a class="dropdown-item" href="index.php?action=profile">
										<i class="bi bi-person"></i> My Profile
									</a></li>
								<li>
									<hr class="dropdown-divider">
								</li>
								<li><a class="dropdown-item" href="index.php?action=logout">
										<i class="bi bi-box-arrow-right"></i> Logout
									</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	<?php endif; ?>

	<!-- Main Content -->
	<div class="container-fluid content-wrapper py-4">
		<div class="container<?= $isAuthPage ? '' : '' ?>">
			<?php echo $content; ?>
		</div>
	</div>

	<!-- Footer -->
	<footer class="text-muted text-center py-3 mt-auto">
		<p class="mb-0">&copy; 2026 CS Ticket System. All rights reserved.</p>
	</footer>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>