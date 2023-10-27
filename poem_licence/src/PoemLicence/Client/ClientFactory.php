<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Client;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Http\ClientFactory as DrupalClientFactory;
use Drupal\Core\Http\HandlerStackConfigurator;
use Drupal\Core\Site\Settings;
use Drupal\poem_licence\PoemLicence\Client\Configuration\ClientConfigurationProviderInterface;
use Drupal\poem_licence\PoemLicence\Client\Middleware\AutoAuthenticateWithPoemLicenceSessionMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * A factory that builds a Poem Licence API client using Guzzle.
 */
final class ClientFactory implements ClientFactoryInterface {

  /**
   * Constructs a new object.
   */
  public function __construct(
    private readonly ClientConfigurationProviderInterface $clientConfigProvider,
    private readonly HandlerStackConfigurator $configurator,
    private readonly DrupalClientFactory $drupalClientFactory,
    private readonly Settings $settings) {
  }

  /**
   * {@inheritdoc}
   */
  public function getClient(): ClientInterface {
    try {
      // This is lazy fetched by design because we do not want to fetch
      // configuration in container build or service build time and fail when
      // the configuration is missing or unreadable; that cannot be caught by
      // callers.
      $clientConfiguration = $this->clientConfigProvider->getClientConfiguration();

      $stack = HandlerStack::create();
      $this->configurator->configure($stack);

      $default_config = [
        'headers' => [
          'User-Agent' => 'Poem Licence API Client',
          'Accept' => 'application/json',
          'Content-Type' => 'application/json',
        ],
        // Make the base url Guzzle compatible.
        // https://docs.guzzlephp.org/en/stable/quickstart.html?highlight=base%20url#creating-a-client
        'base_uri' => $clientConfiguration->server . '/',
        'handler' => $stack,
      ];

      // The goal, keeping Drupal default HTTP client config defaults and
      // modifications (like proxy configuration) but also allow Poem
      // Licence API client specific overrides.
      $config = NestedArray::mergeDeep($default_config, $this->settings::get('poem_licence_client_config', []));

      // Break the endless loop by passing a pre-configured Poem Licence
      // API client to our middleware without the middleware itself.
      /** @var \GuzzleHttp\HandlerStack $stack */
      $stack = clone $config['handler'];
      $stack->push(new AutoAuthenticateWithPoemLicenceSessionMiddleware($this->drupalClientFactory->fromOptions($config), $clientConfiguration));
      $config['handler'] = $stack;

      return $this->drupalClientFactory->fromOptions($config);
    }
    catch (\RuntimeException $e) {
      return new Client([
        'handler' =>
        HandlerStack::create(new MockHandler([
          new Response(500, [], $e->getMessage()),
        ])),
      ]);
    }

  }

}
