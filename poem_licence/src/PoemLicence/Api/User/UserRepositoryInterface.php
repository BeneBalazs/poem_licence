<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\User;

use Drupal\poem_licence\PoemLicence\Api\User\Model\User;

/**
 * Defines a repository for user.
 */
interface UserRepositoryInterface {

  /**
   * Saves a user.
   *
   * @param \Drupal\poem_licence\PoemLicence\Api\User\Model\User $user
   *   A user.
   *
   * @return string
   *   User ID.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Api\User\Exception\UserCouldNotBeSavedException
   * @throws \Drupal\poem_licence\PoemLicence\Api\User\Exception\UnexpectedUserRepositoryException
   */
  public function saveUser(User $user): string;

  /**
   * Gets the user ID by email.
   *
   * @param string $user_email
   *   User email.
   *
   * @return string
   *   User ID.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Api\User\Exception\UserNotFoundWithEmail
   * @throws \Drupal\poem_licence\PoemLicence\Api\User\Exception\UnexpectedUserRepositoryException
   */
  public function getUserIdByEmail(string $user_email): string;

}
