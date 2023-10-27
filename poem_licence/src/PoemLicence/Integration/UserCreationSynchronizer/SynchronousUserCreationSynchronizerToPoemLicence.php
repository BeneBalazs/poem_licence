<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer;

use Drupal\poem_licence\PoemLicence\Api\User\Exception\UserCouldNotBeSavedException;
use Drupal\poem_licence\PoemLicence\Api\User\Model\User;
use Drupal\poem_licence\PoemLicence\Api\User\UserRepositoryInterface;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;

/**
 * Synchronizes user creations to Poem Licence synchronously.
 */
final class SynchronousUserCreationSynchronizerToPoemLicence implements UserCreationSynchronizerInterface {

  /**
   * Constructs a new object.
   */
  public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly LoggerInterface $logger) {
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(UserInterface $user): void {
    try {
      $this->userRepository->saveUser(new User($user->get('field_type')->value, $user->getDisplayName(), $user->getEmail(), $user->get('number')->value, $user->get('address')->value));
      $this->logger->info('User with "{user_id}" id was synchronized to Poem Licence.', [
        'user_id' => $user->uuid(),
      ]);
    }
    catch (UserCouldNotBeSavedException $e) {
      throw new UserCreationSynchronizationFailedException(sprintf(
        'The created user with "%s" id could not be synced to Poem Licence. Reason: "%s".', $user->uuid(), $e->getMessage()
      ), $e->getCode(), $e);
    }
  }

}
