<div class="row mb-4">
	<div class="col-md-12">
		<a href="index.php?action=departments" class="btn btn-secondary">
			<i class="bi bi-arrow-left"></i> Back to Departments List
		</a>
	</div>
</div>

<?php if (!isset($department) || empty($department)): ?>
	<div class="alert alert-danger">
		<i class="bi bi-exclamation-triangle"></i> Department not found.
	</div>
<?php else: ?>
	<div class="row">
		<div class="col-md-4">
			<!-- Department Info Card -->
			<div class="card shadow-sm mb-4 border-0">
				<div class="card-body text-center p-4">
					<div class="mb-4">
						<div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
							style="width: 120px; height: 120px;">
							<i class="bi bi-building" style="font-size: 60px; color: #0d6efd;"></i>
						</div>
					</div>
					<h3 class="card-title mb-2"><?= htmlspecialchars($department['name']) ?></h3>
					<span class="badge bg-<?= $department['status'] === 'active' ? 'success' : 'secondary' ?> px-3 py-2 mb-4">
						<i class="bi bi-<?= $department['status'] === 'active' ? 'check-circle' : 'dash-circle' ?>"></i>
						<?= htmlspecialchars(ucfirst($department['status'])) ?>
					</span>
					<hr class="my-3">
					<div class="d-grid gap-2">
						<a href="index.php?action=department_edit&id=<?= $department['_id'] ?>" class="btn btn-primary btn-lg">
							<i class="bi bi-pencil"></i> Edit Department
						</a>
						<a href="index.php?action=department_delete&id=<?= $department['_id'] ?>" class="btn btn-outline-danger"
							onclick="return confirm('Are you sure you want to delete this department? This action cannot be undone.')">
							<i class="bi bi-trash"></i> Delete Department
						</a>
					</div>
				</div>
			</div>

			<!-- Statistics Card -->
			<div class="card shadow-sm border-0">
				<div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
					<h5 class="mb-0"><i class="bi bi-graph-up"></i> Department Statistics</h5>
				</div>
				<div class="card-body p-4">
					<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
						<div>
							<i class="bi bi-ticket-perforated text-primary fs-4"></i>
							<span class="ms-2">Total Tickets</span>
						</div>
						<h4 class="mb-0 text-primary"><?= $stats['total_tickets'] ?? 0 ?></h4>
					</div>
					<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
						<div>
							<i class="bi bi-hourglass-split text-warning fs-4"></i>
							<span class="ms-2">Open Tickets</span>
						</div>
						<h4 class="mb-0 text-warning"><?= $stats['open_tickets'] ?? 0 ?></h4>
					</div>
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<i class="bi bi-people text-success fs-4"></i>
							<span class="ms-2">Active Agents</span>
						</div>
						<h4 class="mb-0 text-success"><?= $stats['agents_count'] ?? 0 ?></h4>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<!-- Department Details Card -->
			<div class="card shadow-sm mb-4 border-0">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="bi bi-info-circle-fill"></i> Department Information</h5>
				</div>
				<div class="card-body p-4">
					<table class="table table-borderless">
						<tbody>
							<tr>
								<td class="text-muted" style="width: 35%;"><strong>Department ID</strong></td>
								<td><code><?= htmlspecialchars($department['_id']) ?></code></td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Department Name</strong></td>
								<td><strong class="text-dark fs-5"><?= htmlspecialchars($department['name']) ?></strong></td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Description</strong></td>
								<td>
									<?php if (!empty($department['description'])): ?>
										<p class="mb-0"><?= nl2br(htmlspecialchars($department['description'])) ?></p>
									<?php else: ?>
										<span class="text-muted fst-italic">No description provided</span>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Status</strong></td>
								<td>
									<span class="badge bg-<?= $department['status'] === 'active' ? 'success' : 'secondary' ?> px-3 py-2">
										<i class="bi bi-<?= $department['status'] === 'active' ? 'check-circle' : 'dash-circle' ?>"></i>
										<?= htmlspecialchars(ucfirst($department['status'])) ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Created Date</strong></td>
								<td>
									<?php if (isset($department['created_at'])): ?>
										<i class="bi bi-calendar-plus text-primary"></i>
										<?= date('F d, Y', $department['created_at']->toDateTime()->getTimestamp()) ?>
										<small class="text-muted">at
											<?= date('h:i A', $department['created_at']->toDateTime()->getTimestamp()) ?></small>
									<?php else: ?>
										<span class="text-muted fst-italic">Not available</span>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Last Updated</strong></td>
								<td>
									<?php if (isset($department['updated_at'])): ?>
										<i class="bi bi-calendar-check text-success"></i>
										<?= date('F d, Y', $department['updated_at']->toDateTime()->getTimestamp()) ?>
										<small class="text-muted">at
											<?= date('h:i A', $department['updated_at']->toDateTime()->getTimestamp()) ?></small>
									<?php else: ?>
										<span class="text-muted fst-italic">Not available</span>
									<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Recent Tickets Card -->
			<div class="card shadow-sm border-0">
				<div class="card-header text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
					<h5 class="mb-0"><i class="bi bi-ticket-detailed-fill"></i> Tickets Overview</h5>
				</div>
				<div class="card-body p-4">
					<?php if (($stats['total_tickets'] ?? 0) === 0): ?>
						<div class="text-center py-5">
							<i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
							<p class="text-muted mt-3 mb-0">No tickets found for this department yet.</p>
							<small class="text-muted">Tickets will appear here once created.</small>
						</div>
					<?php else: ?>
						<div class="alert alert-info border-0 bg-info bg-opacity-10">
							<i class="bi bi-info-circle-fill text-info"></i>
							This department currently manages <strong><?= $stats['total_tickets'] ?></strong> ticket(s).
						</div>
						<div class="d-grid">
							<a href="index.php?action=tickets&department=<?= $department['_id'] ?>" class="btn btn-primary">
								<i class="bi bi-eye"></i> View All Tickets
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<style>
	.card {
		transition: transform 0.2s, box-shadow 0.2s;
	}

	.card:hover {
		transform: translateY(-2px);
		box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
	}
</style>