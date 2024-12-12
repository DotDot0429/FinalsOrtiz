<?php
session_start(); // Start the session

// Destroy the session
session_unset();
session_destroy();

// Redirect to the homepage or login page
header("Location: index.php");
exit;
?>