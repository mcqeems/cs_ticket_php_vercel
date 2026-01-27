<div class="row justify-content-center">
	<div class="col-md-5">
		<div class="card shadow">
			<div class="card-body p-5">
				<div class="text-center mb-4">
					<i class="bi bi-ticket-perforated" style="font-size: 60px; color: #0d6efd;"></i>
					<h2 class="mt-3">CS Ticket System</h2>
					<p class="text-muted">Sign in to your account</p>
				</div>

				<?php if (isset($_SESSION['error'])): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					<?php unset($_SESSION['error']); ?>
				<?php endif; ?>

				<?php if (isset($_SESSION['success'])): ?>
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					<?php unset($_SESSION['success']); ?>
				<?php endif; ?>

				<form method="POST" action="index.php?action=login">
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
						<label for="password" class="form-label">Password</label>
						<div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-lock"></i>
							</span>
							<input type="password" class="form-control" id="password" name="password" required
								placeholder="Enter your password">
						</div>
					</div>

					<div class="mb-3 form-check">
						<input type="checkbox" class="form-check-input" id="remember">
						<label class="form-check-label" for="remember">Remember me</label>
					</div>

					<div class="d-grid">
						<button type="submit" class="btn btn-primary btn-lg">
							<i class="bi bi-box-arrow-in-right"></i> Sign In
						</button>
					</div>
				</form>

				<hr class="my-4">

				<div class="text-center">
					<p class="mb-0">Don't have an account? <a href="index.php?action=register">Sign up</a></p>
				</div>
			</div>
		</div>
	</div>
</div>