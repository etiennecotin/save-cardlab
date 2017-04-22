<?php
 // src/AppBundle/Entity/User.php

 namespace AppBundle\Entity;

 use FOS\UserBundle\Model\User as BaseUser;
 use Doctrine\ORM\Mapping as ORM;
 use Doctrine\Common\Collections\ArrayCollection;

 /**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
 class User extends BaseUser
 {
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
     protected $id;


     /**
      *
      * @var string
      *
      * @ORM\Column(type="string", length=150, nullable = true)
      */
     protected $userPrenom;

     /**
      *
      * @var string
      *
      * @ORM\Column(type="string", length=150, nullable = true)
      */
     protected $userNom;

     /**
      *@var int
      *
      * @ORM\Column(name="user_best_score", type="integer", nullable = true)
      */
     protected $userBestScore;


     /**
      * 
      * @ORM\OneToMany(targetEntity="Partie", mappedBy="joueur1")
      */
     private $parties_1;
     /**
      *
      * @ORM\OneToMany(targetEntity="Partie", mappedBy="joueur2")
      */
     private $parties_2;


     private $parties;


     /**
      *
      * @ORM\ManyToMany(targetEntity="Tournois", inversedBy="users")
      */
     private $tournois;

     /**
      * Constructor
      */
     public function __construct()
     {
         parent::__construct();
         $this->parties_1 = new \Doctrine\Common\Collections\ArrayCollection();
         $this->parties_2 = new \Doctrine\Common\Collections\ArrayCollection();
         $this->parties = new \Doctrine\Common\Collections\ArrayCollection();


         $this->tournois = new \Doctrine\Common\Collections\ArrayCollection();
     }

     /**
      * Get parties
      *
      * @return arrayCollection
      */
     public function getParties()
     {
         if (count($this->parties_1) > 0)
             $this->parties[] = $this->parties_1;

         if (count($this->parties_2) > 0)
             $this->parties[] = $this->parties_2;

         return $this->parties;
     }

    /**
     * Set userPrenom
     *
     * @param string $userPrenom
     *
     * @return User
     */
    public function setUserPrenom($userPrenom)
    {
        $this->userPrenom = $userPrenom;

        return $this;
    }

    /**
     * Get userPrenom
     *
     * @return string
     */
    public function getUserPrenom()
    {
        return $this->userPrenom;
    }

    /**
     * Set userNom
     *
     * @param string $userNom
     *
     * @return User
     */
    public function setUserNom($userNom)
    {
        $this->userNom = $userNom;

        return $this;
    }

    /**
     * Get userNom
     *
     * @return string
     */
    public function getUserNom()
    {
        return $this->userNom;
    }

    /**
     * Add parties1
     *
     * @param \AppBundle\Entity\Partie $parties1
     *
     * @return User
     */
    public function addParties1(\AppBundle\Entity\Partie $parties1)
    {
        $this->parties_1[] = $parties1;

        return $this;
    }

    /**
     * Remove parties1
     *
     * @param \AppBundle\Entity\Partie $parties1
     */
    public function removeParties1(\AppBundle\Entity\Partie $parties1)
    {
        $this->parties_1->removeElement($parties1);
    }

    /**
     * Get parties1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParties1()
    {
        return $this->parties_1;
    }

    /**
     * Add parties2
     *
     * @param \AppBundle\Entity\Partie $parties2
     *
     * @return User
     */
    public function addParties2(\AppBundle\Entity\Partie $parties2)
    {
        $this->parties_2[] = $parties2;

        return $this;
    }

    /**
     * Remove parties2
     *
     * @param \AppBundle\Entity\Partie $parties2
     */
    public function removeParties2(\AppBundle\Entity\Partie $parties2)
    {
        $this->parties_2->removeElement($parties2);
    }

    /**
     * Get parties2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParties2()
    {
        return $this->parties_2;
    }

    /**
     * Set userBestScore
     *
     * @param integer $userBestScore
     *
     * @return User
     */
    public function setUserBestScore($userBestScore)
    {
        $this->userBestScore = $userBestScore;

        return $this;
    }

    /**
     * Get userBestScore
     *
     * @return integer
     */
    public function getUserBestScore()
    {
        return $this->userBestScore;
    }

    /**
     * Add tournois
     *
     * @param \AppBundle\Entity\Tournois $tournois
     *
     * @return User
     */
    public function addTournois(\AppBundle\Entity\Tournois $tournois)
    {
        $this->tournois[] = $tournois;

        return $this;
    }

    /**
     * Remove tournois
     *
     * @param \AppBundle\Entity\Tournois $tournois
     */
    public function removeTournois(\AppBundle\Entity\Tournois $tournois)
    {
        $this->tournois->removeElement($tournois);
    }

    /**
     * Get tournois
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTournois()
    {
        return $this->tournois;
    }
}
