<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Client\Configuration;

use Drupal\Component\Utility\UrlHelper;
use Webmozart\Assert\Assert;

/**
 * Creates a client configuration for Poem Licence.
 */
final class ClientConfiguration {

  /**
   * Creates a ClientConfiguration instance.
   *
   * @param string $server
   *   Poem Licence API server url with base path.
   * @param string $username
   *   Username.
   * @param string $password
   *   Password.
   */
  public function __construct(public readonly string $server, public readonly string $username, public readonly string $password) {
    Assert::notEmpty($username, 'The username cannot be empty.');
    Assert::notEmpty($password, 'The password cannot be empty.');

    Assert::notEmpty($server, 'The Poem Licence API base URL cannot be empty.');
    Assert::notEndsWith($server, '/', 'The Poem Licence API base url must not end with a trailing slash.');
    if (!UrlHelper::isValid($server, TRUE)) {
      throw new \InvalidArgumentException('The Poem Licence API base URL must be a valid absolute URL.');
    }
  }

}
