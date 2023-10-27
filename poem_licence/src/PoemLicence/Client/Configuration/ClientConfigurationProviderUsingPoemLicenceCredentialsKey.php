<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Client\Configuration;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\key\KeyInterface;
use Drupal\poem_licence\Exception\RuntimeException;

/**
 * Default client configuration provider that uses Settings API under the hood.
 *
 * @infrastucture
 */
final class ClientConfigurationProviderUsingPoemLicenceCredentialsKey implements ClientConfigurationProviderInterface {

  /**
   * Key storage.
   */
  private EntityStorageInterface $keyStorage;

  /**
   * Constructs a new object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->keyStorage = $entity_type_manager->getStorage('key');
  }

  /**
   * {@inheritdoc}
   */
  public function getClientConfiguration(): ClientConfiguration {
    $key_entity = $this->keyStorage->load('poem_licence_credentials');
    if ($key_entity === NULL) {
      throw new RuntimeException('Missing credentials for Poem Licence integration');
    }
    assert($key_entity instanceof KeyInterface);
    $key_values = $key_entity->getKeyValues();
    if ($key_values === []) {
      throw new RuntimeException('Missing credentials for Poem Licence integration');
    }
    return new ClientConfiguration($key_values['endpoint'], $key_values['username'], $key_values['password']);
  }

}
