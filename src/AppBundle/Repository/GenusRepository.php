<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    /**
     * @return Genus[]
     */
     public function findAllPublishedOrderedByRecentlyActive(){
        return $this->createQueryBuilder('genus')
              ->andWhere('genus.isPublished = :isPublished')
              ->setParameter('isPublished', true)
              ->leftJoin('genus.notes','genus_note')
              ->leftJoin('genus.subFamily','sub_family')
              ->orderBy('genus_note.createdAt', 'DESC')
              ->getQuery()
              ->execute();
            }
}
