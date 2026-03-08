<?php

namespace App\Model;

use App\Entity\Accessories;
use App\Entity\Position;
use App\Enum\IntensityEnum;
use App\Repository\AccessoriesRepository;
use App\Repository\PositionRepository;

class Game
{
    /** @var int[] */
    private array $selectedAccessoryIds = [];

    /** @var array<IntensityEnum, int[]> */
    private array $positionIdsByIntensity = [];

    /** @var array<IntensityEnum, int> */
    private array $intensityQuota = [];

    /** PositionRepository et AccessoriesRepository ne sont pas sérialisables */
    private ?PositionRepository $positionRepository = null;
    private ?AccessoriesRepository $accessoriesRepository = null;

    public function __construct(
        ?PositionRepository $positionRepository = null,
        ?AccessoriesRepository $accessoriesRepository = null
    ) {
        $this->positionRepository = $positionRepository;
        $this->accessoriesRepository = $accessoriesRepository;
    }

    public function setSelectedAccessories(array $accessories): void
    {
        $this->selectedAccessoryIds = array_map(fn(Accessories $a) => $a->getId(), $accessories);
    }

    public function setIntensityQuota(array $quota): void
    {
        $this->intensityQuota = $quota;
    }

    public function initialize(): void
    {
        $allPositions = $this->positionRepository->findAll();

        foreach ($allPositions as $position) {
            $intensity = $position->getIntensity();
            $accessories = $position->getAccessories()->map(fn($a) => $a->getId())->toArray();

            if (count($accessories) === 0 || $this->accessoriesAreCompatible($accessories)) {
                $this->positionIdsByIntensity[$intensity->value][] = $position->getId();
            }
        }

        foreach ($this->positionIdsByIntensity as $intensity => &$ids) {
            shuffle($ids);
            if (isset($this->intensityQuota[$intensity])) {
                $ids = array_slice($ids, 0, $this->intensityQuota[$intensity]);
            }
        }
    }

    public function drawNextPosition(): ?Position
    {
        foreach (IntensityEnum::cases() as $intensity) {
            if (!empty($this->positionIdsByIntensity[$intensity->value])) {
                $id = array_shift($this->positionIdsByIntensity[$intensity->value]);
                return $this->positionRepository?->find($id);
            }
        }

        return null;
    }

    public function toArray(): array
    {
        // Transformer les clés enum en string pour sérialisation
        $formattedQuota = [];
        foreach ($this->intensityQuota as $enum => $count) {
                $formattedQuota[$enum] = $count;
        }

        // Même chose si nécessaire pour positionIdsByIntensity
        $formattedPositions = [];
        foreach ($this->positionIdsByIntensity as $enum => $ids) {
                $formattedPositions[$enum] = array_values($ids);
        }

        return [
            'selectedAccessoryIds' => $this->selectedAccessoryIds,
            'positionIdsByIntensity' => $formattedPositions,
            'intensityQuota' => $formattedQuota,
        ];
    }


    public static function fromArray(
        array $data,
        PositionRepository $positionRepository,
        AccessoriesRepository $accessoriesRepository
    ): self {
        $game = new self($positionRepository, $accessoriesRepository);
        $game->selectedAccessoryIds = $data['selectedAccessoryIds'] ?? [];

        foreach ($data['intensityQuota'] ?? [] as $key => $count) {
            $game->intensityQuota[IntensityEnum::from($key)->value] = $count;
        }

        foreach ($data['positionIdsByIntensity'] ?? [] as $key => $ids) {
            $game->positionIdsByIntensity[IntensityEnum::from($key)->value] = $ids;
        }

        return $game;
    }

    public function getSelectedAccessories(): array
    {
        return $this->accessoriesRepository
            ? $this->accessoriesRepository->findBy(['id' => $this->selectedAccessoryIds])
            : [];
    }

    private function accessoriesAreCompatible(array $required): bool
    {
        return empty(array_diff($required, $this->selectedAccessoryIds));
    }
}
