<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandrill Email Demo - PHP</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📧 Mandrill Email Demo</h1>
            <p class="subtitle">PHP Implementation - Mailchimp Transactional API</p>
        </header>

        <?php if (!empty($scriptRunStatus)): ?>
            <div class="result-container">
                <?= $scriptRunStatus ?>
            </div>
        <?php endif; ?>

        <form action="/testEmailbasedOnScriptID" method="POST" class="demo-form">
            <div class="form-group">
                <label for="Script_name">Select Email Operation:</label>
                <select name="Script_name" id="Script_name" onchange="updateForm()">
                    <option value="single">1. Send a Single Email to a Single Recipient</option>
                    <option value="mergeTags">2. Send Email with Merge Tags</option>
                    <option value="attachments">3. Send Email with Attachments</option>
                    <option value="templates">4. Send Email Using Template</option>
                    <option value="allInOne">5. Kitchen Sink - All Features</option>
                    <option value="sms">📱 6. Send SMS Message</option>
                </select>
            </div>

            <div class="description-box" id="descriptionBox">
                <p id="descriptionText">Send a single email to a single recipient. This script uses the Mailchimp Transactional API to send a simple email with a subject, from name, from email, to name, to email, and content.</p>
            </div>

            <!-- Merge Tags Form Fields -->
            <div id="mergeTagsFields" class="conditional-fields" style="display: none;">
                <h3>Personalization Fields</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" name="firstName" id="firstName" value="John" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" name="lastName" id="lastName" value="Smith" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="companyName">Company Name:</label>
                        <input type="text" name="companyName" id="companyName" value="Intuit Developer Program" required>
                    </div>
                    <div class="form-group">
                        <label for="membershipLevel">Membership Level:</label>
                        <input type="text" name="membershipLevel" id="membershipLevel" value="Premium" required>
                    </div>
                </div>
            </div>

            <!-- Template Selection -->
            <div id="templateFields" class="conditional-fields" style="display: none;">
                <h3>Select Template</h3>
                <div class="template-options">
                    <label class="template-option">
                        <input type="radio" name="template_name" value="template1" checked>
                        <div class="template-preview">
                            <h4>Template 1 - Welcome</h4>
                            <p>A welcome message template with personalized greeting.</p>
                        </div>
                    </label>
                    <label class="template-option">
                        <input type="radio" name="template_name" value="template2">
                        <div class="template-preview">
                            <h4>Template 2 - Greetings</h4>
                            <p>A general greetings template with account information.</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Attachments Info -->
            <div id="attachmentsInfo" class="conditional-fields" style="display: none;">
                <h3>Attachments</h3>
                <div class="info-box">
                    <p>This demo will attach the following files:</p>
                    <ul>
                        <li>📄 sample.pdf - A sample PDF document</li>
                        <li>📝 readme.txt - Dynamically generated text file</li>
                    </ul>
                </div>
            </div>

            <!-- Kitchen Sink Info -->
            <div id="kitchenSinkInfo" class="conditional-fields" style="display: none;">
                <h3>All Features Demo</h3>
                <div class="info-box">
                    <p>This comprehensive demo includes:</p>
                    <ul>
                        <li>✅ Merge tags (global and per-recipient)</li>
                        <li>📎 File attachments</li>
                        <li>📊 Open and click tracking</li>
                        <li>🏷️ Tags and metadata</li>
                        <li>📨 Custom headers</li>
                    </ul>
                </div>
            </div>

            <!-- SMS Input Fields -->
            <div id="smsFields" class="conditional-fields" style="display: none;">
                <h3>📱 SMS Message Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="smsToPhone">Recipient Phone Number (E.164 format):</label>
                        <input type="tel" name="smsToPhone" id="smsToPhone" 
                               placeholder="+1234567890"
                               pattern="^\+[1-9]\d{1,14}$"
                               title="Phone number in E.164 format (e.g., +1234567890)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="smsMessage">Message Text:</label>
                    <textarea name="smsMessage" id="smsMessage" 
                              placeholder="Enter your SMS message here (max 1600 characters)"
                              maxlength="1600"
                              rows="3"></textarea>
                    <small><span id="smsCharCount">0</span>/1600 characters</small>
                </div>
                <div class="info-box">
                    <p>ℹ️ <strong>Note:</strong> Phone numbers must be in E.164 format (e.g., +1234567890). SMS requires a verified sender phone number configured in your Mandrill account.</p>
                    <p>📝 <strong>Consent:</strong> By default, messages are sent with 'onetime' consent type.</p>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">🚀 Generate Test Email</button>
        </form>

        <footer>
            <p>
                <a href="https://github.com/mailchimp/mailchimp-transactional-php" target="_blank">PHP SDK</a> |
                <a href="https://mailchimp.com/developer/transactional/api/" target="_blank">API Docs</a>
            </p>
        </footer>
    </div>

    <script>
        const descriptions = {
            single: 'Send a single email to a single recipient. This script uses the Mailchimp Transactional API to send a simple email with a subject, from name, from email, to name, to email, and content. For this demo all values will be used from config files.',
            mergeTags: 'Send an email with merge tags. Merge tags are placeholders in your email content that are replaced with dynamic data when the email is sent. You can use this script to send personalized emails to your recipients.',
            attachments: 'Send an email with attachments. Attachments can be added to your email by providing a URL to the file or by providing the file as a base64 encoded string. For simplicity, this demo uses a dynamic text file and sample attachments.',
            templates: 'Send an email with a template. Templates allow you to create reusable email layouts that can be populated with dynamic content. For this demo pre-defined email templates will be created and used.',
            allInOne: 'Send an email with all the supported features. This comprehensive example demonstrates merge tags, attachments, tracking, metadata, and custom headers all in one email.',
            sms: 'Send an SMS to a single recipient. This script uses the Mailchimp Transactional API to send an SMS message. SMS messages require a verified sender phone number and recipient consent. You can specify the recipient phone number (E.164 format), message text, and consent type. This is useful for sending transactional SMS notifications like order confirmations, appointment reminders, or verification codes.'
        };

        function updateForm() {
            const select = document.getElementById('Script_name');
            const value = select.value;
            const submitBtn = document.getElementById('submitBtn');
            
            // Update description
            document.getElementById('descriptionText').textContent = descriptions[value] || '';
            
            // Hide all conditional fields
            document.querySelectorAll('.conditional-fields').forEach(el => {
                el.style.display = 'none';
            });
            
            // Reset button text
            submitBtn.innerHTML = '🚀 Generate Test Email';
            
            // Show relevant fields
            switch(value) {
                case 'mergeTags':
                    document.getElementById('mergeTagsFields').style.display = 'block';
                    break;
                case 'templates':
                    document.getElementById('templateFields').style.display = 'block';
                    break;
                case 'attachments':
                    document.getElementById('attachmentsInfo').style.display = 'block';
                    break;
                case 'allInOne':
                    document.getElementById('kitchenSinkInfo').style.display = 'block';
                    break;
                case 'sms':
                    document.getElementById('smsFields').style.display = 'block';
                    submitBtn.innerHTML = '📱 Send Test SMS';
                    break;
            }
        }
        
        // SMS character counter
        function updateSmsCharCount() {
            const textarea = document.getElementById('smsMessage');
            const counter = document.getElementById('smsCharCount');
            if (textarea && counter) {
                counter.textContent = textarea.value.length;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateForm();
            
            // Add SMS character counter listener
            const smsTextarea = document.getElementById('smsMessage');
            if (smsTextarea) {
                smsTextarea.addEventListener('input', updateSmsCharCount);
            }
        });
    </script>
</body>
</html>

