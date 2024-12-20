<?php
// Get the user's IP address
$user_ip = $_SERVER['SERVER_ADDR'];

setcookie("bellakeo", '', time() + 3600, "/", $_SERVER['SERVER_ADDR']);

// Display the IP address
echo "Your IP address is: soy yo we" . $user_ip;
?>