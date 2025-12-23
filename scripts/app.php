<?php
/**
 * Mandrill Email Demo Web Application
 *
 * This is a simple PHP web application that provides a UI for testing
 * different Mandrill email sending features.
 *
 * Usage:
 *     cd scripts
 *     php -S localhost:8000
 *
 * Then open your browser to: http://localhost:8000
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_with_single_recipient.php';
require_once __DIR__ . '/email_with_merge_tags.php';
require_once __DIR__ . '/email_with_attachments.php';
require_once __DIR__ . '/email_with_template.php';
require_once __DIR__ . '/kitchen_sink_email.php';
require_once __DIR__ . '/create_template.php';

// Start session for flash messages
session_start();

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Simple router
if ($requestUri === '/' || $requestUri === '/index.php') {
    // Home page
    $scriptRunStatus = $_SESSION['script_run_status'] ?? null;
    unset($_SESSION['script_run_status']);
    include __DIR__ . '/../views/index.php';
} elseif ($requestUri === '/testEmailbasedOnScriptID' && $requestMethod === 'POST') {
    // Handle form submission
    $scriptName = $_POST['Script_name'] ?? '';
    $result = null;
    $statusMessage = '';

    switch ($scriptName) {
        case 'single':
            $result = sendSingleEmail();
            break;
        case 'mergeTags':
            $firstName = $_POST['firstName'] ?? 'John';
            $lastName = $_POST['lastName'] ?? 'Smith';
            $companyName = $_POST['companyName'] ?? 'Intuit Developer Program';
            $membershipLevel = $_POST['membershipLevel'] ?? 'Premium';
            $result = sendEmailWithMergeTags($firstName, $lastName, $companyName, $membershipLevel);
            break;
        case 'attachments':
            $result = sendWithAttachments();
            break;
        case 'templates':
            $templateName = $_POST['template_name'] ?? 'template1';
            $result = sendEmailWithTemplate($templateName);
            break;
        case 'allInOne':
            $result = sendKitchenSinkEmail();
            break;
        default:
            $result = ['success' => false, 'error' => 'Unknown script type'];
    }

    if ($result['success']) {
        $recipients = $result['result'] ?? [];
        $statusLines = [];
        foreach ($recipients as $r) {
            $statusLines[] = "{$r['email']}: {$r['status']}";
        }
        $statusMessage = "<div class='status-success'>✅ Email sent successfully!<br>" . implode('<br>', $statusLines) . "</div>";
    } else {
        $error = $result['error'] ?? 'Unknown error';
        $statusMessage = "<div class='status-error'>❌ Error: {$error}</div>";
    }

    $_SESSION['script_run_status'] = $statusMessage;
    header('Location: /');
    exit;
} elseif (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico)$/', $requestUri)) {
    // Serve static files
    $filePath = __DIR__ . '/../public' . $requestUri;
    if (file_exists($filePath)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon'
        ];
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        readfile($filePath);
        exit;
    }
    http_response_code(404);
    echo "File not found";
    exit;
} else {
    http_response_code(404);
    echo "Page not found";
    exit;
}

/**
 * Get description for each script type.
 *
 * @param string $scriptType The script type identifier
 * @return string Description text
 */
function getDescription(string $scriptType): string
{
    $descriptions = [
        'single' => 'Send a single email to a single recipient. This script uses the Mailchimp Transactional API to send a simple email with a subject, from name, from email, to name, to email, and content. For this demo all values will be used from config files.',
        'mergeTags' => 'Send an email with merge tags. Merge tags are placeholders in your email content that are replaced with dynamic data when the email is sent. You can use this script to send personalized emails to your recipients.',
        'attachments' => 'Send an email with attachments. Attachments can be added to your email by providing a URL to the file or by providing the file as a base64 encoded string. For simplicity, this demo uses a dynamic text file and sample attachments.',
        'templates' => 'Send an email with a template. Templates allow you to create reusable email layouts that can be populated with dynamic content. For this demo pre-defined email templates will be created and used.',
        'allInOne' => 'Send an email with all the supported features. This comprehensive example demonstrates merge tags, attachments, tracking, metadata, and custom headers all in one email.'
    ];

    return $descriptions[$scriptType] ?? '';
}

// Print startup message if running from CLI
if (php_sapi_name() === 'cli-server') {
    error_log("\n" . str_repeat('=', 60));
    error_log("🚀 Mandrill Email Demo Web Application");
    error_log(str_repeat('=', 60));
    error_log("\n📧 Web server running...");
    error_log("🌐 Open your browser to: http://localhost:8000");
    error_log("\n💡 Press Ctrl+C to stop the server\n");
}

