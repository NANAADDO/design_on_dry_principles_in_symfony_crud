<?php

namespace App\Controller;

use App\BaseController;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Serializer\SerializerInterface;

class StatusController extends BaseController
{

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->modelRepository = $this->entityManager->getRepository(Status::class);
        $this->entityModel = new Status();
        $this->serializer = $serializer;

    }
}
