# Kitchen Sink - All Features Demo - PHP

## Overview

This comprehensive example demonstrates all major features of the Mailchimp Transactional (Mandrill) API in a single email.

## Features Included

- ✅ Merge tags (global and per-recipient)
- ✅ File attachments
- ✅ Embedded images
- ✅ Open tracking
- ✅ Click tracking
- ✅ Custom headers
- ✅ Metadata
- ✅ Tags

## Complete Code Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

// Prepare attachments
$attachments = [];

// PDF attachment
if (file_exists('sample.pdf')) {
    $attachments[] = [
        'type' => 'application/pdf',
        'name' => 'sample.pdf',
        'content' => base64_encode(file_get_contents('sample.pdf'))
    ];
}

// Dynamic text file
$textContent = "Kitchen Sink Demo\nGenerated: " . date('c');
$attachments[] = [
    'type' => 'text/plain',
    'name' => 'readme.txt',
    'content' => base64_encode($textContent)
];

// Embedded images
$images = [];
if (file_exists('logo.png')) {
    $images[] = [
        'type' => 'image/png',
        'name' => 'logo',
        'content' => base64_encode(file_get_contents('logo.png'))
    ];
}

// Build the message
$message = [
    // Content
    'html' => '
        <h1>Hello {{fname}}!</h1>
        <p>This demonstrates all API features.</p>
        <p>Company: {{company_name}}</p>
        <p>Account: {{account_id}}</p>
        <p><a href="https://example.com">Click here</a> for tracking demo.</p>
        <img src="cid:logo" alt="Logo">
    ',
    'text' => 'Hello {{fname}}! This demonstrates all API features.',
    'subject' => 'Kitchen Sink Demo - {{fname}}',
    
    // Sender
    'from_email' => 'sender@example.com',
    'from_name' => 'Demo Sender',
    
    // Recipients
    'to' => [
        [
            'email' => 'recipient@example.com',
            'name' => 'John Smith',
            'type' => 'to'
        ]
    ],
    
    // Custom headers
    'headers' => [
        'Reply-To' => 'reply@example.com',
        'X-Custom-Header' => 'Kitchen-Sink-Demo',
        'X-Campaign-ID' => 'demo-2024'
    ],
    
    // Merge variables
    'global_merge_vars' => [
        ['name' => 'company_name', 'content' => 'Acme Corp']
    ],
    'merge_vars' => [
        [
            'rcpt' => 'recipient@example.com',
            'vars' => [
                ['name' => 'fname', 'content' => 'John'],
                ['name' => 'account_id', 'content' => 'ACC-12345']
            ]
        ]
    ],
    'merge_language' => 'handlebars',
    
    // Attachments and images
    'attachments' => $attachments,
    'images' => $images,
    
    // Tracking
    'track_opens' => true,
    'track_clicks' => true,
    
    // Processing options
    'auto_text' => true,
    'auto_html' => false,
    'inline_css' => true,
    
    // Organization
    'tags' => ['demo', 'kitchen-sink', 'all-features'],
    'metadata' => [
        'campaign' => 'kitchen-sink',
        'version' => '1.0',
        'environment' => 'development'
    ],
    
    // Delivery options
    'important' => true,
    'view_content_link' => true,
    'preserve_recipients' => false,
    'async' => false
];

try {
    $result = $mailchimp->messages->send(['message' => $message]);
    print_r($result);
} catch (\MailchimpTransactional\ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Message Options Explained

### Tracking Options

| Option | Description |
|--------|-------------|
| `track_opens` | Track when recipients open the email |
| `track_clicks` | Track when recipients click links |

### Processing Options

| Option | Description |
|--------|-------------|
| `auto_text` | Generate plain text from HTML |
| `auto_html` | Generate HTML from plain text |
| `inline_css` | Inline CSS styles for better email client support |

### Delivery Options

| Option | Description |
|--------|-------------|
| `important` | Mark as high priority |
| `async` | Send asynchronously (returns immediately) |
| `preserve_recipients` | Show all recipients in headers |

### Organization

| Option | Description |
|--------|-------------|
| `tags` | Array of tags for categorizing messages |
| `metadata` | Key-value pairs for your own tracking |

## Embedded Images

Use `cid:` (Content-ID) to reference embedded images:

```html
<img src="cid:logo" alt="Logo">
```

```php
'images' => [
    [
        'type' => 'image/png',
        'name' => 'logo',  // Referenced as cid:logo
        'content' => base64_encode($imageContent)
    ]
]
```

## Related Documentation

- [Mandrill API - Messages](https://mailchimp.com/developer/transactional/api/messages/)
- [Message Options Reference](https://mailchimp.com/developer/transactional/api/messages/send-new-message/)

