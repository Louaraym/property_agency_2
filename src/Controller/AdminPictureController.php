<?php

namespace App\Controller;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPictureController extends AbstractController
{
    /**
     * @Route("/admin/picture/{id}", name="admin_picture_delete", methods= "DELETE")
     * @param Picture $picture
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws JsonException
     */
    public function delete(Picture $picture, Request $request, EntityManagerInterface $manager): Response
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])){
            $manager->remove($picture);
            $manager->flush();
            $this->addFlash('success', 'Votre suppression a été effectuée avec succès !');
            return  new JsonResponse(['success' => 1]);
        }

        return  new JsonResponse(['error' => 'Token invalide'], 400);

    }
}
