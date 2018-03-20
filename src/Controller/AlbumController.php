<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Serializer\FormErrorSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormErrorSerializer
     */
    private $formErrorSerializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormErrorSerializer $formErrorSerializer
    )
    {
        $this->entityManager = $entityManager;
        $this->formErrorSerializer = $formErrorSerializer;
    }

    /**
     * @Route("/album", name="post_album", methods={"POST"})
     */
    public function post(
        Request $request
    )
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $form = $this->createForm(AlbumType::class, new Album());

        $form->submit($data);

        if (false === $form->isValid()) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'errors' => $this->formErrorSerializer->convertFormToArray($form),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'status' => 'ok',
            ],
            JsonResponse::HTTP_CREATED
        );
    }
}
