<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ConversationRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }


    public function getConversation(int $userId)
    {
        $qb = $this->createQueryBuilder();

        $query = $qb->select('u.username', 'u.id')
            ->from(User::class,'u')
            ->where('u.id != :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
        ;
        return $query->getResult();
    }

    public function getMessageFor(String $from, String $to)
    {


    }






}