<div class="card">
	<div class="card-header bg-primary text-white">
		<h3 class="mb-0"><i class="bi bi-people-fill"></i> Users Management</h3>
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

		<?php if (empty($users)): ?>
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i> No users found in the database.
			</div>
		<?php else: ?>
			<div class="d-flex justify-content-between align-items-center mb-3">
				<p class="text-muted mb-0">
					<strong>Total users:</strong> <?= count($users) ?>
				</p>
				<a href="index.php?action=user_create" class="btn btn-success btn-sm">
					<i class="bi bi-plus-circle"></i> Add New User
				</a>
			</div>

			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead class="table-dark">
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Email</th>
							<th>Role</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php $counter = 1; ?>
						<?php foreach ($users as $user): ?>
							<tr>
								<td><?= $counter++ ?></td>
								<td><?= htmlspecialchars($user['name'] ?? 'N/A') ?></td>
								<td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
								<td>
									<span
										class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'support_agent' ? 'primary' : 'secondary') ?>">
										<?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role'] ?? 'N/A'))) ?>
									</span>
								</td>
								<td>
									<a href="index.php?action=user_detail&id=<?= $user['_id'] ?>" class="btn btn-sm btn-primary"
										title="View Details">
										<i class="bi bi-eye"></i>
									</a>
									<a href="index.php?action=user_edit&id=<?= $user['_id'] ?>" class="btn btn-sm btn-warning"
										title="Edit User">
										<i class="bi bi-pencil"></i>
									</a>
									<?php if ((string) $user['_id'] !== $_SESSION['user_id']): ?>
										<a href="index.php?action=user_delete&id=<?= $user['_id'] ?>" class="btn btn-sm btn-danger"
											title="Delete User" onclick="return confirm('Are you sure you want to delete this user?')">
											<i class="bi bi-trash"></i>
										</a>
									<?php else: ?>
										<button class="btn btn-sm btn-secondary" disabled title="Cannot delete yourself">
											<i class="bi bi-trash"></i>
										</button>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>