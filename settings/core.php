// Settings/core.php
<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}


//for header redirection
ob_start();

//function to check if a user is logged in
function isUserLoggedIn() {
	return (session_status() === PHP_SESSION_ACTIVE) && isset($_SESSION['id']);
}

//function to check if user has administrative privileges
function isAdmin() {
	return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}


//function to get user ID
function getUserID() {
    return isset($_SESSION['id']) ? $_SESSION['id'] : null;
}

//function to check for role (admin, customer, etc)
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

function checkRole($role) {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role);
}


?>