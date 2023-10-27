<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync;

use Drupal\poem_licence\Exception\RuntimeException;

/**
 * Thrown when synchronisation fails.
 */
final class PoemLicenceFormSynchronisationFailedException extends RuntimeException {

  /**
   * Constructs a poem licence form sync fail exception.
   *
   * @param string $username
   *   Username.
   * @param string $userEmailAddress
   *   User email address.
   * @param string $message
   *   Exception message.
   * @param int $code
   *   Exception HTTP code.
   * @param \Throwable|null $previous
   *   The previous throwable used for the exception chaining.
   */
  public function __construct(
    private readonly string $username,
    private readonly string $userEmailAddress,
    string $message = '',
    int $code = 0,
    ?\Throwable $previous = NULL
  ) {
    parent::__construct($message, $code, $previous);
  }

  /**
   * Gets the username.
   *
   * @return string
   *   Username.
   */
  public function getUsername(): string {
    return $this->username;
  }

  /**
   * Gets the user email address.
   *
   * @return string
   *   User email.
   */
  public function getUserEmail(): string {
    return $this->userEmailAddress;
  }

}
