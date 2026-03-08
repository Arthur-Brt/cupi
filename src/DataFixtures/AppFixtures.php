<?php

namespace App\DataFixtures;

use App\Entity\Accessories;
use App\Entity\Position;
use App\Enum\IntensityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var Accessories[] $accessories */
        $accessories = [];


        // 1. Créer 10 accessoires avec des fausses images
        for ($i = 0; $i < 10; $i++) {
            $accessory = new Accessories();
            $accessory->setName($faker->word);

            $imageContent = file_get_contents('https://placehold.com/640x480?text=Accessory');
            $filePath = sys_get_temp_dir() . '/accessory_' . uniqid() . '.png';
            file_put_contents($filePath, $imageContent);

            $imageFile = new UploadedFile(
                $filePath,
                basename($filePath),
                'image/png',
                null,
                true
            );

            $accessory->setImageUpload($imageFile);
            $manager->persist($accessory);
            $accessories[] = $accessory;
        }

        // 2. Créer 100 positions : 20 par intensité
        $intensities = IntensityEnum::cases();
        $positionCount = 0;

        foreach ($intensities as $intensity) {
            for ($i = 0; $i < 20; $i++) {
                $position = new Position();
                $position->setName($faker->words(3, true));
                $position->setDescription($faker->sentence());
                $position->setIntensity($intensity);

                // Lier un accessoire à la moitié des positions
                if ($positionCount < 50) {
                    $randAccessories = $faker->randomElements($accessories, random_int(1, 3)); // entre 1 et 3 accessoires
                    foreach ($randAccessories as $acc) {
                        $position->addAccessory($acc);
                    }
                }

                // Générer une fausse image
                $imageContent = file_get_contents('https://placehold.co/640x480?text=Position');
                $filePath = sys_get_temp_dir() . '/position_' . uniqid() . '.png';
                file_put_contents($filePath, $imageContent);

                $imageFile = new UploadedFile(
                    $filePath,
                    basename($filePath),
                    'image/png',
                    null,
                    true
                );

                $position->setImageUpload($imageFile);

                $manager->persist($position);
                $positionCount++;
            }
        }

        $manager->flush();
    }
}
