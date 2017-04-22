<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Carte
 *
 * @ORM\Table(name="carte")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CarteRepository")
 */
class Carte
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="carte_valeur", type="integer")
     */
    private $carteValeur;

    /**
     * @var bool
     *
     * @ORM\Column(name="carte_extra", type="boolean")
     */
    private $carteExtra;

    /**
     * @var string
     *
     * @ORM\Column(name="carte_nom", type="string", length=255)
     */
    private $carteNom;

    /**
     * @var string
     *
     * @ORM\Column(name="carte_image", type="string", length=255, nullable=true)
     */
    private $carteImage;


    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="cartes")
     *
     */

    private $carteCategorie;


    /**
     * @var file
     *
     */
    private $file;

    public function __toString()
    {
        return $this->carteNom;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set carteValeur
     *
     * @param integer $carteValeur
     *
     * @return Carte
     */
    public function setCarteValeur($carteValeur)
    {
        $this->carteValeur = $carteValeur;

        return $this;
    }

    /**
     * Get carteValeur
     *
     * @return int
     */
    public function getCarteValeur()
    {
        return $this->carteValeur;
    }

    /**
     * Set carteExtra
     *
     * @param boolean $carteExtra
     *
     * @return Carte
     */
    public function setCarteExtra($carteExtra)
    {
        $this->carteExtra = $carteExtra;

        return $this;
    }

    /**
     * Get carteExtra
     *
     * @return bool
     */
    public function getCarteExtra()
    {
        return $this->carteExtra;
    }

    /**
     * Set carteNom
     *
     * @param string $carteNom
     *
     * @return Carte
     */
    public function setCarteNom($carteNom)
    {
        $this->carteNom = $carteNom;

        return $this;
    }

    /**
     * Get carteNom
     *
     * @return string
     */
    public function getCarteNom()
    {
        return $this->carteNom;
    }

    /**
     * Set carteImage
     *
     * @param string $carteImage
     *
     * @return Carte
     */
    public function setCarteImage($carteImage)
    {
        $this->carteImage = $carteImage;

        return $this;
    }

    /**
     * Get carteImage
     *
     * @return string
     */
    public function getCarteImage()
    {
        return $this->carteImage;
    }

    /**
     * Set carteCategorie
     *
     * @param \AppBundle\Entity\Categorie $carteCategorie
     *
     * @return Carte
     */
    public function setCarteCategorie(\AppBundle\Entity\Categorie $carteCategorie = null)
    {
        $this->carteCategorie = $carteCategorie;

        return $this;
    }

    /**
     * Get carteCategorie
     *
     * @return \AppBundle\Entity\Categorie
     */
    public function getCarteCategorie()
    {
        return $this->carteCategorie;
    }

    public function getFile()
    {
        return $this->file;
    }
}
