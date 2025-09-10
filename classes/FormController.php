<?php
/**
 * Form Controller
 * 
 * @author Stevie Hu
 * @version 1.0
 */

class FormController
{
    private $validator;
    private $validYears;
    private $formData = [
        'firstName' => '',
        'lastName' => '',
        'email' => '',
        'accountNumber' => '',
        'year' => ''
    ];
    
    public function __construct()
    {
        $this->validator = new FormValidator();
        $this->validYears = FormUtility::generateValidYears(ALLOWED_YEARS_COUNT);
    }
    
    /**
     * Process form submission
     * 
     * @param array $postData Submitted form data
     * @return array Processing result
     */
    public function processForm($postData)
    {
        $result = [
            'success' => false,
            'errors' => [],
            'data' => []
        ];
        
        // Validate CSRF token
        if (!isset($postData['csrf_token']) || !FormUtility::validateCsrfToken($postData['csrf_token'])) {
            $result['errors']['security'] = 'Security validation failed. Please try again！';
            return $result;
        }
        
        // Sanitize and store form data
        foreach ($this->formData as $key => $value) {
            if (isset($postData[$key])) {
                $this->formData[$key] = FormUtility::sanitizeInput($postData[$key]);
            }
        }
        
        // Validate form data
        $this->validateFormData();
        
        if ($this->validator->isValid()) {
            $result['success'] = true;
            $result['data'] = $this->formData;
        } else {
            $result['errors'] = $this->validator->getErrors();
            $result['data'] = $this->formData;
        }
        return $result;
    }
    
    /**
     * Validate form data
     * 
     * @return void
     */
    private function validateFormData()
    {
        $this->validator->clearErrors();
        $this->validator->validateRequired('firstName', $this->formData['firstName'], 'First name');
        $this->validator->validateRequired('lastName', $this->formData['lastName'], 'Last name');
        $this->validator->validateEmail('email', $this->formData['email']);
        $this->validator->validateRequired('email', $this->formData['email'], 'Email');
        $this->validator->validateAccountNumber('accountNumber', $this->formData['accountNumber']);
        $this->validator->validateRequired('accountNumber', $this->formData['accountNumber'], 'Account number');
        $this->validator->validateYear('year', $this->formData['year'], $this->validYears);
        $this->validator->validateRequired('year', $this->formData['year'], 'Year');
    }
    
    /**
     * Get valid years
     * 
     * @return array
     */
    public function getValidYears()
    {
        return $this->validYears;
    }
    
    /**
     * Get form data
     * 
     * @return array
     */
    public function getFormData()
    {
        return $this->formData;
    }
    
    /**
     * Get CSRF token
     * 
     * @return string
     */
    public function getCsrfToken()
    {
        return FormUtility::generateCsrfToken();
    }
}