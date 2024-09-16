<?php

namespace App;

use App\constant\DataTypes;
use App\constant\StatusCodes;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Traits\ApiResTypes;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController
{

    use ApiResTypes;

    public $entityManager;
    public $serializer;
    public $modelRepository;
    public $entityModel;


    public function index(): JsonResponse
    {
        $records = $this->modelRepository->findAll();
        return $this->Ok($records);
    }

    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $requestData = $request->getContent();
        $modelData = $this->serializer->deserialize($requestData, $this->entityModel::class, DataTypes::JSON);
        $errors = $validator->validate($modelData);
        if (count($errors)) {
            return $this->ValidationError($errors);
        }
        $this->entityManager->persist($modelData);
        try {
            $this->entityManager->flush();
            return $this->ObjectCreated($modelData);
        } catch (Exception $e) {
            return $this->customError($e->getMessage(), StatusCodes::INTERNAL_SERVER_ERROR);

        }

    }

    public function edit($id): JsonResponse
    {
        $records = $this->modelRepository->findOneBy([ 'id' => $id ]);
        if (empty($records)) {
            return $this->NotFound();
        }
        return $this->json($records, StatusCodes::OK);

    }

    public function update(Request $request, ValidatorInterface $validator, $id): JsonResponse
    {
        $requestData = $request->getContent();
        $records = $this->modelRepository->find([ 'id' => $id ]);
        if (empty($records)) {
            return $this->NotFound();
        }
        $modelData = $this->serializer->deserialize($requestData, $this->entityModel::class, DataTypes::JSON);
        $errors = $validator->validate($modelData);
        if (count($errors)) {
            return $this->ValidationError($errors);
        }
        $response = $this->modelRepository->update($records, $modelData);

        if ($response == "true") {
            return $this->Ok($modelData);
        }
        return $this->customError($response, StatusCodes::INTERNAL_SERVER_ERROR);


    }

    public function delete($id): JsonResponse
    {
        $records = $this->modelRepository->find([ 'id' => $id ]);
        if (empty($records)) {
            return $this->NotFound();
        }
        $this->entityManager->remove($records);
        try {
            $this->entityManager->flush();
            return $this->NoContent();
        } catch (Exception $e) {
            return $this->customError($e->getMessage(), StatusCodes::INTERNAL_SERVER_ERROR);


        }
    }
}
