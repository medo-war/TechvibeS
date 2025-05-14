<?php
// Include the NetworkUtils class
require_once 'utils/NetworkUtils.php';

// Get server information
$server_info = [
    'SERVER_ADDR' => $_SERVER['SERVER_ADDR'] ?? 'Not available',
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'Not available',
    'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'Not available',
    'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 'Not available',
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'Not available',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not available',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Not available',
];

// Get the local IP address
$local_ip = NetworkUtils::getLocalIP();

// Get the base URL
$base_url = NetworkUtils::getBaseUrl();

// Generate a test partner offer URL
$test_offer_url = NetworkUtils::getPageUrl('view/front_office/partner-offer.php', [
    'id' => 1,
    'code' => 'TEST123'
]);

// Create a direct link to test
$direct_link = "http://{$_SERVER['HTTP_HOST']}/livethemusic/view/front_office/partner-offer.php?id=1&code=TEST123";

// Create a link with the specific IP address
$ip_link = "http://192.168.137.209/livethemusic/view/front_office/partner-offer.php?id=1&code=TEST123";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code URL Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .url-display {
            font-family: monospace;
            padding: 10px;
            background-color: #eee;
            border: 1px solid #ccc;
            border-radius: 4px;
            word-break: break-all;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .test-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .test-link:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>QR Code URL Test</h1>
    
    <div class="section">
        <h2>Server Information</h2>
        <table>
            <tr>
                <th>Variable</th>
                <th>Value</th>
            </tr>
            <?php foreach ($server_info as $key => $value): ?>
            <tr>
                <td><?php echo htmlspecialchars($key); ?></td>
                <td><?php echo htmlspecialchars($value); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    
    <div class="section">
        <h2>Network Information</h2>
        <p><strong>Local IP Address:</strong> <?php echo htmlspecialchars($local_ip); ?></p>
    </div>
    
    <div class="section">
        <h2>URL Generation</h2>
        <p><strong>Base URL:</strong></p>
        <div class="url-display"><?php echo htmlspecialchars($base_url); ?></div>
        
        <p><strong>Generated Partner Offer URL:</strong></p>
        <div class="url-display"><?php echo htmlspecialchars($test_offer_url); ?></div>
        
        <p><strong>Direct Link (using HTTP_HOST):</strong></p>
        <div class="url-display"><?php echo htmlspecialchars($direct_link); ?></div>
        
        <p><strong>Direct Link with Specific IP Address:</strong></p>
        <div class="url-display"><?php echo htmlspecialchars($ip_link); ?></div>
        
        <a href="<?php echo htmlspecialchars($test_offer_url); ?>" class="test-link" target="_blank">Test Generated URL</a>
        <a href="<?php echo htmlspecialchars($direct_link); ?>" class="test-link" target="_blank">Test Direct Link</a>
        <a href="<?php echo htmlspecialchars($ip_link); ?>" class="test-link" target="_blank">Test IP Link</a>
    </div>
    
    <div class="section">
        <h2>QR Code Test</h2>
        <p>Scan this QR code to test if it works on your device:</p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($ip_link); ?>" alt="Test QR Code">
        <p class="mt-3"><strong>Important:</strong> Make sure your phone is connected to the same WiFi network as this computer.</p>
    </div>
</body>
</html>
