<?php

declare(strict_types=1);

namespace Qubrics\Mailbox;

use Qubrics\Mailbox\Api\MailboxAPIApi;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Qubrics\Core\ApiKeySurface;
use Qubrics\Core\Auth;
use Qubrics\Core\RetryMiddleware;
use Qubrics\Core\RetryOptions;

final class ClientFactory
{
    public static function configuration(string $apiKey, ?string $baseUrl = null): Configuration
    {
        $configuration = new Configuration();
        if ($baseUrl !== null && $baseUrl !== '') {
            $configuration->setHost($baseUrl);
        }

        /** @var Configuration $configured */
        $configured = Auth::configureBearer($configuration, $apiKey, ApiKeySurface::Mailbox);
        return $configured;
    }

    /** @param string|callable(): string|null $accessToken */
    public static function httpClient(
        ?RetryOptions $retryOptions = null,
        string|callable|null $accessToken = null
    ): ClientInterface {
        $stack = HandlerStack::create();
        $stack->push(RetryMiddleware::create($retryOptions), 'qubrics_retry');
        if ($accessToken !== null) {
            $stack->push(Auth::accessTokenMiddleware($accessToken), 'qubrics_oauth');
        }

        return new Client(['handler' => $stack, 'allow_redirects' => $accessToken === null]);
    }

    public static function createMailboxAPIApi(
        string $apiKey,
        ?string $baseUrl = null,
        ?RetryOptions $retryOptions = null
    ): MailboxAPIApi {
        return new MailboxAPIApi(
            self::httpClient($retryOptions),
            self::configuration($apiKey, $baseUrl)
        );
    }

    /** @param string|callable(): string $accessToken */
    public static function createMailboxAPIApiWithAccessToken(
        string|callable $accessToken,
        ?string $baseUrl = null,
        ?RetryOptions $retryOptions = null
    ): MailboxAPIApi {
        $configuration = new Configuration();
        if ($baseUrl !== null && $baseUrl !== '') {
            $configuration->setHost($baseUrl);
        }

        return new MailboxAPIApi(
            self::httpClient($retryOptions, $accessToken),
            $configuration
        );
    }
}
