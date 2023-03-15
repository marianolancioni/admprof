<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;


#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
##[Assert\EnableAutoMapping]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface, TrustedDeviceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: false)]
#    #[Assert\NotBlank (message:'Ingrese el alias del usuario!')]
    private $username;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $dni;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $apellido;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $email;

    #[ORM\Column(type: 'date', nullable: true)]
    private $fechaAlta;

    #[ORM\Column(type: 'date', nullable: true)]
    private $fechaBaja;

    #[ORM\Column(type: 'datetimetz', nullable: true)]
    private $ultimoAcceso;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $cantidadAccesos;

    #[ORM\ManyToOne(targetEntity: Colegio::class, inversedBy: 'usuarios')]
    private $colegio;

    #[ORM\ManyToOne(targetEntity: Circunscripcion::class, inversedBy: 'usuarios')]
    private $circunscripcion;

    #[ORM\Column(type: 'string', nullable: true)]
    private $authCode;

    #[ORM\Column(type: 'integer')]
    private $trustedVersion;

	#[ORM\Column(type: 'integer', nullable: true)]
    private $lastUserAppId;

    /**
     * Variable no mapeada. Usada para auditar al momento de borrar la entidad
     */
    private $storeId;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: UsuarioLogAccion::class)]
    private $usuarioLogAcciones;

    public function __construct()
    {
        $this->usuarioLogAcciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDni(): ?int
    {
        return $this->dni;
    }

    public function setDni(?int $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): self
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function getFechaBaja(): ?\DateTimeInterface
    {
        return $this->fechaBaja;
    }

    public function setFechaBaja(?\DateTimeInterface $fechaBaja): self
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    public function getUltimoAcceso(): ?\DateTimeInterface
    {
        return $this->ultimoAcceso;
    }

    public function setUltimoAcceso(?\DateTimeInterface $ultimoAcceso): self
    {
        $this->ultimoAcceso = $ultimoAcceso;

        return $this;
    }

    public function getCantidadAccesos(): ?int
    {
        return $this->cantidadAccesos;
    }

    public function setCantidadAccesos(?int $cantidadAccesos): self
    {
        $this->cantidadAccesos = $cantidadAccesos;

        return $this;
    }

    public function getTrustedVersion(): ?int
    {
        return $this->trustedVersion;
    }

    public function setTrustedVersion(?int $trustedVersion): self
    {
        $this->trustedVersion = $trustedVersion;

        return $this;
    }


    /**
     * Método usado en Datatable
     */
    public function getApeNom()
    {
        if ($this->apellido && $this->nombre)
            return $this->apellido . ', ' . $this->nombre;
        else if ($this->apellido && !$this->nombre)
            return $this->apellido;
        else if (!$this->apellido && $this->nombre)
            return $this->nombre;
        return '';
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

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getTrustedTokenVersion(): int
    {
        return $this->trustedVersion;
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

    /**
     * @return Collection<int, UsuarioLogAccion>
     */
    public function getUsuarioLogAcciones(): Collection
    {
        return $this->usuarioLogAcciones;
    }

    public function addUsuarioLogAccione(UsuarioLogAccion $usuarioLogAccione): self
    {
        if (!$this->usuarioLogAcciones->contains($usuarioLogAccione)) {
            $this->usuarioLogAcciones[] = $usuarioLogAccione;
            $usuarioLogAccione->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioLogAccione(UsuarioLogAccion $usuarioLogAccione): self
    {
        if ($this->usuarioLogAcciones->removeElement($usuarioLogAccione)) {
            // set the owning side to null (unless already changed)
            if ($usuarioLogAccione->getUsuario() === $this) {
                $usuarioLogAccione->setUsuario(null);
            }
        }

        return $this;
    }
}
