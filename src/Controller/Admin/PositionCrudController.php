<?php

namespace App\Controller\Admin;

use App\Entity\Accessories;
use App\Entity\Position;
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
        $description = TextField::new('description');
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

        return match ($pageName) {
                Crud::PAGE_INDEX => [$name, $imageDisplay, $intensity, $duration],
                Crud::PAGE_DETAIL => [$name, $imageDisplay, $intensity, $duration],
                Crud::PAGE_NEW, Crud::PAGE_EDIT => [$name, $description, $imageFile, $accessories, $intensity, $duration],
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


}
