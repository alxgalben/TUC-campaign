<?php

namespace App\Entity;

use App\Repository\ReceiptRegistrationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReceiptRegistrationRepository::class)
 */
class ReceiptRegistration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telefon;

    /**
     * @ORM\Column(type="float", unique=true)
     */
    private $idNet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nrBon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dataBon;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acordTermeni;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acordVarsta;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acordRegulament;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $magazin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $submittedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    public function setTelefon(string $telefon): self
    {
        $this->telefon = $telefon;

        return $this;
    }

    public function getIdNet(): ?int
    {
        return $this->idNet;
    }

    public function setIdNet(int $idNet): self
    {
        $this->idNet = $idNet;

        return $this;
    }

    public function getNrBon(): ?string
    {
        return $this->nrBon;
    }

    public function setNrBon(string $nrBon): self
    {
        $this->nrBon = $nrBon;

        return $this;
    }

    public function getDataBon(): ?string
    {
        return $this->dataBon;
    }

    public function setDataBon(string $dataBon): self
    {
        $this->dataBon = $dataBon;

        return $this;
    }

    public function isAcordTermeni(): ?bool
    {
        return $this->acordTermeni;
    }

    public function setAcordTermeni(bool $acordTermeni): self
    {
        $this->acordTermeni = $acordTermeni;

        return $this;
    }

    public function isAcordVarsta(): ?bool
    {
        return $this->acordVarsta;
    }

    public function setAcordVarsta(bool $acordVarsta): self
    {
        $this->acordVarsta = $acordVarsta;

        return $this;
    }

    public function isAcordRegulament(): ?bool
    {
        return $this->acordRegulament;
    }

    public function setAcordRegulament(bool $acordRegulament): self
    {
        $this->acordRegulament = $acordRegulament;

        return $this;
    }

    public function getMagazin(): ?string
    {
        return $this->magazin;
    }

    public function setMagazin(string $magazin): self
    {
        $this->magazin = $magazin;

        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeInterface $submittedAt): self
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }
}
