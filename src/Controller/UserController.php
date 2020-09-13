<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route(path="/user")
 */
class UserController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/add", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request): JsonResponse
    {
        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $email = $request->request->get('email');
        $phoneNumber = $request->request->get('phoneNumber');

        if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->userRepository->saveUser($firstName, $lastName, $email, $phoneNumber);

        return new JsonResponse(['status' => 'Customer added!'], Response::HTTP_CREATED);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/get/{id}", name="get_one_user", methods={"GET"})
     */
    public function getOneUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (empty($user))
            return new JsonResponse(["status" => "no find user"], Response::HTTP_NOT_FOUND);
        $data = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'phoneNumber' => $user->getPhoneNumber(),
        ];

        return new JsonResponse(['user' => $data], Response::HTTP_OK);
    }

    /**
     * @return JsonResponse
     * @Route("/", name="get_all_user", methods={"GET"})
     */
    public function getAllUsers(): JsonResponse
    {
        $customers = $this->userRepository->findAll();
        $data = [];

        foreach ($customers as $customer) {
            $data[] = [
                'id' => $customer->getId(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'phoneNumber' => $customer->getPhoneNumber(),
            ];
        }
        return new JsonResponse(['users' => $data], Response::HTTP_OK);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @Route("/update/{id}", name="update_user", methods={"PUT"})
     */
    public function updateUser($id, Request $request): JsonResponse
    {
        dd($request->request->all());
        $customer = $this->userRepository->findOneBy(['id' => $id]);
        $data = json_encode($request->request->all(), true);
        $this->userRepository->updateUser($customer, $data);

        return new JsonResponse(['status' => 'user updated!']);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @Route("/delete/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser($id): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if (empty($user))
            return new JsonResponse(["status" => "no find user"], Response::HTTP_NOT_FOUND);
        $this->userRepository->removeUser($user);

        return new JsonResponse(['status' => 'user deleted']);
    }
}