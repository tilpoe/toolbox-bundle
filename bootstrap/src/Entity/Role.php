<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Feierstoff\ToolboxBundle\Entity\RoleInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Role {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected int $id;

    #[ORM\Column(type: "string", length: 255)]
    protected string $name;

    #[ORM\Column(type: "text", nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: "json")]
    protected array $privileges = [];

    #[ORM\OneToMany(mappedBy: "role", targetEntity: User::class)]
    protected Collection $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    public function create(
        string $name,
        array $privileges,
        ?string $description = ""
    ) {
        return (new Role())
            ->setName($name)
            ->setPrivileges($privileges)
            ->setDescription($description);
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getPrivileges(): array {
        return $this->privileges;
    }

    public function setPrivileges(array $privileges): self {
        $this->privileges = $privileges;
        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection {
        return $this->users;
    }

    public function addUser(User $user): self {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }

}