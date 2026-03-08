<?php

namespace App\Controller;

use App\Repository\AccessoriesRepository;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuickFireController extends AbstractController
{
    public function __construct(private readonly PositionRepository $positionRepository,
                                private readonly AccessoriesRepository $accessoriesRepository,
                                private RequestStack $requestStack){

    }

    #[Route('/quick/fire/setup', name: 'app_quick_fire_setup')]
    public function setup(Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isMethod('POST')) {
            $selectedIds = array_map('intval', $request->request->all('accessories'));
            $session->set('selected_accessories', $selectedIds);
            $session->set('player1', trim($request->request->getString('player1')) ?: 'Joueur 1');
            $session->set('player2', trim($request->request->getString('player2')) ?: 'Joueur 2');
            $session->remove('game');
            $session->remove('count');

            return $this->redirectToRoute('app_quick_fire');
        }

        $accessories = $this->accessoriesRepository->findAll();
        $selectedIds = $session->get('selected_accessories', []);

        return $this->render('quick_fire/setup.html.twig', [
            'accessories' => $accessories,
            'selectedIds' => $selectedIds,
            'player1'     => $session->get('player1', ''),
            'player2'     => $session->get('player2', ''),
        ]);
    }

    #[Route('/quick/fire', name: 'app_quick_fire')]
    public function index(): Response
    {
        return $this->render('quick_fire/index.html.twig');
    }
}
