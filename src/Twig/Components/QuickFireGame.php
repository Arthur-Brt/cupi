<?php

namespace App\Twig\Components;

use App\Entity\Position;
use App\Enum\IntensityEnum;
use App\Model\Game;
use App\Repository\AccessoriesRepository;
use App\Repository\PositionRepository;
use App\Service\GameInitializer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class QuickFireGame
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    public Game $game;
    public ?Position $position = null;
    public int $count = 0;
    public function __construct(private readonly GameInitializer $gameInitializer,
                                private RequestStack $requestStack,
    private readonly PositionRepository $positionRepository,
    private readonly AccessoriesRepository $accessoriesRepository,){

    }
    public function mount(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $gameData = $session->get('game');

        if ($gameData) {
            $this->game = Game::fromArray($gameData, $this->positionRepository, $this->accessoriesRepository);
        } else {
            $this->game = $this->gameInitializer->quickFireGameInitialize();
            $session->set('game', $this->game->toArray());
        }

        $this->position = $this->game->drawNextPosition();
        $storedCount = $session->get('count', 0);
        $this->count = $storedCount;
        $this->saveGameToSession();
    }

    #[LiveAction]
    public function next(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $gameData = $session->get('game');

        if ($gameData) {
            $this->game = Game::fromArray($gameData, $this->positionRepository, $this->accessoriesRepository);
        } else {
            $this->game = $this->gameInitializer->quickFireGameInitialize();
            $session->set('game', $this->game->toArray());
        }

        $this->position = $this->game->drawNextPosition();
        if($this->position === null){
            $this->dispatchBrowserEvent('removeCountdown');
        }else{
            $this->dispatchBrowserEvent('countdownUpdate');
        }



        // sauvegarder l'état modifié du jeu en session
        $this->saveGameToSession();
    }

    #[LiveAction]
    public function reset(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $this->game = $this->gameInitializer->quickFireGameInitialize();
        $session->set('game', $this->game->toArray());
        $this->position = $this->game->drawNextPosition();
        if($this->position === null){
            $this->dispatchBrowserEvent('removeCountdown');
        }else{
            $this->dispatchBrowserEvent('countdownUpdate');
        }
        $this->saveGameToSession();
    }

    private function saveGameToSession(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set('game', $this->game->toArray());
        //le countdown doit etre reinitialiser

    }

    #[LiveListener('countdownASEnded')]
    public function countdownEnd(): void
    {

        $this->next();
    }

}
