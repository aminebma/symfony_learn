<?php


namespace App\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class TodoRepository extends DocumentRepository
{
    public function findById(string $id){
        return $this->createQueryBuilder()
            ->field('_id')->equals($id)
            ->getQuery()
            ->execute();
    }

    public function findAllOrderedByName()
    {
        return $this->createQueryBuilder()
            ->sort('date', 'ASC')
            ->getQuery()
            ->execute();
    }
}
