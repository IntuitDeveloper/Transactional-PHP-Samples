<?php
/**
 * Send Email with Merge Tags using Mandrill API
 *
 * This script demonstrates how to personalize emails using merge tags
 * with Mailchimp Transactional (Mandrill) API.
 *
 * Usage:
 *     php email_with_merge_tags.php
 *
 * Requirements:
 *     - mailchimp/transactional
 *     - vlucas/phpdotenv
 */

require_once __DIR__ . '/config.php';

/**
 * Send a personalized welcome email using merge tags.
 *
 * @param string $firstName Recipient's first name
 * @param string $lastName Recipient's last name
 * @param string $companyName Company name
 * @param string $membershipLevel Membership level
 * @return array Result from the API
 */
function sendEmailWithMergeTags(
    string $firstName = 'John',
    string $lastName = 'Smith',
    string $companyName = 'Intuit Developer Program',
    string $membershipLevel = 'Premium'
): array {
    $mailchimp = getMailchimpClient();

    $message = [
        'html' => '
            <h1>Welcome {{fname}}!</h1>
            <p>Hi {{fname}} {{lname}},</p>
            <p>Thanks for joining the {{company_name}}! Your account is now active.</p>
            <p>Your membership level: {{membership_level}}</p>
            <p>Best regards,<br>The {{company_name}} Team</p>
        ',
        'text' => "
            Welcome {{fname}}!
            
            Hi {{fname}} {{lname}},
            
            Thanks for joining the {{company_name}}! Your account is now active.
            Your membership level: {{membership_level}}
            
            Best regards,
            The {{company_name}} Team
        ",
        'subject' => 'Welcome to {{company_name}}, {{fname}}!',
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => DEFAULT_FROM_NAME,
        'to' => [
            [
                'email' => DEFAULT_TO_EMAIL,
                'name' => DEFAULT_TO_NAME,
                'type' => 'to'
            ]
        ],
        'headers' => [
            'Reply-To' => DEFAULT_FROM_EMAIL
        ],
        'global_merge_vars' => [
            ['name' => 'company_name', 'content' => $companyName],
            ['name' => 'membership_level', 'content' => $membershipLevel]
        ],
        'merge_vars' => [
            [
                'rcpt' => DEFAULT_TO_EMAIL,
                'vars' => [
                    ['name' => 'fname', 'content' => $firstName],
                    ['name' => 'lname', 'content' => $lastName]
                ]
            ]
        ],
        'merge_language' => 'handlebars'
    ];

    try {
        $result = $mailchimp->messages->send(['message' => $message]);
        return ['success' => true, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Send email with multiple recipients and personalized merge tags.
 *
 * @return array Result from the API
 */
function sendBulkEmailWithMergeTags(): array
{
    $mailchimp = getMailchimpClient();

    $recipients = [
        ['email' => DEFAULT_TO_EMAIL, 'name' => 'John Smith', 'fname' => 'John', 'lname' => 'Smith'],
    ];

    $to = [];
    $mergeVars = [];

    foreach ($recipients as $recipient) {
        $to[] = [
            'email' => $recipient['email'],
            'name' => $recipient['name'],
            'type' => 'to'
        ];
        $mergeVars[] = [
            'rcpt' => $recipient['email'],
            'vars' => [
                ['name' => 'fname', 'content' => $recipient['fname']],
                ['name' => 'lname', 'content' => $recipient['lname']]
            ]
        ];
    }

    $message = [
        'html' => '
            <h1>Hello {{fname}}!</h1>
            <p>This is a personalized message for {{fname}} {{lname}}.</p>
            <p>Company: {{company_name}}</p>
        ',
        'subject' => 'Hello {{fname}}!',
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => DEFAULT_FROM_NAME,
        'to' => $to,
        'global_merge_vars' => [
            ['name' => 'company_name', 'content' => 'Intuit Developer Program']
        ],
        'merge_vars' => $mergeVars,
        'merge_language' => 'handlebars',
        'preserve_recipients' => false
    ];

    try {
        $result = $mailchimp->messages->send(['message' => $message]);
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

    echo "Sending email with merge tags...\n\n";
    $result = sendEmailWithMergeTags();

    if ($result['success']) {
        echo "Email with merge tags sent successfully!\n";
        echo str_repeat('=', 50) . "\n";
        foreach ($result['result'] as $recipient) {
            $email = is_object($recipient) ? $recipient->email : $recipient['email'];
            $status = is_object($recipient) ? $recipient->status : $recipient['status'];
            echo "{$email}: {$status}\n";
        }
        echo str_repeat('=', 50) . "\n";
    } else {
        echo "Error sending email!\n";
        echo "Mandrill API Error: {$result['error']}\n";
    }
}

