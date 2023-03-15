<?php

namespace App\Entity;

use App\Repository\LocalidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocalidadRepository::class)]
class Localidad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 80)]
    private $localidad;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $codigoPostal;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $subCodigoPostal;

    #[ORM\ManyToOne(targetEntity: Provincia::class, inversedBy: 'localidades')]
    #[ORM\JoinColumn(nullable: false)]
    private $provincia;

    #[ORM\Column(type: 'smallint')]
    private $estado;

    #[ORM\OneToMany(mappedBy: 'localidad', targetEntity: Profesional::class)]
    private $profesionales;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $lastUserAppId;

    /**
     * Variable no mapeada. Usada para auditar al momento de borrar la entidad
     */
    private $storeId;  

    public function __construct()
    {
        $this->profesionales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(string $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getCodigoPostal(): ?int
    {
        return $this->codigoPostal;
    }

    public function setCodigoPostal(int $codigoPostal): self
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    public function getSubCodigoPostal(): ?int
    {
        return $this->subCodigoPostal;
    }

    public function setSubCodigoPostal(?int $subCodigoPostal): self
    {
        $this->subCodigoPostal = $subCodigoPostal;

        return $this;
    }

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * @return Collection|Profesional[]
     */
    public function getProfesionales(): Collection
    {
        return $this->profesionales;
    }

    public function addProfesionale(Profesional $profesionale): self
    {
        if (!$this->profesionales->contains($profesionale)) {
            $this->profesionales[] = $profesionale;
            $profesionale->setLocalidad($this);
        }

        return $this;
    }

    public function removeProfesionale(Profesional $profesionale): self
    {
        if ($this->profesionales->removeElement($profesionale)) {
            // set the owning side to null (unless already changed)
            if ($profesionale->getLocalidad() === $this) {
                $profesionale->setLocalidad(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->localidad;
    } 

    public function getLastUserAppId(): ?int
    {
        return $this->lastUserAppId;
    }

    public function setLastUserAppId(?int $lastUserAppId): self
    {
        $this->lastUserAppId = $lastUserAppId;

        return $this;
    }

    public function setStoreId(int $storeId): self
    {
        $this->storeId = $storeId;

        return $this;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }        
}
