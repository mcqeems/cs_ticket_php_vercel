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
			<div class="card mb-2">
				<div class="card-header bg-info text-white">
					<h5 class="mb-0"><i class="bi bi-bar-chart"></i> Quick Stats</h5>
				</div>
				<div class="card-body">
					<div class="d-flex justify-content-between mb-2">
						<span>Total Tickets:</span>
						<strong><?= $ticketStats['total'] ?? 0 ?></strong>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>Open Tickets:</span>
						<strong class="text-warning"><?= $ticketStats['open'] ?? 0 ?></strong>
					</div>
					<?php if (isset($ticketStats['process'])): ?>
						<div class="d-flex justify-content-between mb-2">
							<span>In Progress:</span>
							<strong class="text-info"><?= $ticketStats['process'] ?></strong>
						</div>
					<?php endif; ?>
					<div class="d-flex justify-content-between">
						<span>Closed Tickets:</span>
						<strong class="text-success"><?= $ticketStats['closed'] ?? $ticketStats['close'] ?? 0 ?></strong>
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
				<div class="card-body" style="height: 280px; overflow: auto;">
					<?php if (empty($recentActivity)): ?>
						<div class="alert alert-info mb-0">
							<i class="bi bi-info-circle"></i> No recent activity found.
						</div>
					<?php else: ?>
						<div class="activity-timeline">
							<?php foreach ($recentActivity as $activity): ?>
								<div class="activity-item mb-3 pb-3 border-bottom">
									<div class="d-flex justify-content-between align-items-start">
										<div class="flex-grow-1">
											<div class="d-flex align-items-center mb-1">
												<?php
												$actionIcon = match ($activity['action']) {
													'created' => '<i class="bi bi-plus-circle text-success"></i>',
													'status_changed' => '<i class="bi bi-arrow-repeat text-info"></i>',
													'assigned' => '<i class="bi bi-person-check text-primary"></i>',
													'replied' => '<i class="bi bi-chat-dots text-secondary"></i>',
													default => '<i class="bi bi-dot text-muted"></i>'
												};
												echo $actionIcon;
												?>
												<strong class="ms-2"><?= htmlspecialchars($activity['message']) ?></strong>
											</div>
											<?php if (isset($activity['ticket'])): ?>
												<small class="text-muted">
													Ticket:
													<a href="index.php?action=ticket_detail&id=<?= $activity['ticket']['_id'] ?>">
														<?= htmlspecialchars($activity['ticket']['ticket_number']) ?>
													</a>
													- <?= htmlspecialchars($activity['ticket']['subject']) ?>
												</small>
											<?php endif; ?>
										</div>
										<small class="text-muted text-nowrap ms-2">
											<?php
											$timestamp = $activity['timestamp']->toDateTime();
											$now = new DateTime();
											$diff = $now->diff($timestamp);

											if ($diff->d > 0) {
												echo $timestamp->format('M d, Y');
											} elseif ($diff->h > 0) {
												echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
											} elseif ($diff->i > 0) {
												echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
											} else {
												echo 'Just now';
											}
											?>
										</small>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>