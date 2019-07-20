<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
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
    private $thequest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Survey", inversedBy="questions")
     */
    private $Surveyrelation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="question")
     */
    private $answerRelation;

    public function __construct()
    {
        $this->Surveyrelation = new ArrayCollection();
        $this->answerRelation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThequest(): ?string
    {
        return $this->thequest;
    }

    public function setThequest(?string $thequest): self
    {
        $this->thequest = $thequest;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Survey[]
     */
    public function getSurveyrelation(): Collection
    {
        return $this->Surveyrelation;
    }

    public function addSurveyrelation(Survey $surveyrelation): self
    {
        if (!$this->Surveyrelation->contains($surveyrelation)) {
            $this->Surveyrelation[] = $surveyrelation;
        }

        return $this;
    }

    public function removeSurveyrelation(Survey $surveyrelation): self
    {
        if ($this->Surveyrelation->contains($surveyrelation)) {
            $this->Surveyrelation->removeElement($surveyrelation);
        }

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswerRelation(): Collection
    {
        return $this->answerRelation;
    }

    public function addAnswerRelation(Answer $answerRelation): self
    {
        if (!$this->answerRelation->contains($answerRelation)) {
            $this->answerRelation[] = $answerRelation;
            $answerRelation->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswerRelation(Answer $answerRelation): self
    {
        if ($this->answerRelation->contains($answerRelation)) {
            $this->answerRelation->removeElement($answerRelation);
            // set the owning side to null (unless already changed)
            if ($answerRelation->getQuestion() === $this) {
                $answerRelation->setQuestion(null);
            }
        }

        return $this;
    }
}
