<div class="row justify-content-center">
	<div class="col-lg-10">
		<!-- Article Header -->
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start mb-3">
					<div>
						<span class="badge bg-<?= $article['status'] === 'published' ? 'success' : 'warning' ?> mb-2">
							<?= ucfirst($article['status']) ?>
						</span>
						<span class="badge bg-secondary mb-2 ms-1"><?= ucfirst($article['category'] ?? 'general') ?></span>
					</div>
					<div>
						<?php
						// Check if user can edit/delete this article
						$canModify = false;
						if ($_SESSION['user_role'] === 'admin') {
							$canModify = true;
						} elseif (in_array($_SESSION['user_role'], ['agent', 'support_agent']) && isset($article['created_by'])) {
							$canModify = $article['created_by']->__toString() === $_SESSION['user_id'];
						} elseif (isset($article['created_by'])) {
							$canModify = $article['created_by']->__toString() === $_SESSION['user_id'];
						}
						?>
						<?php if ($canModify): ?>
							<a href="index.php?action=kb_edit&id=<?= $article['_id'] ?>" class="btn btn-sm btn-warning">
								<i class="bi bi-pencil"></i> Edit
							</a>
							<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
								<i class="bi bi-trash"></i> Delete
							</button>
						<?php endif; ?>
					</div>
				</div>

				<h1 class="display-5 mb-3"><?= htmlspecialchars($article['title']) ?></h1>

				<div class="d-flex justify-content-between align-items-center text-muted">
					<div>
						<i class="bi bi-person-circle"></i>
						<strong><?= htmlspecialchars($article['author']['name'] ?? 'Unknown') ?></strong>
						<?php if (isset($article['created_at'])): ?>
							<span class="ms-3">
								<i class="bi bi-calendar"></i>
								<?= date('F d, Y', $article['created_at']->toDateTime()->getTimestamp()) ?>
							</span>
						<?php endif; ?>
					</div>
					<div>
						<i class="bi bi-eye"></i> <?= $article['views'] ?? 0 ?> views
					</div>
				</div>

				<?php if (!empty($article['tags'])): ?>
					<div class="mt-3">
						<?php foreach ($article['tags'] as $tag): ?>
							<span class="badge bg-light text-dark">#<?= htmlspecialchars($tag) ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Article Content -->
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<div class="article-content">
					<?= $article['content'] ?>
				</div>
			</div>
		</div>

		<!-- Article Footer -->
		<div class="card shadow-sm">
			<div class="card-body">
				<a href="index.php?action=knowledge_base" class="btn btn-secondary">
					<i class="bi bi-arrow-left"></i> Back to Knowledge Base
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<?php if (isset($canModify) && $canModify): ?>
	<div class="modal fade" id="deleteModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-danger text-white">
					<h5 class="modal-title">Confirm Delete</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to delete this article?</p>
					<p class="text-muted mb-0">This action cannot be undone.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<form method="POST" action="index.php?action=kb_delete" class="d-inline">
						<input type="hidden" name="article_id" value="<?= $article['_id'] ?>">
						<button type="submit" class="btn btn-danger">
							<i class="bi bi-trash"></i> Delete Article
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<style>
	.article-content {
		font-size: 1.1rem;
		line-height: 1.8;
	}
</style>