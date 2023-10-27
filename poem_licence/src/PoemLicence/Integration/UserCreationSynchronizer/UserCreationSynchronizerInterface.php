<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer;

use Drupal\user\UserInterface;

/**
 * Contract for synchronizing user creations to other places.
 */
interface UserCreationSynchronizerInterface {

  /**
   * Performs a sync.
   *
   * @param \Drupal\user\UserInterface $user
   *   The created user.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer\UserCreationSynchronizationFailedException
   *   When synchronization fails for any reason.
   */
  public function __invoke(UserInterface $user): void;

}
