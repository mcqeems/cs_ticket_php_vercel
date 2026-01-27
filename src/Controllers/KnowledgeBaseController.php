<?php
namespace App\Controllers;

use App\Models\KnowledgeBaseModel;

class KnowledgeBaseController
{
	private $model;

	public function __construct()
	{
		$this->model = new KnowledgeBaseModel();
	}

	/**
	 * List all knowledge base articles
	 */
	public function index()
	{
		$search = $_GET['search'] ?? '';
		$category = $_GET['category'] ?? '';
		$tag = $_GET['tag'] ?? '';
		$userRole = $_SESSION['user_role'] ?? 'customer';

		if (!empty($search)) {
			$articles = $this->model->search($search);
		} else {
			// Admin, agents and support_agents can see drafts, customers only see published
			$publishedOnly = !in_array($userRole, ['admin', 'agent', 'support_agent']);

			// Build filters
			$filters = [];
			if (!empty($category)) {
				$filters['category'] = $category;
			}
			if (!empty($tag)) {
				$filters['tag'] = $tag;
			}

			$articles = $this->model->getAll($publishedOnly, $filters);
		}

		// Get statistics for agents, support_agents and admins
		$stats = null;
		if (in_array($userRole, ['admin', 'agent', 'support_agent'])) {
			$stats = $this->model->getStatistics();
		}

		$viewPath = __DIR__ . '/../Views/knowledge_base_list.php';
		include $viewPath;
	}

	/**
	 * View single article
	 */
	public function view($articleId)
	{
		$article = $this->model->getById($articleId);

		if (!$article) {
			$_SESSION['error'] = 'Article not found.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Check if customer trying to view draft
		if ($article['status'] === 'draft' && $_SESSION['user_role'] === 'customer') {
			$_SESSION['error'] = 'Article not available.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Increment view count
		$this->model->incrementViews($articleId);

		$viewPath = __DIR__ . '/../Views/knowledge_base_detail.php';
		include $viewPath;
	}

	/**
	 * Show create form
	 */
	public function create()
	{
		// Only agents, support_agents and admins can create
		if (!in_array($_SESSION['user_role'], ['admin', 'agent', 'support_agent'])) {
			$_SESSION['error'] = 'You do not have permission to create articles.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		$viewPath = __DIR__ . '/../Views/knowledge_base_form.php';
		include $viewPath;
	}

	/**
	 * Store new article
	 */
	public function store()
	{
		// Only agents, support_agents and admins can create
		if (!in_array($_SESSION['user_role'], ['admin', 'agent', 'support_agent'])) {
			$_SESSION['error'] = 'You do not have permission to create articles.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Validate input
		$errors = [];

		if (empty($_POST['title'])) {
			$errors[] = 'Title is required';
		}

		if (empty($_POST['content'])) {
			$errors[] = 'Content is required';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=kb_create');
			exit;
		}

		// Create article
		try {
			$data = $_POST;
			$data['author_id'] = $_SESSION['user_id'];
			$this->model->create($data);
			$_SESSION['success'] = 'Article created successfully!';
			header('Location: index.php?action=knowledge_base');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error creating article: ' . $e->getMessage();
			header('Location: index.php?action=kb_create');
		}
		exit;
	}

	/**
	 * Show edit form
	 */
	public function edit($articleId)
	{
		$article = $this->model->getById($articleId);

		if (!$article) {
			$_SESSION['error'] = 'Article not found.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Check permissions
		$canEdit = $this->canModifyArticle($article);
		if (!$canEdit) {
			$_SESSION['error'] = 'You do not have permission to edit this article.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		$viewPath = __DIR__ . '/../Views/knowledge_base_form.php';
		include $viewPath;
	}

	/**
	 * Update article
	 */
	public function update()
	{
		$articleId = $_POST['article_id'] ?? null;

		if (!$articleId) {
			$_SESSION['error'] = 'Article ID is required.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		$article = $this->model->getById($articleId);

		if (!$article) {
			$_SESSION['error'] = 'Article not found.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Check permissions
		$canEdit = $this->canModifyArticle($article);
		if (!$canEdit) {
			$_SESSION['error'] = 'You do not have permission to edit this article.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Validate input
		$errors = [];

		if (empty($_POST['title'])) {
			$errors[] = 'Title is required';
		}

		if (empty($_POST['content'])) {
			$errors[] = 'Content is required';
		}

		if (!empty($errors)) {
			$_SESSION['error'] = implode('<br>', $errors);
			header('Location: index.php?action=kb_edit&id=' . $articleId);
			exit;
		}

		// Update article
		try {
			$this->model->update($articleId, $_POST);
			$_SESSION['success'] = 'Article updated successfully!';
			header('Location: index.php?action=kb_view&id=' . $articleId);
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error updating article: ' . $e->getMessage();
			header('Location: index.php?action=kb_edit&id=' . $articleId);
		}
		exit;
	}

	/**
	 * Delete article
	 */
	public function delete()
	{
		$articleId = $_POST['article_id'] ?? null;

		if (!$articleId) {
			$_SESSION['error'] = 'Article ID is required.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		$article = $this->model->getById($articleId);

		if (!$article) {
			$_SESSION['error'] = 'Article not found.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Check permissions
		$canDelete = $this->canModifyArticle($article);
		if (!$canDelete) {
			$_SESSION['error'] = 'You do not have permission to delete this article.';
			header('Location: index.php?action=knowledge_base');
			exit;
		}

		// Delete article
		try {
			$this->model->delete($articleId);
			$_SESSION['success'] = 'Article deleted successfully!';
			header('Location: index.php?action=knowledge_base');
		} catch (\Exception $e) {
			$_SESSION['error'] = 'Error deleting article: ' . $e->getMessage();
			header('Location: index.php?action=knowledge_base');
		}
		exit;
	}

	/**
	 * Check if user can modify article (edit or delete)
	 */
	private function canModifyArticle($article)
	{
		$userRole = $_SESSION['user_role'] ?? 'customer';
		$userId = $_SESSION['user_id'] ?? null;

		// Admin can modify anything
		if ($userRole === 'admin') {
			return true;
		}

		// Agent or support_agent can only modify their own articles
		if (in_array($userRole, ['agent', 'support_agent']) && isset($article['created_by'])) {
			return $article['created_by']->__toString() === $userId;
		}

		// Check if user is the author (for any other role)
		if (isset($article['created_by']) && $userId) {
			return $article['created_by']->__toString() === $userId;
		}

		// Default: cannot modify
		return false;
	}
}
?>