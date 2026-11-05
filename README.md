# qubrics/mailbox

[![Packagist version](https://img.shields.io/packagist/v/qubrics/mailbox.svg)](https://packagist.org/packages/qubrics/mailbox)
[![PHP version](https://img.shields.io/packagist/dependency-v/qubrics/mailbox/php.svg)](https://packagist.org/packages/qubrics/mailbox)
[![License](https://img.shields.io/packagist/l/qubrics/mailbox.svg)](https://packagist.org/packages/qubrics/mailbox)

Qubrics Mailbox API client for PHP.

Read the PHP SDK guide at [qubrics.com/docs/sdks/php](https://qubrics.com/docs/sdks/php).

## Requirements

- PHP 8.2 or newer.
- Composer.
- A mailbox-scoped `smx_mbx_` key or scoped `smx_agent_` token. Alternatively, use a REST OAuth access token with the required scopes.

## Installation

Run this command only after
[Packagist lists `qubrics/mailbox` version 3.0.0](https://packagist.org/packages/qubrics/mailbox).
Until then, retain your existing Composer constraint.

```bash
composer require qubrics/mailbox:^3.0
```

Before upgrading from 2.x, read the [Mailbox 3.0 migration guide](https://github.com/qubrics/qubrics-sdk/blob/main/packages/php/UPGRADING-3.0.md) for changes-response return types, the distinct thread-message list response, and operation-specific metadata getter/setter types. Update typed wrappers and manually constructed metadata. If you use 1.x, apply the [PHP 2.0 migration guide](https://github.com/qubrics/qubrics-sdk/blob/main/packages/php/UPGRADING.md) first.

If your custom Guzzle client uses `FileCookieJar` or `SessionCookieJar`, back up its state and re-authenticate into a fresh jar when upgrading. Guzzle rejects old records without a boolean `HostOnly`; do not guess that value. Default Qubrics API clients do not enable cookies. See the [upstream migration warning](https://github.com/guzzle/guzzle/security/advisories/GHSA-wm3w-8rrp-j577).

## Usage

Create the Mailbox API client with a mailbox-scoped key or scoped agent token.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Qubrics\Mailbox\ClientFactory;

$mailboxApi = ClientFactory::createMailboxAPIApi(
    getenv('QUBRICS_MAILBOX_API_KEY') ?: ''
);
```

Use `$mailboxApi->mailboxGetConnection()` to test credentials without selecting a target mailbox.

The generated `MailboxAPIApi` exposes mailbox methods such as `mailboxGetMe()`, `mailboxListMessages()`, `mailboxSendMessage()`, and `mailboxUploadAttachment()`.

## Attachments and events

Message and event attachment metadata includes `download_url`, a short-lived presigned URL for that single attachment. Fetch it promptly with a plain HTTP client and no `Authorization` header. If it expires, re-fetch the message or attachment metadata to receive a fresh URL.

Use `mailboxUploadAttachment()` to upload bytes and pass the returned `blob_id` into `mailboxSendMessage()` attachments. Inline base64 attachments remain available in the generated send body shape for small payloads.

`mailboxStreamEvents()` exposes the Mailbox SSE endpoint for clients that want live `message.received` events.

## OAuth access tokens

`ClientFactory::createMailboxAPIApiWithAccessToken($accessToken)` accepts a bare token or a callable returning one. Every API group factory has the same `WithAccessToken` variant. Providers run on each request and retry; your application owns token storage and refresh coordination. OAuth clients do not follow redirects.

```php
$client = ClientFactory::createMailboxAPIApiWithAccessToken(
    static fn (): string => getenv('QUBRICS_ACCESS_TOKEN') ?: ''
);
```

See [OAuth setup and lifecycle](https://qubrics.com/docs/developer-tools/oauth).

For API-key authentication, omit `apiKey` to read `QUBRICS_API_KEY`.
Omit `baseUrl` to read `QUBRICS_BASE_URL`.
Nonempty Qubrics values take precedence over `SENDMUX_API_KEY` and
`SENDMUX_BASE_URL`. Explicit arguments take precedence over environment values.

## Features

- Validates `smx_mbx_` keys or scoped `smx_agent_` tokens before configuring the client.
- Uses `https://app.qubrics.com/api/v1` by default.
- Adds retry and rate-limit backoff behaviour through `qubrics/core`.
- Covers mailbox identity, folders, messages, attachments, submissions, threads, quota, usage, and event streams.
- Includes generated models for Mailbox API requests and responses.

## Related packages

| Package | Use it for |
| --- | --- |
| [`qubrics/core`](https://packagist.org/packages/qubrics/core) | Shared helpers for auth, headers, retries, pagination, and errors. |
| [`qubrics/sending`](https://packagist.org/packages/qubrics/sending) | Sending API client. |
| [`qubrics/management`](https://packagist.org/packages/qubrics/management) | Management API client. |
| [`qubrics/sdk`](https://packagist.org/packages/qubrics/sdk) | Umbrella package that installs all PHP SDK surfaces. |

## Support

For help, include the package name, version, API surface, and sanitised request details. Do not include API keys, tokens, passwords, webhook secrets, customer data, or private account details.

- PHP SDK guide: [qubrics.com/docs/sdks/php](https://qubrics.com/docs/sdks/php)
- Mailbox API reference: [qubrics.com/docs/mailbox-api/introduction](https://qubrics.com/docs/mailbox-api/introduction)
- API keys guide: [qubrics.com/docs/guides/api-keys](https://qubrics.com/docs/guides/api-keys)
- Source repository: [github.com/qubrics/qubrics-sdk](https://github.com/qubrics/qubrics-sdk)

## License

MIT. See [LICENSE](LICENSE).
