<?php
/**
 * Network Utilities
 * Helper functions for network-related operations
 */
class NetworkUtils {
    /**
     * Get the local network IP address
     * 
     * @return string The local IP address
     */
    public static function getLocalIP() {
        // Use the specific Wi-Fi IP address we found with ipconfig
        return '192.168.137.209';
    }
    
    /**
     * Get the base URL for the application
     * 
     * @param bool $useLocalIP Whether to use the local IP address (true) or hostname (false)
     * @return string The base URL
     */
    public static function getBaseUrl($useLocalIP = true) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        
        if ($useLocalIP) {
            $host = self::getLocalIP();
        } else {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        }
        
        // For XAMPP, we know the standard port is likely 80
        $port = 80;
        $portPart = '';
        
        // For XAMPP, we know the path structure
        return $protocol . $host . $portPart . '/livethemusic';
    }
    
    /**
     * Get the full URL for a specific page
     * 
     * @param string $page The page path relative to the livethemusic root
     * @param array $params URL parameters
     * @return string The full URL
     */
    public static function getPageUrl($page, $params = []) {
        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . '/' . ltrim($page, '/');
        
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $url .= '?' . $queryString;
        }
        
        return $url;
    }
}
?>
