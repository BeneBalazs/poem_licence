<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Form;

use Drupal\poem_licence\PoemLicence\Api\Form\Exception\FormCouldNotBeCreatedException;
use Drupal\poem_licence\PoemLicence\Api\Form\Exception\UnexpectedFormRepositoryException;
use Drupal\poem_licence\PoemLicence\Api\Form\Model\CreateFormRequestPayload;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;

/**
 * Form repository implementation using Poem Licence API.
 */
final class FormRepositoryUsingPoemLicenceApi implements FormRepositoryInterface {

  private const FORM_PATH = 'form';

  /**
   * Constructs a new object.
   */
  public function __construct(private readonly ClientInterface $client) {
  }

  /**
   * {@inheritdoc}
   */
  public function create(CreateFormRequestPayload $form): string {
    try {
      $options = [
        'multipart' => [
          [
            'name' => 'form',
            'contents' => json_encode($form, JSON_THROW_ON_ERROR),
            'headers'  => [
              'Content-Type' => 'application/json',
            ],
          ],
          [
            'name' => 'content',
            'contents' => Utils::tryFopen($form->getFilePath(), 'rb'),
            'filename' => $form->getFileName(),
          ],
        ],
      ];

      $response = $this->client->request('POST', self::FORM_PATH, $options);
    }
    catch (GuzzleException | \RuntimeException $e) {
      throw new FormCouldNotBeCreatedException($e->getMessage(), 0, $e);
    }
    catch (\JsonException $e) {
      throw new UnexpectedFormRepositoryException($e->getMessage(), 0, $e);
    }
    return $response->getBody()->getContents();
  }

}
