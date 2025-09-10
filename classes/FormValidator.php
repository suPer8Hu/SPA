<?php
/**
 * Form Validation Class
 * 
 * @author Stevie Hu
 * @version 1.0
 */

class FormValidator
{
    private $errors = [];
    
    /**
     * Validate required field
     * 
     * @param string $field Field name
     * @param string $value Field value
     * @param string $fieldName Display name for error messages
     * @return void
     */
    public function validateRequired($field, $value, $fieldName)
    {
        if (empty(trim($value))) {
            $this->errors[$field] = "$fieldName is required";
        }
    }
    
    /**
     * Validate email format
     * 
     * @param string $field Field name
     * @param string $email Email address
     * @return void
     */
    public function validateEmail($field, $email)
    {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Invalid email format";
        }
    }
    
    /**
     * Validate account number format
     * 
     * @param string $field Field name
     * @param string $accountNumber Account number
     * @return void
     */
    public function validateAccountNumber($field, $accountNumber)
    {
        if (!empty($accountNumber)) {
            // Check length (12 characters)
            if (strlen($accountNumber) !== ACCOUNT_NUMBER_LENGTH) {
                $this->errors[$field] = "Account number must be exactly " . ACCOUNT_NUMBER_LENGTH . " characters";
                return;
            }
            
            // Check alphanumeric only
            if (!ctype_alnum($accountNumber)) {
                $this->errors[$field] = "Account number must contain only alphanumeric characters";
            }
        }
    }
    
    /**
     * Validate year selection
     * 
     * @param string $field Field name
     * @param string $year Selected year
     * @param array $validYears Array of valid years
     * @return void
     */
    public function validateYear($field, $year, $validYears)
    {
        if (!empty($year) && !in_array($year, $validYears)) {
            $this->errors[$field] = "Please select a valid year";
        }
    }
    
    /**
     * Get all validation errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Check if validation passed
     * 
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }
    
    /**
     * Get specific error message
     * 
     * @param string $field Field name
     * @return string|null
     */
    public function getError($field)
    {
        return isset($this->errors[$field]) ? $this->errors[$field] : null;
    }
    
    /**
     * Clear all errors
     * 
     * @return void
     */
    public function clearErrors()
    {
        $this->errors = [];
    }
}