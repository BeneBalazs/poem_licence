<?php

declare(strict_types = 1);

namespace Drupal\poem_licence\PoemLicence\Api\User\Model;

/**
 * Provides a class for user.
 */
final class User implements \JsonSerializable {

  /**
   * Creates a user.
   *
   * @param string $type
   *   Type.
   * @param string $userName
   *   Username.
   * @param string $email
   *   Email.
   * @param string $cellPhone
   *   Cell phone.
   * @param string $address
   *   Address.
   */
  public function __construct(private readonly string $type, private readonly string $userName, private readonly string $email, private readonly string $cellPhone, private readonly string $address) {
  }

  /**
   * Get type.
   *
   * @return string
   *   Type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Get name.
   *
   * @return string
   *   Name.
   */
  public function getUserName(): string {
    return $this->userName;
  }

  /**
   * Get group.
   *
   * @return string
   *   Group.
   */
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * Get cell phone number.
   *
   * @return string
   *   Cell phone number.
   */
  public function getCellPhoneNumber(): string {
    return $this->cellPhone;
  }

  /**
   * Get address.
   *
   * @return string
   *   Address.
   */
  public function getAddress(): string {
    return $this->address;
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize(): array {
    return get_object_vars($this);
  }

}
