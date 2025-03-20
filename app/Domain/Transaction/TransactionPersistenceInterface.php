<?php

namespace App\Domain\Transaction;

interface TransactionPersistenceInterface
{
    public function create(Transaction $Transaction): void;
    public function findAll(Transaction $Transaction): array;
    public function findAllUserTransactions(Transaction $Transaction, string $user_id): array;
    public function getTransaction(Transaction $Transaction): Transaction;
    public function cancelTransaction(Transaction $Transaction): void;
    public function checkIfExist(Transaction $Transaction): bool;
}
