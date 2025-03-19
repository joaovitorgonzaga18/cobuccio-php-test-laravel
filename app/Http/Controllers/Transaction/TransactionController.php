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

class TransactionController extends Controller {

    public function createTransaction(Request $request): JsonResponse {        

        $response = [];
        $post = $request->all();

        try {          

            $transaction  = new Transaction(new TransactionDb());
            $dateCreation = new DateTime('now');   

            $sender = new User(new UserDb());
            $reciever = new User(new UserDb());

            if (isset($post['sender_id']) && $post['sender_id'] > 0)               
                $transaction->setSender($sender->setId($post['sender_id'])->getUser());       

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
