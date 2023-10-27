<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer;

use Drupal\poem_licence\PoemLicence\Client\Configuration\ClientConfigurationProviderInterface;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;

/**
 * Switches between implementations based Poem Licence connection availability.
 */
final class UserCreationSynchronizerChooser implements UserCreationSynchronizerInterface {

  /**
   * Constructs a new object.
   */
  public function __construct(
    private readonly ClientConfigurationProviderInterface $clientConfigurationProvider,
    private readonly NullUserCreationSynchronizer $nullUserCreationSynchronizer,
    private readonly SynchronousUserCreationSynchronizerToPoemLicence $userCreationSynchronizerToPoemLicence,
    private readonly LoggerInterface $logger
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(UserInterface $user): void {
    try {
      $this->clientConfigurationProvider->getClientConfiguration();
    }
    catch (\RuntimeException) {
      $this->logger->warning('Poem Licence configuration is missing or unreadable, using the null user creation synchronizer.');
      ($this->nullUserCreationSynchronizer)($user);
      return;
    }

    ($this->userCreationSynchronizerToPoemLicence)($user);
  }

}
