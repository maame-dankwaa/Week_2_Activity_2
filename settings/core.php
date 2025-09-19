// Settings/core.php
<?php
session_start();


//for header redirection
ob_start();

//funtion to check for login
if (!isset($_SESSION['id'])) {
    header("Location: ../Login/login_register.php");
    exit;
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