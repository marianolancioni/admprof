<?php

namespace App\Entity;

use App\Repository\CircunscripcionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CircunscripcionRepository::class)]
class Circunscripcion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $circunscripcion;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $estado;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $visible;

    #[ORM\OneToMany(mappedBy: 'circunscripcion', targetEntity: ColegioCirc::class)]
    private $colegiosCircs;

    #[ORM\OneToMany(mappedBy: 'circunscripcion', targetEntity: Profesional::class)]
    private $profesionales;

    #[ORM\OneToMany(mappedBy: 'circunscripcion', targetEntity: Usuario::class)]
    private $usuarios;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $lastUserAppId;

    /**
     * Variable no mapeada. Usada para auditar al momento de borrar la entidad
     */
    private $storeId;


    public function __construct()
    {
        $this->colegiosCircs = new ArrayCollection();
        $this->profesionales = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCircunscripcion(): ?string
    {
        return $this->circunscripcion;
    }

    public function setCircunscripcion(string $circunscripcion): self
    {
        $this->circunscripcion = $circunscripcion;

        return $this;
    }

    /*Para el campo Estado el FALSE or 0: Habilitado y el TRUE or 1: Deshabilitado*/ 
    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(?int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    /*Para el campo Visible el FALSE or 0: No Visible y el TRUE or 1: Visible*/ 
    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?int $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return Collection|ColegioCirc[]
     */
    public function getColegiosCircs(): Collection
    {
        return $this->colegiosCircs;
    }

    public function addColegiosCirc(ColegioCirc $colegiosCirc): self
    {
        if (!$this->colegiosCircs->contains($colegiosCirc)) {
            $this->colegiosCircs[] = $colegiosCirc;
            $colegiosCirc->setCircunscripcion($this);
        }

        return $this;
    }

    public function removeColegiosCirc(ColegioCirc $colegiosCirc): self
    {
        if ($this->colegiosCircs->removeElement($colegiosCirc)) {
            // set the owning side to null (unless already changed)
            if ($colegiosCirc->getCircunscripcion() === $this) {
                $colegiosCirc->setCircunscripcion(null);
            }
        }

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
            $profesionale->setCircunscripcion($this);
        }

        return $this;
    }

    public function removeProfesionale(Profesional $profesionale): self
    {
        if ($this->profesionales->removeElement($profesionale)) {
            // set the owning side to null (unless already changed)
            if ($profesionale->getCircunscripcion() === $this) {
                $profesionale->setCircunscripcion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addUsuario(Usuario $usuario): self
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios[] = $usuario;
            $usuario->setCircunscripcion($this);
        }

        return $this;
    }

    public function removeUsuario(Usuario $usuario): self
    {
        if ($this->usuarios->removeElement($usuario)) {
            // set the owning side to null (unless already changed)
            if ($usuario->getCircunscripcion() === $this) {
                $usuario->setCircunscripcion(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->circunscripcion;
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
