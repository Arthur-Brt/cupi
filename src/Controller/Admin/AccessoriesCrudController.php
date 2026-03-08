<?php

namespace App\Controller\Admin;

use App\Entity\Accessories;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class AccessoriesCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Accessories::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Accessory')
            ->setEntityLabelInPlural('Accessories')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');

        $imageUpload = Field::new('imageUpload', 'Image')
            ->setFormType(FileType::class)
            ->setRequired(false)
            ->onlyOnForms();

        $imageDisplay = ImageField::new('imageName')
            ->setBasePath('/uploads/accessories')
            ->setLabel('Image')
            ->onlyOnIndex();

        return match ($pageName) {
            Crud::PAGE_INDEX => [$name, $imageDisplay],
            Crud::PAGE_DETAIL => [$name, $imageDisplay],
            Crud::PAGE_NEW, Crud::PAGE_EDIT => [$name, $imageUpload],
            default => [],
        };
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleFileUpload($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleFileUpload($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleFileUpload($entityInstance): void
    {
        if (!$entityInstance instanceof Accessories) {
            return;
        }

        $request = $this->getContext()->getRequest();
        $uploadedFile = $request->files->get('Accessories')['imageUpload'] ?? null;

        if ($uploadedFile instanceof UploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            try {
                $uploadedFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/accessories',
                    $newFilename
                );
            } catch (FileException $e) {
                // log or handle exception
            }

            $entityInstance->setImageName($newFilename);
        }
    }
}
