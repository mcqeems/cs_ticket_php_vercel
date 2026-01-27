<div class="card">
	<div class="card-header bg-primary text-white">
		<h3 class="mb-0"><i class="bi bi-tags"></i> Help Topics Management</h3>
	</div>
	<div class="card-body">
		<?php if (isset($_SESSION['success'])): ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<i class="bi bi-check-circle"></i> <?= $_SESSION['success'] ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
			<?php unset($_SESSION['success']); ?>
		<?php endif; ?>

		<?php if (isset($_SESSION['error'])): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
			<?php unset($_SESSION['error']); ?>
		<?php endif; ?>

		<?php if (empty($topics)): ?>
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i> No help topics found in the database.
			</div>
		<?php else: ?>
			<div class="d-flex justify-content-between align-items-center mb-3">
				<p class="text-muted mb-0">
					<strong>Total help topics:</strong> <?= count($topics) ?>
				</p>
				<a href="index.php?action=help_topic_create" class="btn btn-success btn-sm">
					<i class="bi bi-plus-circle"></i> Add New Help Topic
				</a>
			</div>

			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead class="table-dark">
						<tr>
							<th>#</th>
							<th>Topic Name</th>
							<th>Status</th>
							<th>Created At</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php $counter = 1; ?>
						<?php foreach ($topics as $topic): ?>
							<tr>
								<td><?= $counter++ ?></td>
								<td>
									<strong><?= htmlspecialchars($topic['topic_name']) ?></strong>
								</td>
								<td>
									<span class="badge bg-<?= $topic['status'] === 'active' ? 'success' : 'secondary' ?>">
										<?= htmlspecialchars(ucfirst($topic['status'] ?? 'active')) ?>
									</span>
								</td>
								<td>
									<?php
									if (isset($topic['created_at'])) {
										$created = $topic['created_at']->toDateTime();
										echo htmlspecialchars($created->format('M d, Y H:i'));
									} else {
										echo 'N/A';
									}
									?>
								</td>
								<td>
									<a href="index.php?action=help_topic_detail&id=<?= $topic['_id'] ?>" class="btn btn-sm btn-info"
										title="View Details">
										<i class="bi bi-eye"></i>
									</a>
									<a href="index.php?action=help_topic_edit&id=<?= $topic['_id'] ?>" class="btn btn-sm btn-warning"
										title="Edit Help Topic">
										<i class="bi bi-pencil"></i>
									</a>
									<a href="index.php?action=help_topic_delete&id=<?= $topic['_id'] ?>" class="btn btn-sm btn-danger"
										title="Delete Help Topic"
										onclick="return confirm('Are you sure you want to delete this help topic? This action cannot be undone.')">
										<i class="bi bi-trash"></i>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>