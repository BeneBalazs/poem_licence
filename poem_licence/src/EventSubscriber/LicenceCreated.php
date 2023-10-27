<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\EventSubscriber;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Licence\Event\PoemLicenceCreated;
use Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync\PoemLicenceFormSynchronizerInterface;
use Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer\UserCreationSynchronizerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles poem licence integration after licence created.
 */
final class LicenceCreated implements EventSubscriberInterface {

  /**
   * User storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private readonly EntityStorageInterface $userStorage;

  /**
   * Constructs a new object.
   *
   * @param \Drupal\poem_licence\PoemLicence\Integration\UserCreationSynchronizer\UserCreationSynchronizerInterface $userCreationSynchronizer
   *   User creation synchronizer.
   * @param \Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync\PoemLicenceFormSynchronizerInterface $poemLicenceFormSynchronizer
   *   Poem licence synchronizer.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   */
  public function __construct(
    private readonly UserCreationSynchronizerInterface $userCreationSynchronizer,
    private readonly PoemLicenceFormSynchronizerInterface $poemLicenceFormSynchronizer,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    $this->userStorage = $entityTypeManager->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[PoemLicenceCreated::class][] = ['onPoemLicenceCreated'];
    return $events;
  }

  /**
   * Uploads the licence to Poem Licence.
   *
   * @param \Drupal\Licence\Event\PoemLicenceCreated $event
   *   Licence created event.
   */
  public function onPoemLicenceCreated(PoemLicenceCreated $event): void {
    /** @var \Drupal\user\UserInterface|null $user */
    $user = $this->userStorage->load($event->getAccount()->id());
    if ($user === NULL) {
      throw new \LogicException(sprintf('User could not be loaded, this is unexpected. ID: %s', $event->getAccount()->id()));
    }
    ($this->userCreationSynchronizer)($user);
    ($this->poemLicenceFormSynchronizer)($user, $event->getLicence());
  }

}
