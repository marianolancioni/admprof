<?php

namespace App\Entity;

use App\Repository\UsuarioLogAccionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioLogAccionRepository::class)]
class UsuarioLogAccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetimetz')]
    private $fechaHora;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'usuarioLogAcciones')]
    #[ORM\JoinColumn(nullable: false)]
    private $usuario;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private $ip;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $accion;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    private $impersonateTo;

    public function __construct(?Usuario $usuario, $clienteIp, $accion, $timeStamp = new \DateTime('now'))
    {
        $this->setFechaHora($timeStamp);
        $this->setUsuario($usuario);
        $this->setIp($clienteIp);
        $this->setAccion($accion);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaHora(): ?\DateTimeInterface
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeInterface $fechaHora): self
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getAccion(): ?string
    {
        return $this->accion;
    }

    public function setAccion(?string $accion): self
    {
        $this->accion = $accion;

        return $this;
    }

    public function getImpersonateTo(): ?Usuario
    {
        return $this->impersonateTo;
    }

    public function setImpersonateTo(?Usuario $impersonateTo): self
    {
        $this->impersonateTo = $impersonateTo;

        return $this;
    }
}
