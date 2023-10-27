<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\User;

use Drupal\poem_licence\PoemLicence\Api\User\Exception\UnexpectedUserRepositoryException;
use Drupal\poem_licence\PoemLicence\Api\User\Exception\UserCouldNotBeSavedException;
use Drupal\poem_licence\PoemLicence\Api\User\Exception\UserNotFoundWithEmail;
use Drupal\poem_licence\PoemLicence\Api\User\Model\User;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

/**
 * User repository implementation using Poem Licence API.
 */
final class UserRepositoryUsingPoemLicenceApi implements UserRepositoryInterface {

  private const USER_PATH = 'user';

  /**
   * Constructs a new object.
   */
  public function __construct(private readonly ClientInterface $client, private readonly LoggerInterface $logger) {
  }

  /**
   * {@inheritdoc}
   */
  public function saveUser(User $user): string {
    try {
      $response = $this->client->request('POST', self::USER_PATH, ['body' => json_encode($user, JSON_THROW_ON_ERROR)]);
    }
    catch (GuzzleException $e) {
      throw new UserCouldNotBeSavedException($e->getMessage(), 0, $e);
    }
    catch (\JsonException $e) {
      throw new UnexpectedUserRepositoryException($e->getMessage(), 0, $e);
    }
    return $response->getBody()->getContents();
  }

  /**
   * {@inheritdoc}
   */
  public function getUserIdByEmail(string $user_email): string {
    $parameters = [
      'q' => "email='{$user_email}'",
      'pageSize' => 1,
      'page' => 0,
    ];

    try {
      $response = $this->client->request('GET', self::USER_PATH, ['query' => $parameters]);
    }
    catch (GuzzleException $e) {
      throw new UnexpectedUserRepositoryException($e->getMessage(), 0, $e);
    }

    try {
      $users_list = json_decode((string) $response->getBody(), TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $e) {
      $this->logger->error('User API response could not be decoded with content: <pre>{content}</pre>. Reason: {reason}', [
        'content' => (string) $response->getBody(),
        'reason' => $e->getMessage(),
      ]);
      throw new UnexpectedUserRepositoryException('User API response could not be decoded with content.', 0, $e);
    }

    if ($users_list['totalCount'] === 0) {
      throw new UserNotFoundWithEmail($user_email);
    }

    assert(array_key_exists('userList', $users_list));
    assert(array_key_exists('0', $users_list['userList']));
    assert(array_key_exists('id', $users_list['userList'][0]));

    return $users_list['userList'][0]['id'];
  }

}
