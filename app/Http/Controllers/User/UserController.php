<?php

namespace App\Http\Controllers\User;

use App\Domain\User\User;
use App\Http\Controllers\Controller;
use App\Infra\Db\UserDb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;
use DateTime;

class UserController extends Controller {

    public function createUser(Request $request): JsonResponse {

        $response = [];
        $post = $request->all();

        try {

            $user  = new User(new UserDb());
            $dateCreation = new DateTime('now');            

            if (strlen($post['auth_pin']) < 4 || strlen($post['auth_pin']) > 4)
                return response()->json(['success' => false, 'message' => 'The authentication PIN length must be 4 characters long'], 422);

            $user
                ->setName($post['name'])
                ->setEmail($post['email'])
                ->setPassword(Hash::make($post['password']))
                ->setAuthPin(Hash::make($post['auth_pin']))
                ->setDateCreatedAt($dateCreation)
            ;

            if ($user->checkAlreadyCreatedEmail())
                return response()->json(['success' => false, 'message' => 'E-mail already in use'], 409);

            $user->createUser();
            
            $response = [
                'success' => true,
                'message'=> 'User was created successfully',
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'currency' => $user->getCurrency(),
                'created_at' => $user->getDateCreatedAt()->format('Y-m-d H:i:s')
            ];
            
            return response()->json($response, 200);

        } catch(Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
        
    }

    public function getUser(Request $request, string $id): JsonResponse {

        $response = [];

        try {
            $user = new User(new UserDb());

            $user
                ->setId($id)
            ;

            $users = $user->getUser();

            $response = [
                'success' => true,
                'message' => 'User found',
                'id' => $users->getId(),
                'name' => $users->getName(),
                'email' => $users->getEmail(),
                'currency' => $users->getCurrency(),
                'created_at' => $users->getDateCreatedAt()->format('Y-m-d H:i:s')
            ];

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAll(Request $request): JsonResponse {

        $response = [];

        try {

            $user = new User(new UserDb());

            $users = $user->findAll();

            $response['sucess'] = true;
            $response['message'] = 'Users found';

            foreach($users as $user) {
                $response[] = [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'currency' => $user->getCurrency(),
                    'created_at' => $user->getDateCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}