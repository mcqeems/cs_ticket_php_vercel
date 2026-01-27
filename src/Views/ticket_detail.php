<?php
$statusBadge = [
	'open' => '<span class="badge bg-warning">Open</span>',
	'process' => '<span class="badge bg-info">In Progress</span>',
	'close' => '<span class="badge bg-success">Closed</span>'
];

$priorityBadge = [
	'low' => '<span class="badge bg-info">Low</span>',
	'normal' => '<span class="badge bg-secondary">Normal</span>',
	'high' => '<span class="badge bg-warning">High</span>',
	'urgent' => '<span class="badge bg-danger">Urgent</span>'
];

$userRole = $_SESSION['user_role'];
?>

<div class="row mb-4">
	<div class="col">
		<h2><i class="bi bi-ticket-detailed"></i> Ticket #<?= htmlspecialchars($ticket->ticket_number) ?></h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php?action=dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="index.php?action=tickets">Tickets</a></li>
				<li class="breadcrumb-item active"><?= htmlspecialchars($ticket->ticket_number) ?></li>
			</ol>
		</nav>
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

<div class="row">
	<!-- Main Content -->
	<div class="col-md-8">
		<!-- Ticket Details -->
		<div class="card mb-4">
			<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><?= htmlspecialchars($ticket->subject) ?></h5>
				<div>
					<?= $statusBadge[$ticket->status] ?? '' ?>
					<?= $priorityBadge[$ticket->priority] ?? '' ?>
				</div>
			</div>
			<div class="card-body">
				<div class="border-start border-primary border-4 ps-3 mb-3">
					<p class="mb-0"><?= nl2br(htmlspecialchars($ticket->message)) ?></p>
				</div>
				<div class="text-muted small">
					<i class="bi bi-person"></i> <?= htmlspecialchars($ticket->user->name) ?> •
					<i class="bi bi-clock"></i> <?= $ticket->created_at->toDateTime()->format('M d, Y H:i') ?>
				</div>
			</div>
		</div>

		<!-- Replies -->
		<div class="card mb-4">
			<div class="card-header bg-secondary text-white">
				<h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Conversation</h5>
			</div>
			<div class="card-body">
				<?php if (empty($replies)): ?>
					<p class="text-muted">No replies yet.</p>
				<?php else: ?>
					<?php foreach ($replies as $reply): ?>
						<?php
						$isAgent = isset($reply->user->role) && in_array($reply->user->role, ['admin', 'support_agent']);
						$bgClass = $isAgent ? 'bg-light' : 'bg-white';
						?>

						<?php if ($reply->is_internal_note && $userRole === 'client'):
							continue; endif; ?>

						<div class="<?= $bgClass ?> border rounded p-3 mb-3 <?= $reply->is_internal_note ? 'border-warning' : '' ?>">
							<?php if ($reply->is_internal_note): ?>
								<div class="badge bg-warning mb-2">
									<i class="bi bi-lock"></i> Internal Note
								</div>
							<?php endif; ?>

							<div class="d-flex align-items-start">
								<div class="me-3">
									<i class="bi bi-person-circle" style="font-size: 2rem; color: #6c757d;"></i>
								</div>
								<div class="flex-grow-1">
									<div class="d-flex justify-content-between align-items-start">
										<div>
											<strong><?= htmlspecialchars($reply->user->name) ?></strong>
											<?php if ($isAgent): ?>
												<span class="badge bg-primary ms-2">Staff</span>
											<?php endif; ?>
										</div>
										<small class="text-muted">
											<?= $reply->created_at->toDateTime()->format('M d, Y H:i') ?>
										</small>
									</div>
									<p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($reply->message)) ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>

		<!-- Reply Form -->
		<?php if ($ticket->status !== 'close'): ?>
			<div class="card">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0"><i class="bi bi-reply"></i> Add Reply</h5>
				</div>
				<div class="card-body">
					<form method="POST" action="index.php?action=ticket_reply">
						<input type="hidden" name="ticket_id" value="<?= $ticket->_id ?>">

						<div class="mb-3">
							<textarea class="form-control" name="message" rows="5" required
								placeholder="Type your reply here..."></textarea>
						</div>

						<?php if ($userRole !== 'client'): ?>
							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" id="is_internal" name="is_internal" value="1">
								<label class="form-check-label" for="is_internal">
									<i class="bi bi-lock"></i> Internal Note (Only visible to staff)
								</label>
							</div>
						<?php endif; ?>

						<button type="submit" class="btn btn-success">
							<i class="bi bi-send"></i> Send Reply
						</button>
					</form>
				</div>
			</div>
		<?php else: ?>
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i> This ticket is closed. Replies are disabled.
			</div>
		<?php endif; ?>
	</div>

	<!-- Sidebar -->
	<div class="col-md-4">
		<!-- Ticket Info -->
		<div class="card mb-3">
			<div class="card-header bg-info text-white">
				<h6 class="mb-0"><i class="bi bi-info-circle"></i> Ticket Information</h6>
			</div>
			<div class="card-body">
				<div class="mb-2">
					<strong>Status:</strong><br>
					<?= $statusBadge[$ticket->status] ?? 'N/A' ?>
				</div>
				<div class="mb-2">
					<strong>Priority:</strong><br>
					<?= $priorityBadge[$ticket->priority] ?? 'N/A' ?>
				</div>
				<div class="mb-2">
					<strong>Department:</strong><br>
					<?= htmlspecialchars($ticket->department->name ?? 'N/A') ?>
				</div>
				<div class="mb-2">
					<strong>Help Topic:</strong><br>
					<?= htmlspecialchars($ticket->help_topic->topic_name ?? 'N/A') ?>
				</div>
				<div class="mb-2">
					<strong>Assigned To:</strong><br>
					<?= isset($ticket->agent) ? htmlspecialchars($ticket->agent->name) : '<span class="text-muted">Unassigned</span>' ?>
				</div>
				<div class="mb-2">
					<strong>Created:</strong><br>
					<?= $ticket->created_at->toDateTime()->format('M d, Y H:i') ?>
				</div>
				<?php if ($ticket->updated_at): ?>
					<div class="mb-2">
						<strong>Last Updated:</strong><br>
						<?= $ticket->updated_at->toDateTime()->format('M d, Y H:i') ?>
					</div>
				<?php endif; ?>
				<?php if ($ticket->closed_at): ?>
					<div class="mb-2">
						<strong>Closed:</strong><br>
						<?= $ticket->closed_at->toDateTime()->format('M d, Y H:i') ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Actions (Staff Only) -->
		<?php if ($userRole !== 'client'): ?>
			<div class="card mb-3">
				<div class="card-header bg-warning text-dark">
					<h6 class="mb-0"><i class="bi bi-tools"></i> Actions</h6>
				</div>
				<div class="card-body">
					<!-- Change Status -->
					<form method="POST" action="index.php?action=ticket_update_status" class="mb-3">
						<input type="hidden" name="ticket_id" value="<?= $ticket->_id ?>">
						<label class="form-label small"><strong>Change Status:</strong></label>
						<div class="d-grid gap-2">
							<?php if ($ticket->status !== 'process'): ?>
								<button type="submit" name="status" value="process" class="btn btn-sm btn-info">
									<i class="bi bi-arrow-repeat"></i> Mark as In Progress
								</button>
							<?php endif; ?>
							<?php if ($ticket->status !== 'close'): ?>
								<button type="submit" name="status" value="close" class="btn btn-sm btn-success"
									onclick="return confirm('Are you sure you want to close this ticket?')">
									<i class="bi bi-check-circle"></i> Close Ticket
								</button>
							<?php endif; ?>
							<?php if ($ticket->status === 'close'): ?>
								<button type="submit" name="status" value="open" class="btn btn-sm btn-warning"
									onclick="return confirm('Are you sure you want to reopen this ticket?')">
									<i class="bi bi-arrow-counterclockwise"></i> Reopen Ticket
								</button>
							<?php endif; ?>
						</div>
					</form>

					<!-- Assign Agent -->
					<?php if (!empty($agents) || $userRole === 'admin'): ?>
						<form method="POST" action="index.php?action=ticket_assign">
							<input type="hidden" name="ticket_id" value="<?= $ticket->_id ?>">
							<label class="form-label small"><strong>Assign To:</strong></label>
							<select name="agent_id" class="form-select form-select-sm mb-2" required>
								<option value="">-- Select Agent --</option>
								<?php foreach ($agents as $agent): ?>
									<option value="<?= $agent['_id'] ?>" <?= (isset($ticket->agent) && (string) $agent['_id'] === (string) $ticket->agent->_id) ? 'selected' : '' ?>>
										<?= htmlspecialchars($agent['name']) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<button type="submit" class="btn btn-sm btn-primary w-100">
								<i class="bi bi-person-check"></i> Assign
							</button>
						</form>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Activity History -->
		<div class="card">
			<div class="card-header bg-secondary text-white">
				<h6 class="mb-0"><i class="bi bi-clock-history"></i> Activity History</h6>
			</div>
			<div class="card-body">
				<?php if (empty($history)): ?>
					<p class="small text-muted mb-0">No activity yet.</p>
				<?php else: ?>
					<div class="timeline">
						<?php foreach ($history as $log): ?>
							<div class="mb-3 pb-2 border-bottom">
								<div class="small">
									<strong><?= htmlspecialchars($log->user->name ?? 'System') ?></strong>
								</div>
								<div class="small text-muted">
									<?= htmlspecialchars($log->message) ?>
								</div>
								<div class="small text-muted">
									<i class="bi bi-clock"></i> <?= $log->timestamp->toDateTime()->format('M d, H:i') ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>