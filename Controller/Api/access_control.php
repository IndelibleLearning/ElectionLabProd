<?php
function checkWhitelist() {
    // Define the lists of allowed IPs and allowed origins
    $allowedIPs = ['123.45.67.89'];  
    $allowedOrigins = ['https://indeliblelearning.com/'];  // Allowed domains

    // Get the IP address and origin of the incoming request
    $requesterIP = $_SERVER['REMOTE_ADDR'];
    $requesterOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

    // Check the requester's IP address and origin against the allowed lists
    if (in_array($requesterIP, $allowedIPs) || in_array($requesterOrigin, $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $requesterOrigin);
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    } else {
        // Deny access if not from an allowed IP or origin
        header('HTTP/1.1 403 Forbidden');
        exit('Access denied');
    }
}
