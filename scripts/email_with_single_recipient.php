<?php
/**
 * Send Email to Single Recipient using Mandrill API
 *
 * This script demonstrates how to send a basic transactional email
 * using Mailchimp Transactional (Mandrill) API.
 *
 * Usage:
 *     php email_with_single_recipient.php
 *
 * Requirements:
 *     - mailchimp/transactional
 *     - vlucas/phpdotenv
 */

require_once __DIR__ . '/config.php';

/**
 * Send a simple transactional email.
 *
 * @return array Result from the API
 */
function sendSingleEmail(): array
{
    $mailchimp = getMailchimpClient();

    $message = [
        'html' => '<p>Hello HTML world! from MailChimp Transactional API Demo</p>',
        'text' => 'Hello plain world! from MailChimp Transactional API Demo',
        'subject' => 'Hello world',
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
        ]
    ];

    try {
        $result = $mailchimp->messages->send(['message' => $message]);
        return ['success' => true, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Send email with advanced options (tracking, metadata).
 *
 * @return array Result from the API
 */
function sendAdvancedEmail(): array
{
    $mailchimp = getMailchimpClient();

    $message = [
        'html' => '
            <h1>Welcome!</h1>
            <p>This is an advanced email with tracking enabled.</p>
            <p>Click <a href="https://mailchimp.com">here</a> to visit Mailchimp.</p>
        ',
        'text' => 'Welcome! This is an advanced email with tracking enabled.',
        'subject' => 'Advanced Email Example',
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
            'Reply-To' => DEFAULT_FROM_EMAIL,
            'X-Custom-Header' => 'PHP-Demo'
        ],
        'track_opens' => true,
        'track_clicks' => true,
        'tags' => ['welcome', 'demo'],
        'metadata' => [
            'user_id' => '12345',
            'campaign' => 'php-demo'
        ]
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
    // Check if API key is configured
    if (empty(MANDRILL_API_KEY)) {
        echo "Error: MANDRILL_API_KEY not found in environment variables!\n";
        echo "Please create a .env file with your Mandrill API key.\n";
        exit(1);
    }

    echo "Sending single email...\n\n";
    $result = sendSingleEmail();

    if ($result['success']) {
        echo "Email sent successfully!\n";
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
        echo "Error sending email!\n";
        echo str_repeat('=', 50) . "\n";
        echo "Mandrill API Error: {$result['error']}\n";
        echo str_repeat('=', 50) . "\n";
    }
}

