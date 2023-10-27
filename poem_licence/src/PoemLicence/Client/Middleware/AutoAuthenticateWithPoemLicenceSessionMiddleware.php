<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Client\Middleware;

use Drupal\poem_licence\PoemLicence\Client\Configuration\ClientConfiguration;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Message\RequestInterface;

/**
 * Provides a middleware to add session id to the request.
 */
final class AutoAuthenticateWithPoemLicenceSessionMiddleware {

  /**
   * Constructs a new object.
   */
  public function __construct(private readonly ClientInterface $client, private readonly ClientConfiguration $clientConfiguration) {
  }

  /**
   * Add session ID to the request.
   *
   * @param callable $handler
   *   The callback.
   *
   * @return callable
   *   The processed callable.
   */
  public function __invoke(callable $handler): callable {
    $session_id = $this->getSessionId();
    $domain = (new UriFactory())->createUri($this->clientConfiguration->server)->getHost();

    return static function (RequestInterface $request, array $options) use ($handler, $session_id, $domain) {
      $options['cookies'] = CookieJar::fromArray(['SESSIONID' => $session_id], $domain);
      return Middleware::cookies()($handler)($request, $options);
    };
  }

  /**
   * Gets the session id.
   */
  private function getSessionId(): string {
    $options = [
      RequestOptions::FORM_PARAMS => [
        'username' => $this->clientConfiguration->username,
        'password' => $this->clientConfiguration->password,
      ],
      RequestOptions::HEADERS => [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
    ];
    $response = $this->client->request('POST', 'session', $options);
    return $response->getBody()->getContents();
  }

}
