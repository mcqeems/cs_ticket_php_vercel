<?php
namespace App\Controllers;

use App\Models\DepartmentModel;

class DepartmentController
{
	private $model;

	public function __construct()
	{
		$this->model = new DepartmentModel();
	}

	public function index()
	{
		$departments = $this->model->getAll(false); // Get all including inactive

		// Load the view
		$viewPath = __DIR__ . '/../Views/departments_list.php';
		include $viewPath;
	}

	public function detail($deptId)
	{
		$department = $this->model->getById($deptId);

		if (!$department) {
			$_SESSION['error'] = 'Department not found.';
			header('Location: index.php?action=departments');
			exit;
		}

		// Get statistics
		$stats = $this->model->getStatistics($deptId);

		// Load the view
		$viewPath = __DIR__ . '/../Views/department_detail.php';
		include $viewPath;
	}

	public function create()
	{
		// Show create form
		$viewPath = __DIR__ . '/../Views/department_form.php';
		include $viewPath;
	}

	public function store()
	{
		// Validate input
		$errors = [];

		if (empty($_POST['name'])) {
			$errors[] = 'Department name is required';
		} else {
			// Check if name already exists
			$existing = $this->model->findByName($_POST['name']);
			if ($existing) {
				$errors[] = 'Department name already exists';
			}
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=department_create');
			exit;
		}

		// Create department
		try {
			$this->model->create($_POST);
			$_SESSION['success'] = 'Department created successfully!';
			header('Location: index.php?action=departments');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error creating department: ' . $e->getMessage();
			header('Location: index.php?action=department_create');
		}
		exit;
	}

	public function edit($deptId)
	{
		$department = $this->model->getById($deptId);

		if (!$department) {
			$_SESSION['error'] = 'Department not found.';
			header('Location: index.php?action=departments');
			exit;
		}

		// Load edit form
		$viewPath = __DIR__ . '/../Views/department_form.php';
		include $viewPath;
	}

	public function updateDepartment()
	{
		$deptId = $_POST['dept_id'] ?? null;

		if (!$deptId) {
			$_SESSION['error'] = 'Department ID is required';
			header('Location: index.php?action=departments');
			exit;
		}

		// Validate input
		$errors = [];

		if (empty($_POST['name'])) {
			$errors[] = 'Department name is required';
		} else {
			// Check if name exists for other departments
			$existing = $this->model->findByName($_POST['name']);
			if ($existing && (string) $existing['_id'] !== $deptId) {
				$errors[] = 'Department name already exists';
			}
		}

		if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Invalid email format';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=department_edit&id=' . $deptId);
			exit;
		}

		// Update department
		try {
			$this->model->update($deptId, $_POST);
			$_SESSION['success'] = 'Department updated successfully!';
			header('Location: index.php?action=departments');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error updating department: ' . $e->getMessage();
			header('Location: index.php?action=department_edit&id=' . $deptId);
		}
		exit;
	}

	public function deleteDepartment()
	{
		$deptId = $_GET['id'] ?? null;

		if (!$deptId) {
			$_SESSION['error'] = 'Department ID is required';
			header('Location: index.php?action=departments');
			exit;
		}

		// Check if department has tickets
		$stats = $this->model->getStatistics($deptId);
		if ($stats['total_tickets'] > 0) {
			$_SESSION['error'] = 'Cannot delete department with existing tickets. Please reassign tickets first.';
			header('Location: index.php?action=departments');
			exit;
		}

		try {
			$this->model->delete($deptId);
			$_SESSION['success'] = 'Department deleted successfully!';
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error deleting department: ' . $e->getMessage();
		}

		header('Location: index.php?action=departments');
		exit;
	}
}
?>