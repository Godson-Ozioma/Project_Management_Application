<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_name = null;

    #[ORM\OneToMany(mappedBy: 'team_id', targetEntity: TeamMembers::class)]
    private Collection $teamMembers;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Project::class)]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TeamMessages::class)]
    private Collection $teamMessages;

    public function __construct()
    {
        $this->teamMembers = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->teamMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->team_name;
    }

    public function setTeamName(string $team_name): self
    {
        $this->team_name = $team_name;

        return $this;
    }

    /**
     * @return Collection<int, TeamMembers>
     */
    public function getTeamMembers(): Collection
    {
        return $this->teamMembers;
    }

    public function addTeamMember(TeamMembers $teamMember): self
    {
        if (!$this->teamMembers->contains($teamMember)) {
            $this->teamMembers->add($teamMember);
            $teamMember->setTeam($this);
        }

        return $this;
    }

    public function removeTeamMember(TeamMembers $teamMember): self
    {
        if ($this->teamMembers->removeElement($teamMember)) {
            // set the owning side to null (unless already changed)
            if ($teamMember->getTeam() === $this) {
                $teamMember->setTeam(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setTeam($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getTeam() === $this) {
                $project->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TeamMessages>
     */
    public function getTeamMessages(): Collection
    {
        return $this->teamMessages;
    }

    public function addTeamMessage(TeamMessages $teamMessage): self
    {
        if (!$this->teamMessages->contains($teamMessage)) {
            $this->teamMessages->add($teamMessage);
            $teamMessage->setTeam($this);
        }

        return $this;
    }

    public function removeTeamMessage(TeamMessages $teamMessage): self
    {
        if ($this->teamMessages->removeElement($teamMessage)) {
            // set the owning side to null (unless already changed)
            if ($teamMessage->getTeam() === $this) {
                $teamMessage->setTeam(null);
            }
        }

        return $this;
    }
}
