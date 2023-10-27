<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer;

use Drupal\user\UserInterface;

/**
 * Used when Poem Licence configuration is missing.
 */
final class NullUserCreationSynchronizer implements UserCreationSynchronizerInterface {

  /**
   * {@inheritdoc}
   */
  public function __invoke(UserInterface $user): void {
    // Noop.
  }

}
