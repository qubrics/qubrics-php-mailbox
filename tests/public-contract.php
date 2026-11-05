<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Qubrics\Mailbox\ClientFactory;
use Qubrics\Mailbox\Configuration;
use Qubrics\Mailbox\Model\Connection;
use Qubrics\Mailbox\Model\ConnectionCredential;
use Qubrics\Mailbox\Model\ConnectionResponse;
use Qubrics\Mailbox\ObjectSerializer;

require dirname(__DIR__) . '/vendor/autoload.php';

$expect = static function (bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$configuration = new Configuration();
$expect($configuration->getHost() === 'https://app.qubrics.com/api/v1', 'Mailbox defaults to the Qubrics host');
$expect(
    $configuration->getHostSettings()[0]['url'] === 'https://app.qubrics.com/api/v1',
    'Server settings use the Qubrics host'
);
$expect($configuration->getUserAgent() === 'OpenAPI-Generator/3.0.0/PHP', 'User agent identifies version 3.0.0');
$expect(InstalledVersions::getRootPackage()['name'] === 'qubrics/mailbox', 'Composer exposes qubrics/mailbox');
$expect(InstalledVersions::isInstalled('qubrics/core'), 'Composer installs Qubrics core');

$api = ClientFactory::createMailboxAPIApi('smx_mbx_fixture');
$request = $api->mailboxGetConnectionRequest();
$expect(
    (string) $request->getUri() === 'https://app.qubrics.com/api/v1/mailbox/connection',
    'Connection request uses the Qubrics URL'
);
$expect(
    $request->getHeaderLine('Authorization') === 'Bearer smx_mbx_fixture',
    'Existing mailbox credentials authenticate'
);
$expect(
    $request->getHeaderLine('User-Agent') === 'OpenAPI-Generator/3.0.0/PHP',
    'Generated requests carry the package version'
);
$override = ClientFactory::createMailboxAPIApi('smx_mbx_fixture', 'https://custom.fixture.test/api/v1');
$expect(
    (string) $override->mailboxGetConnectionRequest()->getUri()
        === 'https://custom.fixture.test/api/v1/mailbox/connection',
    'Explicit hosts remain supported'
);

$oauth = ClientFactory::createMailboxAPIApiWithAccessToken('oauth.fixture');
$expect($oauth->getConfig()->getHost() === 'https://app.qubrics.com/api/v1', 'OAuth factories use the Qubrics default');
$oauthOverride = ClientFactory::createMailboxAPIApiWithAccessToken(
    'oauth.fixture',
    'https://custom.fixture.test/api/v1'
);
$expect(
    $oauthOverride->getConfig()->getHost() === 'https://custom.fixture.test/api/v1',
    'OAuth factories preserve explicit hosts'
);
$http = ClientFactory::httpClient(accessToken: static fn (): string => 'oauth.fixture');
$history = [];
$stack = $http->getConfig('handler');
$stack->setHandler(new MockHandler([new Response(204)]));
$stack->push(Middleware::history($history));
$http->send($oauth->mailboxGetConnectionRequest());
$expect(
    $history[0]['request']->getHeaderLine('Authorization') === 'Bearer oauth.fixture',
    'OAuth middleware authenticates generated requests'
);
$expect($http->getConfig('allow_redirects') === false, 'OAuth clients keep redirects disabled');

$response = ObjectSerializer::deserialize(json_decode('{
    "meta": {}, "ok": true, "data": {
        "credential": {"id": "credential_fixture", "name": "Fixture", "type": "api_key"},
        "label": "Fixture", "mailboxes": [], "permissions": [],
        "team": {"id": "team_fixture", "name": "Fixture"}
    }
}', false, 512, JSON_THROW_ON_ERROR), ConnectionResponse::class);
$expect($response instanceof ConnectionResponse, 'Connection response loads through Composer');
$expect($response->getData() instanceof Connection, 'Nested connection type loads in the Qubrics namespace');
$expect(
    $response->getData()->getCredential() instanceof ConnectionCredential,
    'Nested credential type loads in the Qubrics namespace'
);
$expect(
    $response->getData()->getCredential()->getName() === 'Fixture',
    'Nested response data survives deserialisation'
);

fwrite(STDOUT, "Mailbox public contract passed\n");
