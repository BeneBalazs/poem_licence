<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\Plugin\KeyInput;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyInputBase;

/**
 * Input text field for Poem Licence credentials.
 *
 * @KeyInput(
 *   id = "poem_licence_input",
 *   label = @Translation("Input text field for Poem Licence credentials."),
 *   description = @Translation("Provides an input text field for Poem Licence credentials.")
 * )
 */
final class PoemLicenceCredentialsKeyInput extends KeyInputBase {

  /**
   * Build the config form.
   *
   * @param array $form
   *   The config orm.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The config form state.
   *
   * @return array
   *   The config form to be rendered.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['alert'] = [
      '#title' => $this->t('Unsupported type!'),
      '#type' => 'item',
      '#description' => $this->t('Do not store the credentials in configuration as they are sensitive data. Please use Environment Variables.'),
    ];

    return $form;
  }

}
