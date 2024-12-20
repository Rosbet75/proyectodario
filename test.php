<?php
// Get the user's IP address
$SERVER_ip = $_SERVER['SERVER_ADDR'];
$user_ip = $_SERVER['REMOTE_ADDR'];

setcookie("bellakeo", '', time() + 3600, "/", $_SERVER['SERVER_ADDR']);

// Display the IP address
echo "Your IP address is: soy yo we" . $user_ip."       ". $SERVER_ip;

$metadataUrl = 'http://169.254.169.254/latest/meta-data/public-ipv4';
$ch = curl_init($metadataUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$publicIp = curl_exec($ch);
if (curl_errno($ch)) {
    echo "Error: " . curl_error($ch);
    $publicIp = "Unable to fetch IP";
}
curl_close($ch);

// Output the public IP address
echo "Public IP Address: " . $publicIp;
?>