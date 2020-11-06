<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Company;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/company/offer")
 */
class OfferController extends AbstractController
{
    /**
     * company_id field here is got
     * by the user section
     * $this->getuser
     */

    /**
     * @Route("/", name="offer_index", methods={"GET"})
     */
    public function index(OfferRepository $offerRepository, EntityManagerInterface $entityManager): Response
    {
        // TODO: use the Offer repository
        $company = $entityManager->getRepository(Company::class)->findOneBy(
            [
                'owner' => $this->getUser(),
            ]
        );

        $offers = $entityManager->getRepository(Offer::class)->findBy(
            [
                'company' => $company
            ]
        );
        return $this->render('offer/index.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/new", name="offer_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $offer->setCompany($entityManager->getRepository(Company::class)->findOneBy(
                [
                    'owner' => $this->getUser(),
                ]
            ));
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('offer_index');
        }

        return $this->render('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offer_show", methods={"GET"})
     */
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="offer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Offer $offer): Response
    {
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offer_index');
        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Offer $offer): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offer_index');
    }
}
