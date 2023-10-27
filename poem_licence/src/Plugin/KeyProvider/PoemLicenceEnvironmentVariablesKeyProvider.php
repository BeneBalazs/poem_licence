<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\Plugin\KeyProvider;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Error;
use Drupal\key\KeyInterface;
use Drupal\key\Plugin\KeyPluginFormInterface;
use Drupal\key\Plugin\KeyProviderBase;
use Drupal\poem_licence\Exception\KeyProviderRequirementsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Stores Poem Licence credentials as environment variables.
 *
 * @KeyProvider(
 *   id = "poem_licence_environment_variables",
 *   label = @Translation("Poem Licence Environment Variables"),
 *   description = @Translation("Stores the credentials of Poem Licence in environment variables:"),
 *   storage_method = "env",
 *   key_value = {
 *     "accepted" = FALSE,
 *     "required" = FALSE
 *   }
 * )
 */
final class PoemLicenceEnvironmentVariablesKeyProvider extends KeyProviderBase implements KeyPluginFormInterface {

  /**
   * PoemLicenceEnvironmentVariablesKeyProvider constructor.
   *
   * @param array $configuration
   *   Configuration about the plugin.
   * @param string $plugin_id
   *   Plugin instance ID.
   * @param mixed $plugin_definition
   *   The definition of the plugin implementation.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logger.
   */
  public function __construct(array $configuration, string $plugin_id, mixed $plugin_definition, private readonly LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.channel.poem_licence')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\key\Form\KeyEditForm $key_edit_form */
    $key_edit_form = $form_state->getFormObject();
    /** @var \Drupal\key\KeyInterface $key_entity */
    $key_entity = $key_edit_form->getEntity();
    $form['poem_licence_environment_variables'] = [
      '#markup' => implode(', ', $this->getEnvironmentVariables($key_entity)),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    try {
      /** @var \Drupal\key\Form\KeyEditForm $key_edit_form */
      $key_edit_form = $form_state->getFormObject();
      /** @var \Drupal\key\KeyInterface $key_entity */
      $key_entity = $key_edit_form->getEntity();
      $this->checkRequirements($key_entity);
    }
    catch (KeyProviderRequirementsException $exception) {
      $form_state->setError($form['settings']['provider_section']['key_provider'], $exception->getErrorMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->setConfiguration($form_state->getValues());
  }

  /**
   * Checks the requirements of the key provider.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key entity.
   *
   * @throws \Drupal\poem_licence\Exception\KeyProviderRequirementsException
   *   Exception thrown when the requirements of the key provider are not
   *   fulfilled.
   */
  public function checkRequirements(KeyInterface $key): void {
    $missing_env_variables = [];
    foreach ($this->getEnvironmentVariables($key, TRUE) as $variable) {
      if (!getenv($variable)) {
        $missing_env_variables[] = $variable;
      }
    }

    if (!empty($missing_env_variables)) {
      $missing_env_variables_to_string = implode(', ', $missing_env_variables);
      throw new KeyProviderRequirementsException('The following environment variables are not set: ' . $missing_env_variables_to_string, 'The following environment variables are not set:' . $missing_env_variables_to_string . '.');
    }
  }

  /**
   * Returns an array containing the environment variables by key type.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key entity.
   * @param bool $required
   *   Returns only the required environment variables.
   *
   * @return array
   *   The environment variables.
   */
  protected function getEnvironmentVariables(KeyInterface $key, bool $required = FALSE): array {
    $environment_variables = [];
    /** @var \Drupal\poem_licence\Plugin\KeyType\PoemLicenceCredentialsKeyType $key_type */
    $key_type = $key->getKeyType();
    foreach ($key_type->getPluginDefinition()['multivalue']['fields'] as $id => $field) {
      if ($required && isset($field['required']) && !$field['required']) {
        continue;
      }
      $environment_variables[$id] = 'POEM_LICENCE_' . strtoupper($id);
    }

    return $environment_variables;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key): bool|string|null {
    // Throwing an exception would be better than returning NULL but the key
    // module's design does not allow this.
    // Related issue: https://www.drupal.org/project/key/issues/3038212
    try {
      $this->checkRequirements($key);
    }
    catch (KeyProviderRequirementsException $exception) {
      $context = [
        '@info' => (string) $exception,
      ];
      $context += Error::decodeException($exception);
      $this->logger->error('Poem Licence authentication key value cannot be retrieved from the environment variables: @info %function (line %line of %file). <pre>@backtrace_string</pre>', $context);
      return '';
    }

    $key_value = [];
    foreach ($this->getEnvironmentVariables($key) as $id => $variable) {
      if (getenv($variable)) {
        $key_value[$id] = getenv($variable);
      }
    }

    return Json::encode((object) $key_value);
  }

}
