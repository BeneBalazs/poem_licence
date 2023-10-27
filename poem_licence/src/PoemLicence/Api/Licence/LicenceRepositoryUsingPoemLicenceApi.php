<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Licence;

use Drupal\poem_licence\PoemLicence\Api\Licence\Exception\LicenceCouldNotBeSavedException;
use Drupal\poem_licence\PoemLicence\Api\Licence\Exception\LicenceNotFoundWithIdException;
use Drupal\poem_licence\PoemLicence\Api\Licence\Exception\UnexpectedLicenceRepositoryException;
use Drupal\poem_licence\PoemLicence\Api\Licence\Model\Licence;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * Licence repository implementation using Poem Licence API.
 */
final class LicenceRepositoryUsingPoemLicenceApi implements LicenceRepositoryInterface {

  private const LICENCE_PATH = 'licence';

  /**
   * Constructs a new object.
   */
  public function __construct(private readonly ClientInterface $client, private readonly LoggerInterface $logger) {
  }

  /**
   * {@inheritdoc}
   */
  public function saveLicence(Licence $licence): string {
    try {
      $response = $this->client->request('POST', self::LICENCE_PATH, ['body' => json_encode($licence, JSON_THROW_ON_ERROR)]);
    }
    catch (GuzzleException $e) {
      throw new LicenceCouldNotBeSavedException($e->getMessage(), 0, $e);
    }
    catch (\JsonException $e) {
      throw new UnexpectedLicenceRepositoryException($e->getMessage(), 0, $e);
    }
    return $response->getBody()->getContents();
  }

  /**
   * {@inheritdoc}
   */
  public function getLicenceIdByLicenceId(string $licence_id): string {
    $parameters = [
      'q' => "LicenceID='{$licence_id}'",
      'pageSize' => 1,
      'page' => 0,
    ];

    try {
      $response = $this->client->request('GET', self::LICENCE_PATH, ['query' => $parameters]);
    }
    catch (GuzzleException $e) {
      throw new LicenceNotFoundWithIdException($e->getMessage(), 0, $e);
    }

    try {
      $licence = json_decode((string) $response->getBody(), TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $e) {
      $this->logger->error('Poem Licence API response could not be decoded with content: <pre>{content}</pre>. Reason: {reason}', [
        'content' => (string) $response->getBody(),
        'reason' => $e->getMessage(),
      ]);
      throw new UnexpectedLicenceRepositoryException('Poem Licence API response could not be decoded with content.', 0, $e);
    }

    if ($licence['totalCount'] === 0) {
      throw new LicenceCouldNotBeSavedException($licence_id);
    }

    assert(array_key_exists('licenceList', $licence));
    assert(array_key_exists('0', $licence['licenceList']));
    assert(array_key_exists('id', $licence['licenceList'][0]));

    return $licence['licenceList'][0]['id'];
  }

}
