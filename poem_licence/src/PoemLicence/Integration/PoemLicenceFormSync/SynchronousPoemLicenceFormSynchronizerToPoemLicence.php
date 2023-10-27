<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Integration\PoemLicenceFormSync;

use Drupal\Core\File\Exception\FileException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Licence\Entity\LicenceInterface;
use Drupal\entity_print\Plugin\EntityPrintPluginManagerInterface;
use Drupal\entity_print\PrintBuilderInterface;
use Drupal\poem_licence\PoemLicence\Api\Form\FormRepositoryInterface;
use Drupal\poem_licence\PoemLicence\Api\Form\Model\CreateFormRequestPayload;
use Drupal\poem_licence\PoemLicence\Api\Licence\Exception\LicenceNotFoundWithIdException;
use Drupal\poem_licence\PoemLicence\Api\Licence\LicenceRepositoryInterface;
use Drupal\poem_licence\PoemLicence\Api\Licence\Model\Licence;
use Drupal\poem_licence\PoemLicence\Api\User\UserRepositoryInterface;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;

/**
 * Synchronizes poem licence form to Poem Licence synchronously.
 */
final class SynchronousPoemLicenceFormSynchronizerToPoemLicence implements PoemLicenceFormSynchronizerInterface {

  /**
   * Constructs a new object.
   */
  public function __construct(
    private readonly LicenceRepositoryInterface $licenceRepository,
    private readonly UserRepositoryInterface $userRepository,
    private readonly FormRepositoryInterface $formRepository,
    private readonly PrintBuilderInterface $printBuilder,
    private readonly EntityPrintPluginManagerInterface $printPluginManager,
    private readonly FileSystemInterface $fileSystem,
    private readonly LoggerInterface $logger,
  ) {
  }

  /**
   * Creates a licence for a form if it does not exist already.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user who created the licence.
   * @param \Drupal\Licence\Entity\LicenceInterface $licence
   *   The licence.
   *
   * @return string
   *   The UUID of the existing/created licence in Poem Licence.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Api\Licence\Exception\LicenceCouldNotBeSavedException
   *   When licence could not be created in Poem Licence.
   */
  private function createLicenceIfMissing(UserInterface $user, LicenceInterface $licence): string {
    try {
      return $this->licenceRepository->getLicenceIdByLicenceId($this->generateLicenceId($user, $licence));
    }
    catch (LicenceNotFoundWithIdException) {
      $licence_id = $this->licenceRepository->saveLicence(
        new Licence(
          // A user is created for a user earlier therefore it must
          // exist in Poem Licence at this point.
          $this->userRepository->getUserIdByEmail($user->getEmail()),
          $this->generateLicenceId($user, $licence),
          $licence->getType(),
          $licence->getLicenceType(),
          $licence->getTitle(),
          $licence->getAuthors(),
          $licence->getGenre(),
          $licence->getFormat(),
          $licence->getLanguage(),
          $licence->getSummary(),
          $licence->getPublicationYear(),
        )
          );
      $this->logger->info('New licence was created in Poem Licence with "{created_licence_id}" id for the licence with "{licence_id}" id by "{user_id}" user.', [
        'created_licence_id' => $licence_id,
        'licence_id' => $licence->uuid(),
        'user_id' => $user->uuid(),
      ]);

      return $licence_id;
    }
  }

  /**
   * Generates a cross-system unique ID for the licence.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user.
   * @param \Drupal\Licence\Entity\LicenceInterface $licence
   *   The licence.
   *
   * @return string
   *   The generated licence ID.
   */
  private function generateLicenceId(UserInterface $user, LicenceInterface $licence): string {
    return $user->uuid() . '---' . $licence->uuid();
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(UserInterface $user, LicenceInterface $licence): void {
    $licence_pdf_path = NULL;
    try {
      $licence_id = $this->createLicenceIfMissing($user, $licence);
      $licence_pdf_path = $this->printBuilder->savePrintable([$licence], $this->printPluginManager->createSelectedInstance('pdf'), 'temporary');
      // Sending a form multiple times to Poem Licence is fine.
      $form_id = $this->formRepository->create(
        new CreateFormRequestPayload(
          pathinfo($licence_pdf_path, PATHINFO_BASENAME),
          // When the format becomes configurable, file.mime_type.guesser
          // can help with guessing the mime type.
          'application/pdf',
          $licence_pdf_path,
          [$licence_id]
        )
      );
      $this->logger->info('New form was created in Poem Licence with "{form_id}" id for the licence with "{licence_id}" id by "{user_id}" user.', [
        'form_id' => $form_id,
        'licence_id' => $licence->uuid(),
        'user_id' => $user->uuid(),
      ]);
    }
    catch (\RuntimeException $e) {
      // We do not know exactly how PDF generation can fail, so we catch all
      // runtime exceptions.
      throw new PoemLicenceFormSynchronisationFailedException(
        $user->getDisplayName(),
        $user->getEmail(),
        sprintf('The licence of "%s" user with "%s" id could not be synchronised to Poem Licence. Reason: %s', $user->uuid(), $licence->uuid(), $e->getMessage()),
        $e->getCode(),
        $e
      );
    } finally {
      if ($licence_pdf_path !== NULL) {
        try {
          $this->fileSystem->delete($licence_pdf_path);
        }
        catch (FileException $e) {
          // This is just a warning since the PDF was saved to temporary files,
          // so eventually it gets purged.
          $this->logger->warning('The generated licence PDF export could not be removed from "{path}". Reason: {reason}', [
            'reason' => $e->getMessage(),
            'path' => $licence_pdf_path,
          ]);
        }
      }
    }
  }

}
