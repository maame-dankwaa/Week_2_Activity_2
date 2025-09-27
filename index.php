<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<style>
		.menu-tray {
			position: fixed;
			top: 16px;
			right: 16px;
			background: rgba(255,255,255,0.95);
			border: 1px solid #e6e6e6;
			border-radius: 8px;
			padding: 6px 10px;
			box-shadow: 0 4px 10px rgba(0,0,0,0.06);
			z-index: 1000;
		}
		.menu-tray a { margin-left: 8px; }
		.welcome-message {
			background: linear-gradient(135deg, #D19C97, #b77a7a);
			color: white;
			padding: 20px;
			border-radius: 10px;
			margin-bottom: 20px;
		}
		.user-info {
			background: #f8f9fa;
			padding: 15px;
			border-radius: 8px;
			border-left: 4px solid #D19C97;
		}
	</style>
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (isset($_SESSION['user_id'])): ?>
			<span class="me-2 text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
			<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
				<a href="category.php" class="btn btn-sm btn-outline-success">
					<i class="fa fa-tags me-1"></i>Category
				</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">
				<i class="fa fa-sign-out-alt me-1"></i>Logout
			</a>
		<?php else: ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">
				<i class="fa fa-user-plus me-1"></i>Register
			</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">
				<i class="fa fa-sign-in-alt me-1"></i>Login
			</a>
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<h1>Welcome to Taste of Africa</h1>
			<?php if (isset($_SESSION['user_id'])): ?>
				<div class="welcome-message">
					<h3><i class="fa fa-heart me-2"></i>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
					<p class="mb-0">You are successfully logged in.</p>
				</div>
				<div class="row justify-content-center">
					<div class="col-md-6">
						<div class="user-info">
							<h5><i class="fa fa-user me-2"></i>Your Profile</h5>
							<p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
							<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
							<p><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['user_phone']); ?></p>
							<p><strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['user_city'] . ', ' . $_SESSION['user_country']); ?></p>
							<p><strong>Role:</strong> <?php echo $_SESSION['user_role'] == 1 ? 'Admin' : 'Customer'; ?></p>
						</div>
					</div>
				</div>
			<?php else: ?>
				<p class="text-muted">Use the menu in the top-right to Register or Login.</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
