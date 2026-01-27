<div class="row justify-content-center">
	<div class="col-md-6">
		<div class="card shadow">
			<div class="card-body p-5">
				<div class="text-center mb-4">
					<i class="bi bi-person-plus-fill" style="font-size: 60px; color: #0d6efd;"></i>
					<h2 class="mt-3">Create Account</h2>
					<p class="text-muted">Sign up for a new account</p>
				</div>

				<?php if (isset($_SESSION['error'])): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					<?php unset($_SESSION['error']); ?>
				<?php endif; ?>

				<form method="POST" action="index.php?action=register">
					<div class="mb-3">
						<label for="name" class="form-label">Full Name</label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-person"></i>
							</span>
							<input type="text" class="form-control" id="name" name="name" required placeholder="Enter your full name">
						</div>
					</div>

					<div class="mb-3">
						<label for="email" class="form-label">Email Address</label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-envelope"></i>
							</span>
							<input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
						</div>
					</div>

					<div class="mb-3">
						<label for="phone" class="form-label">Phone Number</label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-telephone"></i>
							</span>
							<input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
						</div>
					</div>

					<div class="mb-3">
						<label for="password" class="form-label">Password</label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-lock"></i>
							</span>
							<input type="password" class="form-control" id="password" name="password" required
								placeholder="Create a password" minlength="6">
						</div>
						<small class="text-muted">Password must be at least 6 characters</small>
					</div>

					<div class="mb-3">
						<label for="confirm_password" class="form-label">Confirm Password</label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-lock-fill"></i>
							</span>
							<input type="password" class="form-control" id="confirm_password" name="confirm_password" required
								placeholder="Confirm your password" minlength="6">
						</div>
					</div>

					<div class="mb-3 form-check">
						<input type="checkbox" class="form-check-input" id="terms" required>
						<label class="form-check-label" for="terms">
							I agree to the <a href="#">Terms and Conditions</a>
						</label>
					</div>

					<div class="d-grid">
						<button type="submit" class="btn btn-primary btn-lg">
							<i class="bi bi-person-check"></i> Create Account
						</button>
					</div>
				</form>

				<hr class="my-4">

				<div class="text-center">
					<p class="mb-0">Already have an account? <a href="index.php?action=login">Sign in</a></p>
				</div>
			</div>
		</div>
	</div>
</div>