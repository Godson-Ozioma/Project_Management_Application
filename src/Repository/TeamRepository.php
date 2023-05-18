<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\TeamMembers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    private $projectRepository;
    public function __construct(ManagerRegistry $registry)
    {
        $this->projectRepository = new ProjectRepository($registry);
        parent::__construct($registry, Team::class);
    }

    public function save(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Team $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createTeam($teamName, $loggedInUser)
    {
        $team = new Team();
        $team->setTeamName($teamName);
        $team->setUser($loggedInUser);

        $this->getEntityManager()->persist($team);
        $this->getEntityManager()->flush();

        // add the user that created the team as a member of the team
        $this->addUserToTeam($team, $loggedInUser);
    }

    public function addUserToTeam($team, $user)
    {
        $existingUsers = $this->getEntityManager()->getRepository(TeamMembers::class)->findBy([
            'user' => $user,
            'team' => $team
        ]);

        if(!empty($existingUsers)){ // the user is already a team member
            return;
        }
        // add the user as a team member
        $teamMembers = new TeamMembers();
        $teamMembers->setUser($user);
        $teamMembers->setTeam($team);

        $this->getEntityManager()->persist($teamMembers);
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return Team[] Returns an array of Team objects
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

//    public function findOneBySomeField($value): ?Team
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function listAllTeamsForUser($user): array
    {
        $teamMemberList = $this->getEntityManager()->getRepository(TeamMembers::class)->findBy([
            'user' => $user
        ]);

        $teamList = [];

        foreach ($teamMemberList as $teamMember){
            $team = $teamMember->getTeam();

            $teamList[] = $team;
        }

        return $teamList;
    }

    public function getAllMembersInTeam($team): array
    {
        // get all the objects in the team members table (i.e teams and users)
        $teamMembers = $this->getEntityManager()->getRepository(TeamMembers::class)->findBy([
            'team' => $team
        ]);

        $allMembers = []; // store all the users in the team (stores user objects not team member objects)
        foreach ($teamMembers as $teamMember){
            $user = $teamMember->getUser();
            $allMembers[] = $user;
        }

        return $allMembers;
    }

    public function getTeamByTeamName($teamName){
        return $this->getEntityManager()->getRepository(Team::class)->findOneBy([
            'team_name' => $teamName,
        ]);
    }

    public function getTeamById($teamId){
        return $this->getEntityManager()->getRepository(Team::class)->find($teamId);
    }

    public function deleteTeam(Team $team){
        $projects = $this->getEntityManager()->getRepository(Project::class)->findBy([
            'team' =>  $team
        ]);

        // remove projects
        foreach ($projects as $project){
            $this->projectRepository->deleteProject($project);
        }

        // remove team
        $this->getEntityManager()->remove($team);
        $this->getEntityManager()->flush();
    }
}
