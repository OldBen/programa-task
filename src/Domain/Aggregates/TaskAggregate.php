<?php

namespace App\Domain\Aggregates;

use App\Domain\Enums\TaskStatusEnum;
use App\Repository\TaskAggregateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskAggregateRepository::class)]
#[ORM\Table(name: 'tasks')]
class TaskAggregate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: TaskStatusEnum::class)]
    private ?TaskStatusEnum $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserAggregate $assigned_user = null;

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

    public function getStatus(): ?TaskStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TaskStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAssignedUser(): ?UserAggregate
    {
        return $this->assigned_user;
    }

    public function setAssignedUser(?UserAggregate $assigned_user): static
    {
        $this->assigned_user = $assigned_user;

        return $this;
    }
}
