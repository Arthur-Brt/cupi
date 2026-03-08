<?php

namespace App\Service;

use App\Enum\IntensityEnum;
use App\Model\Game;
use App\Repository\AccessoriesRepository;
use App\Repository\PositionRepository;

class GameInitializer
{

    public function __construct(private readonly PositionRepository $positionRepository,
    private readonly AccessoriesRepository $accessoriesRepository
    )
    {
    }

    public function quickFireGameInitialize(): Game
    {
        $game = new Game($this->positionRepository, $this->accessoriesRepository);

        //TODO créer un form pour selectionner les accesoires
        $selectedAccessories = [];
        $game->setSelectedAccessories($selectedAccessories);
        $game->setIntensityQuota([
            IntensityEnum::WARMUP->value => 2,
            IntensityEnum::DESIRE->value => 3,
            IntensityEnum::SPARK->value => 3,
            IntensityEnum::FIRE->value => 2,
            IntensityEnum::ERUPTION->value => 1,
        ]);

        $game->initialize();

        return $game;
    }

}
