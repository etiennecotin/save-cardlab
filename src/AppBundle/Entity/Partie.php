<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Partie
 *
 * @ORM\Table(name="partie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PartieRepository")
 */
class Partie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="partie_tour_joueur", type="integer")
     */
    private $partieTourJoueur = 1;

    /**
     * @var array
     *
     * @ORM\Column(name="partie_pioche", type="json_array", nullable=true)
     */
    private $partiePioche;

    /**
     * @var array
     *
     * @ORM\Column(name="partie_terrain_joueur1", type="json_array", nullable=true)
     */
    private $partieTerrainJoueur1;

    /**
     * @var array
     *
     * @ORM\Column(name="partie_terrain_joueur2", type="json_array", nullable=true)
     */
    private $partieTerrainJoueur2;

    /**
     * @var array
     *
     * @ORM\Column(name="partie_defausse", type="json_array", nullable=true)
     */
    private $partieDefausse;

    /**
     * @var array
     *
     * @ORM\Column(name="partie_main_joueur1", type="json_array", nullable=true)
     */
    private $partieMainJoueur1;

    /**
     * @var array
     *
     * @ORM\Column(name="partie_main_joueur2", type="json_array", nullable=true)
     *
     */
    private $partieMainJoueur2;

    /**
     * @var int
     *
     * @ORM\Column(name="partie_score_joueur1", type="integer")
     */
    private $partieScoreJoueur1 =0;

    /**
     * @var int
     *
     * @ORM\Column(name="partie_score_joueur2", type="integer")
     */
    private $partieScoreJoueur2 = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="partie_date", type="datetime", nullable=true)
     */
    private $partieDate;


    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="parties_1", fetch="EAGER")
     */

    private $joueur1;

    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="parties_2", fetch="EAGER")
     */

    private $joueur2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="partie_date_fin", type="datetime", nullable=true)
     */
    private $partieDateFin;


    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Carte")
     *
     */
    private $cartes;


    /**
     * @var boolean
     *
     * @ORM\Column(name="partie_carte_posee", type="boolean", nullable=true)
     */
    private $partieCartePosee;


    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Tournois", inversedBy="parties", fetch="EAGER")
     *
     */
    private $tournois;


    public function __construct()
    {
        $this->setPartieDate(new \DateTime("now"));

//        $this->cartes = new  \Doctrine\Common\Collections\ArrayCollection();
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
     * Set partieTourJoueur
     *
     * @param integer $partieTourJoueur
     *
     * @return Partie
     */
    public function setPartieTourJoueur($partieTourJoueur)
    {
        $this->partieTourJoueur = $partieTourJoueur;

        return $this;
    }

    /**
     * Get partieTourJoueur
     *
     * @return int
     */
    public function getPartieTourJoueur()
    {
        return $this->partieTourJoueur;
    }

    /**
     * Set partiePioche
     *
     * @param array $partiePioche
     *
     * @return Partie
     */
    public function setPartiePioche($partiePioche)
    {
        $this->partiePioche = $partiePioche;

        return $this;
    }

    /**
     * Get partiePioche
     *
     * @return array
     */
    public function getPartiePioche()
    {
//        return $this->partiePioche;

        $j = json_decode($this->partiePioche);
        return $j;
    }

    /**
     * Set partieTerrainJoueur1
     *
     * @param array $partieTerrainJoueur1
     *
     * @return Partie
     */
    public function setPartieTerrainJoueur1($partieTerrainJoueur1)
    {
        $this->partieTerrainJoueur1 = $partieTerrainJoueur1;

        return $this;
    }

    /**
     * Get partieTerrainJoueur1
     *
     * @return array
     */
    public function getPartieTerrainJoueur1()
    {
//        return $this->partieTerrainJoueur1;

        $j = json_decode($this->partieTerrainJoueur1);
        return $j;
    }

    /**
     * Set partieTerrainJoueur2
     *
     * @param array $partieTerrainJoueur2
     *
     * @return Partie
     */
    public function setPartieTerrainJoueur2($partieTerrainJoueur2)
    {
        $this->partieTerrainJoueur2 = $partieTerrainJoueur2;

        return $this;
    }

    /**
     * Get partieTerrainJoueur2
     *
     * @return array
     */
    public function getPartieTerrainJoueur2()
    {
//        return $this->partieTerrainJoueur2;

        $j = json_decode($this->partieTerrainJoueur2);
        return $j;
    }

    /**
     * Set partieDefausse
     *
     * @param array $partieDefausse
     *
     * @return Partie
     */
    public function setPartieDefausse($partieDefausse)
    {
        $this->partieDefausse = $partieDefausse;

        return $this;
    }

    /**
     * Get partieDefausse
     *
     * @return array
     */
    public function getPartieDefausse()
    {
//        return $this->partieDefausse;

        $j = json_decode($this->partieDefausse);
        return $j;
    }

    /**
     * Set partieMainJoueur1
     *
     * @param array $partieMainJoueur1
     *
     * @return Partie
     */
    public function setPartieMainJoueur1($partieMainJoueur1)
    {
        $this->partieMainJoueur1 = $partieMainJoueur1;

        return $this;
    }

    /**
     * Get partieMainJoueur1
     *
     * @return array
     */
    public function getPartieMainJoueur1()
    {
//        return $this->partieMainJoueur1;

        $j = json_decode($this->partieMainJoueur1);


        return $j;
    }

    /**
     * Set partieMainJoueur2
     *
     * @param array $partieMainJoueur2
     *
     * @return Partie
     */
    public function setPartieMainJoueur2($partieMainJoueur2)
    {
        $this->partieMainJoueur2 = $partieMainJoueur2;

        return $this;
    }

    /**
     * Get partieMainJoueur2
     *
     * @return array
     */
    public function getPartieMainJoueur2()
    {
//        return $this->partieMainJoueur2;

        $j = json_decode($this->partieMainJoueur2);

        return $j;
    }

    /**
     * Set partieScoreJoueur1
     *
     * @param integer $partieScoreJoueur1
     *
     * @return Partie
     */
    public function setPartieScoreJoueur1($partieScoreJoueur1)
    {
        $this->partieScoreJoueur1 = $partieScoreJoueur1;

        return $this;
    }

    /**
     * Get partieScoreJoueur1
     *
     * @return int
     */
    public function getPartieScoreJoueur1()
    {
        return $this->partieScoreJoueur1;
    }

    /**
     * Set partieScoreJoueur2
     *
     * @param integer $partieScoreJoueur2
     *
     * @return Partie
     */
    public function setPartieScoreJoueur2($partieScoreJoueur2)
    {
        $this->partieScoreJoueur2 = $partieScoreJoueur2;

        return $this;
    }

    /**
     * Get partieScoreJoueur2
     *
     * @return int
     */
    public function getPartieScoreJoueur2()
    {
        return $this->partieScoreJoueur2;
    }

    /**
     * Set partieDate
     *
     * @param \DateTime $partieDate
     *
     * @return Partie
     */
    public function setPartieDate($partieDate)
    {
        $this->partieDate = $partieDate;

        return $this;
    }

    /**
     * Get partieDate
     *
     * @return \DateTime
     */
    public function getPartieDate()
    {
        return $this->partieDate;
    }


    /**
     * Set joueur1
     *
     * @param \AppBundle\Entity\User $joueur1
     *
     * @return Partie
     */
    public function setJoueur1(\AppBundle\Entity\User $joueur1 = null)
    {
        $this->joueur1 = $joueur1;

        return $this;
    }

    /**
     * Get joueur1
     *
     * @return \AppBundle\Entity\User
     */
    public function getJoueur1()
    {
        return $this->joueur1;
    }

    /**
     * Set joueur2
     *
     * @param \AppBundle\Entity\User $joueur2
     *
     * @return Partie
     */
    public function setJoueur2(\AppBundle\Entity\User $joueur2 = null)
    {
        $this->joueur2 = $joueur2;

        return $this;
    }

    /**
     * Get joueur2
     *
     * @return \AppBundle\Entity\User
     */
    public function getJoueur2()
    {
        return $this->joueur2;
    }

    /**
     * Set partieDateFin
     *
     * @param \DateTime $partieDateFin
     *
     * @return Partie
     */
    public function setPartieDateFin($partieDateFin)
    {
        $this->partieDateFin = $partieDateFin;

        return $this;
    }

    /**
     * Get partieDateFin
     *
     * @return \DateTime
     */
    public function getPartieDateFin()
    {
        return $this->partieDateFin;
    }



    /**
     * Add carte
     *
     * @param \AppBundle\Entity\Carte $carte
     *
     * @return Partie
     */
    public function addCarte(\AppBundle\Entity\Carte $carte)
    {
        $this->cartes[] = $carte;

        return $this;
    }

    /**
     * Remove carte
     *
     * @param \AppBundle\Entity\Carte $carte
     */
    public function removeCarte(\AppBundle\Entity\Carte $carte)
    {
        $this->cartes->removeElement($carte);
    }

    /**
     * Get cartes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartes()
    {
        return $this->cartes;
    }

    /**
     * Set partieCartePosee
     *
     * @param boolean $partieCartePosee
     *
     * @return Partie
     */
    public function setPartieCartePosee($partieCartePosee)
    {
        $this->partieCartePosee = $partieCartePosee;

        return $this;
    }

    /**
     * Get partieCartePosee
     *
     * @return boolean
     */
    public function getPartieCartePosee()
    {
        return $this->partieCartePosee;
    }

    /**
     * Set tournois
     *
     * @param \AppBundle\Entity\Tournois $tournois
     *
     * @return Partie
     */
    public function setTournois(\AppBundle\Entity\Tournois $tournois = null)
    {
        $this->tournois = $tournois;

        return $this;
    }

    /**
     * Get tournois
     *
     * @return \AppBundle\Entity\Tournois
     */
    public function getTournois()
    {
        return $this->tournois;
    }
}
