<?php
/**
 * PDF Generation Performance Configuration
 *
 * This file contains optimized PHP settings for PDF generation.
 * Include this file at the beginning of PDF generation methods.
 */

if (!defined('PDF_CONFIG_LOADED')) {
    define('PDF_CONFIG_LOADED', true);

    // Memory and execution time optimizations
    ini_set('max_execution_time', 180); // 3 minutes
    ini_set('memory_limit', '1024M'); // 1GB memory limit
    ini_set('max_input_time', 180);
    ini_set('default_socket_timeout', 180);

    // OPcache optimizations for better performance
    if (function_exists('opcache_reset')) {
        ini_set('opcache.enable', '1');
        ini_set('opcache.memory_consumption', '256');
        ini_set('opcache.max_accelerated_files', '7963');
        ini_set('opcache.revalidate_freq', '0');
    }

    // Garbage collection settings
    ini_set('zend.enable_gc', '1');
    ini_set('gc_probability', '1');
    ini_set('gc_divisor', '100');
    ini_set('gc_maxlifetime', '3600');

    // Disable output buffering for better memory management
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Set error reporting for production
    if (!config('app.debug')) {
        error_reporting(E_ERROR | E_PARSE);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
    }
}
