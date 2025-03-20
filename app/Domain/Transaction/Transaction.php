<?php

namespace App\Domain\Transaction;

use App\Domain\User\User;
use App\Infra\Db\UserDb;
use DateTime;

class Transaction {

    private string $id;
    private string $uuid;
    private float $amount;
    private int $type;
    private ?User $sender = null;
    private User $reciever;
    private DateTime $date_time;
    private bool $cancelled;

    private TransactionPersistenceInterface $persistence;

    public function __construct(TransactionPersistenceInterface $persistence) {
        $this->persistence = $persistence;
    }

    public function setId(string $id): Transaction {

        $this->id = $id;

        return $this;
    }

    public function getId(): string {
        return $this->id;
    }

    public function setUuid(string $uuid): Transaction {

        $this->uuid = $uuid;

        return $this;
    }

    public function getUuid(): string {
        return $this->uuid;
    }

    public function setAmount(float $amount): Transaction {

        $this->amount = $amount;

        return $this;
    }
 
    public function getAmount(): float {
        return $this->amount;
    }

    public function setType(int $type): Transaction {

        $this->type = $type;

        return $this;
    }
 
    public function getType(): int {
        return $this->type;
    }

    public function setSender(User $sender): Transaction {

        $this->sender = $sender;

        return $this;
    }
 
    public function getSender(): User {
        $user = new User(new UserDb());
        return ($this->sender) ? $this->sender : $user->setName('');
    }

    public function setReciever(User $reciever): Transaction {

        $this->reciever = $reciever;

        return $this;
    }
 
    public function getReciever(): User {
        return $this->reciever;
    }
 
    public function getDateTime(): DateTime {
        return $this->date_time;
    }

    public function setDateTime(DateTime $date_time): Transaction {

        $this->date_time = $date_time;

        return $this;
    }
 
    public function isCancelled(): bool {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): Transaction {

        $this->cancelled = $cancelled;

        return $this;
    }

    public function createTransaction(): void {
        $this->persistence->create($this);
    } 

    public function getTransaction(): Transaction {
        return $this->persistence->getTransaction($this);
    }

    public function findAll(): array {
        return $this->persistence->findAll($this);
    }

    public function findAlluserTransactions(string $user_id): array {
        return $this->persistence->findAlluserTransactions($this, $user_id);
    }

    public function cancelTransaction(): void {
        $this->persistence->cancelTransaction($this);
    }

    public function checkIfExist(): bool {
        return $this->persistence->checkIfExist($this);
    }
}