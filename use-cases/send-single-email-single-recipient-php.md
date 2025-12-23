# Send Single Email to Single Recipient - PHP

## Overview

This use case demonstrates how to send a basic transactional email to a single recipient using the Mailchimp Transactional (Mandrill) API with PHP.

## Prerequisites

- PHP 7.4+
- Composer
- Mandrill API key
- Configured `.env` file

## Code Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

$message = [
    'html' => '<p>Hello! This is a test email.</p>',
    'text' => 'Hello! This is a test email.',
    'subject' => 'Hello World',
    'from_email' => 'sender@example.com',
    'from_name' => 'Sender Name',
    'to' => [
        [
            'email' => 'recipient@example.com',
            'name' => 'Recipient Name',
            'type' => 'to'
        ]
    ]
];

try {
    $result = $mailchimp->messages->send(['message' => $message]);
    print_r($result);
} catch (\MailchimpTransactional\ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Key Points

1. **API Client Initialization**: Create a new `ApiClient` and set your API key
2. **Message Structure**: The message array contains all email details
3. **Recipient Type**: Use `'type' => 'to'` for primary recipients (also supports `cc` and `bcc`)
4. **Error Handling**: Catch `ApiException` for API-related errors

## Response

A successful response returns an array:

```php
[
    [
        'email' => 'recipient@example.com',
        'status' => 'sent',
        '_id' => 'abc123...',
        'reject_reason' => null
    ]
]
```

## Related Documentation

- [Mandrill API - Send Message](https://mailchimp.com/developer/transactional/api/messages/send-new-message/)
- [PHP SDK GitHub](https://github.com/mailchimp/mailchimp-transactional-php)

