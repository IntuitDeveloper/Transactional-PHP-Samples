# Send Email with Attachments - PHP

## Overview

This use case demonstrates how to send emails with file attachments using the Mailchimp Transactional (Mandrill) API.

## Prerequisites

- PHP 7.4+
- Composer
- Mandrill API key
- Files to attach

## Code Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

// Read and encode a PDF file
$pdfContent = base64_encode(file_get_contents('document.pdf'));

// Create a dynamic text file
$textContent = "Generated report\nDate: " . date('Y-m-d');
$textBase64 = base64_encode($textContent);

$message = [
    'html' => '<h1>Your Documents</h1><p>Please find the attached files.</p>',
    'subject' => 'Documents Attached',
    'from_email' => 'sender@example.com',
    'from_name' => 'Document Service',
    'to' => [
        [
            'email' => 'recipient@example.com',
            'type' => 'to'
        ]
    ],
    'attachments' => [
        [
            'type' => 'application/pdf',
            'name' => 'document.pdf',
            'content' => $pdfContent
        ],
        [
            'type' => 'text/plain',
            'name' => 'report.txt',
            'content' => $textBase64
        ]
    ]
];

$result = $mailchimp->messages->send(['message' => $message]);
```

## Attachment Structure

Each attachment requires:

| Field | Description |
|-------|-------------|
| `type` | MIME type (e.g., `application/pdf`, `image/png`) |
| `name` | Filename as it will appear to recipient |
| `content` | Base64-encoded file content |

## Common MIME Types

| Extension | MIME Type |
|-----------|-----------|
| `.pdf` | `application/pdf` |
| `.txt` | `text/plain` |
| `.csv` | `text/csv` |
| `.json` | `application/json` |
| `.png` | `image/png` |
| `.jpg` | `image/jpeg` |
| `.zip` | `application/zip` |

## Dynamic Attachments

Create attachments on-the-fly:

```php
// CSV report
$csv = "Name,Email,Status\n";
$csv .= "John,john@example.com,Active\n";

$attachments[] = [
    'type' => 'text/csv',
    'name' => 'users.csv',
    'content' => base64_encode($csv)
];

// JSON data
$data = ['status' => 'success', 'count' => 42];

$attachments[] = [
    'type' => 'application/json',
    'name' => 'data.json',
    'content' => base64_encode(json_encode($data, JSON_PRETTY_PRINT))
];
```

## Size Limits

- Maximum message size (including attachments): 25MB
- Consider using links for very large files

## Related Documentation

- [Mandrill API - Attachments](https://mailchimp.com/developer/transactional/api/messages/)

