<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer;

use Drupal\poem_licence\Exception\RuntimeException;

/**
 * Thrown when synchronisation fails.
 */
final class UserCreationSynchronizationFailedException extends RuntimeException {

}
