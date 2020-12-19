<?php

namespace App\Controller;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/biens")
 */
class AdminPropertyController extends AbstractController
{
    /**
     * @Route("/", name="admin_property_index", methods={"GET"})
     * @param PropertyRepository $propertyRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $propertyRepository->getPropertiesQuery(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 ,/*limit per page*/
            [
                'align' => 'center',
                'size' => 'medium',
                'rounded' => true,
            ]
        );
        return $this->render('admin_property/index.html.twig', [
            'properties' => $pagination,
            'count' => $propertyRepository->my_dql_count(),
        ]);
    }

    /**
     * @Route("/new", name="admin_property_new", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($property);
            $entityManager->flush();
            $this->addFlash('success','Ajout efféctué avec succès');
            return $this->redirectToRoute('admin_property_index');
        }

        return $this->render('admin_property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{slug}-{id}", name="admin_property_edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param Request $request
     * @param Property $property
     * @param string $slug
     * @return Response
     */
    public function edit(Request $request, Property $property, string $slug): Response
    {
        if ($property->getSlug() !== $slug){
            return $this->redirectToRoute('admin_property_edit', [
                'id' => $property->getId(),
                'slug' => $property->getSlug(),
            ], 301);
        }

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Modification efféctuée avec succès');
            return $this->redirectToRoute('admin_property_index');
        }

        return $this->render('admin_property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_property_delete", methods={"DELETE"})
     * @param Request $request
     * @param Property $property
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Property $property, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$property->getId(), $request->request->get('_token'))) {
            $entityManager->remove($property);
            $entityManager->flush();
            $this->addFlash('success','Suppression efféctuée avec succès');
        }

        return $this->redirectToRoute('admin_property_index');
    }
}

