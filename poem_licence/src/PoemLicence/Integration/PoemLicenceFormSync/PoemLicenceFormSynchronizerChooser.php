<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync;

use Drupal\Licence\Entity\LicenceInterface;
use Drupal\poem_licence\PoemLicence\Client\Configuration\ClientConfigurationProviderInterface;
use Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer\SynchronousUserCreationSynchronizerToPoemLicence;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;

/**
 * Switches between implementations based Poem licence connection availability.
 */
final class PoemLicenceFormSynchronizerChooser implements PoemLicenceFormSynchronizerInterface {

  /**
   * Constructs a new object.
   */
  public function __construct(
    private readonly ClientConfigurationProviderInterface $clientConfigurationProvider,
    private readonly NullLicenceSynchronizer $nullLicenceSynchronizer,
    private readonly SynchronousUserCreationSynchronizerToPoemLicence $licenceSynchronizerToPoemLicence,
    private readonly LoggerInterface $logger
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(UserInterface $user, LicenceInterface $licence): void {
    try {
      $this->clientConfigurationProvider->getClientConfiguration();
    }
    catch (\RuntimeException) {
      $this->logger->warning('Poem Licence configuration is missing or unreadable, using the null poem licence form synchronizer.');
      ($this->nullLicenceSynchronizer)($user, $licence);
      return;
    }

    ($this->licenceSynchronizerToPoemLicence)($user, $licence);
  }

}
