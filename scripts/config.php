<?php
/**
 * Configuration module for Mailchimp Transactional PHP App.
 *
 * Technical Documentation:
 * - Loads environment variables from a .env file using vlucas/phpdotenv.
 * - Exposes key configuration values (API key, sender/recipient info) as constants.
 * - Used throughout the app to securely access sensitive credentials and default email addresses.
 *
 * User Documentation:
 * - Edit the .env file in the scripts directory to set your Mailchimp API key and email addresses.
 * - These variables are automatically loaded and used by the application.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Configuration constants
define('MANDRILL_API_KEY', $_ENV['MANDRILL_API_KEY'] ?? '');
define('DEFAULT_FROM_EMAIL', $_ENV['DEFAULT_FROM_EMAIL'] ?? 'test@example.com');
define('DEFAULT_FROM_NAME', $_ENV['DEFAULT_FROM_NAME'] ?? 'Test Sender');
define('DEFAULT_TO_EMAIL', $_ENV['DEFAULT_TO_EMAIL'] ?? 'recipient@example.com');
define('DEFAULT_TO_NAME', $_ENV['DEFAULT_TO_NAME'] ?? 'Test Recipient');

/**
 * Get a configured Mailchimp Transactional API client.
 *
 * @return \MailchimpTransactional\ApiClient
 */
function getMailchimpClient(): \MailchimpTransactional\ApiClient
{
    $mailchimp = new \MailchimpTransactional\ApiClient();
    $mailchimp->setApiKey(MANDRILL_API_KEY);
    return $mailchimp;
}

