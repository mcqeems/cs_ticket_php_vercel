<?php
$isEdit = isset($topic) && $topic;
$pageTitle = $isEdit ? 'Edit Help Topic' : 'Create New Help Topic';
?>

<div class="row justify-content-center">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<h3 class="mb-0">
					<i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?>"></i> <?= $pageTitle ?>
				</h3>
			</div>
			<div class="card-body">
				<?php if (isset($_SESSION['error'])): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					<?php unset($_SESSION['error']); ?>
				<?php endif; ?>

				<form method="POST" action="index.php?action=<?= $isEdit ? 'help_topic_update' : 'help_topic_store' ?>">
					<?php if ($isEdit): ?>
						<input type="hidden" name="topic_id" value="<?= $topic['_id'] ?>">
					<?php endif; ?>

					<div class="mb-3">
						<label for="topic_name" class="form-label">Help Topic Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="topic_name" name="topic_name"
							value="<?= $isEdit ? htmlspecialchars($topic['topic_name']) : '' ?>" required
							placeholder="e.g., Password Reset, Login Issues, Billing Question">
					</div>

					<div class="mb-3">
						<label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
						<select class="form-select" id="department_id" name="department_id" required>
							<option value="">-- Select Department --</option>
							<?php foreach ($departments as $dept): ?>
								<option value="<?= $dept['_id'] ?>" <?= ($isEdit && isset($topic['department_id']) && (string) $topic['department_id'] === (string) $dept['_id']) ? 'selected' : '' ?>>
									<?= htmlspecialchars($dept['name']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="mb-3">
						<label class="form-label">Status <span class="text-danger">*</span></label>
						<div class="d-flex gap-4">
							<div class="form-check">
								<input class="form-check-input" type="radio" name="status" id="statusActive" value="active" <?= (!$isEdit || $topic['status'] === 'active') ? 'checked' : '' ?>>
								<label class="form-check-label" for="statusActive">
									<i class="bi bi-check-circle text-success"></i> Active
								</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive"
									<?= ($isEdit && $topic['status'] === 'inactive') ? 'checked' : '' ?>>
								<label class="form-check-label" for="statusInactive">
									<i class="bi bi-x-circle text-danger"></i> Inactive
								</label>
							</div>
						</div>
						<small class="form-text text-muted">Inactive topics won't appear in ticket creation forms</small>
					</div>

					<div class="d-flex justify-content-between">
						<a href="index.php?action=help_topics" class="btn btn-secondary">
							<i class="bi bi-arrow-left"></i> Back to Help Topics
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-<?= $isEdit ? 'check' : 'plus' ?>-circle"></i>
							<?= $isEdit ? 'Update Help Topic' : 'Create Help Topic' ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>