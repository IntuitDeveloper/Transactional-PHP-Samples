<?php
/**
 * Send Email Using Template via Mandrill API
 *
 * This script demonstrates how to send emails using pre-created
 * Mandrill templates.
 *
 * Usage:
 *     php email_with_template.php
 *
 * Requirements:
 *     - mailchimp/transactional
 *     - vlucas/phpdotenv
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/create_template.php';

/**
 * Send an email using a Mandrill template.
 *
 * @param string $templateName Name of the template to use
 * @return array Result from the API
 */
function sendEmailWithTemplate(string $templateName = 'template1'): array
{
    $mailchimp = getMailchimpClient();

    // Ensure the template exists (create if not)
    ensureTemplateExists($templateName);

    $message = [
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => DEFAULT_FROM_NAME,
        'subject' => 'Welcome, {{fname}}',
        'to' => [
            [
                'email' => DEFAULT_TO_EMAIL,
                'name' => DEFAULT_TO_NAME,
                'type' => 'to'
            ]
        ],
        'global_merge_vars' => [
            ['name' => 'company_name', 'content' => 'Intuit Developer Program']
        ],
        'merge_vars' => [
            [
                'rcpt' => DEFAULT_TO_EMAIL,
                'vars' => [
                    ['name' => 'fname', 'content' => 'John'],
                    ['name' => 'account_id', 'content' => 'ACCOUNT-001']
                ]
            ]
        ],
        'merge_language' => 'handlebars',
        'tags' => ['onboarding', 'welcome']
    ];

    // Template content for mc:edit regions
    if ($templateName === 'template1') {
        $templateContent = [
            [
                'name' => 'welcome_message',
                'content' => "<hr><p>Thanks for joining <strong>{{company_name}}</strong>! We're excited to have you on board.</p><hr>This email is generated from template1.<hr>"
            ]
        ];
    } else {
        $templateContent = [
            [
                'name' => 'goodbye_message',
                'content' => "<hr><p>We dont have much updates, but this email is for your account: {{account_id}} in company: {{company_name}}</p><hr>"
            ]
        ];
    }

    try {
        $result = $mailchimp->messages->sendTemplate([
            'template_name' => $templateName,
            'template_content' => $templateContent,
            'message' => $message
        ]);
        return ['success' => true, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    if (empty(MANDRILL_API_KEY)) {
        echo "Error: MANDRILL_API_KEY not found in environment variables!\n";
        echo "Please create a .env file with your Mandrill API key.\n";
        exit(1);
    }

    // Get template name from environment or use default
    $templateName = $_ENV['SELECTED_TEMPLATE'] ?? 'template1';

    echo "Sending email using template: {$templateName}...\n\n";
    $result = sendEmailWithTemplate($templateName);

    if ($result['success']) {
        echo "Template email sent successfully!\n";
        echo str_repeat('=', 50) . "\n";
        foreach ($result['result'] as $recipient) {
            $email = is_object($recipient) ? $recipient->email : $recipient['email'];
            $status = is_object($recipient) ? $recipient->status : $recipient['status'];
            $id = is_object($recipient) ? ($recipient->_id ?? null) : ($recipient['_id'] ?? null);
            echo "{$email}: {$status}\n";
            if (!empty($id)) {
                echo "  Message ID: {$id}\n";
            }
        }
        echo str_repeat('=', 50) . "\n";
    } else {
        echo "Error sending template email!\n";
        echo str_repeat('=', 50) . "\n";
        echo "Mandrill API Error: {$result['error']}\n";
        echo str_repeat('=', 50) . "\n";
    }
}

