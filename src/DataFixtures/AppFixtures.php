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
                '{joueur1} se glisse derrière {joueur2} en cuillère. Caresses lentes sur les bras et le dos. Aucune parole, juste la chaleur des corps.',
                IntensityEnum::WARMUP,
                [],
                60,
            ],
            [
                'Le Massage nuque',
                '{joueur1} masse lentement la nuque et les épaules de {joueur2} pendant toute la durée du compte à rebours.',
                IntensityEnum::WARMUP,
                [],
                60,
            ],
            [
                'Le Frôlement',
                '{joueur1} et {joueur2} s\'allongent face à face et s\'effleurent du bout des doigts sans jamais appuyer. Visage, cou, bras.',
                IntensityEnum::WARMUP,
                [],
                45,
            ],
            [
                'Le Regard profond',
                '{joueur1} et {joueur2} s\'assoient face à face en tailleur, genoux qui se touchent. Yeux dans les yeux, respirez ensemble sans parler.',
                IntensityEnum::WARMUP,
                [],
                60,
            ],
            [
                'Les Souffles',
                '{joueur1} souffle doucement dans le cou, derrière l\'oreille et sur la nuque de {joueur2}, sans jamais toucher.',
                IntensityEnum::WARMUP,
                [],
                30,
            ],
            [
                'Le Massage à la plume',
                '{joueur2} s\'allonge sur le ventre, les yeux fermés. {joueur1} lui passe lentement la plume sur tout le dos et les bras.',
                IntensityEnum::WARMUP,
                [$plume],
                60,
            ],

            // ── DESIRE ────────────────────────────────────────────────────────
            [
                'Le Baiser profond',
                '{joueur1} et {joueur2} face à face, mains dans les cheveux de l\'autre. Long baiser sans limite de durée, les yeux fermés.',
                IntensityEnum::DESIRE,
                [],
                60,
            ],
            [
                'L\'Exploration',
                '{joueur2} s\'allonge, les yeux fermés. {joueur1} l\'explore lentement avec les mains uniquement, des pieds jusqu\'au cou.',
                IntensityEnum::DESIRE,
                [],
                90,
            ],
            [
                'Le Face-à-face assis',
                '{joueur1} et {joueur2} s\'assoient face à face, jambes entrelacées, regards dans les yeux. Caresses mutuelles du visage et des mains.',
                IntensityEnum::DESIRE,
                [],
                60,
            ],
            [
                'Le Baiser interdit',
                '{joueur1} pose des baisers sur tout le corps de {joueur2} sauf les lèvres pendant toute la durée. Qui craquera le premier ?',
                IntensityEnum::DESIRE,
                [],
                60,
            ],
            [
                'L\'Emprise douce',
                '{joueur1} tient le visage de {joueur2} entre ses mains et pose un baiser très lent sur chaque paupière, le front, le nez, les joues, puis les lèvres.',
                IntensityEnum::DESIRE,
                [],
                45,
            ],
            [
                'Le Massage aux huiles',
                '{joueur2} s\'allonge. {joueur1} masse lentement le dos et les jambes avec l\'huile chauffée dans ses mains.',
                IntensityEnum::DESIRE,
                [$huile],
                90,
            ],

            // ── SPARK ─────────────────────────────────────────────────────────
            [
                'La Domination douce',
                '{joueur1} maintient doucement les poignets de {joueur2} au-dessus de sa tête contre le lit. {il1} l\'embrasse dans le cou pendant toute la durée.',
                IntensityEnum::SPARK,
                [],
                60,
            ],
            [
                'Le Bain de regard',
                '{joueur2} s\'allonge. {joueur1} se met à genoux sur elle, face à face. Regards dans les yeux, mouvements du bassin lents et continus.',
                IntensityEnum::SPARK,
                [],
                60,
            ],
            [
                'La Danse des corps',
                '{joueur1} et {joueur2} debout, corps collés, mains dans le dos de l\'autre. Mouvements lents et ondulants, comme une danse très lente.',
                IntensityEnum::SPARK,
                [],
                60,
            ],
            [
                'Le Bandeau sensoriel',
                '{joueur1} est bandé. {joueur2} lui fait découvrir tour à tour la plume, un glaçon et la chaleur d\'un souffle. En silence.',
                IntensityEnum::SPARK,
                [$bandeau, $plume, $glacons],
                90,
            ],
            [
                'Les Menottes douces',
                '{joueur1} a les mains attachées dans le dos. {joueur2} l\'embrasse librement et lentement pendant toute la durée.',
                IntensityEnum::SPARK,
                [$menottes],
                60,
            ],
            [
                'La Bougie',
                '{joueur2} s\'allonge, les yeux fermés. {joueur1} approche la bougie de massage et laisse couler quelques gouttes de cire tiède dans son dos.',
                IntensityEnum::SPARK,
                [$bougie],
                45,
            ],

            // ── FIRE ──────────────────────────────────────────────────────────
            [
                'Le Missionnaire lent',
                '{joueur1} au-dessus de {joueur2}. Rythme imposé très lent, sans accélérer. Contact frontal maximal, regards dans les yeux.',
                IntensityEnum::FIRE,
                [],
                60,
            ],
            [
                'La Cuillère intime',
                '{joueur1} et {joueur2} allongés sur le côté, {joueur1} derrière elle. Pénétration en cuillère, main de {joueur1} posée sur le ventre de {joueur2}.',
                IntensityEnum::FIRE,
                [],
                60,
            ],
            [
                'La Chevauchée guidée',
                '{joueur2} à cheval sur {joueur1}. {il1} guide le rythme avec les deux mains posées sur ses hanches. Rythme lent imposé.',
                IntensityEnum::FIRE,
                [],
                45,
            ],
            [
                'Le Galop',
                '{joueur1} et {joueur2} choisissent une position, rythme intense. Changement de position toutes les 20 secondes sur signal.',
                IntensityEnum::FIRE,
                [],
                45,
            ],
            [
                'La Levrette suspendue',
                '{joueur1} debout derrière {joueur2}, penché en avant, mains posées sur ses hanches. Mouvements profonds et lents.',
                IntensityEnum::FIRE,
                [],
                60,
            ],
            [
                'Le 69 au ralenti',
                '{joueur1} et {joueur2} en position 69. Rythme volontairement lent, chacun attentif aux réactions de l\'autre.',
                IntensityEnum::FIRE,
                [],
                90,
            ],

            // ── ERUPTION ──────────────────────────────────────────────────────
            [
                'Le Grand final',
                'La position préférée de {joueur1} et {joueur2}, rythme entièrement libre. Tout ce qui a été mis de côté ce soir est maintenant permis.',
                IntensityEnum::ERUPTION,
                [],
                120,
            ],
            [
                'L\'Apothéose',
                '{joueur1} et {joueur2} choisissent ensemble, en 10 secondes, la position qui les a le plus excités ce soir. Exécutez-la sans retenue.',
                IntensityEnum::ERUPTION,
                [],
                90,
            ],
            [
                'Le Fantasme du soir',
                '{joueur1} chuchote son fantasme du moment à l\'oreille de {joueur2}. On l\'exécute immédiatement, sans condition.',
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
                'description' => '{joueur1} arqué au-dessus de {joueur2}, bras tendus, pénétration profonde. {il2} s\'abandonne, bras étendus de chaque côté. Rythme lent imposé.',
                'intensity'   => IntensityEnum::FIRE,
                'accessories' => [],
                'duration'    => 60,
                'image'       => $fixturesImageDir . 'stock-vector-kama-sutra-sex-pose-man-and-woman-in-love-illustration-1015840804.jpg',
            ],
            [
                'name'        => 'La Levrette classique',
                'description' => '{joueur2} à quatre pattes, {joueur1} agenouillé derrière elle. {il1} pose les mains sur ses hanches et impose un rythme régulier et profond.',
                'intensity'   => IntensityEnum::FIRE,
                'accessories' => [],
                'duration'    => 45,
                'image'       => $fixturesImageDir . 'stock-vector-kama-sutra-sexual-pose-sex-poses-illustration-of-man-and-woman-on-white-background-1015836436.jpg',
            ],
            [
                'name'        => 'La Prosternée',
                'description' => '{joueur2} allongée, buste au sol. {joueur1} agenouillé derrière elle, se penche pour l\'enlacer et l\'embrasser dans le cou pendant la pénétration lente.',
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
