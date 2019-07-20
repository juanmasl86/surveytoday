<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
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
    private $theAnswer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answerRelation")
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheAnswer(): ?string
    {
        return $this->theAnswer;
    }

    public function setTheAnswer(?string $theAnswer): self
    {
        $this->theAnswer = $theAnswer;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
