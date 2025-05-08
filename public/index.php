<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

define('LARAVEL_START', microtime(true));

/**
 * Enhanced Laravel index.php with performance monitoring and diagnostics
 * 
 * This version includes:
 * - Performance tracking
 * - Basic request logging
 * - Maintenance mode handling with custom messages
 * - Simple emergency recovery mode
 * - Request information capture
 */

// Custom error handler to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        Log::error('Fatal error occurred: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']);
        if (env('APP_DEBUG', false)) {
            echo "<h1>Critical Error</h1>";
            echo "<p>A critical error occurred. Please check the logs for more information.</p>";
            echo "<pre>" . print_r($error, true) . "</pre>";
        } else {
            echo "<h1>Service Temporarily Unavailable</h1>";
            echo "<p>The application is currently experiencing technical difficulties. Our team has been notified.</p>";
        }
        exit(1);
    }
});

// IP-based rate limiting for high traffic scenarios
$clientIP = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$rateLimitFile = __DIR__.'/../storage/framework/rate_limits/' . md5($clientIP) . '.json';
$isRateLimited = false;

if (file_exists($rateLimitFile)) {
    $rateData = json_decode(file_get_contents($rateLimitFile), true);
    $timeWindow = 60; // 1 minute
    $maxRequests = 120; // 2 requests per second
    
    if (time() - $rateData['timestamp'] <= $timeWindow && $rateData['count'] >= $maxRequests) {
        http_response_code(429);
        header('Retry-After: ' . (($rateData['timestamp'] + $timeWindow) - time()));
        echo json_encode(['error' => 'Too many requests. Please try again later.']);
        exit;
    } elseif (time() - $rateData['timestamp'] > $timeWindow) {
        // Reset counter for new time window
        $rateData = ['timestamp' => time(), 'count' => 1];
    } else {
        // Increment counter in current time window
        $rateData['count']++;
    }
} else {
    // First request from this IP
    $rateData = ['timestamp' => time(), 'count' => 1];
}

// Save rate limiting data
if (!is_dir(dirname($rateLimitFile))) {
    mkdir(dirname($rateLimitFile), 0755, true);
}
file_put_contents($rateLimitFile, json_encode($rateData));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    // Check for maintenance bypass token
    $bypass = isset($_GET['bypass_maintenance']) && $_GET['bypass_maintenance'] === env('MAINTENANCE_BYPASS_TOKEN');
    
    if (!$bypass) {
        require $maintenance;
    }
}

// Emergency recovery mode - allows admins to access the site even when completely broken
$recoveryToken = env('EMERGENCY_RECOVERY_TOKEN');
if ($recoveryToken && isset($_GET['recovery']) && $_GET['recovery'] === $recoveryToken) {
    define('EMERGENCY_RECOVERY_MODE', true);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Capture incoming request information
$requestInfo = [
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'uri' => $_SERVER['REQUEST_URI'] ?? '/',
    'ip' => $clientIP,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown User Agent',
    'referer' => $_SERVER['HTTP_REFERER'] ?? null,
    'timestamp' => date('Y-m-d H:i:s'),
];

// Initialize app
$app = require_once __DIR__.'/../bootstrap/app.php';

// Custom middleware to track performance
if (env('APP_ENV') === 'local' || env('APP_ENV') === 'development') {
    $app->singleton('performance.monitor', function() {
        return new class {
            protected $markers = [];
            
            public function mark($name) {
                $this->markers[$name] = microtime(true);
                return $this;
            }
            
            public function getReport() {
                $report = [];
                $start = LARAVEL_START;
                
                foreach ($this->markers as $name => $time) {
                    $report[$name] = [
                        'time' => $time,
                        'elapsed_from_start' => ($time - $start) * 1000 . 'ms',
                    ];
                }
                
                return $report;
            }
        };
    });
    
    app('performance.monitor')->mark('app_init');
}

// Handle the request
$response = $app->handleRequest(Request::capture());

// Record performance data if enabled
if (isset($app['performance.monitor'])) {
    $app['performance.monitor']->mark('response_sent');
    
    if (env('APP_DEBUG') && isset($_GET['show_performance'])) {
        $performanceData = $app['performance.monitor']->getReport();
        $totalTime = (microtime(true) - LARAVEL_START) * 1000;
        
        echo "<!-- Performance data: \n";
        echo "Total execution time: {$totalTime}ms\n";
        echo print_r($performanceData, true);
        echo "-->";
    }
}

// Send response
$response->send();

// Terminate the application
$app->terminate();