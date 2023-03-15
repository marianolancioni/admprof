<?php

namespace App\Entity;

use App\Repository\EstadoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstadoRepository::class)]
class Estado
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30,  nullable: true)]
    private $estadoProfesional;

    #[ORM\Column(type: 'smallint')]
    private $estado;

    #[ORM\OneToMany(mappedBy: 'estadoProfesional', targetEntity: Profesional::class)]
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

    public function getEstadoProfesional(): ?string
    {
        return $this->estadoProfesional;
    }

    public function setEstadoProfesional(string $estadoProfesional): self
    {
        $this->estadoProfesional = $estadoProfesional;

        return $this;
    }

    public function getEstado(): ?int
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
            $profesionale->setEstadoProfesional($this);
        }

        return $this;
    }

    public function removeProfesionale(Profesional $profesionale): self
    {
        if ($this->profesionales->removeElement($profesionale)) {
            // set the owning side to null (unless already changed)
            if ($profesionale->getEstadoProfesional() === $this) {
                $profesionale->setEstadoProfesional(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->estadoProfesional;
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
