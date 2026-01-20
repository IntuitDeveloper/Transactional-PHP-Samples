# Send an SMS to a Single Recipient (PHP)

This use case demonstrates how to send an SMS message to a single recipient using the Mandrill API with PHP.

> **Note:** The PHP SDK doesn't include the `send_sms` method yet, so we use the REST API directly with the `/api/1.1/messages/send-sms` endpoint.

## Basic Example

Here's how to send an SMS using the Mandrill REST API:

```php
<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

define('SMS_API_ENDPOINT', 'https://mandrillapp.com/api/1.1/messages/send-sms');

function sendSms($toPhone, $fromPhone, $messageText, $consentType = 'onetime') {
    $payload = [
        'key' => $_ENV['MANDRILL_API_KEY'],
        'message' => [
            'sms' => [
                'text' => $messageText,
                'to' => $toPhone,           // E.164 format (e.g., +1234567890)
                'from' => $fromPhone,       // Must be verified in Mandrill
                'consent' => $consentType,
                'track_clicks' => true
            ]
        ]
    ];

    $ch = curl_init(SMS_API_ENDPOINT);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        echo "SMS sent! Status: {$result[0]['status']}\n";
        return $result;
    } else {
        echo "Failed: $response\n";
        return null;
    }
}

// Send an SMS
sendSms('+1234567890', '+0987654321', 'Hello from Mandrill SMS!');
```

## API Features

| Feature | Mandrill Implementation |
|---------|-------------------------|
| **Endpoint** | `POST https://mandrillapp.com/api/1.1/messages/send-sms` |
| **Recipient** | `message.sms.to`: Phone number in E.164 format (e.g., `+1234567890`) |
| **Sender** | `message.sms.from`: Verified sender ID (E.164 number, short code, or alphanumeric) |
| **Message** | `message.sms.text`: SMS content (max 1600 characters) |
| **Consent** | `message.sms.consent`: Type of consent (`onetime`, `recurring`, `recurring-no-confirm`) |

## Message Structure

The Mandrill SMS payload requires these key properties:

- **key**: Your Mandrill API key
- **message.sms.text**: Content of the SMS message
- **message.sms.to**: Recipient's phone number in E.164 format
- **message.sms.from**: Approved sender ID (must be verified)
- **message.sms.consent**: Consent type for the message
- **message.sms.track_clicks**: Boolean to enable URL click tracking (optional)

### Phone Number Format (E.164)

Phone numbers must be in E.164 format:
- Starts with `+` followed by the country code
- No spaces, dashes, or parentheses
- Examples:
  - US: `+14155551234`
  - UK: `+442071234567`
  - Australia: `+61412345678`

### Consent Types

| Type | Description |
|------|-------------|
| `onetime` | Single transactional message (default) |
| `recurring` | Ongoing messages with confirmation |
| `recurring-no-confirm` | Ongoing messages without confirmation |

## Advanced Options

You can enhance your SMS with additional options:

```php
$payload = [
    'key' => $apiKey,
    'message' => [
        'sms' => [
            'text' => 'Your order #12345 has shipped! Track it here: https://example.com/track/12345',
            'to' => '+1234567890',
            'from' => '+0987654321',
            'consent' => 'onetime',
            'track_clicks' => true  // Track link clicks in the message
        ]
    ]
];
```

## Environment Variables

Create a `.env` file in your project root with:

```
MANDRILL_API_KEY=your_api_key_here
SMS_TO_PHONE=+1234567890
SMS_FROM_PHONE=+0987654321
SMS_MESSAGE="Hello from Mandrill SMS!"
SMS_CONSENT_TYPE=onetime
SMS_TRACK_CLICKS=true
```

## Error Handling

```php
<?php
function sendSmsWithErrorHandling($toPhone, $fromPhone, $messageText) {
    $payload = [
        'key' => $_ENV['MANDRILL_API_KEY'],
        'message' => [
            'sms' => [
                'text' => $messageText,
                'to' => $toPhone,
                'from' => $fromPhone,
                'consent' => 'onetime'
            ]
        ]
    ];

    try {
        $ch = curl_init(SMS_API_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'error' => "cURL Error: $curlError"];
        }

        switch ($httpCode) {
            case 200:
                $result = json_decode($response, true);
                if ($result[0]['status'] === 'rejected') {
                    return ['success' => false, 'error' => "Rejected: {$result[0]['reject_reason']}"];
                }
                return ['success' => true, 'data' => $result];
            case 401:
                return ['success' => false, 'error' => 'Invalid API key'];
            case 400:
                return ['success' => false, 'error' => "Bad request: $response"];
            default:
                return ['success' => false, 'error' => "HTTP $httpCode: $response"];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
```

## Prerequisites

Before sending SMS messages, ensure you have:

1. **Verified Sender Phone Number**: Your sending phone number must be verified in your Mandrill account
2. **SMS Enabled**: SMS functionality must be enabled for your account
3. **Proper Consent**: You must have appropriate consent from recipients to send SMS messages
4. **Valid API Key**: Your Mandrill API key with SMS permissions

## Common Use Cases

### Order Confirmation

```php
sendSms(
    $customerPhone,
    $businessPhone,
    "Your order #{$orderId} has been confirmed! Expected delivery: {$deliveryDate}"
);
```

### Appointment Reminder

```php
sendSms(
    $patientPhone,
    $clinicPhone,
    "Reminder: Your appointment is scheduled for {$appointmentTime}. Reply CONFIRM to confirm."
);
```

### Verification Code

```php
$code = rand(100000, 999999);
sendSms(
    $userPhone,
    $servicePhone,
    "Your verification code is: {$code}. This code expires in 10 minutes."
);
```

### Shipping Notification

```php
sendSms(
    $customerPhone,
    $storePhone,
    "Great news! Your package has shipped. Track it here: https://example.com/track/{$trackingId}"
);
```

## Notes

- **Character Limit**: SMS messages can be up to 1600 characters
- **Link Tracking**: Enable `track_clicks: true` to track link clicks in your messages
- **Rate Limits**: Be aware of SMS rate limits for your account
- **Compliance**: Ensure compliance with SMS regulations (TCPA, GDPR, etc.)
- **Costs**: SMS messages may incur additional costs depending on your plan
- **International**: International SMS delivery may have different rates and regulations

## API Response

A successful response looks like:

```json
[
  {
    "status": "sent",
    "to": "+1234567890",
    "_id": "abc123def456"
  }
]
```

Possible status values:
- `sent`: Message was sent successfully
- `queued`: Message is queued for delivery
- `rejected`: Message was rejected (check `reject_reason`)
- `invalid`: Invalid phone number or parameters

