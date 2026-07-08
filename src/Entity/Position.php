<?php

namespace App\Entity;

use App\Enum\IntensityEnum;
use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
#[Vich\Uploadable]
class Position
{
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Accessories>
     */
    #[ORM\ManyToMany(targetEntity: Accessories::class, inversedBy: 'positions')]
    private Collection $accessories;

    #[ORM\Column(enumType: IntensityEnum::class)]
    private ?IntensityEnum $intensity = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    // NE PAS mapper avec Doctrine
    #[Vich\UploadableField(mapping:'position', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;


    public function __construct()
    {
        $this->accessories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, accessories>
     */
    public function getAccessories(): Collection
    {
        return $this->accessories;
    }

    public function addAccessory(accessories $accessory): static
    {
        if (!$this->accessories->contains($accessory)) {
            $this->accessories->add($accessory);
        }

        return $this;
    }

    public function removeAccessory(accessories $accessory): static
    {
        $this->accessories->removeElement($accessory);

        return $this;
    }

    public function getIntensity(): ?IntensityEnum
    {
        return $this->intensity;
    }

    public function setIntensity(IntensityEnum $intensity): static
    {
        $this->intensity = $intensity;

        return $this;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;
        if (null !== $image) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getEffectiveDuration(): int
    {
        return $this->duration ?? 60;
    }
}
