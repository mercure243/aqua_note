<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity(repositoryClass="AppBundle\Repository\GenusRepository")
* @ORM\Table(name="genus")
*/
class Genus
{
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
    * @Assert\NotBlank()
    * @ORM\Column(type="string")
    */
    private $name;

    /**
    * @Assert\NotBlank()
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubFamily")
    * @ORM\JoinColumn(nullable=false)
    */
    private $subFamily;

    /**
    * @Assert\NotBlank()
    * @Assert\Range(min=0, minMessage="Negative species! Come on...")
    * @ORM\Column(type="integer")
    */
    private $speciesCount;

    /**
    * @ORM\Column(type="string", nullable=true)
    */
    private $funFact;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date")
     */
     private $discoveredAt;

    /**
    * @ORM\OneToMany(targetEntity="GenusNote", mappedBy="genus")
    * @ORM\OrderBy({"createdAt" = "DESC"})
    */
    private $notes;

    public function _construct()
    {
      $this->notes = new ArrayCollection();
    }

    public function getId()
    {
      return $this->id;
    }

    /*
    * @return ArrayCollection|GenusNote[]
    */
    public function getNotes()
    {
      return $this->notes;
    }

    public function setIsPublished($isPublished)
    {
      $this->isPublished = $isPublished;
    }

    public function GetIsPublished()
    {
      return $this->isPublished;
    }

    public function getSubFamily()
    {
      return $this->subFamily;
    }

    public function setSubFamily($subFamily)
    {
      $this->subFamily = $subFamily;
    }

    public function getName()
    {
      return $this->name;
    }

    public function setName($name)
    {
      $this->name = $name;
    }

    public function getSpeciesCount()
    {
            return $this->speciesCount;
    }
    public function setSpeciesCount($speciesCount)
    {
            $this->speciesCount = $speciesCount;
    }
    public function getFunFact()
    {
            return $this->funFact;
    }
    public function setFunFact($funFact)
    {
            $this->funFact = $funFact;
    }
    public function getUpdatedAt()
    {
      return new \DateTime('-'.rand(0,100).' days');
    }

    public function getDiscoveredAt()
    {
      return $this->discoveredAt;
    }

    public function setDiscoveredAt($discoveredAt)
    {
      $this->discoveredAt = $discoveredAt;
    }


}
