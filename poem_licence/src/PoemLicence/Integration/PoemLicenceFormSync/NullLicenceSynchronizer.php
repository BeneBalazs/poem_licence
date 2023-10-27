<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync;

use Drupal\Licence\Entity\LicenceInterface;
use Drupal\user\UserInterface;

/**
 * Used when Poem Licence configuration is missing.
 */
final class NullLicenceSynchronizer implements PoemLicenceFormSynchronizerInterface {

  /**
   * {@inheritdoc}
   */
  public function __invoke(UserInterface $user, LicenceInterface $licence): void {
    // Noop.
  }

}
