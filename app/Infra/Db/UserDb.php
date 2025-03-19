<?php

namespace App\Infra\Db;

use App\Domain\User\User;
use App\Domain\User\UserPersistenceInterface;
use Illuminate\Support\Facades\DB;

class UserDb implements UserPersistenceInterface {
    private const TABLE_NAME = 'users';

    private const COLUMN_ID = 'id';
    private const COLUMN_NAME = 'name';
    private const COLUMN_EMAIL = 'email';
    private const COLUMN_PASSWORD = 'password';
    private const COLUMN_CURRENCY = 'currency';
    private const COLUMN_AUTH_PIN = 'auth_pin';
    private const COLUMN_CREATED_AT = 'created_at';
    private const COLUMN_UPDATED_AT = 'updated_at';

    public function create(User $user): void {
        DB::table(self::TABLE_NAME)->insert([
            self::COLUMN_NAME => $user->getName(),
            self::COLUMN_EMAIL => $user->getEmail(),
            self::COLUMN_PASSWORD => $user->getPassword(),
            self::COLUMN_AUTH_PIN => $user->getAuthPin(),
            self::COLUMN_CREATED_AT => $user->getDateCreatedAt(),
        ]);
    }

    public function isEmailAlreadyCreated(User $user): bool {
        return DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_EMAIL => $user->getEmail()])
            ->exists()
        ;
    }

    public function findAll(User $user): array {
        $users = [];

        $records = DB::table(self::TABLE_NAME)
            ->select([
                self::COLUMN_ID,
                self::COLUMN_NAME,
                self::COLUMN_EMAIL,
                self::COLUMN_CURRENCY,
            ])
            ->get()
        ;

        foreach ($records as $record) {
            $users[] = (new User(new UserDb()))
                ->setId($record->id)
                ->setName($record->name)
                ->setEmail($record->email)
                ->setCurrency($record->currency)
            ;
        }

        return $users;
    }

    public function getUser(User $user): User {
        $records = DB::table(self::TABLE_NAME)
            ->select([
                self::COLUMN_ID,
                self::COLUMN_NAME,
                self::COLUMN_EMAIL,
                self::COLUMN_CURRENCY,
            ])
            ->where([
                self::COLUMN_ID => $user->getId()
            ])
            ->first()
        ;

        $users = (new User(new UserDb()))
            ->setId($records->id)
            ->setName($records->name)
            ->setEmail($records->email)
            ->setCurrency($records->currency)
        ;

        return $users;
    }

    public function deleteUser(User $user): void {
        /* DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_ID => $user->getId(), self::COLUMN_DELETED_AT => null])
            ->update([
                self::COLUMN_DELETED_AT => $user->getDateDeleted(),
            ])
        ; */
    }

    public function editName(User $user): void {
        DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_ID => $user->getId()])
            ->update([
                self::COLUMN_NAME => $user->getName(),
            ])
        ;
    }

    public function editEmail(User $user): void {
        DB::table(self::TABLE_NAME)
            ->where([self::COLUMN_ID => $user->getId()])
            ->update([
                self::COLUMN_EMAIL => $user->getEmail(),
            ])
        ;
    }
}
