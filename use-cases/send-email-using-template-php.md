# Send Email Using Template - PHP

## Overview

This use case demonstrates how to send emails using pre-created Mandrill templates with the Mailchimp Transactional API.

## What are Templates?

Templates are reusable email layouts stored in your Mandrill account. They allow you to:
- Maintain consistent branding
- Separate design from code
- Use dynamic content regions

## Prerequisites

- PHP 7.4+
- Composer
- Mandrill API key
- A template created in Mandrill

## Creating a Template

```php
<?php
$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

$templateData = [
    'name' => 'welcome-template',
    'from_email' => 'sender@example.com',
    'from_name' => 'Company Name',
    'subject' => 'Welcome {{fname}}!',
    'code' => '
        <h1>Hello {{fname}}!</h1>
        <div mc:edit="main_content">
            <p>Default content here.</p>
        </div>
    ',
    'text' => 'Hello {{fname}}!',
    'publish' => true
];

$result = $mailchimp->templates->add($templateData);
```

## Sending with a Template

```php
<?php
$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

$message = [
    'from_email' => 'sender@example.com',
    'from_name' => 'Company Name',
    'to' => [
        [
            'email' => 'recipient@example.com',
            'name' => 'John Smith',
            'type' => 'to'
        ]
    ],
    'global_merge_vars' => [
        ['name' => 'fname', 'content' => 'John']
    ],
    'merge_language' => 'handlebars'
];

// Content for mc:edit regions
$templateContent = [
    [
        'name' => 'main_content',
        'content' => '<p>Welcome to our platform!</p>'
    ]
];

$result = $mailchimp->messages->sendTemplate([
    'template_name' => 'welcome-template',
    'template_content' => $templateContent,
    'message' => $message
]);
```

## mc:edit Regions

Use `mc:edit` attributes to define editable regions:

```html
<div mc:edit="header">
    Default header content
</div>

<div mc:edit="body">
    Default body content
</div>

<div mc:edit="footer">
    Default footer content
</div>
```

Then override when sending:

```php
$templateContent = [
    ['name' => 'header', 'content' => '<h1>Custom Header</h1>'],
    ['name' => 'body', 'content' => '<p>Custom body content</p>'],
    ['name' => 'footer', 'content' => '<p>© 2024 Company</p>']
];
```

## Template Management

```php
// List all templates
$templates = $mailchimp->templates->list();

// Get template info
$info = $mailchimp->templates->info(['name' => 'welcome-template']);

// Delete template
$mailchimp->templates->delete(['name' => 'old-template']);
```

## Key Points

1. Create templates via API or Mandrill dashboard
2. Use `mc:edit` for dynamic content regions
3. Combine with merge tags for personalization
4. Templates can have default values that get overridden

## Related Documentation

- [Mandrill - Templates](https://mailchimp.com/developer/transactional/docs/templates-dynamic-content/)
- [Template API](https://mailchimp.com/developer/transactional/api/templates/)

