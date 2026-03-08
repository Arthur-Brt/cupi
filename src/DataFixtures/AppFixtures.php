<?php

namespace App\DataFixtures;

use App\Entity\Accessories;
use App\Entity\Position;
use App\Enum\IntensityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ── Accessoires ──────────────────────────────────────────────────────
        $accessoryData = [
            'Menottes',
            'Bandeau',
            'Plume',
            'Huile de massage',
            'Glaçons',
            'Bougie de massage',
        ];

        /** @var Accessories[] $accessoriesByName */
        $accessoriesByName = [];

        foreach ($accessoryData as $name) {
            $accessory = new Accessories();
            $accessory->setName($name);
            $manager->persist($accessory);
            $accessoriesByName[$name] = $accessory;
        }

        $menottes       = $accessoriesByName['Menottes'];
        $bandeau        = $accessoriesByName['Bandeau'];
        $plume          = $accessoriesByName['Plume'];
        $huile          = $accessoriesByName['Huile de massage'];
        $glacons        = $accessoriesByName['Glaçons'];
        $bougie         = $accessoriesByName['Bougie de massage'];

        // ── Positions ─────────────────────────────────────────────────────────
        // Format : [nom, description, intensité, accessoires requis[], durée en secondes]
        $positionsData = [

            // ── WARMUP ────────────────────────────────────────────────────────
            [
                'Le Câlin cuillère',
                'Allongés l\'un contre l\'autre en cuillère, caresses lentes sur les bras et le dos. Aucune parole, juste la chaleur des corps.',
                IntensityEnum::WARMUP,
                [],
                60,
            ],
            [
                'Le Massage nuque',
                'L\'un masse lentement la nuque et les épaules de l\'autre pendant toute la durée du compte à rebours.',
                IntensityEnum::WARMUP,
                [],
                60,
            ],
            [
                'Le Frôlement',
                'Allongés face à face, effleurez-vous mutuellement du bout des doigts sans jamais appuyer. Visage, cou, bras.',
                IntensityEnum::WARMUP,
                [],
                45,
            ],
            [
                'Le Regard profond',
                'Assis face à face en tailleur, genoux qui se touchent. Yeux dans les yeux, respirez ensemble sans parler.',
                IntensityEnum::WARMUP,
                [],
                60,
            ],
            [
                'Les Souffles',
                'L\'un souffle doucement dans le cou, derrière l\'oreille et sur la nuque de l\'autre, sans jamais toucher.',
                IntensityEnum::WARMUP,
                [],
                30,
            ],
            [
                'Le Massage à la plume',
                'L\'un allongé sur le ventre, les yeux fermés. L\'autre lui passe lentement la plume sur tout le dos et les bras.',
                IntensityEnum::WARMUP,
                [$plume],
                60,
            ],

            // ── DESIRE ────────────────────────────────────────────────────────
            [
                'Le Baiser profond',
                'Face à face debout, mains dans les cheveux de l\'autre, long baiser sans limite de durée. Les yeux restent fermés.',
                IntensityEnum::DESIRE,
                [],
                60,
            ],
            [
                'L\'Exploration',
                'L\'un allongé, les yeux fermés. L\'autre l\'explore lentement avec les mains uniquement, des pieds jusqu\'au cou.',
                IntensityEnum::DESIRE,
                [],
                90,
            ],
            [
                'Le Face-à-face assis',
                'Assis face à face, jambes entrelacées, regards dans les yeux. Caresses mutuelles du visage et des mains.',
                IntensityEnum::DESIRE,
                [],
                60,
            ],
            [
                'Le Baiser interdit',
                'Baisers sur tout le corps sauf les lèvres pendant toute la durée. Qui craquera le premier ?',
                IntensityEnum::DESIRE,
                [],
                60,
            ],
            [
                'L\'Emprise douce',
                'L\'un tient le visage de l\'autre entre ses mains et pose un baiser très lent sur chaque paupière, le front, le nez, les joues, puis les lèvres.',
                IntensityEnum::DESIRE,
                [],
                45,
            ],
            [
                'Le Massage aux huiles',
                'L\'un allongé, l\'autre masse lentement le dos et les jambes avec l\'huile chauffée dans les mains.',
                IntensityEnum::DESIRE,
                [$huile],
                90,
            ],

            // ── SPARK ─────────────────────────────────────────────────────────
            [
                'La Domination douce',
                'L\'un maintient doucement les poignets de l\'autre au-dessus de sa tête contre le lit. Baisers dans le cou pendant 60 secondes.',
                IntensityEnum::SPARK,
                [],
                60,
            ],
            [
                'Le Bain de regard',
                'L\'un allongé, l\'autre à genoux sur lui, face à face. Regards dans les yeux, mouvements du bassin lents et continus.',
                IntensityEnum::SPARK,
                [],
                60,
            ],
            [
                'La Danse des corps',
                'Debout, corps collés, mains dans le dos de l\'autre. Mouvements lents et ondulants, comme une danse très lente.',
                IntensityEnum::SPARK,
                [],
                60,
            ],
            [
                'Le Bandeau sensoriel',
                'L\'un est bandé. L\'autre lui fait découvrir tour à tour la plume, un glaçon et la chaleur d\'un souffle. En silence.',
                IntensityEnum::SPARK,
                [$bandeau, $plume, $glacons],
                90,
            ],
            [
                'Les Menottes douces',
                'L\'un a les mains attachées dans le dos. L\'autre l\'embrasse librement, lentement, pendant toute la durée.',
                IntensityEnum::SPARK,
                [$menottes],
                60,
            ],
            [
                'La Bougie',
                'L\'un allongé, les yeux fermés. L\'autre approche la bougie de massage et laisse couler quelques gouttes de cire tiède dans le dos.',
                IntensityEnum::SPARK,
                [$bougie],
                45,
            ],

            // ── FIRE ──────────────────────────────────────────────────────────
            [
                'Le Missionnaire lent',
                'Position classique. Rythme imposé très lent, sans accélérer. Contact frontal maximal, regards dans les yeux.',
                IntensityEnum::FIRE,
                [],
                60,
            ],
            [
                'La Cuillère intime',
                'Allongés sur le côté, l\'un derrière l\'autre. Pénétration en cuillère, main de l\'un posée sur le ventre de l\'autre.',
                IntensityEnum::FIRE,
                [],
                60,
            ],
            [
                'La Chevauchée guidée',
                'L\'un sur l\'autre, le dessous guide le rythme avec les deux mains posées sur les hanches. Rythme lent imposé.',
                IntensityEnum::FIRE,
                [],
                45,
            ],
            [
                'Le Galop',
                'Position libre au choix, rythme intense. Changement de position toutes les 20 secondes sur signal.',
                IntensityEnum::FIRE,
                [],
                45,
            ],
            [
                'La Levrette suspendue',
                'L\'un debout derrière l\'autre, penché en avant, mains posées sur les hanches. Mouvements profonds et lents.',
                IntensityEnum::FIRE,
                [],
                60,
            ],
            [
                'Le 69 au ralenti',
                'Position 69. Rythme volontairement lent, chacun attentif aux réactions de l\'autre.',
                IntensityEnum::FIRE,
                [],
                90,
            ],

            // ── ERUPTION ──────────────────────────────────────────────────────
            [
                'Le Grand final',
                'La position préférée du couple, rythme entièrement libre. Tout ce qui a été mis de côté ce soir est maintenant permis.',
                IntensityEnum::ERUPTION,
                [],
                120,
            ],
            [
                'L\'Apothéose',
                'Choisissez ensemble, en 10 secondes, la position qui vous a le plus excités ce soir. Exécutez-la sans retenue.',
                IntensityEnum::ERUPTION,
                [],
                90,
            ],
            [
                'Le Fantasme du soir',
                'L\'un chuchote son fantasme du moment à l\'oreille de l\'autre. On l\'exécute immédiatement, sans condition.',
                IntensityEnum::ERUPTION,
                [],
                90,
            ],
        ];

        // ── Couleurs placeholder par intensité ───────────────────────────────
        $placeholderColors = [
            IntensityEnum::WARMUP->value   => '4ade80/ffffff',
            IntensityEnum::DESIRE->value   => 'facc15/ffffff',
            IntensityEnum::SPARK->value    => 'fb923c/ffffff',
            IntensityEnum::FIRE->value     => 'ef4444/ffffff',
            IntensityEnum::ERUPTION->value => '7f00ff/ffffff',
        ];

        foreach ($positionsData as [$name, $description, $intensity, $requiredAccessories, $duration]) {
            $position = new Position();
            $position->setName($name);
            $position->setDescription($description);
            $position->setIntensity($intensity);
            $position->setDuration($duration);

            foreach ($requiredAccessories as $accessory) {
                $position->addAccessory($accessory);
            }

            $color = $placeholderColors[$intensity->value];
            $label = urlencode($name);
            $imageContent = @file_get_contents("https://placehold.co/640x960/{$color}?text={$label}");

            if ($imageContent !== false) {
                $filePath = sys_get_temp_dir() . '/position_' . uniqid() . '.png';
                file_put_contents($filePath, $imageContent);

                $imageFile = new UploadedFile($filePath, basename($filePath), 'image/png', null, true);
                $position->setImageFile($imageFile);
            }

            $manager->persist($position);
        }

        // ── Positions avec images réelles ────────────────────────────────────
        $fixturesImageDir = __DIR__ . '/../../assets/styles/images/fixtures/';

        $positionsWithImages = [
            [
                'name'        => 'L\'Arc',
                'description' => 'L\'homme arqué au-dessus d\'elle, bras tendus, pénétration profonde. Elle s\'abandonne, bras étendus de chaque côté. Rythme lent imposé.',
                'intensity'   => IntensityEnum::FIRE,
                'accessories' => [],
                'duration'    => 60,
                'image'       => $fixturesImageDir . 'stock-vector-kama-sutra-sex-pose-man-and-woman-in-love-illustration-1015840804.jpg',
            ],
            [
                'name'        => 'La Levrette classique',
                'description' => 'Elle à quatre pattes, lui agenouillé derrière. Il pose les mains sur ses hanches et impose un rythme régulier et profond.',
                'intensity'   => IntensityEnum::FIRE,
                'accessories' => [],
                'duration'    => 45,
                'image'       => $fixturesImageDir . 'stock-vector-kama-sutra-sexual-pose-sex-poses-illustration-of-man-and-woman-on-white-background-1015836436.jpg',
            ],
            [
                'name'        => 'La Prosternée',
                'description' => 'Elle allongée, buste au sol, lui agenouillé derrière elle. Il se penche pour l\'enlacer et l\'embrasser dans le cou pendant qu\'il la pénètre lentement.',
                'intensity'   => IntensityEnum::SPARK,
                'accessories' => [],
                'duration'    => 60,
                'image'       => $fixturesImageDir . 'stock-vector-kama-sutra-sexual-pose-sex-poses-illustration-of-man-and-woman-on-white-background-1015836511.jpg',
            ],
        ];

        foreach ($positionsWithImages as $data) {
            $position = new Position();
            $position->setName($data['name']);
            $position->setDescription($data['description']);
            $position->setIntensity($data['intensity']);

            foreach ($data['accessories'] as $accessory) {
                $position->addAccessory($accessory);
            }

            $position->setDuration($data['duration']);

            $source = $data['image'];
            $ext = pathinfo($source, PATHINFO_EXTENSION);
            $tempPath = sys_get_temp_dir() . '/position_fixture_' . uniqid() . '.' . $ext;
            copy($source, $tempPath);

            $imageFile = new UploadedFile($tempPath, basename($source), 'image/jpeg', null, true);
            $position->setImageFile($imageFile);

            $manager->persist($position);
        }

        $manager->flush();
    }
}
