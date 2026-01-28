<?php
$isEdit = isset($user) && $user;
$pageTitle = $isEdit ? 'Edit User' : 'Create New User';
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

				<form method="POST" action="index.php?action=<?= $isEdit ? 'user_update' : 'user_store' ?>">
					<?php if ($isEdit): ?>
						<input type="hidden" name="user_id" value="<?= $user['_id'] ?>">
					<?php endif; ?>

					<div class="mb-3">
						<label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name" name="name"
							value="<?= $isEdit ? htmlspecialchars($user['name']) : '' ?>" required>
					</div>

					<div class="mb-3">
						<label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
						<input type="email" class="form-control" id="email" name="email"
							value="<?= $isEdit ? htmlspecialchars($user['email']) : '' ?>" required>
					</div>

					<div class="mb-3">
						<label for="password" class="form-label">
							Password <?= $isEdit ? '(leave blank to keep current)' : '<span class="text-danger">*</span>' ?>
						</label>
						<input type="password" class="form-control" id="password" name="password" <?= $isEdit ? '' : 'required' ?>
							minlength="6">
						<small class="form-text text-muted">Minimum 6 characters</small>
					</div>

					<div class="mb-3">
						<label for="role" class="form-label">Role <span class="text-danger">*</span></label>
						<select class="form-select" id="role" name="role" required>
							<option value="">Select Role</option>
							<option value="admin" <?= ($isEdit && $user['role'] === 'admin') ? 'selected' : '' ?>>
								Admin
							</option>
							<option value="support_agent" <?= ($isEdit && $user['role'] === 'support_agent') ? 'selected' : '' ?>>
								Support Agent
							</option>
							<option value="client" <?= ($isEdit && $user['role'] === 'client') ? 'selected' : '' ?>>
								Client
							</option>
						</select>
					</div>

					<div class="mb-3" id="departmentField" style="display: none;">
						<label for="department" class="form-label">Department</label>
						<select class="form-select" id="department" name="department">
							<option value="">Select Department</option>
							<?php if (isset($departments) && !empty($departments)): ?>
								<?php foreach ($departments as $dept): ?>
									<option value="<?= htmlspecialchars($dept['_id']) ?>" <?= ($isEdit && isset($user['department']) && (string) $user['department'] === (string) $dept['_id']) ? 'selected' : '' ?>>
										<?= htmlspecialchars($dept['name']) ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<small class="form-text text-muted">Only applicable for Support Agents</small>
					</div>

					<div class="d-flex justify-content-between">
						<a href="index.php?action=users" class="btn btn-secondary">
							<i class="bi bi-arrow-left"></i> Back to Users
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-<?= $isEdit ? 'check' : 'plus' ?>-circle"></i>
							<?= $isEdit ? 'Update User' : 'Create User' ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	// Show/hide department field based on role
	document.getElementById('role').addEventListener('change', function () {
		const departmentField = document.getElementById('departmentField');
		if (this.value === 'support_agent') {
			departmentField.style.display = 'block';
		} else {
			departmentField.style.display = 'none';
			document.getElementById('department').value = '';
		}
	});

	// Trigger on page load for edit mode
	window.addEventListener('DOMContentLoaded', function () {
		const roleSelect = document.getElementById('role');
		if (roleSelect.value === 'support_agent') {
			document.getElementById('departmentField').style.display = 'block';
		}
	});
</script>