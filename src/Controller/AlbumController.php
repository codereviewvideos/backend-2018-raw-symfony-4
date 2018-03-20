<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use App\Serializer\FormErrorSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    /**
     * @var AlbumRepository
     */
    private $albumRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormErrorSerializer $formErrorSerializer,
        AlbumRepository $albumRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->formErrorSerializer = $formErrorSerializer;
        $this->albumRepository = $albumRepository;
    }

    /**
     * @Route("/album/{id}", name="get_album", methods={"GET"}, requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function get($id)
    {
        return new JsonResponse(
            $this->findAlbumById($id),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/album", name="cget_album", methods={"GET"})
     * @return JsonResponse
     */
    public function cget()
    {
        return new JsonResponse(
            $this->albumRepository->findAll(),
            JsonResponse::HTTP_OK
        );
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

    /**
     * @Route("/album/{id}", name="put_album", methods={"PUT"}, requirements={"id": "\d+"})
     */
    public function put(Request $request, $id)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $existingAlbum = $this->findAlbumById($id);

        $form = $this->createForm(AlbumType::class, $existingAlbum);

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

        $this->entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }


    /**
     * @Route("/album/{id}", name="patch_album", methods={"PATCH"}, requirements={"id": "\d+"})
     */
    public function patch(Request $request, $id)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $existingAlbum = $this->findAlbumById($id);

        $form = $this->createForm(AlbumType::class, $existingAlbum);

        $form->submit($data, false);

        if (false === $form->isValid()) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'errors' => $this->formErrorSerializer->convertFormToArray($form),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }


    /**
     * @Route("/album/{id}", name="delete_album", methods={"DELETE"}, requirements={"id": "\d+"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $existingAlbum = $this->findAlbumById($id);

        $this->entityManager->remove($existingAlbum);
        $this->entityManager->flush();

        return new JsonResponse(
            null,
            JsonResponse::HTTP_NO_CONTENT
        );
    }


    private function findAlbumById($id)
    {
        $album = $this->albumRepository->find($id);

        if (null === $album) {
            throw new NotFoundHttpException();
        }

        return $album;
    }
}
