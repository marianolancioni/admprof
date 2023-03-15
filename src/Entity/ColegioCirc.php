<?php

namespace App\Entity;

use App\Repository\ColegioCircRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColegioCircRepository::class)]
class ColegioCirc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Colegio::class, inversedBy: 'colegiosCircs')]
    #[ORM\JoinColumn(nullable: false)]
    private $colegio;

    #[ORM\ManyToOne(targetEntity: Circunscripcion::class, inversedBy: 'colegiosCircs')]
    #[ORM\JoinColumn(nullable: false)]
    private $circunscripcion;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $caracteresPermitidos;

    #[ORM\Column(type: 'string', length: 120, nullable: true)]
    private $domicilio;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $telefono1;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $telefono2;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $correo;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $estado;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $visible;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private $localidad;

    #[ORM\ManyToOne(targetEntity: Provincia::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $provincia;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $lastUserAppId;

    /**
     * Variable no mapeada. Usada para auditar al momento de borrar la entidad
     */
    private $storeId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColegio(): ?Colegio
    {
        return $this->colegio;
    }

    public function setColegio(?Colegio $colegio): self
    {
        $this->colegio = $colegio;

        return $this;
    }

    public function getCircunscripcion(): ?Circunscripcion
    {
        return $this->circunscripcion;
    }

    public function setCircunscripcion(?Circunscripcion $circunscripcion): self
    {
        $this->circunscripcion = $circunscripcion;

        return $this;
    }

    public function getCaracteresPermitidos(): ?string
    {
        return $this->caracteresPermitidos;
    }

    public function setCaracteresPermitidos(?string $caracteresPermitidos): self
    {
        $this->caracteresPermitidos = $caracteresPermitidos;

        return $this;
    }

    public function getDomicilio(): ?string
    {
        return $this->domicilio;
    }

    public function setDomicilio(?string $domicilio): self
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(?string $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getTelefono1(): ?string
    {
        return $this->telefono1;
    }

    public function setTelefono1(?string $telefono1): self
    {
        $this->telefono1 = $telefono1;

        return $this;
    }

    public function getTelefono2(): ?string
    {
        return $this->telefono2;
    }

    public function setTelefono2(?string $telefono2): self
    {
        $this->telefono2 = $telefono2;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(?string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

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