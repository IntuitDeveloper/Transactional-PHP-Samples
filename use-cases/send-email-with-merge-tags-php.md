# Send Email with Merge Tags - PHP

## Overview

This use case demonstrates how to personalize emails using merge tags (also known as merge variables) with the Mailchimp Transactional (Mandrill) API.

## What are Merge Tags?

Merge tags are placeholders in your email content that get replaced with actual data when the email is sent. They enable personalization at scale.

## Prerequisites

- PHP 7.4+
- Composer
- Mandrill API key
- Configured `.env` file

## Code Example

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

$message = [
    'html' => '
        <h1>Welcome {{fname}}!</h1>
        <p>Thanks for joining {{company_name}}.</p>
        <p>Your membership level: {{membership_level}}</p>
    ',
    'subject' => 'Welcome to {{company_name}}, {{fname}}!',
    'from_email' => 'sender@example.com',
    'from_name' => 'Company Name',
    'to' => [
        [
            'email' => 'recipient@example.com',
            'name' => 'John Smith',
            'type' => 'to'
        ]
    ],
    // Global merge vars apply to all recipients
    'global_merge_vars' => [
        ['name' => 'company_name', 'content' => 'Acme Corp'],
        ['name' => 'membership_level', 'content' => 'Premium']
    ],
    // Per-recipient merge vars
    'merge_vars' => [
        [
            'rcpt' => 'recipient@example.com',
            'vars' => [
                ['name' => 'fname', 'content' => 'John']
            ]
        ]
    ],
    'merge_language' => 'handlebars'
];

$result = $mailchimp->messages->send(['message' => $message]);
```

## Merge Tag Types

### Global Merge Variables
Apply to all recipients in the email:

```php
'global_merge_vars' => [
    ['name' => 'company_name', 'content' => 'Acme Corp']
]
```

### Recipient-Specific Variables
Different values per recipient:

```php
'merge_vars' => [
    [
        'rcpt' => 'john@example.com',
        'vars' => [
            ['name' => 'fname', 'content' => 'John']
        ]
    ],
    [
        'rcpt' => 'jane@example.com',
        'vars' => [
            ['name' => 'fname', 'content' => 'Jane']
        ]
    ]
]
```

## Merge Languages

- `handlebars` - Use `{{variable_name}}` syntax (recommended)
- `mailchimp` - Use `*|VARIABLE_NAME|*` syntax

## Key Points

1. Set `merge_language` to specify syntax
2. Use `global_merge_vars` for values shared across all recipients
3. Use `merge_vars` for per-recipient personalization
4. Merge tags work in subject, HTML body, and text body

## Related Documentation

- [Mandrill - Merge Tags](https://mailchimp.com/developer/transactional/docs/templates-dynamic-content/)

