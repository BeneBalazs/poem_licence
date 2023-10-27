<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\Exception;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines an exception for malformed configuration.
 */
final class KeyProviderRequirementsException extends RuntimeException {

  use StringTranslationTrait;

  /**
   * The error message.
   */
  private string $errorMessage;

  /**
   * KeyProviderRequirementsException constructor.
   *
   * @param string $message
   *   Exception message.
   * @param string|null $translatable_markup_message
   *   Exception to display on the pages where the exception is caught.
   * @param int $code
   *   Error code.
   * @param \Throwable|null $previous
   *   Previous throwable used for the exception chaining.
   */
  public function __construct(string $message, string $translatable_markup_message = NULL, int $code = 0, \Throwable $previous = NULL) {
    $this->errorMessage = $translatable_markup_message;
    parent::__construct($message, $code, $previous);
  }

  /**
   * Gets the error message.
   *
   * @return string
   *   Error message.
   */
  public function getErrorMessage(): string {
    return $this->errorMessage ?? 'Malformed Poem Licence configuration: ' . $this->message . '.';
  }

}
