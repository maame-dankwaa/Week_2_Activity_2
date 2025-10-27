<?php
require_once 'settings/core.php';
session_start();

$is_admin = function_exists('isAdmin')
  ? isAdmin()
  : (isset($_SESSION['user_role']) && (string)$_SESSION['user_role'] === '1');

$is_logged_in = function_exists('isUserLoggedIn')
  ? isUserLoggedIn()
  : isset($_SESSION['user_id']);
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
		.search-container .form-control {
			border-radius: 20px;
			border: 1px solid #e1e5e9;
			padding: 5px 15px;
		}
		.search-container .form-control:focus {
			border-color: #667eea;
			box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
		}
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

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm py-2">
  <div class="container-fluid px-3">
    <a class="navbar-brand fw-semibold" href="index.php">
      <i class="fa fa-store me-1 text-brand"></i>Taste of Africa
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
      aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <!-- Left side -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="view/all_product.php">
            <i class="fa fa-box me-1"></i>All Products
          </a>
        </li>

        <?php if ($is_admin): ?>
          <li class="nav-item">
            <a class="nav-link" href="admin/category.php">
              <i class="fa fa-tags me-1"></i>Categories
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin/brand.php">
              <i class="fa fa-trademark me-1"></i>Brands
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin/product.php">
              <i class="fa fa-plus me-1"></i>Add Product
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Search bar -->
      <form action="view/product_search_result.php" method="GET" class="d-flex me-3">
        <input type="text" name="q" placeholder="Search products..." class="form-control form-control-sm me-2" style="width: 200px;">
        <button type="submit" class="btn btn-sm btn-outline-primary">
          <i class="fa fa-search"></i>
        </button>
      </form>

      <!-- Right side -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if ($is_logged_in): ?>
          <li class="nav-item d-flex align-items-center me-2 text-muted small">
            Welcome, <strong class="ms-1"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>
          </li>
          <li class="nav-item">
            <a href="login/login.php" class="btn btn-sm btn-outline-danger">
              <i class="fa fa-sign-out-alt me-1"></i>Login
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item me-1">
            <a href="login/register.php" class="btn btn-sm btn-outline-primary">
              <i class="fa fa-user-plus me-1"></i>Register
            </a>
          </li>
          <li class="nav-item">
            <a href="login/logout.php" class="btn btn-sm btn-outline-secondary">
              <i class="fa fa-sign-in-alt me-1"></i>Logout
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

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