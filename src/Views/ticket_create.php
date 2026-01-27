<div class="row mb-4">
	<div class="col">
		<h2><i class="bi bi-plus-circle"></i> Create New Ticket</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="index.php?action=dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="index.php?action=tickets">Tickets</a></li>
				<li class="breadcrumb-item active">Create Ticket</li>
			</ol>
		</nav>
	</div>
</div>

<?php if (isset($_SESSION['error'])): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
	<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row">
	<div class="col-md-8">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0"><i class="bi bi-ticket-detailed"></i> Ticket Information</h5>
			</div>
			<div class="card-body">
				<form method="POST" action="index.php?action=ticket_store" id="ticketForm">
					<div class="mb-3">
						<label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
						<select class="form-select" id="department_id" name="department_id" required>
							<option value="">-- Select Department --</option>
							<?php foreach ($departments as $dept): ?>
								<option value="<?= htmlspecialchars($dept['_id']) ?>">
									<?= htmlspecialchars($dept['name']) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<small class="text-muted">Choose the department that best matches your issue</small>
					</div>

					<div class="mb-3">
						<label for="help_topic_id" class="form-label">Help Topic <span class="text-danger">*</span></label>
						<select class="form-select" id="help_topic_id" name="help_topic_id" required disabled>
							<option value="">-- Select Department First --</option>
						</select>
						<small class="text-muted">Specify what your ticket is about</small>
					</div>

					<div class="mb-3">
						<label for="priority" class="form-label">Priority</label>
						<select class="form-select" id="priority" name="priority">
							<option value="low">Low</option>
							<option value="normal" selected>Normal</option>
							<option value="high">High</option>
							<option value="urgent">Urgent</option>
						</select>
					</div>

					<div class="mb-3">
						<label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="subject" name="subject" required
							placeholder="Brief description of your issue" maxlength="255">
					</div>

					<div class="mb-3">
						<label for="message" class="form-label">Message <span class="text-danger">*</span></label>
						<textarea class="form-control" id="message" name="message" rows="8" required
							placeholder="Please describe your issue in detail..."></textarea>
						<small class="text-muted">Provide as much detail as possible to help us resolve your issue quickly</small>
					</div>

					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-send"></i> Submit Ticket
						</button>
						<a href="index.php?action=tickets" class="btn btn-secondary">
							<i class="bi bi-x-circle"></i> Cancel
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="card bg-light">
			<div class="card-body">
				<h5><i class="bi bi-info-circle"></i> Tips for Creating Tickets</h5>
				<ul class="small">
					<li>Use a clear and descriptive subject line</li>
					<li>Provide detailed information about your issue</li>
					<li>Include any error messages you received</li>
					<li>Mention steps you've already tried</li>
					<li>Set appropriate priority based on urgency</li>
				</ul>
			</div>
		</div>

		<div class="card mt-3">
			<div class="card-header bg-success text-white">
				<h6 class="mb-0"><i class="bi bi-book"></i> Need Help?</h6>
			</div>
			<div class="card-body">
				<p class="small">Check our <a href="index.php?action=knowledge_base">Knowledge Base</a> for quick solutions to
					common issues.</p>
			</div>
		</div>
	</div>
</div>

<script>
	// Load help topics when department is selected
	document.getElementById('department_id').addEventListener('change', function () {
		const departmentId = this.value;
		const helpTopicSelect = document.getElementById('help_topic_id');

		if (!departmentId) {
			helpTopicSelect.disabled = true;
			helpTopicSelect.innerHTML = '<option value="">-- Select Department First --</option>';
			return;
		}

		// Fetch help topics for selected department
		fetch(`index.php?action=api_help_topics&department_id=${departmentId}`)
			.then(response => response.json())
			.then(data => {
				if (data.success && data.topics) {
					helpTopicSelect.disabled = false;
					helpTopicSelect.innerHTML = '<option value="">-- Select Help Topic --</option>';

					data.topics.forEach(topic => {
						const option = document.createElement('option');
						option.value = topic._id.$oid || topic._id;
						option.textContent = topic.topic_name;
						helpTopicSelect.appendChild(option);
					});
				}
			})
			.catch(error => {
				console.error('Error loading help topics:', error);
				helpTopicSelect.disabled = true;
			});
	});
</script>