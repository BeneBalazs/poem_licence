<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Form\Model;

/**
 * Describes a request payload for creating a form.
 */
final class CreateFormRequestPayload implements \JsonSerializable {

  // phpcs:disable
  /**
   * The path the file that this form wraps.
   *
   * This is an internal property it MUST NOT be sent to the API backend.
   *
   * @var string
   */
  private string $_filePath;
  // phpcs:enable

  /**
   * Constructs a form.
   *
   * @param string $fileName
   *   File name.
   * @param string $fileType
   *   The file mime type, like application/pdf.
   * @param string $filePath
   *   The path the file that this form wraps.
   * @param array $objects
   *   Array of parent object(s) ID.
   */
  public function __construct(private readonly string $fileName, private readonly string $fileType, string $filePath, private readonly array $objects) {
    $this->_filePath = $filePath;
  }

  /**
   * Get file name.
   *
   * @return string
   *   File name.
   */
  public function getFileName(): string {
    return $this->fileName;
  }

  /**
   * Get file mime type.
   *
   * @return string
   *   File mime type.
   */
  public function getFileType(): string {
    return $this->fileType;
  }

  /**
   * Get objects.
   *
   * @return array
   *   Objects.
   */
  public function getObjects(): array {
    return $this->objects;
  }

  /**
   * The path the file that this form wraps.
   *
   * @return string
   *   The file path.
   */
  public function getFilePath(): string {
    return $this->_filePath;
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize(): array {
    $properties = get_object_vars($this);
    // Remove internal properties before the object is serialized for
    // request payload.
    foreach ($properties as $property => $value) {
      if (str_starts_with($property, '_')) {
        unset($properties[$property]);
      }
    }
    return $properties;
  }

}
