<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\TeamMembers;
use App\Entity\TeamMessages;
use App\Entity\User;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TeamMessagesRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    private TeamRepository $teamRepository;
    private ProjectRepository $projectRepository;
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private TeamMessagesRepository $messagesRepository;
    private ?Team $currentTeam;
    private $session;

    public function __construct(ManagerRegistry $doctrine, SessionInterface $session){
        $this->teamRepository = new TeamRepository($doctrine);
        $this->projectRepository = new ProjectRepository($doctrine);
        $this->taskRepository = new TaskRepository($doctrine);
        $this->userRepository = new UserRepository($doctrine);
        $this->messagesRepository = new TeamMessagesRepository($doctrine);
        $this->currentTeam = null;
        $this->session = $session;
    }

    #[Route('/teams', name: 'teams')]
    public function listTeams(ManagerRegistry $doctrine, EntityManagerInterface $entityManager) : Response
    {
        $loggedInUser = $this->getUser();

        // Create a new team
        if(isset($_POST['createTeamBtn'])){
            $teamName = htmlentities($_POST['teamName']);

            $this->teamRepository->createTeam($teamName, $loggedInUser);
        }

        // Add a user to the team
        if(isset($_POST['addTeamMemberBtn'])){
            $username = $_POST['username'];
            $user = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $username
            ]);

            $entityManager->persist($user);
            $entityManager->flush();
        }


        if(isset($_GET['leaveTeamId'])){
            $teamID = htmlentities($_GET['leaveTeamId']);

            $team = $this->teamRepository->getTeamById($teamID);

            $teamMember = $entityManager->getRepository(TeamMembers::class)->findOneBy([
                'user' => $this->getUser(),
                'team' => $team
            ]);


            $entityManager->remove($teamMember);
            $entityManager->flush();

            /// check if there are still users left in the team

            $allTeamMembers = $entityManager->getRepository(TeamMembers::class)->findBy([
                'team' => $team
            ]);

            $allMembers = [];
            foreach ($allTeamMembers as $teamMember){
                $allMembers[] = $teamMember->getUser();
            }
            // the last member of the team left
            if(empty($allMembers)){
//                $this->deleteTeam($entityManager, $team);
                $this->teamRepository->deleteTeam($team);
            }else{
                /// Unassign all tasks assigned to the user

                $teamProjects = $this->projectRepository->getTeamProjectList($team);
                foreach ($teamProjects as $project){
                    $allUserTasks = $entityManager->getRepository(Task::class)->findBy([
                        'user' => $this->getUser(),
                        'project' => $project
                    ]);
                    // there are tasks in the project assigned to the user
                    if(!empty($allUserTasks)){
                        foreach ($allUserTasks as $userTask){
//                            $userTask->setUser($allMembers[0]); // reassign to another member
                            $this->taskRepository->editTaskOwner($userTask, $allMembers[0]);
                        }
                    }
                }



            }
        }

        /// List the teams that the logged-in user is a member of

        $teamList = $this->teamRepository->listAllTeamsForUser($this->getUser());

        return $this->render('teams.html.twig', [
            'teamList' => $teamList
        ]);

    }


    #[Route('/teamView', name: 'teamView')]
    public function teamView(ManagerRegistry $doctrine, EntityManagerInterface $entityManager) : ?Response{

        $teamName = null;

        if(isset($_GET['teamName'])){
            $teamName = htmlentities($_GET['teamName']);

        }

        $team = $this->teamRepository->getTeamByTeamName($teamName);

        if($teamName != null){
            $this->currentTeam = $team;
            $this->session->set('team', $team);
            $this->session->set('teamName', $teamName);
        }

        // Live search for users
        if(isset($_GET['liveSearchForUser'])){
            $searchInput = htmlentities($_GET['liveSearchForUser']);

            $matchingUsers = $this->userRepository->findUser($searchInput);

            $response = json_encode($matchingUsers);

            return new Response($response);
        }

        // Live Message
        if(isset($_GET['liveMessages'])){
            $team = $this->session->get('team');
            $teamMessages = $this->messagesRepository->fetchAllTeamMessages($team);

            $data = [];

            foreach ($teamMessages as $message){
                $user = $this->userRepository->find($message->getUser());
                $messageText = $message->getMessage();

                $data[] = [
                    'username' => $user->getUsername(),
                    'message' => $messageText
                ];
            }

            return new Response(json_encode($data));
        }

//         Send live message
        if(isset($_GET['sendLiveMessage'])){
            $message = htmlentities($_GET['sendLiveMessage']);

            $team = $this->teamRepository->getTeamByTeamName($this->session->get('teamName'));



            $this->messagesRepository->sendMessage($message, $team, $this->getUser());


            return new Response();
        }

        // Add team member
        if(isset($_GET['addUserId'])){

            $user = $this->userRepository->find(htmlentities($_GET['addUserId']));


           $this->teamRepository->addUserToTeam($team, $user);



        }

        // create team project
        if(isset($_POST['createTeamProjectBtn'])){
            $project = new Project();
            $project->setTitle(htmlentities(htmlentities($_POST['projectName'])));
            $project->setUser($this->getUser());
            $project->setTeam($team);
            $deadline = DateTime::createFromFormat('Y-m-d', htmlentities($_POST['deadline']));
            $project->setDeadline($deadline);

            $entityManager->persist($project);
            $entityManager->flush();
        }

        // Edit task
        if(isset($_POST['editTaskSubmitBtn']) && isset($_GET['editTaskId'])){
            $task = $this->taskRepository->getTask(htmlentities($_GET['editTaskId']));
            $this->taskRepository->editTask($task, htmlentities($_POST['newTaskName']));
        }

        // Delete task
        if(isset($_GET['deleteTaskId'])){
            $task = $this->taskRepository->getTask(htmlentities($_GET['deleteTaskId']));
            $this->taskRepository->remove($task, true);

        }

        // Create task
        if (isset($_POST['createTaskSubmitBtn'])){
            $projectId = htmlentities($_GET['addTaskProjectId']);
            $project = $this->projectRepository->getProject($projectId);

            $this->taskRepository->addTask(htmlentities($_POST['taskName']), $this->getUser(), $project);
        }

        // Delete Project
        if (isset($_GET['deleteProjectId'])){
            $this->projectRepository->deleteProject(htmlentities($_GET['deleteProjectId']));
        }



        $allMembers = $this->teamRepository->getAllMembersInTeam($team);

        // get all the projects in the team
        $projectList = $this->projectRepository->getTeamProjectList($team);


        $theme = "info";
        return $this->render('teamView.html.twig', [
            'theme' => $theme,
            'team' => $team,
            'teamName' => $teamName,
            'allMembers' => $allMembers,
            'projectList' => $projectList
        ]);
    }


}