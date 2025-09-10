<?php
/**
 * User Information Form 
 * 
 * A single page application that captures user information with validation
 * and verification dialog before submission.
 * 
 * @author Stevie Hu
 * @version 1.0
 */

require_once 'config.php';
require_once 'classes/FormValidator.php';
require_once 'classes/FormUtility.php';
require_once 'classes/FormController.php';


$controller = new FormController();
$result = null;
$csrfToken = $controller->getCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $result = $controller->processForm($_POST);
}

// Get valid years for the dropdown
$validYears = $controller->getValidYears();

// Get form data (either from POST or defaults)
$formData = $result && isset($result['data']) ? $result['data'] : $controller->getFormData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Information Form">
    <meta name="author" content="Stevie Hu">
    <title><?php echo APP_NAME; ?></title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --error-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 2;
            padding: 30px;
            min-height: 100vh;
        }

        .container {
            max-width: 700px;
            margin: 2rem auto;
            background: white;
        }

        .header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 300;
        }

        .header p {
            margin-top: 0.5rem;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .content {
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.8rem;
            margin-top: 0.2rem;
            display: block;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .verification-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary-color);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #eee;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #aaa;
            padding: 0;
            width: auto;
        }

        .close-modal:hover {
            color: #333;
        }

        .verification-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .verification-info p {
            margin: 0.5rem 0;
        }

        .verification-info strong {
            color: var(--dark-color);
        }

        .data-summary {
            margin: 1rem 0;
            padding: 1rem;
            background: #e8f4fc;
            border-radius: var(--border-radius);
        }

        .data-summary p {
            margin: 0.5rem 0;
            display: flex;
        }

        .data-summary span:first-child {
            font-weight: 600;
            width: 150px;
            flex-shrink: 0;
        }

        .btn-success {
            background: var(--success-color);
        }

        .btn-success:hover {
            background: #219653;
        }

        .btn-danger {
            background: var(--error-color);
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .security-note {
            font-size: 0.8rem;
            color: #7f8c8d;
            text-align: center;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem;
            }
            
            .content {
                padding: 1.5rem;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo APP_NAME; ?></h1>
            <p>Please fill in all required information</p>
        </div>
        
        <div class="content">
            <?php if ($result && $result['success']): ?>
                <div class="alert alert-success">
                    <h2>Form Submitted Successfully!</h2>
                    <p>Thank you for submitting your information.</p>
                    <div class="data-summary">
                        <p><span>First Name:</span> <?php echo htmlspecialchars($formData['firstName']); ?></p>
                        <p><span>Last Name:</span> <?php echo htmlspecialchars($formData['lastName']); ?></p>
                        <p><span>Email:</span> <?php echo htmlspecialchars($formData['email']); ?></p>
                        <p><span>Account Number:</span> <?php echo htmlspecialchars($formData['accountNumber']); ?></p>
                        <p><span>Year:</span> <?php echo htmlspecialchars($formData['year']); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($result && !empty($result['errors'])): ?>
                    <div class="alert alert-error">
                        <strong>Please correct the following errors:</strong>
                        <?php if (isset($result['errors']['security'])): ?>
                            <p><?php echo htmlspecialchars($result['errors']['security']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <form id="userForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input 
                            type="text" 
                            id="firstName" 
                            name="firstName" 
                            class="form-control<?php echo (isset($result['errors']['firstName']) ? ' error' : ''); ?>" 
                            value="<?php echo htmlspecialchars($formData['firstName']); ?>" 
                            required
                            maxlength="50">
                        <?php if (isset($result['errors']['firstName'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($result['errors']['firstName']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input 
                            type="text" 
                            id="lastName" 
                            name="lastName" 
                            class="form-control<?php echo (isset($result['errors']['lastName']) ? ' error' : ''); ?>" 
                            value="<?php echo htmlspecialchars($formData['lastName']); ?>" 
                            required
                            maxlength="50">
                        <?php if (isset($result['errors']['lastName'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($result['errors']['lastName']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control<?php echo (isset($result['errors']['email']) ? ' error' : ''); ?>" 
                            value="<?php echo htmlspecialchars($formData['email']); ?>" 
                            required
                            maxlength="100">
                        <?php if (isset($result['errors']['email'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($result['errors']['email']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="accountNumber">Account Number (<?php echo ACCOUNT_NUMBER_LENGTH; ?> characters alphanumeric)</label>
                        <input 
                            type="text" 
                            id="accountNumber" 
                            name="accountNumber" 
                            class="form-control<?php echo (isset($result['errors']['accountNumber']) ? ' error' : ''); ?>" 
                            value="<?php echo htmlspecialchars($formData['accountNumber']); ?>" 
                            required
                            maxlength="<?php echo ACCOUNT_NUMBER_LENGTH; ?>"
                            pattern="[a-zA-Z0-9]{<?php echo ACCOUNT_NUMBER_LENGTH; ?>}">
                        <?php if (isset($result['errors']['accountNumber'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($result['errors']['accountNumber']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select 
                            id="year" 
                            name="year" 
                            class="form-control<?php echo (isset($result['errors']['year']) ? ' error' : ''); ?>" 
                            required>
                            <option value="">Select a year</option>
                            <?php foreach ($validYears as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo ($formData['year'] == $year ? 'selected' : ''); ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($result['errors']['year'])): ?>
                            <span class="error-message"><?php echo htmlspecialchars($result['errors']['year']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="submit" id="submitBtn" class="btn btn-block">Submit</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!$result || !$result['success']): ?>
        <!-- Verification Dialog -->
        <div id="verificationModal" class="verification-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Verify Your Information</h2>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Please confirm that the following information is accurate and truthful before submission:</p>
                    <div class="verification-info" id="verificationContent"></div>
                    <p><strong>By clicking "Confirm and Submit", you certify that the above information is true and correct.</strong></p>
                </div>
                <div class="modal-footer">
                    <button id="cancelSubmit" class="btn btn-danger">Cancel</button>
                    <button id="confirmSubmit" class="btn btn-success">Confirm and Submit</button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!$result || !$result['success']): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get DOM elements
                const form = document.getElementById('userForm');
                const verificationModal = document.getElementById('verificationModal');
                const verificationContent = document.getElementById('verificationContent');
                const confirmSubmit = document.getElementById('confirmSubmit');
                const cancelSubmit = document.getElementById('cancelSubmit');
                const closeModal = document.querySelector('.close-modal');
                
                // Add form submission event listener
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Perform client-side validation
                        if (validateForm()) {
                            showVerificationDialog();
                        }
                    });
                }
                
                // Verification dialog event listeners
                if (confirmSubmit) {
                    confirmSubmit.addEventListener('click', function() {
                        form.submit();
                    });
                }
                
                if (cancelSubmit) {
                    cancelSubmit.addEventListener('click', function() {
                        closeVerificationDialog();
                    });
                }
                
                if (closeModal) {
                    closeModal.addEventListener('click', function() {
                        closeVerificationDialog();
                    });
                }
                
                /**
                 * The user can either close modal when clicking outside of the box
                 * or close the modal with Escape key
                 */
                window.addEventListener('click', function(e) {
                    if (e.target === verificationModal) {
                        closeVerificationDialog();
                    }
                });
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && verificationModal.style.display === 'flex') {
                        closeVerificationDialog();
                    }
                });
            });
            
            /**
             * Validate form fields on user side
             * @returns True if the form is valid
             */
            function validateForm() {
                let isValid = true;
                clearErrorStyles();
                
                const firstName = document.getElementById('firstName');
                if (firstName && (!firstName.value.trim() || firstName.value.trim().length < 2)) {
                    showError(firstName, 'First name must be at least 2 characters');
                    isValid = false;
                }
                
                const lastName = document.getElementById('lastName');
                if (lastName && (!lastName.value.trim() || lastName.value.trim().length < 2)) {
                    showError(lastName, 'Last name must be at least 2 characters');
                    isValid = false;
                }
                
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && (!email.value.trim() || !emailRegex.test(email.value.trim()))) {
                    showError(email, 'Please enter a valid email address');
                    isValid = false;
                }
                
                const accountNumber = document.getElementById('accountNumber');
                const accountRegex = /^[a-zA-Z0-9]{<?php echo ACCOUNT_NUMBER_LENGTH; ?>}$/;
                if (accountNumber && (!accountNumber.value.trim() || !accountRegex.test(accountNumber.value.trim()))) {
                    showError(accountNumber, 'Account number must be exactly <?php echo ACCOUNT_NUMBER_LENGTH; ?> alphanumeric characters');
                    isValid = false;
                }
                
                const year = document.getElementById('year');
                if (year && !year.value) {
                    showError(year, 'Please select a year');
                    isValid = false;
                }
                
                return isValid;
            }
            
            /**
             * Show error styling for a field
             * @param {HTMLElement} element Form element
             * @param {string} message Error message
             */
            function showError(element, message) {
                element.classList.add('error');
                // Create error message element if it doesn't exist
                let errorElement = element.parentNode.querySelector('.error-message');
                if (!errorElement) {
                    errorElement = document.createElement('span');
                    errorElement.className = 'error-message';
                    element.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = message;
            }
            
            /**
             * Clear all error styling
             */
            function clearErrorStyles() {
                const errorElements = document.querySelectorAll('.form-control.error');
                errorElements.forEach(element => {
                    element.classList.remove('error');
                });
                
                const errorMessageElements = document.querySelectorAll('.error-message');
                errorMessageElements.forEach(element => {
                    element.remove();
                });
            }
            
            /**
             * Show verification dialog with form data
             */
            function showVerificationDialog() {
                const firstName = document.getElementById('firstName').value.trim();
                const lastName = document.getElementById('lastName').value.trim();
                const email = document.getElementById('email').value.trim();
                const accountNumber = document.getElementById('accountNumber').value.trim();
                const year = document.getElementById('year').value;
                
                // Populate verification content
                verificationContent.innerHTML = `
                    <div class="data-summary">
                        <p><span>First Name:</span> ${escapeHtml(firstName)}</p>
                        <p><span>Last Name:</span> ${escapeHtml(lastName)}</p>
                        <p><span>Email:</span> ${escapeHtml(email)}</p>
                        <p><span>Account Number:</span> ${escapeHtml(accountNumber)}</p>
                        <p><span>Year:</span> ${escapeHtml(year)}</p>
                    </div>
                `;
                
                // Show modal
                document.getElementById('verificationModal').style.display = 'flex';
            }
            
            /**
             * Close verification dialog
             */
            function closeVerificationDialog() {
                document.getElementById('verificationModal').style.display = 'none';
            }
            
            /**
             * Escape HTML to prevent XSS
             * @param {string} text Text to escape
             * @returns {string} Escaped text
             */
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        </script>
    <?php endif; ?>
</body>
</html>