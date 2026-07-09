<?php

namespace App\Controller\Admin;

use App\Entity\Accessories;
use App\Entity\Position;
use App\Enum\GenderCombinationEnum;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PositionCrudController extends AbstractCrudController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }
    public static function getEntityFqcn(): string
    {
        return Position::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Position')
            ->setEntityLabelInPlural('Positions')
            ->setDefaultSort(['id' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $description = TextareaField::new('description')
            ->setNumOfRows(5)
            ->setHelp($this->buildPlaceholderHelp());
        $imageFile = TextareaField::new('imageFile', 'Image')
            ->setFormType(VichImageType::class)
            ->setRequired(false)
            ->onlyOnForms();

        $imageDisplay = ImageField::new('imageName')
            ->setBasePath('images/position')
            ->setLabel('Image')
            ->onlyOnIndex();
        $accessories = AssociationField::new('accessories');
        $intensity = ChoiceField::new('intensity');
        $duration = IntegerField::new('duration', 'Durée (secondes)')
            ->setHelp('Laisser vide pour utiliser la durée par défaut (60s)')
            ->setRequired(false);
        $genderCombination = ChoiceField::new('genderCombination', 'Public concerné')
            ->setChoices([
                GenderCombinationEnum::ANY->label() => GenderCombinationEnum::ANY,
                GenderCombinationEnum::HOMME_FEMME->label() => GenderCombinationEnum::HOMME_FEMME,
                GenderCombinationEnum::HOMME_HOMME->label() => GenderCombinationEnum::HOMME_HOMME,
                GenderCombinationEnum::FEMME_FEMME->label() => GenderCombinationEnum::FEMME_FEMME,
            ])
            ->setHelp('Restreint la position aux parties dont les joueurs correspondent à ce choix. "Un homme + une femme" débloque en plus les placeholders {joueurHomme}/{joueurFemme}.');
        $createdBy = TextField::new('createdBy', 'Créé par')->onlyOnDetail();
        $updatedBy = TextField::new('updatedBy', 'Modifié par')->onlyOnDetail();

        return match ($pageName) {
                Crud::PAGE_INDEX => [$name, $imageDisplay, $intensity, $genderCombination, $duration],
                Crud::PAGE_DETAIL => [$name, $imageDisplay, $intensity, $genderCombination, $duration, $createdBy, $updatedBy],
                Crud::PAGE_NEW, Crud::PAGE_EDIT => [$name, $description, $imageFile, $accessories, $intensity, $genderCombination, $duration],
        };
    }
//    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
//    {
//        $this->handleFileUpload($entityInstance);
//        parent::persistEntity($entityManager, $entityInstance);
//    }
//
//    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
//    {
//        $this->handleFileUpload($entityInstance);
//        parent::updateEntity($entityManager, $entityInstance);
//    }
//
//    private function handleFileUpload($entityInstance): void
//    {
//        if (!$entityInstance instanceof Position) {
//            return;
//        }
//
//        $request = $this->getContext()->getRequest();
//        $uploadedFile = $request->files->get('Position')['imageUpload'] ?? null;
//
//        if ($uploadedFile instanceof UploadedFile) {
//            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
//            $safeFilename = $this->slugger->slug($originalFilename);
//            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
//
//            try {
//                $uploadedFile->move(
//                    $this->getParameter('kernel.project_dir') . '/public/uploads/position',
//                    $newFilename
//                );
//            } catch (FileException $e) {
//                // log or handle exception
//            }
//
//            $entityInstance->setImageName($newFilename);
//        }
//    }

    private function buildPlaceholderHelp(): string
    {
        $placeholders = [
            '{joueur1}' => 'prénom du joueur 1',
            '{joueur2}' => 'prénom du joueur 2',
            '{il1}' => '"il" / "elle" (joueur 1)',
            '{il2}' => '"il" / "elle" (joueur 2)',
            '{e1}' => 'accord joueur 1 (ex: "prêt{e1}")',
            '{e2}' => 'accord joueur 2 (ex: "prêt{e2}")',
            '{joueurHomme}' => 'prénom du joueur homme (uniquement si "Public concerné" = Un homme + une femme)',
            '{joueurFemme}' => 'prénom du joueur femme (uniquement si "Public concerné" = Un homme + une femme)',
        ];

        $chips = '';
        foreach (array_keys($placeholders) as $placeholder) {
            $chips .= sprintf(
                '<button type="button" class="placeholder-chip" data-action="placeholder-inserter#insert" data-placeholder="%1$s">%1$s</button>',
                $placeholder
            );
        }

        $legend = implode(' · ', array_map(
            static fn (string $placeholder, string $label) => sprintf('%s → %s', $placeholder, $label),
            array_keys($placeholders),
            array_values($placeholders)
        ));

        return sprintf(
            '<div data-controller="placeholder-inserter">'
            .'<div class="placeholder-chips">%s</div>'
            .'<div class="placeholder-legend">%s</div>'
            .'</div>',
            $chips,
            $legend
        );
    }
}
