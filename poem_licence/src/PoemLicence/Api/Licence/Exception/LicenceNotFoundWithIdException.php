<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Licence\Exception;

use Drupal\poem_licence\Exception\RuntimeException;

/**
 * Thrown when a licence not found by a number.
 */
final class LicenceNotFoundWithIdException extends RuntimeException {

  /**
   * The licence ID.
   *
   * @var string
   */
  public readonly string $licenceId;

  /**
   * Constructs a new object.
   */
  public function __construct(string $licence_id) {
    parent::__construct(sprintf('Licence not found with "%s" ID.', $licence_id));
    $this->licenceId = $licence_id;
  }

}
