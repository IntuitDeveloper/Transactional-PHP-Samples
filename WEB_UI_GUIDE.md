# Web UI Guide - Mandrill Email Demo (PHP)

This guide explains how to use the web-based user interface for testing Mandrill email sending features.

## 🌐 Overview

The web UI provides an easy-to-use interface for testing all Mandrill email features without running PHP scripts directly from the command line. Built with PHP's built-in server, it offers a modern, responsive design.

## 🚀 Quick Start

### 1. Install Dependencies

```bash
cd scripts
composer install
```

### 2. Configure Environment

Make sure your `.env` file is configured in the `scripts` directory:

```bash
cp env.example .env
```

Edit the `.env` file with your credentials:

```env
MANDRILL_API_KEY=your_mandrill_api_key_here
DEFAULT_FROM_EMAIL=sender@yourdomain.com
DEFAULT_FROM_NAME="Your Name"
DEFAULT_TO_EMAIL=recipient@example.com
DEFAULT_TO_NAME="Recipient Name"
```

> **Important:** Always quote values that contain spaces or special characters. The PHP dotenv library strictly enforces this.

### 3. Start the Web Server

```bash
cd scripts
php -S localhost:8000
```

You should see:
```
🚀 Mandrill Email Demo Web Application
============================================================

📧 Web server running...
🌐 Open your browser to: http://localhost:8000

💡 Press Ctrl+C to stop the server
```

### 4. Open Your Browser

Navigate to:
```
http://localhost:8000
```

## 📋 Features

### Available Email Operations

The web UI provides access to all five email sending operations:

#### 1. **Send a Single Email to a Single Recipient**
- Basic email sending demonstration
- Uses values from your `.env` configuration
- Perfect for testing basic setup

#### 2. **Send Email with Merge Tags**
- Dynamic form fields for personalization
- Enter: First Name, Last Name, Company Name, Membership Level
- Demonstrates dynamic content replacement

#### 3. **Send Email with Attachments**
- Sends emails with file attachments
- Demo includes PDF and text files
- Shows base64 encoding in action

#### 4. **Send Email Using Template**
- Choose between two pre-defined templates
- Visual template preview
- Demonstrates template-based email sending

#### 5. **Kitchen Sink - All Features**
- Comprehensive demo of all features combined
- Attachments, merge tags, metadata, tracking, etc.
- Shows the full power of the Mandrill API

## 🎨 User Interface

### Main Screen

The home page displays:
- **Dropdown Menu**: Select which email operation to test
- **Description Box**: Dynamic description of the selected operation
- **Conditional Inputs**: Form fields appear based on your selection
- **Generate Button**: Triggers the email sending process

### Result Display

After sending an email:
- **Success Message**: Green banner with success status
- **Error Message**: Red banner with error details

## 🔧 Technical Details

### Technology Stack

- **Language**: PHP 7.4+
- **Web Server**: PHP Built-in Server
- **SDK**: mailchimp/transactional
- **Environment**: vlucas/phpdotenv
- **CSS**: Custom responsive design

### How It Works

1. **User Selection**: User selects an operation from the dropdown
2. **Dynamic UI**: JavaScript shows/hides relevant form fields
3. **Form Submission**: Form data is POST-ed to `/testEmailbasedOnScriptID`
4. **Script Execution**: PHP backend calls the appropriate function
5. **Result Display**: Success/error message is shown to the user

## 🐛 Troubleshooting

### Server Won't Start

**Error: Port already in use**
```bash
lsof -ti:8000 | xargs kill -9
php -S localhost:8000
```

### Composer Issues

```bash
# If dependencies missing:
composer install

# If autoload issues:
composer dump-autoload
```

### Email Not Sending

**Check your `.env` file:**
```bash
cat .env
```

**Common Issues:**
- Invalid API key
- Missing environment variables
- Invalid email addresses in `.env`

## 🔐 Security Notes

⚠️ **This demo application is for development/testing only!**

For production use, consider:
1. **Authentication**: Add user authentication
2. **Rate Limiting**: Prevent abuse
3. **Input Validation**: Sanitize all user inputs
4. **HTTPS**: Use SSL/TLS in production

## 📱 Responsive Design

The UI is fully responsive and works on:
- 🖥️ Desktop computers
- 💻 Laptops
- 📱 Tablets
- 📱 Mobile phones

## 🎨 Customization

### Changing the Port

```bash
php -S localhost:9000
```

### Custom Styling

Edit `public/styles.css` to customize colors, fonts, and layout.

## 📚 Additional Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [Mandrill API Documentation](https://mailchimp.com/developer/transactional/api/)
- [PHP SDK on GitHub](https://github.com/mailchimp/mailchimp-transactional-php)

---

**Ready to test your emails?**

```bash
cd scripts
php -S localhost:8000
# Open http://localhost:8000 in your browser
```

Happy emailing! 📧✨

