<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Licence\Model;

/**
 * Provides a class for licence.
 */
final class Licence implements \JsonSerializable {

  /**
   * User data.
   *
   * @var array
   */
  readonly private array $userData;

  /**
   * Constructs a licence.
   *
   * @param string $userId
   *   User ID.
   * @param string $licenceId
   *   Licence ID.
   * @param string $licenceType
   *   Licence type.
   * @param string $licenceDate
   *   Licence date.
   * @param string $title
   *   Title.
   * @param array $authors
   *   Authors.
   * @param string $genre
   *   Genre.
   * @param string $format
   *   Format.
   * @param string $language
   *   Language.
   * @param string $summary
   *   Summary.
   * @param string $publicationYear
   *   Publication year.
   */
  public function __construct(
    private readonly string $userId,
    private readonly string $licenceId,
    private readonly string $licenceType,
    private readonly string $licenceDate,
    private readonly string $title,
    private readonly array $authors,
    private readonly string $genre,
    private readonly string $format,
    private readonly string $language,
    private readonly string $summary,
    private readonly string $publicationYear,
  ) {
    $this->userData = ['id' => $userId];
  }

  /**
   * Get user ID.
   *
   * @return string
   *   UserID.
   */
  public function getUserId(): string {
    return $this->userData['id'];
  }

  /**
   * Get licence ID.
   *
   * @return string
   *   Licence ID.
   */
  public function getLicenceId(): string {
    return $this->licenceId;
  }

  /**
   * Get licence type.
   *
   * @return string
   *   Licence type.
   */
  public function getLicenceType(): string {
    return $this->licenceType;
  }

  /**
   * Get licence date.
   *
   * @return string
   *   Licence date.
   */
  public function getLicenceDate(): string {
    return $this->licenceDate;
  }

  /**
   * Get title.
   *
   * @return string
   *   Title.
   */
  public function getTitle(): string {
    return $this->title;
  }

  /**
   * Get authors.
   *
   * @return array
   *   Authors.
   */
  public function getAuthors(): array {
    return $this->authors;
  }

  /**
   * Get genre.
   *
   * @return string
   *   Genre.
   */
  public function getGenre(): string {
    return $this->genre;
  }

  /**
   * Get language.
   *
   * @return string
   *   Language.
   */
  public function getLanguage(): string {
    return $this->language;
  }

  /**
   * Get summary.
   *
   * @return string
   *   Summary.
   */
  public function getSummary(): string {
    return $this->summary;
  }

  /**
   * Get publication year.
   *
   * @return string
   *   Publication year.
   */
  public function getPublicationYear(): string {
    return $this->publicationYear;
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize(): array {
    return get_object_vars($this);
  }

}
