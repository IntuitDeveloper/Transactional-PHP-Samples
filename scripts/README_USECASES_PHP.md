# PHP Scripts for Mandrill API Use Cases

This directory contains PHP implementations of Mandrill API use cases using the `mailchimp/transactional` library.

## Prerequisites

- PHP 7.4 or higher (8.0+ recommended)
- Composer
- A Mandrill API key from [Mailchimp](https://mandrillapp.com/)

## Installation

### Option 1: Using Composer (Recommended)

1. Install Composer if you haven't already:
   - Visit [getcomposer.org](https://getcomposer.org/)

2. Install the required packages:

```bash
cd scripts
composer install
```

### Option 2: Manual Installation

Install the required packages individually:

```bash
composer require mailchimp/transactional vlucas/phpdotenv
```

## Configuration

1. Copy the `env.example` file to `.env`:

```bash
cp env.example .env
```

2. Edit the `.env` file and add your credentials:

```env
MANDRILL_API_KEY=your_mandrill_api_key_here
DEFAULT_FROM_EMAIL=sender@yourdomain.com
DEFAULT_FROM_NAME="Your Name"
DEFAULT_TO_EMAIL=recipient@example.com
DEFAULT_TO_NAME="Recipient Name"
```

> **Important:** Always quote values that contain spaces or special characters. The PHP dotenv library strictly enforces this.

## Available PHP Scripts

### 1. Send Single Email to Single Recipient

**File:** `email_with_single_recipient.php`

Demonstrates how to send a basic email to a single recipient.

```bash
php email_with_single_recipient.php
```

**Features:**
- Basic email sending
- HTML and plain text content
- Custom headers
- Advanced options with tracking and metadata

### 2. Send Email with Merge Tags

**File:** `email_with_merge_tags.php`

Personalize emails using merge tags for dynamic content.

```bash
php email_with_merge_tags.php
```

**Features:**
- Global merge variables (apply to all recipients)
- Recipient-specific merge variables
- Handlebars template syntax
- Multiple recipient personalization

### 3. Send Email Using Template

**File:** `email_with_template.php`

Send emails using pre-created Mandrill templates.

```bash
php email_with_template.php
```

**Features:**
- Use stored templates
- Override template defaults
- Dynamic content with merge tags
- mc:edit region replacement

**Note:** You must first create a template using `create_template.php` or via the Mandrill UI.

### 4. Send Email with Attachments

**File:** `email_with_attachments.php`

Attach files (PDF, CSV, JSON, etc.) to your emails.

```bash
php email_with_attachments.php
```

**Features:**
- Attach local files (PDF, images, etc.)
- Create dynamic attachments (CSV, JSON, text)
- Base64 encoding handling
- Multiple attachment types

### 5. Create Template

**File:** `create_template.php`

Create and manage reusable email templates.

```bash
php create_template.php
```

**Features:**
- Create new templates
- List all templates
- Get template information
- Delete templates
- mc:edit regions for dynamic content

### 6. Kitchen Sink - All Features

**File:** `kitchen_sink_email.php`

Comprehensive example demonstrating all Mandrill features.

```bash
php kitchen_sink_email.php
```

**Features:**
- All message options in one example
- Attachments, tracking, metadata
- Multiple recipient types (TO, CC, BCC)
- Advanced headers and custom fields

## Error Handling

All scripts include error handling for:
- Missing API credentials
- API client errors (`MailchimpTransactional\ApiException`)
- Network issues
- Invalid email addresses

Example error handling pattern:

```php
try {
    $result = $mailchimp->messages->send(['message' => $message]);
    // Handle success
} catch (\MailchimpTransactional\ApiException $e) {
    echo "Mandrill API Error: " . $e->getMessage();
}
```

## API Response

A successful API response is an array of results:

```php
[
    [
        "email" => "recipient@example.org",
        "status" => "sent",  // or "queued", "rejected", "invalid"
        "_id" => "abc123",
        "reject_reason" => null  // or reason if rejected
    ]
]
```

## Common Status Values

- `sent` - Message was successfully sent
- `queued` - Message is queued for sending
- `scheduled` - Message is scheduled for future sending
- `rejected` - Message was rejected
- `invalid` - Invalid recipient email address

## Resources

- [Mandrill API Documentation](https://mailchimp.com/developer/transactional/api/)
- [PHP SDK on GitHub](https://github.com/mailchimp/mailchimp-transactional-php)
- [PHP Documentation](https://www.php.net/docs.php)
- [Composer Documentation](https://getcomposer.org/doc/)

## Common Issues

### Issue 1: Class Not Found

```
Fatal error: Class 'MailchimpTransactional\ApiClient' not found
```

**Solution:**
```bash
composer install
# or
composer dump-autoload
```

### Issue 2: Permission Denied

```
Permission denied: .env
```

**Solution:**
Check file permissions:
```bash
chmod 644 .env
```

### Issue 3: PHP Version

```
Your PHP version does not satisfy requirements
```

**Solution:**
Check your PHP version and upgrade if needed:
```bash
php -v
```

## License

See the LICENSE file in the root directory.

