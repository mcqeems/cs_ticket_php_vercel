<div class="row">
	<div class="col-md-12 mb-3">
		<a href="index.php?action=users" class="btn btn-secondary">
			<i class="bi bi-arrow-left"></i> Back to Users List
		</a>
	</div>
</div>

<?php if (!isset($user) || empty($user)): ?>
	<div class="alert alert-danger">
		<i class="bi bi-exclamation-triangle"></i> User not found.
	</div>
<?php else: ?>
	<div class="row">
		<div class="col-md-4">
			<!-- User Profile Card -->
			<div class="card mb-4">
				<div class="card-body text-center">
					<div class="mb-3">
						<i class="bi bi-person-circle" style="font-size: 100px; color: #6c757d;"></i>
					</div>
					<h4 class="card-title"><?= htmlspecialchars($user['name']) ?></h4>
					<p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
					<span
						class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'support_agent' ? 'primary' : 'secondary') ?> mb-3">
						<?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role']))) ?>
					</span>
					<div class="d-grid gap-2">
						<a href="index.php?action=user_edit&id=<?= $user['_id'] ?>" class="btn btn-primary">
							<i class="bi bi-pencil"></i> Edit User
						</a>
						<?php if ((string) $user['_id'] !== $_SESSION['user_id']): ?>
							<a href="index.php?action=user_delete&id=<?= $user['_id'] ?>" class="btn btn-danger"
								onclick="return confirm('Are you sure you want to delete this user?')">
								<i class="bi bi-trash"></i> Delete User
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Quick Stats Card -->
			<div class="card">
				<div class="card-header bg-info text-white">
					<h5 class="mb-0"><i class="bi bi-bar-chart"></i> Quick Stats</h5>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-between mb-2">
						<span>Total Tickets:</span>
						<strong>0</strong>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>Open Tickets:</span>
						<strong class="text-warning">0</strong>
					</div>
					<div class="d-flex justify-content-between">
						<span>Closed Tickets:</span>
						<strong class="text-success">0</strong>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<!-- User Details Card -->
			<div class="card mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="bi bi-info-circle"></i> User Information</h5>
				</div>
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-md-4">
							<strong>User ID:</strong>
						</div>
						<div class="col-md-8">
							<?= htmlspecialchars($user['_id'] ?? 'N/A') ?>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-4">
							<strong>Full Name:</strong>
						</div>
						<div class="col-md-8">
							<?= htmlspecialchars($user['name']) ?>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-4">
							<strong>Email Address:</strong>
						</div>
						<div class="col-md-8">
							<?= htmlspecialchars($user['email']) ?>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-4">
							<strong>Status:</strong>
						</div>
						<div class="col-md-8">
							<span class="badge bg-success">Active</span>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-4">
							<strong>Member Since:</strong>
						</div>
						<div class="col-md-8">
							<?= date('F d, Y') ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Recent Activity Card -->
			<div class="card">
				<div class="card-header bg-secondary text-white">
					<h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h5>
				</div>
				<div class="card-body">
					<div class="alert alert-info mb-0">
						<i class="bi bi-info-circle"></i> No recent activity found.
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>