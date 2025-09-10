<?php
/**
 * Application Config
 * 
 * @author Stevie Hu
 * @version 1.0
 */

// Application settings
define('APP_NAME', 'User Information Form');
define('APP_VERSION', '1.0.0');

define('ALLOWED_YEARS_COUNT', 5);
define('ACCOUNT_NUMBER_LENGTH', 12);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session config
session_start();

// Set default timezone
date_default_timezone_set('UTC');