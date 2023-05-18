<?php

namespace App\Repository;

use App\Entity\TeamMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Client\Curl\User;

/**
 * @extends ServiceEntityRepository<TeamMessages>
 *
 * @method TeamMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamMessages[]    findAll()
 * @method TeamMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamMessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamMessages::class);
    }

    public function save(TeamMessages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TeamMessages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function fetchAllTeamMessages($team): array
    {
        return $this->getEntityManager()->getRepository(TeamMessages::class)->findBy([
            'team' => $team,
        ]);
    }

//    /**
//     * @return TeamMessages[] Returns an array of TeamMessages objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TeamMessages
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function sendMessage($message, $team, $user)
    {
        $teamMessage = new TeamMessages();
        $teamMessage->setTeam($team);

        $teamMessage->setUser($user);
        $teamMessage->setMessage($message);

        $this->getEntityManager()->persist($teamMessage);
        $this->getEntityManager()->flush();
    }
}
