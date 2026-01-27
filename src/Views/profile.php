<div class="row justify-content-center">
	<div class="col-lg-8">
		<div class="card shadow">
			<div class="card-header bg-primary text-white">
				<h3 class="mb-0">
					<i class="bi bi-person-circle"></i> My Profile
				</h3>
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

				<form method="POST" action="index.php?action=profile_update">
					<!-- Profile Information Section -->
					<h5 class="border-bottom pb-2 mb-3">
						<i class="bi bi-info-circle text-primary"></i> Profile Information
					</h5>

					<div class="row mb-3">
						<div class="col-md-6">
							<label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="name" name="name"
								value="<?= htmlspecialchars($user['name']) ?>" required>
						</div>
						<div class="col-md-6">
							<label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
							<input type="email" class="form-control" id="email" name="email"
								value="<?= htmlspecialchars($user['email']) ?>" required>
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-md-6">
							<label class="form-label">Role</label>
							<input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
							<small class="text-muted">Contact an administrator to change your role</small>
						</div>
						<div class="col-md-6">
							<label class="form-label">Member Since</label>
							<input type="text" class="form-control"
								value="<?= isset($user['created_at']) ? date('F d, Y', $user['created_at']->toDateTime()->getTimestamp()) : 'N/A' ?>"
								disabled>
						</div>
					</div>

					<!-- Change Password Section -->
					<h5 class="border-bottom pb-2 mb-3 mt-4">
						<i class="bi bi-shield-lock text-warning"></i> Change Password
					</h5>

					<div class="alert alert-info">
						<i class="bi bi-info-circle"></i> Leave password fields blank if you don't want to change your password.
					</div>

					<div class="mb-3">
						<label for="current_password" class="form-label">Current Password</label>
						<input type="password" class="form-control" id="current_password" name="current_password"
							placeholder="Enter your current password">
					</div>

					<div class="row mb-3">
						<div class="col-md-6">
							<label for="new_password" class="form-label">New Password</label>
							<input type="password" class="form-control" id="new_password" name="new_password"
								placeholder="Enter new password (min 6 characters)">
						</div>
						<div class="col-md-6">
							<label for="confirm_password" class="form-label">Confirm New Password</label>
							<input type="password" class="form-control" id="confirm_password" name="confirm_password"
								placeholder="Re-enter new password">
						</div>
					</div>

					<!-- Submit Button -->
					<div class="d-flex justify-content-between mt-4">
						<a href="index.php?action=dashboard" class="btn btn-secondary">
							<i class="bi bi-arrow-left"></i> Back to Dashboard
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-check-circle"></i> Update Profile
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Additional Info Card -->
		<div class="card shadow mt-4">
			<div class="card-body">
				<h5 class="card-title">
					<i class="bi bi-shield-check text-success"></i> Security Tips
				</h5>
				<ul class="mb-0">
					<li>Use a strong password with at least 6 characters</li>
					<li>Don't share your password with anyone</li>
					<li>Change your password regularly</li>
					<li>Use a unique password for this account</li>
				</ul>
			</div>
		</div>
	</div>
</div>