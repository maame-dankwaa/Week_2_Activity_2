<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}


//for header redirection
ob_start();

//function to check if a user is logged in
function isUserLoggedIn() {
	return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

//function to check if user has administrative privileges
function isAdmin() {
    return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}

//function to get user ID
function getUserID() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

//function to check for role (admin, customer, etc)
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

function checkRole($role) {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] == (string)$role);
}


?>