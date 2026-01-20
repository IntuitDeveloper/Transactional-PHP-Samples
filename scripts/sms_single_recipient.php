<?php
/**
 * Send an SMS to a Single Recipient using Mandrill API
 *
 * This script demonstrates how to send an SMS message to a single recipient
 * using the Mailchimp Transactional (Mandrill) API.
 *
 * Usage:
 *     php sms_single_recipient.php
 *
 * Requirements:
 *     - PHP 7.4+
 *     - vlucas/phpdotenv
 *
 * Install with:
 *     composer require vlucas/phpdotenv
 *
 * Note: SMS functionality uses the Mandrill REST API v1.1 directly since the
 * PHP SDK doesn't include the send_sms method yet.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// SMS API endpoint (note: uses API version 1.1, not 1.0)
define('SMS_API_ENDPOINT', 'https://mandrillapp.com/api/1.1/messages/send-sms');

// SSL verification mode - set SSL_VERIFY=false if behind corporate proxy
define('SSL_VERIFY', ($_ENV['SSL_VERIFY'] ?? 'true') !== 'false');

/**
 * Send an SMS message using the Mandrill API.
 *
 * @param array $options Optional parameters:
 *   - to: Recipient phone number in E.164 format (e.g., +1234567890)
 *   - from: Sender phone number (must be verified in Mandrill)
 *   - text: SMS message content (max 1600 characters)
 *   - consent: Consent type ('onetime', 'recurring', 'recurring-no-confirm')
 *   - track_clicks: Whether to track link clicks in the message
 *
 * @return array|null API response on success, null on failure
 */
function sendSms(array $options = []): ?array
{
    $apiKey = $_ENV['MANDRILL_API_KEY'] ?? null;

    if (!$apiKey) {
        echo "Error: MANDRILL_API_KEY not found in environment variables!\n";
        echo "Please create a .env file with your Mandrill API key.\n";
        return null;
    }

    // Build the SMS message payload with defaults from environment
    $toPhone = $options['to'] ?? $_ENV['SMS_TO_PHONE'] ?? '+1234567890';
    $fromPhone = $options['from'] ?? $_ENV['SMS_FROM_PHONE'] ?? '+0987654321';
    $messageText = $options['text'] ?? $_ENV['SMS_MESSAGE'] ?? 'Hello from Mandrill SMS! This is a test message.';
    $consentType = $options['consent'] ?? $_ENV['SMS_CONSENT_TYPE'] ?? 'onetime';
    $trackClicks = $options['track_clicks'] ?? (($_ENV['SMS_TRACK_CLICKS'] ?? 'false') === 'true');

    $payload = [
        'key' => $apiKey,
        'message' => [
            'sms' => [
                'text' => $messageText,
                'to' => $toPhone,
                'from' => $fromPhone,
                'consent' => $consentType,
                'track_clicks' => $trackClicks
            ]
        ]
    ];

    try {
        // Initialize cURL
        $ch = curl_init(SMS_API_ENDPOINT);

        // Set cURL options
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => SSL_VERIFY,
            CURLOPT_SSL_VERIFYHOST => SSL_VERIFY ? 2 : 0
        ]);

        if (!SSL_VERIFY) {
            echo "⚠️  SSL verification disabled (SSL_VERIFY=false)\n";
        }

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        echo "SMS Request sent!\n";
        echo str_repeat('=', 50) . "\n";

        if ($curlError) {
            echo "cURL Error: $curlError\n";
            echo str_repeat('=', 50) . "\n";
            return null;
        }

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            echo "SMS sent successfully!\n";
            echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";

            // Display key details from the response
            if (is_array($result) && isset($result[0])) {
                $firstResult = $result[0];
                echo "\nDetails:\n";
                if (isset($firstResult['status'])) {
                    echo "  Status: {$firstResult['status']}\n";
                }
                if (isset($firstResult['to'])) {
                    echo "  To: {$firstResult['to']}\n";
                }
                if (isset($firstResult['_id'])) {
                    echo "  Message ID: {$firstResult['_id']}\n";
                }
                if (isset($firstResult['reject_reason'])) {
                    echo "  Reject Reason: {$firstResult['reject_reason']}\n";
                }
            } elseif (is_array($result)) {
                if (isset($result['status'])) {
                    echo "  Status: {$result['status']}\n";
                }
                if (isset($result['_id'])) {
                    echo "  Message ID: {$result['_id']}\n";
                }
            }

            echo str_repeat('=', 50) . "\n";
            return $result;
        } else {
            $errorBody = json_decode($response, true) ?? $response;

            echo "SMS sending failed!\n";
            echo "HTTP Status: $httpCode\n";
            if (is_array($errorBody)) {
                echo "Error: " . json_encode($errorBody, JSON_PRETTY_PRINT) . "\n";
            } else {
                echo "Error: $errorBody\n";
            }
            echo str_repeat('=', 50) . "\n";
            return null;
        }
    } catch (Exception $e) {
        echo "Error sending SMS!\n";
        echo str_repeat('=', 50) . "\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo str_repeat('=', 50) . "\n";
        return null;
    }
}

