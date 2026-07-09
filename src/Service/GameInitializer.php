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

    public function quickFireGameInitialize(array $selectedAccessoryIds = [], string $gender1 = 'm', string $gender2 = 'f'): Game
    {
        $game = new Game($this->positionRepository, $this->accessoriesRepository);

        $selectedAccessories = $selectedAccessoryIds
            ? $this->accessoriesRepository->findBy(['id' => $selectedAccessoryIds])
            : [];
        $game->setSelectedAccessories($selectedAccessories);
        $game->setPlayerGenders($gender1, $gender2);
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
