<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync;

use Drupal\Licence\Entity\LicenceInterface;
use Drupal\user\UserInterface;

/**
 * Contract for synchronizing poem licence forms to other places.
 */
interface PoemLicenceFormSynchronizerInterface {

  /**
   * Performs a sync.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user who created the licence.
   * @param \Drupal\Licence\Entity\LicenceInterface $licence
   *   The licence.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync\PoemLicenceFormSynchronisationFailedException
   *   When synchronization fails for any reason.
   */
  public function __invoke(UserInterface $user, LicenceInterface $licence): void;

}
