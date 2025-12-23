# Mandrill Email Demo - PHP Implementation

A comprehensive PHP implementation for testing Mailchimp's Mandrill Transactional API with both command-line scripts and a web-based UI.

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+ (8.0+ recommended)
- Composer
- A Mandrill API key from [Mailchimp](https://mandrillapp.com/)

### Installation

```bash
# Clone or navigate to the project
cd Transactional-PHP-Samples

# Install dependencies
cd scripts
composer install

# Configure environment variables
cp env.example .env
# Edit .env with your Mandrill API key and email settings
```

## 💻 Two Ways to Use This Project

### Option 1: Web UI (Recommended for Testing)

Start the web server:

```bash
cd scripts
php -S localhost:8000
```

Then open your browser to:
```
http://localhost:8000
```

The web UI provides:
- ✨ Beautiful, modern interface
- 📋 Dropdown menu to select email operations
- 🎨 Dynamic forms and previews
- ✅ Instant feedback on email sending

**[Read the Web UI Guide →](WEB_UI_GUIDE.md)**

### Option 2: Command Line Scripts

Run individual PHP scripts directly:

```bash
cd scripts

# Send a basic email
php email_with_single_recipient.php

# Send with merge tags
php email_with_merge_tags.php

# Send with attachments
php email_with_attachments.php

# Send using a template
php email_with_template.php

# Kitchen sink - all features
php kitchen_sink_email.php
```

**[Read the Setup Guide →](scripts/README_USECASES_PHP.md)**

## 📋 Available Features

### 1. **Single Email to Single Recipient**
Send a basic email with subject, body, and recipient.

### 2. **Email with Merge Tags**
Personalize emails with dynamic content using merge variables.

### 3. **Email with Attachments**
Attach files (PDF, CSV, images, etc.) to your emails.

### 4. **Email Using Templates**
Use pre-created Mandrill templates for consistent branding.

### 5. **Kitchen Sink**
Comprehensive example using all features combined.

## 📁 Project Structure

```
Transactional-PHP-Samples/
├── public/
│   ├── index.php                   # Web UI entry point
│   └── styles.css                  # Web UI styles
├── scripts/
│   ├── app.php                     # Web UI application router
│   ├── composer.json               # PHP dependencies
│   ├── config.php                  # Configuration loader
│   ├── .env                        # Your configuration (create from env.example)
│   ├── email_with_single_recipient.php
│   ├── email_with_merge_tags.php
│   ├── email_with_attachments.php
│   ├── email_with_template.php
│   ├── kitchen_sink_email.php
│   └── create_template.php
├── views/
│   └── index.php                   # Web UI template
├── use-cases/                      # Detailed documentation for each use case
├── WEB_UI_GUIDE.md                 # Web UI documentation
└── README.md                       # This file
```

## 🔧 Configuration

Create a `.env` file in the `scripts` directory:

```env
MANDRILL_API_KEY=your_mandrill_api_key_here
DEFAULT_FROM_EMAIL=sender@yourdomain.com
DEFAULT_FROM_NAME="Your Name"
DEFAULT_TO_EMAIL=recipient@example.com
DEFAULT_TO_NAME="Recipient Name"
```

> **Important:** Always quote values that contain spaces or special characters. The PHP dotenv library strictly enforces this requirement.

## 🎯 Use Cases

Each script demonstrates a specific use case:

| Script | Description | Documentation |
|--------|-------------|---------------|
| `email_with_single_recipient.php` | Basic email sending | [Docs](use-cases/send-single-email-single-recipient-php.md) |
| `email_with_merge_tags.php` | Personalized emails | [Docs](use-cases/send-email-with-merge-tags-php.md) |
| `email_with_attachments.php` | Emails with files | [Docs](use-cases/send-email-with-attachments-php.md) |
| `email_with_template.php` | Template-based emails | [Docs](use-cases/send-email-using-template-php.md) |
| `kitchen_sink_email.php` | All features combined | [Docs](use-cases/kitchen-sink-php.md) |

## 🐛 Troubleshooting

### Composer Installation Issues

```bash
# If you see: "Could not find package mailchimp/transactional"
composer install

# If composer version mismatch:
rm composer.lock
composer install
```

### PHP Version Issues

The SDK requires PHP 7.2+. Check your version:

```bash
php -v
```

### Web Server Issues

```bash
# Port already in use:
lsof -ti:8000 | xargs kill -9

# Then restart:
php -S localhost:8000
```

## 🔐 Security Notes

⚠️ **Never commit your `.env` file or API keys to version control!**

The `.env` file is in `.gitignore` by default. Keep your API keys secure.

## 🎓 Learning Resources

- [Mandrill API Documentation](https://mailchimp.com/developer/transactional/api/)
- [PHP SDK on GitHub](https://github.com/mailchimp/mailchimp-transactional-php)
- [PHP Documentation](https://www.php.net/docs.php)
- [Composer Documentation](https://getcomposer.org/doc/)

## 📄 License

See the LICENSE file in the root directory.

## 🚦 Getting Started Now

### For Quick Testing (Web UI):

```bash
cd scripts
composer install
php -S localhost:8000
# Open http://localhost:8000
```

### For Development (Scripts):

```bash
cd scripts
composer install
php email_with_single_recipient.php
```

---

**Ready to send your first email?** 🚀

Choose your preferred method above and get started!

Happy emailing! 📧✨

