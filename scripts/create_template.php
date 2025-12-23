<?php
/**
 * Create Template using Mandrill API
 *
 * This script demonstrates how to create and manage email templates
 * with Mailchimp Transactional (Mandrill) API.
 *
 * Usage:
 *     php create_template.php
 *
 * Requirements:
 *     - mailchimp/transactional
 *     - vlucas/phpdotenv
 */

require_once __DIR__ . '/config.php';

/**
 * Check if a template exists.
 *
 * @param string $templateName Name of the template
 * @return bool True if template exists
 */
function templateExists(string $templateName): bool
{
    $mailchimp = getMailchimpClient();

    try {
        $templates = $mailchimp->templates->list();
        foreach ($templates as $template) {
            $name = is_object($template) ? $template->name : $template['name'];
            if ($name === $templateName) {
                return true;
            }
        }
        return false;
    } catch (\MailchimpTransactional\ApiException $e) {
        return false;
    }
}

/**
 * Create a new template if it doesn't exist.
 *
 * @param string $templateName Name of the template to create
 * @return array Result from the API
 */
function createTemplate(string $templateName): array
{
    $mailchimp = getMailchimpClient();

    // Check if template already exists
    if (templateExists($templateName)) {
        return ['success' => true, 'exists' => true, 'message' => "Template '{$templateName}' already exists."];
    }

    // Define template content based on name
    if ($templateName === 'template1') {
        $code = '
            <h1>Hello {{fname}}!</h1>
            <div mc:edit="welcome_message">
                <p>Welcome to {{company_name}}.</p>
            </div>
            <p>Your account: {{account_id}}</p>
        ';
        $subject = 'Hello {{fname}}!';
        $text = 'This is a simple greetings from template1.';
    } else {
        $code = '
            <h1>Greetings {{fname}}!</h1>
            <p>Hope your Account: {{account_id}} is all set in Company: {{company_name}}</p>
            <div mc:edit="goodbye_message">
                <p>We will see you soon {{company_name}}.</p>
            </div>
        ';
        $subject = 'Greetings {{fname}}!';
        $text = 'This is a simple greetings from template2.';
    }

    $templateData = [
        'name' => $templateName,
        'from_email' => DEFAULT_FROM_EMAIL,
        'from_name' => DEFAULT_FROM_NAME,
        'subject' => $subject,
        'code' => $code,
        'text' => $text,
        'publish' => false,
        'labels' => ['hello', 'demo']
    ];

    try {
        $result = $mailchimp->templates->add($templateData);
        return ['success' => true, 'exists' => false, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Ensure a template exists, creating it if necessary.
 *
 * @param string $templateName Name of the template
 * @return bool True if template exists or was created
 */
function ensureTemplateExists(string $templateName): bool
{
    $result = createTemplate($templateName);
    return $result['success'];
}

/**
 * List all templates.
 *
 * @return array Result from the API
 */
function listTemplates(): array
{
    $mailchimp = getMailchimpClient();

    try {
        $result = $mailchimp->templates->list();
        return ['success' => true, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Delete a template.
 *
 * @param string $templateName Name of the template to delete
 * @return array Result from the API
 */
function deleteTemplate(string $templateName): array
{
    $mailchimp = getMailchimpClient();

    try {
        $result = $mailchimp->templates->delete(['name' => $templateName]);
        return ['success' => true, 'result' => $result];
    } catch (\MailchimpTransactional\ApiException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    if (empty(MANDRILL_API_KEY)) {
        echo "Error: MANDRILL_API_KEY not found in environment variables!\n";
        echo "Please create a .env file with your Mandrill API key.\n";
        exit(1);
    }

    echo "Creating template...\n\n";
    
    // Create template1
    $result = createTemplate('template1');
    if ($result['success']) {
        if ($result['exists'] ?? false) {
            echo "Template 'template1' already exists.\n";
        } else {
            echo "Template 'template1' created successfully!\n";
        }
    } else {
        echo "Error creating template: {$result['error']}\n";
    }

    // Create template2
    $result = createTemplate('template2');
    if ($result['success']) {
        if ($result['exists'] ?? false) {
            echo "Template 'template2' already exists.\n";
        } else {
            echo "Template 'template2' created successfully!\n";
        }
    } else {
        echo "Error creating template: {$result['error']}\n";
    }

    echo "\nListing all templates...\n";
    echo str_repeat('=', 50) . "\n";
    
    $templates = listTemplates();
    if ($templates['success']) {
        foreach ($templates['result'] as $template) {
            $name = is_object($template) ? $template->name : $template['name'];
            $slug = is_object($template) ? $template->slug : $template['slug'];
            echo "- {$name} (slug: {$slug})\n";
        }
    } else {
        echo "Error listing templates: {$templates['error']}\n";
    }
    
    echo str_repeat('=', 50) . "\n";
}