/**
 * Send an SMS with comprehensive error handling.
 *
 * @param string $toPhone Recipient phone number in E.164 format
 * @param string $fromPhone Sender phone number
 * @param string $messageText SMS message content
 * @param string $consentType Type of consent (default: 'onetime')
 *
 * @return array Result with 'success' boolean and 'data' or 'error' key
 */
function sendSmsWithErrorHandling(string $toPhone, string $fromPhone, string $messageText, string $consentType = 'onetime'): array
{
    $result = sendSms([
        'to' => $toPhone,
        'from' => $fromPhone,
        'text' => $messageText,
        'consent' => $consentType
    ]);

    if ($result) {
        if (is_array($result) && isset($result[0]['status']) && $result[0]['status'] === 'rejected') {
            return [
                'success' => false,
                'error' => "Rejected: " . ($result[0]['reject_reason'] ?? 'Unknown reason')
            ];
        }
        return ['success' => true, 'data' => $result];
    }

    return ['success' => false, 'error' => 'Failed to send SMS'];
}

/**
 * Send SMS from web UI with custom parameters.
 * This function is called by the web application.
 *
 * @param string|null $toPhone Custom recipient phone number
 * @param string|null $messageText Custom message text
 *
 * @return array Result with 'success' boolean and 'result' or 'error' key
 */
function sendSmsFromWeb(?string $toPhone = null, ?string $messageText = null): array
{
    $options = [];
    
    if ($toPhone && !empty($toPhone)) {
        $options['to'] = $toPhone;
    }
    
    if ($messageText && !empty($messageText)) {
        $options['text'] = $messageText;
    }
    
    $result = sendSms($options);
    
    if ($result) {
        return ['success' => true, 'result' => $result];
    }
    
    return ['success' => false, 'error' => 'Failed to send SMS'];
}

// Main execution
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    // Check if API key is configured
    if (!($_ENV['MANDRILL_API_KEY'] ?? null)) {
        echo "Error: MANDRILL_API_KEY not found in environment variables!\n";
        echo "Please create a .env file with your Mandrill API key.\n";
        exit(1);
    }

    echo "📱 Sending SMS...\n\n";

    // Check for custom message from environment (set by web UI)
    $customMessage = $_ENV['SMS_CUSTOM_MESSAGE'] ?? null;
    $customTo = $_ENV['SMS_CUSTOM_TO'] ?? null;

    if ($customMessage || $customTo) {
        $result = sendSms([
            'to' => $customTo ?: ($_ENV['SMS_TO_PHONE'] ?? null),
            'text' => $customMessage ?: ($_ENV['SMS_MESSAGE'] ?? null)
        ]);
    } else {
        $result = sendSms();
    }

    if ($result) {
        echo "\n✅ SMS operation completed!\n";
    } else {
        echo "\n❌ SMS operation failed!\n";
        exit(1);
    }
}

