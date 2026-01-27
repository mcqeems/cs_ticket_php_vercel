<div class="row mb-4">
	<div class="col">
		<h2><i class="bi bi-ticket-perforated"></i> Tickets</h2>
	</div>
	<div class="col-auto">
		<?php if ($_SESSION['user_role'] === 'client' || $_SESSION['user_role'] === 'admin'): ?>
			<a href="index.php?action=ticket_create" class="btn btn-primary">
				<i class="bi bi-plus-circle"></i> Create New Ticket
			</a>
		<?php endif; ?>
	</div>
</div>

<?php if (isset($_SESSION['success'])): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
	<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
	<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Statistics -->
<div class="row mb-4">
	<div class="col-md-3">
		<div class="card">
			<div class="card-body">
				<h6 class="text-muted">Total</h6>
				<h3><?= $stats['total'] ?></h3>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card border-warning">
			<div class="card-body">
				<h6 class="text-muted">Open</h6>
				<h3 class="text-warning"><?= $stats['open'] ?></h3>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card border-info">
			<div class="card-body">
				<h6 class="text-muted">In Progress</h6>
				<h3 class="text-info"><?= $stats['process'] ?></h3>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card border-success">
			<div class="card-body">
				<h6 class="text-muted">Closed</h6>
				<h3 class="text-success"><?= $stats['closed'] ?></h3>
			</div>
		</div>
	</div>
</div>

<!-- Filters -->
<div class="card mb-4">
	<div class="card-body">
		<form method="GET" action="index.php" class="row g-3">
			<input type="hidden" name="action" value="tickets">
			<div class="col-md-3">
				<label class="form-label">Status</label>
				<select name="status" class="form-select">
					<option value="">All Status</option>
					<option value="open" <?= (isset($_GET['status']) && $_GET['status'] === 'open') ? 'selected' : '' ?>>Open
					</option>
					<option value="process" <?= (isset($_GET['status']) && $_GET['status'] === 'process') ? 'selected' : '' ?>>In
						Progress</option>
					<option value="close" <?= (isset($_GET['status']) && $_GET['status'] === 'close') ? 'selected' : '' ?>>Closed
					</option>
				</select>
			</div>
			<?php if ($_SESSION['user_role'] === 'support_agent'): ?>
				<div class="col-md-3">
					<label class="form-label">View</label>
					<select name="filter" class="form-select">
						<option value="assigned" <?= (isset($_GET['filter']) && $_GET['filter'] === 'assigned') ? 'selected' : '' ?>>
							Assigned to Me</option>
						<option value="all" <?= (isset($_GET['filter']) && $_GET['filter'] === 'all') ? 'selected' : '' ?>>All Tickets
						</option>
					</select>
				</div>
			<?php endif; ?>
			<div class="col-md-3 d-flex align-items-end">
				<button type="submit" class="btn btn-primary me-2">
					<i class="bi bi-funnel"></i> Filter
				</button>
				<a href="index.php?action=tickets" class="btn btn-secondary">
					<i class="bi bi-x-circle"></i> Clear
				</a>
			</div>
		</form>
	</div>
</div>

<!-- Tickets Table -->
<div class="card">
	<div class="card-header bg-primary text-white">
		<h5 class="mb-0"><i class="bi bi-list-ul"></i> Ticket List</h5>
	</div>
	<div class="card-body">
		<?php if (empty($tickets)): ?>
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i> No tickets found.
			</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Ticket #</th>
							<th>Subject</th>
							<?php if ($_SESSION['user_role'] !== 'client'): ?>
								<th>Client</th>
							<?php endif; ?>
							<th>Department</th>
							<th>Priority</th>
							<th>Status</th>
							<th>Created</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($tickets as $ticket): ?>
							<tr>
								<td>
									<strong><?= htmlspecialchars($ticket->ticket_number) ?></strong>
								</td>
								<td><?= htmlspecialchars($ticket->subject) ?></td>
								<?php if ($_SESSION['user_role'] !== 'client'): ?>
									<td><?= htmlspecialchars($ticket->user->name ?? 'N/A') ?></td>
								<?php endif; ?>
								<td>
									<span class="badge bg-secondary">
										<?= htmlspecialchars($ticket->department->name ?? 'N/A') ?>
									</span>
								</td>
								<td>
									<?php
									$priorityClass = [
										'low' => 'info',
										'normal' => 'secondary',
										'high' => 'warning',
										'urgent' => 'danger'
									];
									$class = $priorityClass[$ticket->priority] ?? 'secondary';
									?>
									<span class="badge bg-<?= $class ?>">
										<?= ucfirst($ticket->priority) ?>
									</span>
								</td>
								<td>
									<?php
									$statusClass = [
										'open' => 'warning',
										'process' => 'info',
										'close' => 'success'
									];
									$class = $statusClass[$ticket->status] ?? 'secondary';
									$statusLabel = [
										'open' => 'Open',
										'process' => 'In Progress',
										'close' => 'Closed'
									];
									?>
									<span class="badge bg-<?= $class ?>">
										<?= $statusLabel[$ticket->status] ?? ucfirst($ticket->status) ?>
									</span>
								</td>
								<td>
									<small><?= $ticket->created_at->toDateTime()->format('M d, Y H:i') ?></small>
								</td>
								<td>
									<a href="index.php?action=ticket_detail&id=<?= $ticket->_id ?>" class="btn btn-sm btn-primary">
										<i class="bi bi-eye"></i> View
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