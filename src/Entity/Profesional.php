<?php

namespace App\Entity;

use App\Repository\ProfesionalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfesionalRepository::class)]
class Profesional
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Circunscripcion::class, inversedBy: 'profesionales')]
    #[ORM\JoinColumn(nullable: false)]
    private $circunscripcion;

    #[ORM\ManyToOne(targetEntity: Colegio::class, inversedBy: 'profesionales')]
    #[ORM\JoinColumn(nullable: false)]
    private $colegio;

    #[ORM\Column(type: 'string', length: 20)]
    private $matricula;

    #[ORM\Column(type: 'string', length: 80)]
    private $apellido;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private $nombre;

    #[ORM\ManyToOne(targetEntity: TipoDocumento::class, inversedBy: 'profesionales')]
    #[ORM\JoinColumn(nullable: true)]
    private $tipoDocumento;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $numeroDocumento;

    #[ORM\Column(type: 'string', length: 120, nullable: true)]
    private $domicilio;

    #[ORM\ManyToOne(targetEntity: Localidad::class, inversedBy: 'profesionales')]
    #[ORM\JoinColumn(nullable: false)]
    private $localidad;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $telefono1;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $telefono2;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $correo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $observaciones;

    #[ORM\ManyToOne(targetEntity: Estado::class, inversedBy: 'profesionales')]
    #[ORM\JoinColumn(nullable: false)]
    private $estadoProfesional;

    #[ORM\Column(type: 'date', nullable: true)]
    private $desde;

    #[ORM\Column(type: 'date', nullable: true)]
    private $hasta;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $clave;

    #[ORM\Column(type: 'smallint')]
    private $estado;

    #[ORM\Column(type: 'datetimetz', nullable: true)]
    private $fechaActualizacion;

    #[ORM\Column(type: 'datetimetz', nullable: true)]
    private $fechaBarrido;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $estadoBarrido;

    #[ORM\Column(type: 'datetimetz', nullable: true)]
    private $fechaClave;

    #[ORM\Column(type: 'datetimetz', nullable: true)]
    private $fechaBaja;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $lastUserAppId;

    /**
     * Variable no mapeada. Usada para auditar al momento de borrar la entidad
     */
    private $storeId;

    #[ORM\Column(type: 'date', nullable: true)]
    private $fechaAlta;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $semilla;  

    public function getId(): ?int
    {
        return $this->id;
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

    public function getColegio(): ?Colegio
    {
        return $this->colegio;
    }

    public function setColegio(?Colegio $colegio): self
    {
        $this->colegio = $colegio;

        return $this;
    }

    public function getMatricula(): ?string
    {
        return $this->matricula;
    }

    public function setMatricula(string $matricula): self
    {
        $this->matricula = $matricula;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getTipoDocumento(): ?TipoDocumento
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?TipoDocumento $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /*public function getTipoDocumento(): ?int
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(int $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }
    */

    public function getNumeroDocumento(): ?int
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento(int $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

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

    public function getLocalidad(): ?Localidad
    {
        return $this->localidad;
    }

    public function setLocalidad(?Localidad $localidad): self
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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getEstadoProfesional(): ?Estado
    {
        return $this->estadoProfesional;
    }

    public function setEstadoProfesional(?Estado $estadoProfesional): self
    {
        $this->estadoProfesional = $estadoProfesional;

        return $this;
    }

    public function getDesde(): ?\DateTimeInterface
    {
        return $this->desde;
    }

    public function setDesde(?\DateTimeInterface $desde): self
    {
        $this->desde = $desde;

        return $this;
    }

    public function getHasta(): ?\DateTimeInterface
    {
        return $this->hasta;
    }

    public function setHasta(?\DateTimeInterface $hasta): self
    {
        $this->hasta = $hasta;

        return $this;
    }

    public function getClave(): ?string
    {
        return $this->clave;
    }

    public function setClave(?string $clave): self
    {
        $this->clave = $clave;

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

    public function getFechaActualizacion(): ?\DateTimeInterface
    {
        return $this->fechaActualizacion;
    }

    public function setFechaActualizacion(?\DateTimeInterface $fechaActualizacion): self
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    public function getFechaBarrido(): ?\DateTimeInterface
    {
        return $this->fechaBarrido;
    }

    public function setFechaBarrido(?\DateTimeInterface $fechaBarrido): self
    {
        $this->fechaBarrido = $fechaBarrido;

        return $this;
    }

    public function getEstadoBarrido(): ?int
    {
        return $this->estadoBarrido;
    }

    public function setEstadoBarrido(?int $estadoBarrido): self
    {
        $this->estadoBarrido = $estadoBarrido;

        return $this;
    }

    public function getFechaClave(): ?\DateTimeInterface
    {
        return $this->fechaClave;
    }

    public function setFechaClave(?\DateTimeInterface $fechaClave): self
    {
        $this->fechaClave = $fechaClave;

        return $this;
    }

    public function getFechaBaja(): ?\DateTimeInterface
    {
        return $this->fechaBaja;
    }

    public function setFechaBaja(?\DateTimeInterface $fechaBaja): self
    {
        $this->fechaBaja = $fechaBaja;

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

    public function getFechaAlta(): ?\DateTimeInterface
    {
        return $this->fechaAlta;
    }

    public function setFechaAlta(?\DateTimeInterface $fechaAlta): self
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    public function getSemilla(): ?int
    {
        return $this->semilla;
    }

    public function setSemilla(?int $semilla): self
    {
        $this->semilla = $semilla;

        return $this;
    }        
}
