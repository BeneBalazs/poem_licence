<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\User\Exception;

use Drupal\poem_licence\Exception\RuntimeException;

/**
 * Thrown when a user not found by email address.
 */
final class UserNotFoundWithEmail extends RuntimeException {

  /**
   * Constructs a new object.
   */
  public function __construct(private readonly string $email) {
    parent::__construct(sprintf('User not found with "%s" name.', $email));
  }

}
