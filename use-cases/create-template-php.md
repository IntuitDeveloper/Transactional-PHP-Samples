# Create Template - PHP

## Overview

This use case demonstrates how to create and manage reusable email templates using the Mailchimp Transactional (Mandrill) API.

## Prerequisites

- PHP 7.4+
- Composer
- Mandrill API key

## Creating a Template

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$mailchimp = new \MailchimpTransactional\ApiClient();
$mailchimp->setApiKey($_ENV['MANDRILL_API_KEY']);

$templateData = [
    'name' => 'welcome-email',
    'from_email' => 'sender@example.com',
    'from_name' => 'Company Name',
    'subject' => 'Welcome to {{company_name}}, {{fname}}!',
    'code' => '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background: #007bff; color: white; padding: 20px; }
                .content { padding: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Welcome {{fname}}!</h1>
            </div>
            <div class="content">
                <div mc:edit="main_content">
                    <p>Thanks for joining {{company_name}}.</p>
                </div>
            </div>
        </body>
        </html>
    ',
    'text' => 'Welcome {{fname}}! Thanks for joining {{company_name}}.',
    'publish' => true,
    'labels' => ['welcome', 'onboarding']
];

try {
    $result = $mailchimp->templates->add($templateData);
    echo "Template created: " . $result['name'];
} catch (\MailchimpTransactional\ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Template Structure

| Field | Required | Description |
|-------|----------|-------------|
| `name` | Yes | Unique template identifier |
| `from_email` | No | Default sender email |
| `from_name` | No | Default sender name |
| `subject` | No | Default subject line |
| `code` | Yes | HTML content |
| `text` | No | Plain text version |
| `publish` | No | Publish immediately (default: false) |
| `labels` | No | Array of labels for organization |

## mc:edit Regions

Define editable regions that can be overridden when sending:

```html
<div mc:edit="header">
    <h1>Default Header</h1>
</div>

<div mc:edit="body">
    <p>Default body content that can be replaced.</p>
</div>

<div mc:edit="footer">
    <p>Default footer</p>
</div>
```

## Managing Templates

### List All Templates

```php
$templates = $mailchimp->templates->list();

foreach ($templates as $template) {
    echo $template['name'] . " - " . $template['slug'] . "\n";
}
```

### Get Template Info

```php
$info = $mailchimp->templates->info([
    'name' => 'welcome-email'
]);

print_r($info);
```

### Update Template

```php
$result = $mailchimp->templates->update([
    'name' => 'welcome-email',
    'subject' => 'New Subject Line',
    'code' => '<h1>Updated HTML</h1>'
]);
```

### Publish Template

```php
$result = $mailchimp->templates->publish([
    'name' => 'welcome-email'
]);
```

### Delete Template

```php
$result = $mailchimp->templates->delete([
    'name' => 'old-template'
]);
```

## Render Template (Preview)

Preview a template with merge tags filled in:

```php
$rendered = $mailchimp->templates->render([
    'template_name' => 'welcome-email',
    'template_content' => [
        ['name' => 'main_content', 'content' => '<p>Custom content</p>']
    ],
    'merge_vars' => [
        ['name' => 'fname', 'content' => 'John'],
        ['name' => 'company_name', 'content' => 'Acme Corp']
    ]
]);

echo $rendered['html'];
```

## Best Practices

1. **Use meaningful names**: Template names should be descriptive
2. **Define mc:edit regions**: Allow content customization
3. **Include both HTML and text**: For better deliverability
4. **Use labels**: Organize templates by category
5. **Test before publishing**: Use the render endpoint to preview

## Related Documentation

- [Mandrill - Templates](https://mailchimp.com/developer/transactional/docs/templates-dynamic-content/)
- [Template API Reference](https://mailchimp.com/developer/transactional/api/templates/)

