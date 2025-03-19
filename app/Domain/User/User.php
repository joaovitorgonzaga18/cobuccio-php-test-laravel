<?php

namespace App\Domain\User;

use DateTime;

class User {
    private string $id;
    private string $name;
    private string $email;
    private string $password;
    private float $currency = 0.0;
    private string $auth_pin;
    private DateTime $dateCreatedAt;
    private DateTime $dateUpdatedAt;

    private UserPersistenceInterface $persistence;

    public function __construct(UserPersistenceInterface $persistence) {
        $this->persistence = $persistence;
    }

    public function setId(string $id): User {

        $this->id = $id;

        return $this;
    }

    public function getId(): string {
        return $this->id;
    }

    public function setName(string $name): User {

        $this->name = $name;

        return $this;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setEmail(string $email): User {

        $this->email = $email;

        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setPassword(string $password): User {
        
        $this->password = $password;

        return $this;
    }    

    public function getPassword(): string {
        return $this->password;
    }    

    public function setCurrency(float $currency): User {

        $this->currency = $currency;

        return $this;
    }
    
    public function getCurrency(): float {
        return $this->currency;
    }

    public function setAuthPin(string $auth_pin): User {

        $this->auth_pin = $auth_pin;

        return $this;
    }

    public function getAuthPin(): string {
        return $this->auth_pin;
    }

    public function setDateCreatedAt(DateTime $dateCreatedAt): User {
        
        $this->dateCreatedAt = $dateCreatedAt;

        return $this;
    }

    public function getDateCreatedAt(): DateTime {
        return $this->dateCreatedAt;
    }

    public function setDateUpdatedAt(DateTime $dateUpdatedAt): User {
        
        $this->dateUpdatedAt = $dateUpdatedAt;

        return $this;
    }

    public function getDateUpdatedAt(): DateTime {
        return $this->dateUpdatedAt;
    }

    public function checkAlreadyCreatedEmail(): bool {
        return $this->persistence->isEmailAlreadyCreated($this);
    }

    public function createUser(): void {
        $this->persistence->create($this);
    }

    public function findAll(): array {
        return $this->persistence->findAll($this);
    }

    public function getUser(): User {
        return $this->persistence->getUser($this);
    }

/*     public function deleteUser(): void {
        $this->persistence->deleteUSer($this);
    } */


/*     public function editName(): void {
        $this->persistence->editName($this);
    } */

/*     public function editEmail(): void {
        $this->checkExistentId();

        $this->checkAlreadyCreatedEmail();

        $this->setDateEdition(date('Y-m-d H:i:s'));

        $this->persistence->editEmail($this);
    } */
}
