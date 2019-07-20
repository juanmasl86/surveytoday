<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 */
class Survey
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="surveys")
     */
    private $userequest;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Question", mappedBy="Surveyrelation")
     */
    private $questions;

    public function __construct()
    {
        $this->userequest = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getUserequest(): Collection
    {
        return $this->userequest;
    }

    public function addUserequest(user $userequest): self
    {
        if (!$this->userequest->contains($userequest)) {
            $this->userequest[] = $userequest;
        }

        return $this;
    }

    public function removeUserequest(user $userequest): self
    {
        if ($this->userequest->contains($userequest)) {
            $this->userequest->removeElement($userequest);
        }

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->addSurveyrelation($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            $question->removeSurveyrelation($this);
        }

        return $this;
    }
}
