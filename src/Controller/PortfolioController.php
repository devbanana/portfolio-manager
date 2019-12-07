<?php

declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\PortfolioRepository;
use App\Repository\PortfolioHoldingRepository;
use App\Entity\Portfolio;
use App\Form\PortfolioType;

class PortfolioController extends AbstractController
{
    /**
     * @Route("/", name="portfolio_index")
     */
    public function index(PortfolioRepository $repository, PortfolioHoldingRepository $phRepository): Response
    {
        return $this->render('portfolio/index.html.twig', [
            'portfolios' => $repository->findAll(),
            'balance' => $phRepository->getBalance(),
        ]);
    }

    /**
     * @Route("/portfolios/new", name="portfolio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $portfolio = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($portfolio);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Portfolio has been created.'
            );

            return $this->redirectToRoute('portfolio_index');
        }

        return $this->render('portfolio/new.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form->createView(),
            'button_label' => 'Add',
        ]);
    }
}
