<?php
/**
 * Entry point for the Mandrill Email Demo Web Application
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_with_single_recipient.php';
require_once __DIR__ . '/email_with_merge_tags.php';
require_once __DIR__ . '/email_with_attachments.php';
require_once __DIR__ . '/email_with_template.php';
require_once __DIR__ . '/kitchen_sink_email.php';
require_once __DIR__ . '/create_template.php';
require_once __DIR__ . '/sms_single_recipient.php';

// Start session for flash messages
session_start();

// Get the request URI and parse it
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Handle static files
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico)$/', $requestUri)) {
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
    echo "File not found: $requestUri";
    exit;
}

// Route: Home page
if ($requestUri === '/' || $requestUri === '/index.php' || $requestUri === '') {
    $scriptRunStatus = $_SESSION['script_run_status'] ?? null;
    unset($_SESSION['script_run_status']);
    include __DIR__ . '/../views/index.php';
    exit;
}

// Route: Handle form submission
if ($requestUri === '/testEmailbasedOnScriptID' && $requestMethod === 'POST') {
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
        case 'sms':
            $toPhone = $_POST['smsToPhone'] ?? null;
            $messageText = $_POST['smsMessage'] ?? null;
            $result = sendSmsFromWeb($toPhone, $messageText);
            break;
        default:
            $result = ['success' => false, 'error' => 'Unknown script type'];
    }

    if ($result['success']) {
        if ($scriptName === 'sms') {
            // Handle SMS result
            $smsResult = $result['result'] ?? [];
            if (is_array($smsResult) && isset($smsResult[0])) {
                $firstResult = $smsResult[0];
                $status = $firstResult['status'] ?? 'unknown';
                $to = $firstResult['to'] ?? 'N/A';
                $msgId = $firstResult['_id'] ?? 'N/A';
                $statusMessage = "<div class='status-success'>✅ SMS sent successfully!<br>📱 Status: {$status}<br>To: {$to}<br>Message ID: {$msgId}</div>";
            } else {
                $statusMessage = "<div class='status-success'>✅ SMS sent successfully!</div>";
            }
        } else {
            // Handle email result
            $recipients = $result['result'] ?? [];
            $statusLines = [];
            foreach ($recipients as $r) {
                // PHP SDK returns objects, not arrays
                $email = is_object($r) ? $r->email : $r['email'];
                $status = is_object($r) ? $r->status : $r['status'];
                $statusLines[] = "{$email}: {$status}";
            }
            $statusMessage = "<div class='status-success'>✅ Email sent successfully!<br>" . implode('<br>', $statusLines) . "</div>";
        }
    } else {
        $error = $result['error'] ?? 'Unknown error';
        $statusMessage = "<div class='status-error'>❌ Error: {$error}</div>";
    }

    $_SESSION['script_run_status'] = $statusMessage;
    header('Location: /');
    exit;
}

// 404 for everything else
http_response_code(404);
echo "Page not found: $requestUri";

