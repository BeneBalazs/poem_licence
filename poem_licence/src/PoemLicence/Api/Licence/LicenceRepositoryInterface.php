<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Licence;

use Drupal\poem_licence\PoemLicence\Api\Licence\Model\Licence;

/**
 * Provides an interface for licence repository.
 */
interface LicenceRepositoryInterface {

  /**
   * Saves a licence.
   *
   * @param \Drupal\poem_licence\PoemLicence\Api\Licence\Model\Licence $licence
   *   A licence.
   *
   * @return string
   *   Licence ID.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Api\Licence\Exception\LicenceCouldNotBeSavedException
   * @throws \Drupal\poem_licence\PoemLicence\Api\Licence\Exception\UnexpectedLicenceRepositoryException
   */
  public function saveLicence(Licence $licence): string;

  /**
   * Gets the licence by licence ID.
   *
   * @param string $licence_id
   *   Licence import ID.
   *
   * @return string
   *   Licence.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Api\Licence\Exception\LicenceNotFoundWithIdException
   * @throws \Drupal\poem_licence\PoemLicence\Api\Licence\Exception\UnexpectedLicenceRepositoryException
   */
  public function getLicenceIdByLicenceId(string $licence_id): string;

}
