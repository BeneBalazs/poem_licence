<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\Form;

use Drupal\poem_licence\PoemLicence\Api\Form\Model\CreateFormRequestPayload;

/**
 * Provides an interface for form repository.
 */
interface FormRepositoryInterface {

  /**
   * Saves the form.
   *
   * @param \Drupal\poem_licence\PoemLicence\Api\Form\Model\CreateFormRequestPayload $form
   *   A form.
   *
   * @return string
   *   Form ID.
   *
   * @throws \Drupal\poem_licence\PoemLicence\Api\Form\Exception\FormCouldNotBeCreatedException
   * @throws \Drupal\poem_licence\PoemLicence\Api\Form\Exception\UnexpectedFormRepositoryException
   */
  public function create(CreateFormRequestPayload $form): string;

}
