<div class="card">
	<div class="card-header bg-primary text-white">
		<div class="d-flex justify-content-between align-items-center">
			<h3 class="mb-0"><i class="bi bi-tags"></i> Help Topic Details</h3>
			<a href="index.php?action=help_topics" class="btn btn-light btn-sm">
				<i class="bi bi-arrow-left"></i> Back to List
			</a>
		</div>
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

		<!-- Help Topic Information -->
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="card bg-light mb-4">
					<div class="card-body">
						<h4 class="mb-4"><?= htmlspecialchars($topic['topic_name']) ?></h4>
						<div class="row">
							<div class="col-md-4">
								<div class="mb-2">
									<strong>Status:</strong>
									<span class="badge bg-<?= $topic['status'] === 'active' ? 'success' : 'secondary' ?>">
										<?= htmlspecialchars(ucfirst($topic['status'] ?? 'active')) ?>
									</span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-2">
									<strong>Department:</strong>
									<?php if ($department): ?>
										<a href="index.php?action=department_detail&id=<?= $department['_id'] ?>"
											class="text-decoration-none">
											<?= htmlspecialchars($department['name']) ?>
										</a>
									<?php else: ?>
										<span class="text-muted fst-italic">Not assigned</span>
									<?php endif; ?>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-2">
									<strong>Created:</strong>
									<?php
									if (isset($topic['created_at'])) {
										$created = $topic['created_at']->toDateTime();
										echo htmlspecialchars($created->format('M d, Y H:i'));
									} else {
										echo 'N/A';
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Statistics -->
				<div class="row mb-4">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header bg-secondary text-white">
								<h5 class="mb-0"><i class="bi bi-graph-up"></i> Statistics</h5>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0">
												<i class="bi bi-ticket-perforated" style="font-size: 2rem;"></i>
											</div>
											<div class="flex-grow-1 ms-3">
												<h6 class="text-muted mb-0">Total Tickets</h6>
												<h3 class="mb-0"><?= $stats['total_tickets'] ?? 0 ?></h3>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Actions -->
				<div class="d-flex gap-2">
					<a href="index.php?action=help_topic_edit&id=<?= $topic['_id'] ?>" class="btn btn-warning">
						<i class="bi bi-pencil"></i> Edit Help Topic
					</a>
					<a href="index.php?action=help_topic_delete&id=<?= $topic['_id'] ?>" class="btn btn-danger"
						onclick="return confirm('Are you sure you want to delete this help topic? This action cannot be undone.')">
						<i class="bi bi-trash"></i> Delete Help Topic
					</a>
				</div>
			</div>
		</div>