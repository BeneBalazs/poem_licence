<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Client\Configuration;

/**
 * Definition of a client configuration configuration provider.
 */
interface ClientConfigurationProviderInterface {

  /**
   * Returns the client configuration.
   *
   * @return \Drupal\poem_licence\PoemLicence\Client\Configuration\ClientConfiguration
   *   The client configuration.
   *
   * @throws \RuntimeException
   *   When configuration is missing or unreadable.
   */
  public function getClientConfiguration(): ClientConfiguration;

}
