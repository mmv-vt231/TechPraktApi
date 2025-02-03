<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
class TestController extends AbstractController
{

    public const USERS_DATA = [
      [
          'id'    => '1',
          'email' => 'random1@example.com',
          'name'  => 'Alice'
      ],
      [
          'id'    => '2',
          'email' => 'random2@example.com',
          'name'  => 'Bob'
      ],
      [
          'id'    => '3',
          'email' => 'random3@example.com',
          'name'  => 'Charlie'
      ],
      [
          'id'    => '4',
          'email' => 'random4@example.com',
          'name'  => 'Diana'
      ],
      [
          'id'    => '5',
          'email' => 'random5@example.com',
          'name'  => 'Ethan'
      ],
      [
          'id'    => '6',
          'email' => 'random6@example.com',
          'name'  => 'Fiona'
      ],
      [
          'id'    => '7',
          'email' => 'random7@example.com',
          'name'  => 'George'
      ],
    ];

    #[Route('/users', name: 'app_collection_users', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getUsers(): JsonResponse
    {
        return new JsonResponse([
            'data' => self::USERS_DATA
        ], Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'app_item_users', methods: ['GET'])]
    public function getUserById(string $id): JsonResponse
    {
        $userData = $this->findUserById($id);

        return new JsonResponse([
            'data' => $userData
        ], Response::HTTP_OK);
    }

    #[Route('/users', name: 'app_create_users', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['email'], $requestData['name'])) {
            throw new UnprocessableEntityHttpException("name and email are required");
        }

        $countOfUsers = count(self::USERS_DATA);

        $newUser = [
            'id'    => $countOfUsers + 1,
            'name'  => $requestData['name'],
            'email' => $requestData['email']
        ];

        return new JsonResponse([
            'data' => $newUser
        ], Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'app_delete_users', methods: ['DELETE'])]
    public function deleteUser(string $id): JsonResponse
    {
        $this->findUserById($id);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', name: 'app_update_users', methods: ['PATCH'])]
    public function updateUser(string $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['name'])) {
            throw new UnprocessableEntityHttpException("name is required");
        }

        $userData = $this->findUserById($id);

        $userData['name'] = $requestData['name'];

        return new JsonResponse(['data' => $userData], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return string[]
     */
    public function findUserById(string $id): array
    {
        $userData = null;

        foreach (self::USERS_DATA as $user) {
            if (!isset($user['id'])) {
                continue;
            }

            if ($user['id'] == $id) {
                $userData = $user;

                break;
            }

        }

        if (!$userData) {
            throw new NotFoundHttpException("User with id " . $id . " not found");
        }

        return $userData;
    }

}
