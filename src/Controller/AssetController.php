<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\AssetClass;
use App\Form\AssetType;
use App\Repository\AssetRepository;
use App\Repository\AssetClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/assets")
 */
class AssetController extends AbstractController
{
    /**
     * @Route("/", name="asset_index", methods={"GET"})
     */
    public function index(AssetRepository $assetRepository, AssetClassRepository $assetClassRepository): Response
    {
        return $this->render('asset/index.html.twig', [
            'assets' => $assetRepository->findAll(),
            'assetClasses' => $assetClassRepository->findAllExcludingCash(),
        ]);
    }

    /**
     * @Route("/new/{slug}", name="asset_new", methods={"GET","POST"})
     */
    public function new(AssetClass $assetClass, Request $request): Response
    {
        $asset = new Asset();
        $asset->setAssetClass($assetClass);
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($asset);
            $entityManager->flush();

            return $this->redirectToRoute('asset_index');
        }

        return $this->render('asset/new.html.twig', [
            'asset' => $asset,
            'form' => $form->createView(),
        ]);
    }
}
