<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteProject($projectId): void
    {
//            $project = $entityManager->getRepository(Project::class)->findOneBy(array('id' => $projectId));
        $project = $this->getEntityManager()->getRepository(Project::class)->find($projectId);

        // delete all tasks in the project
        $tasks = $this->getEntityManager()->getRepository(Task::class)->findBy([
            'project' => $project
        ]);

        foreach ($tasks as $task){
            $this->getEntityManager()->remove($task);
        }

        $this->getEntityManager()->flush();

        // delete the project
        $this->getEntityManager()->remove($project);
////
        $this->getEntityManager()->flush();
    }

    public function getPersonalProjectList($user): array
    {
        return $this->getEntityManager()->getRepository(Project::class)->findBy([
            "user" => $user,
            "team" => null
        ]);
    }

    public function getTeamProjectList($team): array
    {
        return $this->getEntityManager()->getRepository(Project::class)->findBy([
            "team" => $team
        ]);
    }

    public function getProject($projectId){
        return $this->getEntityManager()->getRepository(Project::class)->find($projectId);
    }

    public function addTaskToProject(){

    }




//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
