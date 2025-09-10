<?php
/**
 * Form Utility Class
 * 
 * @author Stevie Hu
 * @version 1.0
 */

class FormUtility
{
    /**
     * Generate array of valid years (past N years)
     * 
     * @param int $count Number of years to generate
     * @return array
     */
    public static function generateValidYears($count = 5)
    {
        $currentYear = (int)date('Y');
        $years = [];
        
        for ($i = 0; $i < $count; $i++) {
            $years[] = $currentYear - $i;
        }
        
        return $years;
    }
    
    /**
     * Sanitize user input
     * 
     * @param string $input User input
     * @return string
     */
    public static function sanitizeInput($input)
    {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Generate CSRF token for security
     * 
     * @return string
     */
    public static function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     * 
     * @param string $token Token to validate
     * @return bool
     */
    public static function validateCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}