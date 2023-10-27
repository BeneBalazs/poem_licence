<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\Plugin\KeyType;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyInterface;
use Drupal\key\Plugin\KeyTypeBase;

/**
 * Key type for Poem Licence credentials.
 *
 * @KeyType(
 *   id = "poem_licence_credentials",
 *   label = @Translation("Poem Licence credentials"),
 *   description = @Translation("Key type to use for Poem Licence credentials."),
 *   group = "poem_licence",
 *   key_value = {
 *     "plugin" = "poem_licence_input"
 *   },
 *   multivalue = {
 *     "enabled" = true,
 *     "fields" = {
 *       "username" = {
 *         "label" = @Translation("Username"),
 *         "required" = true
 *       },
 *       "password" = {
 *         "label" = @Translation("Password"),
 *         "required" = true
 *       },
 *       "endpoint" = {
 *         "label" = @Translation("Endpoint"),
 *         "required" = true
 *       },
 *     }
 *   }
 * )
 */
final class PoemLicenceCredentialsKeyType extends KeyTypeBase {

  /**
   * Unserialize a string of key values into an array.
   *
   * @param string $value
   *   A serialized string of key values.
   *
   * @return array
   *   An array of key values.
   */
  public function unserialize(string $value): array {
    return Json::decode($value) ?? [];
  }

  /**
   * Gets the username.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key entity.
   *
   * @return string
   *   The username.
   */
  public function getUsername(KeyInterface $key): string {
    return $key->getKeyValues()['username'];
  }

  /**
   * Gets the password.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key entity.
   *
   * @return string
   *   The password.
   */
  public function getPassword(KeyInterface $key): string {
    return $key->getKeyValues()['password'];
  }

  /**
   * Gets the endpoint.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key entity.
   *
   * @return string
   *   The endpoint.
   */
  public function getEndpoint(KeyInterface $key): string {
    return $key->getKeyValues()['endpoint'];
  }

  /**
   * Validate the key values.
   *
   * @param array $form
   *   Key form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   * @param string|null $key_value
   *   Key value.
   */
  public function validateKeyValue(array $form, FormStateInterface $form_state, mixed $key_value): void {
    if (empty($key_value)) {
      return;
    }

    $value = Json::decode($key_value);
    if ($value === NULL) {
      $form_state->setError($form, $this->t('The key value does not contain valid JSON: @error', ['@error' => json_last_error_msg()]));
      return;
    }

    foreach ($this->getPluginDefinition()['multivalue']['fields'] as $id => $field) {
      if (isset($field['required']) && !$field['required']) {
        continue;
      }

      $error_element = $form['settings']['input_section']['key_input_settings'][$id] ?? $form;

      if (!isset($value[$id])) {
        $form_state->setError($error_element, $this->t('The key value is missing the field %field.', ['%field' => $field['label']->render()]));
      }
      elseif (empty($value[$id])) {
        $form_state->setError($error_element, $this->t('The key value field %field is empty.', ['%field' => $field['label']->render()]));
      }
    }
  }

  /**
   * Generates the key value.
   *
   * @param array $configuration
   *   Configuration.
   *
   * @return string
   *   Generated key value.
   */
  public static function generateKeyValue(array $configuration): string {
    // There is no need to generate key value, it will be added
    // to the site manually.
    return '[]';
  }

}
