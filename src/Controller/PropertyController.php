<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use App\Service\Mailer;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/biens")
 */
class PropertyController extends AbstractController
{
    /**
     * @Route("/", name="property_index", methods={"GET"})
     * @param PropertyRepository $propertyRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function index(PropertyRepository $propertyRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $propertiesNotSoldQuery = $propertyRepository->getPropertiesNotSoldQuery($search);

        $pagination = $paginator->paginate(
            $propertiesNotSoldQuery, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 ,/*limit per page*/
            [
                'align' => 'center',
                'size' => 'medium',
                'rounded' => true,
            ]
        );

        return $this->render('property/index.html.twig', [
            'properties' => $pagination,
            'count' => $propertyRepository->my_count($search),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{slug}-{id}", name="property_show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param Request $request
     * @param Property $property
     * @param string $slug
     * @param Mailer $mailer
     * @return Response
     */
    public function show(Request $request,Property $property, string $slug, Mailer $mailer): Response
    {
        if ($property->getSlug() !== $slug){
            return $this->redirectToRoute('property_show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug(),
            ], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $bodyMail = $mailer->createBodyMail('contact/mail.html.twig', [
                'contact' => $contact
            ]);

            $mailer->sendMessage($contact->getEmail(), 'louaraym@gmail.com','Contact agence', $bodyMail);
            $this->addFlash('success','Votre message a bien été envoyé');
            return $this->redirectToRoute('property_show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug(),
            ]);
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'form' => $form->createView(),
        ]);
    }
}
