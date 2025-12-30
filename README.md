# EKDSend PHP SDK

The official PHP SDK for the EKDSend API. Send emails, SMS, and voice calls with ease.

[![Packagist Version](https://img.shields.io/packagist/v/ekddigital/ekdsend-php)](https://packagist.org/packages/ekddigital/ekdsend-php)
[![PHP Version](https://img.shields.io/packagist/php-v/ekddigital/ekdsend-php)](https://packagist.org/packages/ekddigital/ekdsend-php)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation

```bash
composer require ekddigital/ekdsend-php
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use EKDSend\EKDSend;

$client = new EKDSend('ek_live_xxxxxxxxxxxxx');

// Send an email
$email = $client->emails()->send([
    'from' => 'hello@yourdomain.com',
    'to' => 'user@example.com',
    'subject' => 'Hello from EKDSend!',
    'html' => '<h1>Welcome!</h1><p>Thanks for joining us.</p>'
]);

echo "Email sent: " . $email->id . "\n";
```

## Configuration

```php
use EKDSend\EKDSend;

$client = new EKDSend('ek_live_xxxxxxxxxxxxx', [
    'base_url' => 'https://es.ekddigital.com/v1',  // Optional
    'timeout' => 30,                              // Request timeout in seconds
    'max_retries' => 3,                           // Auto-retry on failures
    'debug' => true                               // Enable debug logging
]);
```

## Email API

### Send Email

```php
$email = $client->emails()->send([
    'from' => 'hello@yourdomain.com',
    'to' => ['user1@example.com', 'user2@example.com'],
    'subject' => 'Weekly Newsletter',
    'html' => '<h1>Newsletter</h1><p>Your weekly update.</p>',
    'text' => "Newsletter\n\nYour weekly update.",
    'cc' => 'cc@example.com',
    'bcc' => ['bcc1@example.com', 'bcc2@example.com'],
    'reply_to' => 'support@yourdomain.com',
    'tags' => ['newsletter', 'weekly'],
    'metadata' => ['campaign_id' => 'spring-2024']
]);
```

### With Attachments

```php
$pdfContent = base64_encode(file_get_contents('report.pdf'));

$email = $client->emails()->send([
    'from' => 'reports@yourdomain.com',
    'to' => 'manager@company.com',
    'subject' => 'Monthly Report',
    'html' => '<p>Please find the report attached.</p>',
    'attachments' => [
        [
            'filename' => 'report.pdf',
            'content' => $pdfContent,
            'content_type' => 'application/pdf'
        ]
    ]
]);
```

### Schedule Email

```php
$sendTime = (new DateTime('+24 hours'))->format(DateTime::ATOM);

$email = $client->emails()->send([
    'from' => 'hello@yourdomain.com',
    'to' => 'user@example.com',
    'subject' => 'Reminder',
    'html' => "<p>Don't forget your meeting tomorrow!</p>",
    'scheduled_at' => $sendTime
]);

// Cancel scheduled email
$cancelled = $client->emails()->cancel($email->id);
```

### Retrieve & List Emails

```php
// Get specific email
$email = $client->emails()->get('em_xxxxxxxxxxxxx');
echo "Status: " . $email->status . "\n";

// List emails with filters
$emails = $client->emails()->list([
    'limit' => 50,
    'status' => 'delivered',
    'from_date' => '2024-01-01T00:00:00Z',
    'tags' => ['transactional']
]);

foreach ($emails->data as $email) {
    echo "{$email->id}: {$email->subject} - {$email->status}\n";
}
```

## SMS API

### Send SMS

```php
$sms = $client->sms()->send([
    'to' => '+14155551234',
    'message' => 'Your verification code is: 123456',
    'from' => '+14155559999',
    'metadata' => ['type' => 'verification']
]);

echo "SMS sent: " . $sms->id . "\n";
```

### Schedule SMS

```php
$sendTime = (new DateTime('+2 hours'))->format(DateTime::ATOM);

$sms = $client->sms()->send([
    'to' => '+14155551234',
    'message' => 'Your appointment is in 1 hour!',
    'scheduled_at' => $sendTime
]);
```

### Retrieve & List SMS

```php
// Get specific SMS
$sms = $client->sms()->get('sms_xxxxxxxxxxxxx');

// List SMS messages
$messages = $client->sms()->list(['limit' => 25, 'status' => 'delivered']);

foreach ($messages->data as $msg) {
    echo "{$msg->id}: {$msg->to} - {$msg->status}\n";
}
```

## Voice API

### Make a Call with Text-to-Speech

```php
$call = $client->calls()->create([
    'to' => '+14155551234',
    'from' => '+14155559999',
    'tts_message' => 'Hello! This is an important message from EKDSend.',
    'voice' => 'alloy',        // alloy, echo, fable, onyx, nova, shimmer
    'language' => 'en-US',
    'record' => true,
    'machine_detection' => true
]);

echo "Call initiated: " . $call->id . "\n";
```

### Make a Call with Audio File

```php
$call = $client->calls()->create([
    'to' => '+14155551234',
    'from' => '+14155559999',
    'audio_url' => 'https://example.com/message.mp3'
]);
```

### Call Management

```php
// Get call status
$call = $client->calls()->get('call_xxxxxxxxxxxxx');
echo "Call status: {$call->status}, Duration: {$call->duration}s\n";

// List calls
$calls = $client->calls()->list(['limit' => 20, 'status' => 'completed']);

// Hang up active call
$hungUp = $client->calls()->hangup('call_xxxxxxxxxxxxx');

// Get call recording
$recording = $client->calls()->getRecording('call_xxxxxxxxxxxxx');
echo "Recording URL: " . $recording['url'] . "\n";
```

## Error Handling

```php
use EKDSend\EKDSend;
use EKDSend\Exceptions\EKDSendException;
use EKDSend\Exceptions\AuthenticationException;
use EKDSend\Exceptions\ValidationException;
use EKDSend\Exceptions\RateLimitException;
use EKDSend\Exceptions\NotFoundException;

$client = new EKDSend('ek_live_xxxxxxxxxxxxx');

try {
    $email = $client->emails()->send([
        'from' => 'hello@yourdomain.com',
        'to' => 'invalid-email',
        'subject' => 'Test',
        'html' => '<p>Hello</p>'
    ]);
} catch (AuthenticationException $e) {
    echo "Invalid API key: " . $e->getMessage() . "\n";
} catch (ValidationException $e) {
    echo "Validation failed: " . $e->getMessage() . "\n";
    print_r($e->getErrors());
} catch (RateLimitException $e) {
    echo "Rate limited. Retry after " . $e->getRetryAfter() . " seconds\n";
} catch (NotFoundException $e) {
    echo "Resource not found: " . $e->getMessage() . "\n";
} catch (EKDSendException $e) {
    echo "API error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getErrorCode() . "\n";
    echo "Request ID: " . $e->getRequestId() . "\n";
}
```

## Framework Integration

### Laravel

```php
// config/services.php
return [
    'ekdsend' => [
        'api_key' => env('EKDSEND_API_KEY'),
    ],
];

// app/Providers/AppServiceProvider.php
use EKDSend\EKDSend;

public function register()
{
    $this->app->singleton(EKDSend::class, function ($app) {
        return new EKDSend(config('services.ekdsend.api_key'));
    });
}

// In your controller
use EKDSend\EKDSend;

class NotificationController extends Controller
{
    public function sendWelcome(Request $request, EKDSend $client)
    {
        $email = $client->emails()->send([
            'from' => 'welcome@yourdomain.com',
            'to' => $request->user()->email,
            'subject' => 'Welcome!',
            'html' => view('emails.welcome')->render()
        ]);
        
        return response()->json(['email_id' => $email->id]);
    }
}
```

### Symfony

```yaml
# config/services.yaml
services:
    EKDSend\EKDSend:
        arguments:
            $apiKey: '%env(EKDSEND_API_KEY)%'
```

```php
// In your controller
use EKDSend\EKDSend;

class MailController extends AbstractController
{
    public function send(EKDSend $client): Response
    {
        $email = $client->emails()->send([
            'from' => 'hello@yourdomain.com',
            'to' => 'user@example.com',
            'subject' => 'Hello from Symfony!',
            'html' => '<p>Hello World</p>'
        ]);
        
        return $this->json(['email_id' => $email->id]);
    }
}
```

## Requirements

- PHP 8.0+
- Guzzle HTTP 7.0+

## Development

```bash
# Clone the repository
git clone https://github.com/ekddigital/ekdsend-php.git
cd ekdsend-php

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse

# Fix code style
composer cs-fix
```

## License

MIT License - see [LICENSE](LICENSE) for details.

## Links

- [Documentation](https://es.ekddigital.com/docs)
- [API Reference](https://es.ekddigital.com/docs/api-reference)
- [GitHub](https://github.com/ekddigital/ekdsend-php)
- [Packagist](https://packagist.org/packages/ekddigital/ekdsend-php)
