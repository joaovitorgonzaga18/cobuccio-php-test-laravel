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

    private const NAME_MAX_LENGTH = 100;
    private const EMAIL_MAX_LENGTH = 100;
    private const PASSWORD_MAX_LENGTH = 100;
    private const AUTH_PIN_LENGTH = 4;

    public function createUser(Request $request): JsonResponse {

        $response = [];
        $post = $request->all();

        try {

            $user  = new User(new UserDb());
            $dateCreation = new DateTime('now');            

            if (strlen($post['name']) > self::NAME_MAX_LENGTH)
                return $this->buildResponse(['success' => false, 'message' => 'The name maximum length is ' . self::NAME_MAX_LENGTH . ' characters long'], 422);

            if (strlen($post['email']) > self::EMAIL_MAX_LENGTH)
                return $this->buildResponse(['success' => false, 'message' => 'The e-mail maximum length is ' . self::EMAIL_MAX_LENGTH . ' characters long'], 422);
                
            if (strlen($post['password']) > self::PASSWORD_MAX_LENGTH)
                return $this->buildResponse(['success' => false, 'message' => 'The password maximum length is ' . self::PASSWORD_MAX_LENGTH . ' characters long'], 422);

            if (strlen($post['auth_pin']) < self::AUTH_PIN_LENGTH || strlen($post['auth_pin']) > self::AUTH_PIN_LENGTH)
                return $this->buildResponse(['success' => false, 'message' => 'The authentication PIN length must be ' . self::AUTH_PIN_LENGTH . ' characters long'], 422);

            if (!ctype_digit($post['auth_pin']))
                return $this->buildResponse(['success' => false, 'message' => 'Invalid Authentication PIN'], 422);

            $user
                ->setName($post['name'])
                ->setEmail($post['email'])
                ->setPassword(Hash::make($post['password']))
                ->setAuthPin(Hash::make($post['auth_pin']))
                ->setDateCreatedAt($dateCreation)
            ;

            if ($user->checkAlreadyCreatedEmail())
                return $this->buildResponse(['success' => false, 'message' => 'E-mail already in use'], 409);

            $user->createUser();
            
            $response = [
                'success' => true,
                'message'=> 'User was created successfully',
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'currency' => $user->getCurrency(),
                'created_at' => $user->getDateCreatedAt()->format('Y-m-d H:i:s')
            ];
            
            return $this->buildResponse($response, 200);

        } catch(Exception $e) {
            return $this->buildResponse(['success' => false, 'message' => $e], 400);
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

            if (!$user->checkIfExist())
                return $this->buildResponse(['success' => false, 'message' => 'User not found'], 404);

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

            return $this->buildResponse($response, 200);

        } catch (Exception $e) {
            return $this->buildResponse(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAll(Request $request): JsonResponse {

        $response = [];

        try {

            $user = new User(new UserDb());

            $users = $user->findAll();

            if (count($users) == 0)                
                return $this->buildResponse(['success' => false, 'message' => 'No Users found'], 404);

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
            return $this->buildResponse(['success' => false, 'message' => $e], 400);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}