<div class="row mb-4">
	<div class="col">
		<h2><i class="bi bi-person-circle"></i> My Dashboard</h2>
		<p class="text-muted">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
	</div>
	<div class="col-auto">
		<a href="index.php?action=ticket_create" class="btn btn-primary btn-lg">
			<i class="bi bi-plus-circle"></i> Create New Ticket
		</a>
	</div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
	<div class="col-md-3">
		<div class="card border-primary mb-3">
			<div class="card-body text-center">
				<i class="bi bi-ticket" style="font-size: 3rem; color: #0d6efd;"></i>
				<h3 class="mt-2 mb-0"><?= $stats['total'] ?? 0 ?></h3>
				<p class="text-muted mb-0">Total Tickets</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card border-warning mb-3">
			<div class="card-body text-center">
				<i class="bi bi-hourglass-split" style="font-size: 3rem; color: #ffc107;"></i>
				<h3 class="mt-2 mb-0"><?= $stats['open'] ?? 0 ?></h3>
				<p class="text-muted mb-0">Open</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card border-info mb-3">
			<div class="card-body text-center">
				<i class="bi bi-arrow-repeat" style="font-size: 3rem; color: #0dcaf0;"></i>
				<h3 class="mt-2 mb-0"><?= $stats['process'] ?? 0 ?></h3>
				<p class="text-muted mb-0">In Progress</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card border-success mb-3">
			<div class="card-body text-center">
				<i class="bi bi-check-circle" style="font-size: 3rem; color: #198754;"></i>
				<h3 class="mt-2 mb-0"><?= $stats['closed'] ?? 0 ?></h3>
				<p class="text-muted mb-0">Closed</p>
			</div>
		</div>
	</div>
</div>

<!-- Recent Tickets -->
<div class="row mb-4">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0"><i class="bi bi-list-ul"></i> My Tickets</h5>
			</div>
			<div class="card-body">
				<?php if (empty($myTickets)): ?>
					<div class="alert alert-info">
						<i class="bi bi-info-circle"></i> You haven't created any tickets yet. Click "Create New Ticket" to get
						started!
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Ticket #</th>
									<th>Subject</th>
									<th>Status</th>
									<th>Priority</th>
									<th>Created</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($myTickets as $ticket): ?>
									<tr>
										<td><?= htmlspecialchars($ticket['ticket_number']) ?></td>
										<td>
											<?= htmlspecialchars(substr($ticket['subject'], 0, 40)) ?>		<?= strlen($ticket['subject']) > 40 ? '...' : '' ?>
										</td>
										<td>
											<span
												class="badge bg-<?= $ticket['status'] === 'open' ? 'warning' : ($ticket['status'] === 'process' ? 'info' : 'success') ?>">
												<?= ucfirst($ticket['status']) ?>
											</span>
										</td>
										<td>
											<span
												class="badge bg-<?= $ticket['priority'] === 'high' ? 'danger' : ($ticket['priority'] === 'medium' ? 'warning' : 'secondary') ?>">
												<?= ucfirst($ticket['priority']) ?>
											</span>
										</td>
										<td><?= date('M d, Y', $ticket['created_at']->toDateTime()->getTimestamp()) ?></td>
										<td>
											<a href="index.php?action=ticket_detail&id=<?= $ticket['_id'] ?>" class="btn btn-sm btn-primary">
												<i class="bi bi-eye"></i> View
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<a href="index.php?action=tickets" class="btn btn-primary btn-sm">View All My Tickets</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Quick Help -->
	<div class="col-md-4">
		<div class="card">
			<div class="card-header bg-success text-white">
				<h5 class="mb-0"><i class="bi bi-book"></i> Quick Help</h5>
			</div>
			<div class="card-body">
				<h6>Need Help?</h6>
				<p class="small text-muted">Check our knowledge base for quick solutions:</p>
				<ul class="list-unstyled">
					<li class="mb-2">
						<a href="index.php?action=knowledge_base&category=faq" class="text-decoration-none">
							<i class="bi bi-question-circle"></i> FAQs
						</a>
					</li>
					<li class="mb-2">
						<a href="index.php?action=knowledge_base&category=support" class="text-decoration-none">
							<i class="bi bi-tools"></i> Support Articles
						</a>
					</li>
					<li class="mb-2">
						<a href="index.php?action=knowledge_base&category=maintenance" class="text-decoration-none">
							<i class="bi bi-calendar-event"></i> Maintenance Schedule
						</a>
					</li>
				</ul>
				<a href="index.php?action=knowledge_base" class="btn btn-success btn-sm w-100">
					Browse All Articles
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Getting Started -->
<div class="row">
	<div class="col-md-12">
		<div class="card bg-light">
			<div class="card-body">
				<h5><i class="bi bi-info-circle"></i> Getting Started</h5>
				<p class="mb-2">Here's how to use the CS Ticket System:</p>
				<ol>
					<li><strong>Create a Ticket:</strong> Click the "Create New Ticket" button to submit a support request</li>
					<li><strong>Track Your Tickets:</strong> View all your tickets and their status from your dashboard</li>
					<li><strong>Get Updates:</strong> Receive notifications when agents respond to your tickets</li>
					<li><strong>Browse Knowledge Base:</strong> Find answers to common questions</li>
				</ol>
			</div>
		</div>
	</div>
</div>