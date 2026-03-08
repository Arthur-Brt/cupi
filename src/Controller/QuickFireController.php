<?php

namespace App\Controller;

use App\Enum\IntensityEnum;
use App\Model\Game;
use App\Repository\AccessoriesRepository;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuickFireController extends AbstractController
{
    public function __construct(private readonly PositionRepository $positionRepository,
                                private readonly AccessoriesRepository $accessoriesRepository,
                                private RequestStack $requestStack){

    }

    #[Route('/quick/fire', name: 'app_quick_fire')]
    public function index(): Response
    {
        return $this->render('quick_fire/index.html.twig', [
            'controller_name' => 'QuickFireController',
        ]);
    }
}
