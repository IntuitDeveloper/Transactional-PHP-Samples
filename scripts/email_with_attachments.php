<?php
/**
 * Send Email with Attachments using Mandrill API
 *
 * This script demonstrates how to attach files to emails sent via
 * Mailchimp Transactional (Mandrill).
 *
 * Usage:
 *     php email_with_attachments.php
 *
 * Requirements:
 *     - mailchimp/transactional
 *     - vlucas/phpdotenv
 */

require_once __DIR__ . '/config.php';

/**
 * Read a file and return its base64 encoded content.
 *
 * @param string $filePath Path to the file
 * @return string|null Base64 encoded content or null if file not found
 */
function readFileAsBase64(string $filePath): ?string
{
    if (!file_exists($filePath)) {
        echo "Warning: File not found: {$filePath}\n";
        return null;
    }
    return base64_encode(file_get_contents($filePath));
}

/**
 * Send an email with file attachments.
 *
 * @return array Result from the API
 */
function sendWithAttachments(): array
{
    $mailchimp = getMailchimpClient();

    $attachments = [];

    // Try to attach a PDF file if it exists
    $pdfPath = __DIR__ . '/sample.pdf';
    if (file_exists($pdfPath)) {
        $pdfContent = readFileAsBase64($pdfPath);
        if ($pdfContent) {
            $attachments[] = [
                'type' => 'application/pdf',
                'name' => 'sample.pdf',
                'content' => $pdfContent
            ];
        }
    }

    // Create a text file attachment dynamically
    $textContent = "This is a demo text file created by the Mandrill Use Case.\n\n";
    $textContent .= "Generated at: " . date('c') . "\n";
    $textContent .= "This file was created using PHP.\n";
    
    $attachments[] = [
        'type' => 'text/plain',
        'name' => 'readme.txt',
        'content' => base64_encode($textContent)
    ];

    $message = [
        'html' => '
            <h1>Your Documents</h1>
            <p>Please find the attached files for your review.</p>
            <ul>
                <li>Sample PDF document</li>
                <li>Readme text file</li>
            </ul>
        ',
        'text' => 'Your documents are attached. Please review them at your convenience.',
        'subject' => 'Documents Attached',
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => DEFAULT_FROM_NAME,
        'to' => [
            [
                'email' => DEFAULT_TO_EMAIL,
                'name' => DEFAULT_TO_NAME,
                'type' => 'to'
            ]
        ],
        'attachments' => $attachments,
        'tags' => ['attachments', 'outbound-documents']
    ];

    try {
        $result = $mailchimp->messages->send(['message' => $message]);
        return ['success' => true, 'result' => $result, 'attachmentCount' => count($attachments)];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Send an email with a dynamically created CSV attachment.
 *
 * @return array Result from the API
 */
function sendCsvAttachment(): array
{
    $mailchimp = getMailchimpClient();

    // Create CSV content
    $csvContent = "Name,Email,Status,Joined\n";
    $csvContent .= "John Smith,john@example.org,Active,2024-01-15\n";
    $csvContent .= "Jane Doe,jane@example.org,Active,2024-02-20\n";
    $csvContent .= "Bob Johnson,bob@example.org,Pending,2024-03-10\n";

    $attachments = [
        [
            'type' => 'text/csv',
            'name' => 'user_report.csv',
            'content' => base64_encode($csvContent)
        ]
    ];

    $message = [
        'html' => '<h1>User Report</h1><p>Please find the attached CSV report.</p>',
        'text' => 'User Report - CSV file attached.',
        'subject' => 'User Report - CSV Attached',
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => 'Report Service',
        'to' => [
            [
                'email' => DEFAULT_TO_EMAIL,
                'type' => 'to'
            ]
        ],
        'attachments' => $attachments,
        'tags' => ['report', 'csv']
    ];

    try {
        $result = $mailchimp->messages->send(['message' => $message]);
        return ['success' => true, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Send an email with a JSON attachment.
 *
 * @return array Result from the API
 */
function sendJsonAttachment(): array
{
    $mailchimp = getMailchimpClient();

    // Create JSON data
    $data = [
        'status' => 'success',
        'total_users' => 42,
        'active_users' => 38,
        'timestamp' => date('c'),
        'users' => [
            ['name' => 'John', 'email' => 'john@example.org'],
            ['name' => 'Jane', 'email' => 'jane@example.org']
        ]
    ];

    $jsonContent = json_encode($data, JSON_PRETTY_PRINT);

    $attachments = [
        [
            'type' => 'application/json',
            'name' => 'data.json',
            'content' => base64_encode($jsonContent)
        ]
    ];

    $message = [
        'html' => '<h1>API Response Data</h1><p>JSON data file attached.</p>',
        'subject' => 'API Data Export',
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => 'Data Export Service',
        'to' => [
            [
                'email' => DEFAULT_TO_EMAIL,
                'type' => 'to'
            ]
        ],
        'attachments' => $attachments,
        'tags' => ['api', 'json']
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

    echo "Sending email with attachments...\n\n";
    $result = sendWithAttachments();

    if ($result['success']) {
        echo "Email with attachments sent successfully!\n";
        echo str_repeat('=', 50) . "\n";
        echo "Number of attachments: {$result['attachmentCount']}\n";
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
        echo "Error sending email with attachments!\n";
        echo str_repeat('=', 50) . "\n";
        echo "Mandrill API Error: {$result['error']}\n";
        echo str_repeat('=', 50) . "\n";
    }
}

