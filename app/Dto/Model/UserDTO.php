<?php

namespace App\Dto\Model;

use App\Dto\AbstractDataTransferObject;

class UserDTO extends AbstractDataTransferObject
{
    private int|null $id;
    private string|null $name;
    private string|null $email;
    private string|null $email_verified_at;
    private string|null $password;
    private string|null $remember_token;
    private string|null $created_at;
    private string|null $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): UserDTO
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): UserDTO
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): UserDTO
    {
        $this->email = $email;
        return $this;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt(?string $email_verified_at): UserDTO
    {
        $this->email_verified_at = $email_verified_at;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): UserDTO
    {
        $this->password = $password;
        return $this;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken(?string $remember_token): UserDTO
    {
        $this->remember_token = $remember_token;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): UserDTO
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): UserDTO
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
