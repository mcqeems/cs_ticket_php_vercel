<?php
$isEdit = isset($department) && $department;
$pageTitle = $isEdit ? 'Edit Department' : 'Create New Department';
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

				<form method="POST" action="index.php?action=<?= $isEdit ? 'department_update' : 'department_store' ?>">
					<?php if ($isEdit): ?>
						<input type="hidden" name="dept_id" value="<?= $department['_id'] ?>">
					<?php endif; ?>

					<div class="mb-3">
						<label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name" name="name"
							value="<?= $isEdit ? htmlspecialchars($department['name']) : '' ?>" required
							placeholder="e.g., Technical Support, Billing, Sales">
					</div>

					<div class="mb-3">
						<label for="description" class="form-label">Description</label>
						<textarea class="form-control" id="description" name="description" rows="4"
							placeholder="Enter department description, responsibilities, or notes..."><?= $isEdit && isset($department['description']) ? htmlspecialchars($department['description']) : '' ?></textarea>
						<small class="form-text text-muted">Optional: Describe the department's role and responsibilities</small>
					</div>

					<div class="mb-3">
						<label class="form-label">Status <span class="text-danger">*</span></label>
						<div class="d-flex gap-4">
							<div class="form-check">
								<input class="form-check-input" type="radio" name="status" id="statusActive" value="active" <?= (!$isEdit || $department['status'] === 'active') ? 'checked' : '' ?>>
								<label class="form-check-label" for="statusActive">
									<i class="bi bi-check-circle text-success"></i> Active
								</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive"
									<?= ($isEdit && $department['status'] === 'inactive') ? 'checked' : '' ?>>
								<label class="form-check-label" for="statusInactive">
									<i class="bi bi-x-circle text-danger"></i> Inactive
								</label>
							</div>
						</div>
						<small class="form-text text-muted">Inactive departments won't appear in ticket creation forms</small>
					</div>

					<div class="d-flex justify-content-between">
						<a href="index.php?action=departments" class="btn btn-secondary">
							<i class="bi bi-arrow-left"></i> Back to Departments
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-<?= $isEdit ? 'check' : 'plus' ?>-circle"></i>
							<?= $isEdit ? 'Update Department' : 'Create Department' ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>