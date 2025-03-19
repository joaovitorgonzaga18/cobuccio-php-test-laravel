<?php

namespace App\Infra\Db;

use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionPersistenceInterface;
use App\Domain\User\User;
use Illuminate\Support\Facades\DB;
use DateTime;
use Exception;

class TransactionDb implements TransactionPersistenceInterface {
    private const TABLE_NAME = 'transactions';

    private const COLUMN_ID = 'id';
    private const COLUMN_UUID = 'transaction_uuid';
    private const COLUMN_AMOUNT = 'amount';
    private const COLUMN_TYPE = 'type';
    private const COLUMN_SENDER = 'user_sender';
    private const COLUMN_RECIEVER = 'user_reciever';
    private const COLUMN_DATE_TIME = 'date_time';
    private const COLUMN_CANCELLED = 'cancelled';

    public function create(Transaction $transaction): void {  
        $sender = ($transaction->getSender()) ? $transaction->getSender() : null;
        $reciever = ($transaction->getReciever()) ? $transaction->getReciever() : null;
        $db = DB::table(self::TABLE_NAME)->insert([
            self::COLUMN_UUID => $transaction->getUuid(),
            self::COLUMN_AMOUNT => $transaction->getAmount(),
            self::COLUMN_TYPE => $transaction->getType(),
            self::COLUMN_SENDER => $sender->getId(),
            self::COLUMN_RECIEVER => $reciever->getId(),
            self::COLUMN_DATE_TIME => $transaction->getDateTime(),
        ]);

        if ($db) {
            if ($sender) {
                $sender->setCurrency($sender->getCurrency() - $transaction->getAmount());
                $sender->updateCurrency();
            }
            $reciever->setCurrency($reciever->getCurrency() + $transaction->getAmount());
            $reciever->updateCurrency();
        }
    }

    public function findAll(Transaction $transaction): array {
        $transactions = [];

        $records = DB::table(self::TABLE_NAME)
        ->select([
            self::COLUMN_ID,
            self::COLUMN_UUID,
            self::COLUMN_AMOUNT,
            self::COLUMN_TYPE,
            self::COLUMN_SENDER,
            self::COLUMN_RECIEVER,
            self::COLUMN_DATE_TIME,
            self::COLUMN_CANCELLED,
        ])
        ->get();

        foreach ($records as $record) {

            $sender = new User(new UserDb());
            $reciever = new User(new UserDb());    
            
            $transaction = new Transaction(new TransactionDb());

            $transaction->setId($record->id)
            ->setUuid($record->transaction_uuid)
            ->setAmount($record->amount)
            ->setType($record->type)
            ->setReciever($reciever->setId($record->user_reciever)->getUser())
            ->setDateTime(new DateTime($record->date_time))
            ->setCancelled($record->cancelled);

            if ($record->user_sender)               
                $transaction->setSender($sender->setId($record->user_sender)->getUser());
            

            $transactions[] = $transaction;
        }

        return $transactions;
    }

    public function findAllUserTransactions(Transaction $transaction, string $user_id): array {
        $transactions = [];

        $records = DB::table(self::TABLE_NAME)
        ->select([
            self::COLUMN_ID,
            self::COLUMN_UUID,
            self::COLUMN_AMOUNT,
            self::COLUMN_TYPE,
            self::COLUMN_SENDER,
            self::COLUMN_RECIEVER,
            self::COLUMN_DATE_TIME,
            self::COLUMN_CANCELLED,
        ])->where([            
            self::COLUMN_SENDER => $user_id
        ])->orWhere([            
            self::COLUMN_RECIEVER => $user_id
        ])
        ->get();

        foreach ($records as $record) {

            $sender = new User(new UserDb());
            $reciever = new User(new UserDb());    
            
            $transaction = new Transaction(new TransactionDb());

            $transaction->setId($record->id)
            ->setUuid($record->transaction_uuid)
            ->setAmount($record->amount)
            ->setType($record->type)
            ->setReciever($reciever->setId($record->user_reciever)->getUser())
            ->setDateTime(new DateTime($record->date_time))
            ->setCancelled($record->cancelled);

            if ($record->user_sender)               
                $transaction->setSender($sender->setId($record->user_sender)->getUser());
            

            $transactions[] = $transaction;
        }

        return $transactions;
    }

    public function getTransaction(Transaction $transaction): Transaction {
        $records = DB::table(self::TABLE_NAME)
        ->select([
            self::COLUMN_ID,
            self::COLUMN_UUID,
            self::COLUMN_AMOUNT,
            self::COLUMN_TYPE,
            self::COLUMN_SENDER,
            self::COLUMN_RECIEVER,
            self::COLUMN_DATE_TIME,
            self::COLUMN_CANCELLED,
        ])
        ->where([
            self::COLUMN_UUID => $transaction->getUuid()
        ])
        ->first();

        $transactions = new Transaction(new TransactionDb());

        $sender = new User(new UserDb());
        $reciever = new User(new UserDb());   

        if ($records->user_sender)               
            $transactions->setSender($sender->setId($records->user_sender)->getUser());        

        $transactions->setId($records->id)
            ->setUuid($records->transaction_uuid)
            ->setAmount($records->amount)
            ->setType($records->type)
            ->setReciever($reciever->setId($records->user_reciever)->getUser())
            ->setDateTime(new DateTime($records->date_time))
            ->setCancelled($records->cancelled);

        return $transactions;
    }

    public function cancelTransaction(Transaction $transaction): void {        
        DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_UUID => $transaction->getUuid()])
            ->update([
                self::COLUMN_CANCELLED => $transaction->isCancelled(),
            ])
        ;
    }

}