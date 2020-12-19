<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @param PropertyRepository $propertyRepository
     * @return Response
     * @throws Exception
     */
    public function index(PropertyRepository $propertyRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'properties' => $propertyRepository->findAlreadySold(false,'desc',8),
        ]);
    }
}
