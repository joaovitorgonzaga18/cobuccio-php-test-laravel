<?php

namespace App\Http\Controllers\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\User\User;
use App\Http\Controllers\Controller;
use App\Infra\Db\TransactionDb;
use App\Infra\Db\UserDb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use DateTime;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller {

    private const AUTH_PIN_LENGTH = 4;

    public function createTransaction(Request $request): JsonResponse {        

        $response = [];
        $post = $request->all();

        try {          

            $transaction  = new Transaction(new TransactionDb());
            $dateCreation = new DateTime('now');   

            $sender = new User(new UserDb());
            $reciever = new User(new UserDb());

            if (isset($post['sender_id']) && $post['sender_id'] > 0) {     
                $sender = $sender->setId($post['sender_id']);     

                if (!$sender->checkIfExist())                    
                    return $this->buildResponse(['success' => false, 'message' => 'Sender user not found'], 404);

                $sender = $sender->getUser();
                $transaction->setSender($sender); 

                if ((!isset($post['auth_pin'])) || (strlen($post['auth_pin']) < self::AUTH_PIN_LENGTH) || (strlen($post['auth_pin']) > self::AUTH_PIN_LENGTH) || (!Hash::check($post['auth_pin'], $sender->getAuthPin())))
                    return $this->buildResponse(['success' => false, 'message' => 'Invalid PIN'], 422);    

                if ($sender->getCurrency() < $post['amount'])                 
                    return $this->buildResponse(['success' => false, 'message' => 'Insufficient balance'], 422);                
            }

            if (isset($post['reciever_id']) && $post['reciever_id'] > 0) {
                $reciever = $reciever->setId($post['reciever_id']);

                if (!$reciever->checkIfExist())                    
                    return $this->buildResponse(['success' => false, 'message' => 'Reciever user not found'], 404);

                $reciever = $reciever->getUser();
                $transaction->setReciever($reciever); 

                if ($post['type'] == 1) {
                    if ((!isset($post['auth_pin'])) || (strlen($post['auth_pin']) < self::AUTH_PIN_LENGTH) || (strlen($post['auth_pin']) > self::AUTH_PIN_LENGTH) || (!Hash::check($post['auth_pin'], $reciever->getAuthPin())))
                        return $this->buildResponse(['success' => false, 'message' => 'Invalid PIN'], 422);    
                }
            }

            $transaction
                ->setUuid(Str::uuid()->toString())
                ->setAmount($post['amount'])
                ->setType($post['type'])
                ->setReciever($reciever->setId($post['reciever_id'])->getUser())
                ->setDateTime($dateCreation);

            $transaction->createTransaction();
            
            $response = [
                'success' => true,
                'message'=> 'Transaction was created successfully',
                'transaction_uuid' => $transaction->getUuid(),
                'amount' => $transaction->getAmount(),
                'type' => $transaction->getType(),
                'sender' => $transaction->getSender()->getName(),
                'reciever' => $transaction->getReciever()->getName(),
                'date_time' => $transaction->getDateTime()->format('Y-m-d H:i:s')
            ];
            
            return response()->json($response, 200);

        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAll(Request $request): JsonResponse {

        $response = [];

        try {

            $transaction = new Transaction(new TransactionDb());

            $transactions = $transaction->findAll();

            $response['sucess'] = true;
            $response['message'] = 'Transactions found';

            foreach($transactions as $transaction) {
                $response[] = [
                    'transaction_uuid' => $transaction->getUuid(),
                    'amount' => $transaction->getAmount(),
                    'type' => $transaction->getType(),
                    'sender' => $transaction->getSender()->getName(),
                    'reciever' => $transaction->getReciever()->getName(),
                    'date_time' => $transaction->getDateTime()->format('Y-m-d H:i:s'),
                    'cancelled' => $transaction->isCancelled()
                ];
            }

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAllUserTransactions(Request $request, string $user_id): JsonResponse {

        $response = [];

        try {

            $transaction = new Transaction(new TransactionDb());

            $transactions = $transaction->findAllUserTransactions($user_id);

            $response['sucess'] = true;
            $response['message'] = 'Transactions found';

            foreach($transactions as $transaction) {
                $response[] = [
                    'transaction_uuid' => $transaction->getUuid(),
                    'amount' => $transaction->getAmount(),
                    'type' => $transaction->getType(),
                    'sender' => $transaction->getSender()->getName(),
                    'reciever' => $transaction->getReciever()->getName(),
                    'date_time' => $transaction->getDateTime()->format('Y-m-d H:i:s'),
                    'cancelled' => $transaction->isCancelled()
                ];
            }

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getTransaction(Request $request, string $id): JsonResponse {

        $response = [];
        
        try {
            $transaction = new Transaction(new TransactionDb());

            $transaction
                ->setUuid($id)
            ;

            $transactions = $transaction->getTransaction();

            $response = [
                'success' => true,
                'message' => 'Transaction found',
                'transaction_uuid' => $transactions->getUuid(),
                'amount' => $transactions->getAmount(),
                'type' => $transactions->getType(),
                'sender' => $transactions->getSender()->getName(),
                'reciever' => $transactions->getReciever()->getName(),
                'date_time' => $transactions->getDateTime()->format('Y-m-d H:i:s'),
                'cancelled' => $transactions->isCancelled()
            ];

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function cancelTransaction(Request $request, string $id): JsonResponse {

        $response = [];
        
        try {

            $transaction = new Transaction(new TransactionDb());

            $transaction
                ->setUuid($id)
                ->setCancelled(true);
            ;

            $transaction->cancelTransaction();

            $response = [
                'success' => true,
                'message' => 'Transaction cancelled',
                'transaction_uuid' => $transaction->getUuid(),
                'cancelled' => $transaction->isCancelled(),
            ];

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
        
    }
}
