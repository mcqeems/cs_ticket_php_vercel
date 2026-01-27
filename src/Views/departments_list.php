<div class="card">
	<div class="card-header bg-primary text-white">
		<h3 class="mb-0"><i class="bi bi-building"></i> Department Management</h3>
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

		<?php if (empty($departments)): ?>
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i> No departments found in the database.
			</div>
		<?php else: ?>
			<div class="d-flex justify-content-between align-items-center mb-3">
				<p class="text-muted mb-0">
					<strong>Total departments:</strong> <?= count($departments) ?>
				</p>
				<a href="index.php?action=department_create" class="btn btn-success btn-sm">
					<i class="bi bi-plus-circle"></i> Add New Department
				</a>
			</div>

			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead class="table-dark">
						<tr>
							<th>#</th>
							<th>Department Name</th>
							<th>Description</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php $counter = 1; ?>
						<?php foreach ($departments as $dept): ?>
							<tr>
								<td><?= $counter++ ?></td>
								<td>
									<strong><?= htmlspecialchars($dept['name']) ?></strong>
								</td>
								<td>
									<?= htmlspecialchars(substr($dept['description'] ?? 'No description', 0, 50)) ?>
									<?= strlen($dept['description'] ?? '') > 50 ? '...' : '' ?>
								</td>
								<td>
									<span class="badge bg-<?= $dept['status'] === 'active' ? 'success' : 'secondary' ?>">
										<?= htmlspecialchars(ucfirst($dept['status'] ?? 'active')) ?>
									</span>
								</td>
								<td>
									<a href="index.php?action=department_detail&id=<?= $dept['_id'] ?>" class="btn btn-sm btn-info"
										title="View Details">
										<i class="bi bi-eye"></i>
									</a>
									<a href="index.php?action=department_edit&id=<?= $dept['_id'] ?>" class="btn btn-sm btn-warning"
										title="Edit Department">
										<i class="bi bi-pencil"></i>
									</a>
									<a href="index.php?action=department_delete&id=<?= $dept['_id'] ?>" class="btn btn-sm btn-danger"
										title="Delete Department"
										onclick="return confirm('Are you sure you want to delete this department? This action cannot be undone.')">
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