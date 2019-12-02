<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\AssetClass;
use App\Form\AssetType;
use App\Repository\AssetRepository;
use App\Repository\AssetClassRepository;
use App\Form\SearchType;
use App\Util\IexCloud\IexCloudClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $form = $this->createForm(SearchType::class, null, [
          'action' => $this->generateUrl('asset_search'),
          'search_url' => $this->generateUrl('asset_ajax_search'),
      ]);

        return $this->render('asset/index.html.twig', [
            'assets' => $assetRepository->findAll(),
            'assetClasses' => $assetClassRepository->findAllExcludingCash(),
            'form' => $form->createView(),
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

    /**
     * @Route("/{symbol}", name="asset_show", methods={"GET"})
     */
    public function show(string $symbol, AssetRepository $assetRepository, IexCloudClient $client): Response
    {
        $quote = $client
            ->stock($symbol)
            ->quote()
            ->send()
        ;

        $asset = $assetRepository->findOneBySymbol($symbol);

        return $this->render('asset/show.html.twig', [
            'quote' => $quote,
            'asset' => $asset,
        ]);
    }

    /**
     * @Route("/search", name="asset_search", methods={"POST"})
     */
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute('asset_show', ['symbol' => $data['symbol']]);
        }
    }

    /**
     * @Route("/ajax-search", name="asset_ajax_search", methods={"POST"})
     */
    public function ajaxSearch(IexCloudClient $client, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $query = $request->request->get('query');
            $search = $client
            ->search($query)
            ->send()
        ;

            $results = [];
            $suggestions = [];
            foreach ($search as $item) {
                $results[] = $item['symbol'];
                $suggestions[$item['symbol']] = $item['securityName'] . ' (' . $item['symbol'] . ')';
            }

            $data = [
          'results' => $results,
          'suggestions' => $suggestions,
        ];
            return new JsonResponse($data);
        }

        return new Response('This is not an AJAX request.', 400);
    }
}
