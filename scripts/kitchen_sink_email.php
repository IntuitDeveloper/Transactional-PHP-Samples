<?php
/**
 * Kitchen Sink Email - All Features Demo
 *
 * This script demonstrates all Mandrill email features in one comprehensive example:
 * - Merge tags (global and per-recipient)
 * - Attachments
 * - Embedded images
 * - Tracking options
 * - Metadata
 * - Custom headers
 *
 * Usage:
 *     php kitchen_sink_email.php
 *
 * Requirements:
 *     - mailchimp/transactional
 *     - vlucas/phpdotenv
 */

require_once __DIR__ . '/config.php';

/**
 * Send a kitchen sink email with all features.
 *
 * @return array Result from the API
 */
function sendKitchenSinkEmail(): array
{
    $mailchimp = getMailchimpClient();

    // Prepare attachments
    $attachments = [];

    // PDF attachment if available
    $pdfPath = __DIR__ . '/sample.pdf';
    if (file_exists($pdfPath)) {
        $attachments[] = [
            'type' => 'application/pdf',
            'name' => 'sample.pdf',
            'content' => base64_encode(file_get_contents($pdfPath))
        ];
    }

    // Dynamic text file
    $textContent = "Kitchen Sink Demo File\n";
    $textContent .= "======================\n\n";
    $textContent .= "Generated at: " . date('c') . "\n";
    $textContent .= "This file demonstrates dynamic attachment creation.\n";
    
    $attachments[] = [
        'type' => 'text/plain',
        'name' => 'readme.txt',
        'content' => base64_encode($textContent)
    ];

    // Prepare embedded images (if available)
    $images = [];
    $logoPath = __DIR__ . '/../public/images/logo.png';
    if (file_exists($logoPath)) {
        $images[] = [
            'type' => 'image/png',
            'name' => 'logo.png',
            'content' => base64_encode(file_get_contents($logoPath))
        ];
    }

    $message = [
        'html' => '
            <h1>Hello {{fname}}!</h1>
            <p>This email demonstrates <strong>all</strong> Transactional API features.</p>
            
            <h2>Merge Tags Demo</h2>
            <ul>
                <li>First Name: {{fname}}</li>
                <li>Company: {{company_name}}</li>
                <li>Account ID: {{account_id}}</li>
            </ul>
            
            <h2>Attachments</h2>
            <p>Check the attached files:</p>
            <ul>
                <li>sample.pdf - A sample PDF document</li>
                <li>readme.txt - Dynamically generated text file</li>
            </ul>
            
            <h2>Tracking</h2>
            <p>This email has open and click tracking enabled.</p>
            <p>Click <a href="https://mailchimp.com/developer/transactional/">here</a> to visit the Mandrill docs.</p>
            
            <hr>
            <p style="color: #666; font-size: 12px;">
                This is a demo email from the PHP Transactional API samples.
            </p>
        ',
        'text' => "
Hello {{fname}}!

This email demonstrates all Transactional API features.

MERGE TAGS DEMO
- First Name: {{fname}}
- Company: {{company_name}}
- Account ID: {{account_id}}

ATTACHMENTS
Check the attached files:
- sample.pdf - A sample PDF document
- readme.txt - Dynamically generated text file

TRACKING
This email has open and click tracking enabled.

---
This is a demo email from the PHP Transactional API samples.
        ",
        'subject' => 'Hello {{fname}} - Kitchen Sink Demo',
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
            'X-Custom-Header' => 'Kitchen-Sink-Demo',
            'X-Generated-By' => 'PHP-Transactional-Samples'
        ],
        'global_merge_vars' => [
            ['name' => 'company_name', 'content' => 'Intuit Developer Program']
        ],
        'merge_vars' => [
            [
                'rcpt' => DEFAULT_TO_EMAIL,
                'vars' => [
                    ['name' => 'fname', 'content' => 'John'],
                    ['name' => 'account_id', 'content' => 'ACC-12345']
                ]
            ]
        ],
        'merge_language' => 'handlebars',
        'attachments' => $attachments,
        'images' => $images,
        'track_opens' => true,
        'track_clicks' => true,
        'auto_text' => true,
        'auto_html' => false,
        'inline_css' => true,
        'tags' => ['demo', 'kitchen-sink', 'all-features'],
        'metadata' => [
            'campaign' => 'kitchen-sink-demo',
            'version' => '1.0',
            'language' => 'php'
        ],
        'important' => true,
        'view_content_link' => true,
        'preserve_recipients' => false,
        'async' => false
    ];

    try {
        $result = $mailchimp->messages->send(['message' => $message]);
        return [
            'success' => true,
            'result' => $result,
            'features' => [
                'merge_tags' => true,
                'attachments' => count($attachments),
                'images' => count($images),
                'tracking' => true,
                'metadata' => true,
                'custom_headers' => true
            ]
        ];
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

    echo "Sending kitchen sink email (all features)...\n\n";
    $result = sendKitchenSinkEmail();

    if ($result['success']) {
        echo "Kitchen sink email sent successfully!\n";
        echo str_repeat('=', 50) . "\n";
        
        echo "Features used:\n";
        $features = $result['features'];
        echo "  - Merge tags: " . ($features['merge_tags'] ? 'Yes' : 'No') . "\n";
        echo "  - Attachments: {$features['attachments']}\n";
        echo "  - Embedded images: {$features['images']}\n";
        echo "  - Open/Click tracking: " . ($features['tracking'] ? 'Yes' : 'No') . "\n";
        echo "  - Metadata: " . ($features['metadata'] ? 'Yes' : 'No') . "\n";
        echo "  - Custom headers: " . ($features['custom_headers'] ? 'Yes' : 'No') . "\n";
        
        echo str_repeat('=', 50) . "\n";
        echo "Recipients:\n";
        foreach ($result['result'] as $recipient) {
            $email = is_object($recipient) ? $recipient->email : $recipient['email'];
            $status = is_object($recipient) ? $recipient->status : $recipient['status'];
            $id = is_object($recipient) ? ($recipient->_id ?? null) : ($recipient['_id'] ?? null);
            echo "  {$email}: {$status}\n";
            if (!empty($id)) {
                echo "    Message ID: {$id}\n";
            }
        }
        echo str_repeat('=', 50) . "\n";
    } else {
        echo "Error sending kitchen sink email!\n";
        echo str_repeat('=', 50) . "\n";
        echo "Mandrill API Error: {$result['error']}\n";
        echo str_repeat('=', 50) . "\n";
    }
}

