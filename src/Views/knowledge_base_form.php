<?php
$isEdit = isset($article) && $article;
$pageTitle = $isEdit ? 'Edit Article' : 'Create New Article';
?>

<div class="row justify-content-center">
	<div class="col-lg-10">
		<div class="card shadow">
			<div class="card-header bg-primary text-white">
				<h3 class="mb-0">
					<i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?>"></i> <?= $pageTitle ?>
				</h3>
			</div>
			<div class="card-body">
				<?php if (isset($_SESSION['error'])): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					<?php unset($_SESSION['error']); ?>
				<?php endif; ?>

				<form method="POST" action="index.php?action=<?= $isEdit ? 'kb_update' : 'kb_store' ?>">
					<?php if ($isEdit): ?>
						<input type="hidden" name="article_id" value="<?= $article['_id'] ?>">
					<?php endif; ?>

					<div class="mb-3">
						<label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
						<input type="text" class="form-control form-control-lg" id="title" name="title"
							value="<?= $isEdit ? htmlspecialchars($article['title']) : '' ?>" required
							placeholder="e.g., How to Reset Your Password">
					</div>

					<div class="row mb-3">
						<div class="col-md-6">
							<label for="category" class="form-label">Category <span class="text-danger">*</span></label>
							<select class="form-select" id="category" name="category" required>
								<option value="general" <?= ($isEdit && $article['category'] === 'general') ? 'selected' : '' ?>>General
								</option>
								<option value="getting-started" <?= ($isEdit && $article['category'] === 'getting-started') ? 'selected' : '' ?>>Getting Started</option>
								<option value="troubleshooting" <?= ($isEdit && $article['category'] === 'troubleshooting') ? 'selected' : '' ?>>Troubleshooting</option>
								<option value="faq" <?= ($isEdit && $article['category'] === 'faq') ? 'selected' : '' ?>>FAQ</option>
								<option value="account" <?= ($isEdit && $article['category'] === 'account') ? 'selected' : '' ?>>Account
								</option>
								<option value="billing" <?= ($isEdit && $article['category'] === 'billing') ? 'selected' : '' ?>>Billing
								</option>
								<option value="technical" <?= ($isEdit && $article['category'] === 'technical') ? 'selected' : '' ?>>
									Technical</option>
							</select>
						</div>
						<div class="col-md-6">
							<label for="status" class="form-label">Status <span class="text-danger">*</span></label>
							<select class="form-select" id="status" name="status" required>
								<option value="draft" <?= ($isEdit && $article['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
								<option value="published" <?= ($isEdit && $article['status'] === 'published') ? 'selected' : '' ?>>
									Published</option>
							</select>
							<small class="text-muted">Only published articles are visible to customers</small>
						</div>
					</div>

					<div class="mb-3">
						<label for="tags" class="form-label">Tags</label>
						<input type="text" class="form-control" id="tags" name="tags"
							value="<?= $isEdit && !empty($article['tags']) ? htmlspecialchars(implode(', ', (array) $article['tags'])) : '' ?>"
							placeholder="password, login, account (separate with commas)">
						<small class="text-muted">Add tags to help users find this article (separate with commas)</small>
					</div>

					<div class="mb-3">
						<label for="content" class="form-label">Content <span class="text-danger">*</span></label>
						<textarea class="form-control" id="content" name="content" rows="15" required
							placeholder="Write the article content here..."><?= $isEdit ? htmlspecialchars($article['content']) : '' ?></textarea>
						<small class="text-muted">Write clear and detailed instructions to help users</small>
					</div>

					<div class="d-flex justify-content-between">
						<a href="index.php?action=knowledge_base" class="btn btn-secondary">
							<i class="bi bi-arrow-left"></i> Cancel
						</a>
						<div>
							<?php if ($isEdit): ?>
								<button type="submit" name="status" value="draft" class="btn btn-warning">
									<i class="bi bi-file-earmark"></i> Save as Draft
								</button>
							<?php endif; ?>
							<button type="submit" name="status" value="published" class="btn btn-success">
								<i class="bi bi-check-circle"></i> <?= $isEdit ? 'Update & Publish' : 'Publish Article' ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- Writing Tips Card -->
		<div class="card shadow mt-4">
			<div class="card-body">
				<h5 class="card-title">
					<i class="bi bi-lightbulb text-warning"></i> Writing Tips
				</h5>
				<ul class="mb-0">
					<li>Use clear and concise language</li>
					<li>Break content into sections with clear headings</li>
					<li>Include step-by-step instructions where applicable</li>
					<li>Add relevant tags to improve searchability</li>
					<li>Save as draft to review later before publishing</li>
				</ul>
			</div>
		</div>
	</div>
</div>