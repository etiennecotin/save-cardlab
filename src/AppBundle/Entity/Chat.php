<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="chat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChatRepository")
 */
class Chat
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
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="User", fetch="EAGER")
     */
    private $joueur;


    /**
     * @var array
     *
     * @ORM\ManyToOne(targetEntity="Partie")
     *
     */
    private $partie;

    public function __construct()
    {
        $this->setDate(new \DateTime("now"));
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
     * Set message
     *
     * @param string $message
     *
     * @return Chat
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Chat
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set joueur
     *
     * @param \AppBundle\Entity\User $joueur
     *
     * @return Chat
     */
    public function setJoueur(\AppBundle\Entity\User $joueur = null)
    {
        $this->joueur = $joueur;

        return $this;
    }

    /**
     * Get joueur
     *
     * @return \AppBundle\Entity\User
     */
    public function getJoueur()
    {
        return $this->joueur;
    }

    /**
     * Set partie
     *
     * @param \AppBundle\Entity\Partie $partie
     *
     * @return Chat
     */
    public function setPartie(\AppBundle\Entity\Partie $partie = null)
    {
        $this->partie = $partie;

        return $this;
    }

    /**
     * Get partie
     *
     * @return \AppBundle\Entity\Partie
     */
    public function getPartie()
    {
        return $this->partie;
    }
}
