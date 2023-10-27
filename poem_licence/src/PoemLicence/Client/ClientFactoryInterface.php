<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Client;

use GuzzleHttp\ClientInterface;

/**
 * Defines a factory that returns a pre-configured Guzzle HTTP client.
 */
interface ClientFactoryInterface {

  /**
   * Returns a pre-configured client instance.
   */
  public function getClient(): ClientInterface;

}
