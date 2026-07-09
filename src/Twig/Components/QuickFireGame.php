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
    public int $total = 0;
    public string $player1 = 'Joueur 1';
    public string $player2 = 'Joueur 2';
    public string $gender1 = 'm';
    public string $gender2 = 'f';
    public function __construct(private readonly GameInitializer $gameInitializer,
                                private RequestStack $requestStack,
    private readonly PositionRepository $positionRepository,
    private readonly AccessoriesRepository $accessoriesRepository,){

    }
    public function mount(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $gameData = $session->get('game');

        $gender1 = $session->get('gender1', 'm');
        $gender2 = $session->get('gender2', 'f');

        if ($gameData) {
            $this->game = Game::fromArray($gameData, $this->positionRepository, $this->accessoriesRepository);
        } else {
            $selectedIds = $session->get('selected_accessories', []);
            $this->game = $this->gameInitializer->quickFireGameInitialize($selectedIds, $gender1, $gender2);
        }

        $this->total = $this->game->getTotal();
        $this->position = $this->game->drawNextPosition();
        $this->count = $session->get('count', 0) + 1;
        $this->player1 = $session->get('player1', 'Joueur 1');
        $this->player2 = $session->get('player2', 'Joueur 2');
        $this->gender1 = $gender1;
        $this->gender2 = $gender2;
        $this->saveGameToSession();
    }

    #[LiveAction]
    public function next(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $gameData = $session->get('game');
        $gender1 = $session->get('gender1', 'm');
        $gender2 = $session->get('gender2', 'f');

        if ($gameData) {
            $this->game = Game::fromArray($gameData, $this->positionRepository, $this->accessoriesRepository);
        } else {
            $this->game = $this->gameInitializer->quickFireGameInitialize([], $gender1, $gender2);
            $session->set('game', $this->game->toArray());
        }

        $this->total = $this->game->getTotal();
        $this->position = $this->game->drawNextPosition();
        $this->player1 = $session->get('player1', 'Joueur 1');
        $this->player2 = $session->get('player2', 'Joueur 2');
        $this->gender1 = $gender1;
        $this->gender2 = $gender2;
        if ($this->position === null) {
            $this->dispatchBrowserEvent('removeCountdown');
        } else {
            $this->count = $session->get('count', 0) + 1;
            $this->dispatchBrowserEvent('countdownUpdate', ['duration' => $this->position->getEffectiveDuration()]);
        }

        $this->saveGameToSession();
    }

    #[LiveAction]
    public function reset(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $selectedIds = $session->get('selected_accessories', []);
        $this->player1 = $session->get('player1', 'Joueur 1');
        $this->player2 = $session->get('player2', 'Joueur 2');
        $this->gender1 = $session->get('gender1', 'm');
        $this->gender2 = $session->get('gender2', 'f');
        $this->game = $this->gameInitializer->quickFireGameInitialize($selectedIds, $this->gender1, $this->gender2);
        $this->total = $this->game->getTotal();
        $this->position = $this->game->drawNextPosition();
        $this->count = 1;
        if ($this->position === null) {
            $this->dispatchBrowserEvent('removeCountdown');
        } else {
            $this->dispatchBrowserEvent('countdownUpdate', ['duration' => $this->position->getEffectiveDuration()]);
        }
        $this->saveGameToSession();
    }

    private function saveGameToSession(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set('game', $this->game->toArray());
        $session->set('count', $this->count);
    }

    #[LiveListener('countdownASEnded')]
    public function countdownEnd(): void
    {

        $this->next();
    }

}
