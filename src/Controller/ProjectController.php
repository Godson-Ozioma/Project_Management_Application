<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

class ProjectController extends AbstractController
{

    private ProjectRepository $projectRepository;
    private TaskRepository $taskRepository;

    public function __construct(ManagerRegistry $doctrine){
        $this->projectRepository = new ProjectRepository($doctrine);
        $this->taskRepository = new TaskRepository($doctrine);
    }

    #[Route('/createProject', name: 'create_project')]
    public function addProject(Environment $twig, Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectFormType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $project = $form->getData();
            $project->setUser($this->getUser());


            $entityManager->persist($project);

            $entityManager->flush();


            return $this->redirectToRoute('projects');
        }

        return $this->render('newProject.html.twig', [
            'project_form' => $form->createView()
        ]);
    }


    #[Route('/projects', name: 'projects')]
    public function listProjects(ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $pageTitle = "Your Personal Projects";

        //delete a project
        if (isset($_GET['deleteProjectId'])){
            $this->projectRepository->deleteProject(htmlentities($_GET['deleteProjectId']));
        }

        // the user clicked on the submit button to create a task
        if (isset($_POST['createTaskSubmitBtn'])){

            $projectId = htmlentities($_GET['addTaskProjectId']);
            $project = $this->projectRepository->getProject($projectId);

            $this->taskRepository->addTask(htmlentities($_POST['taskName']), $this->getUser(), $project);
        }

        // delete task
        if(isset($_GET['deleteTaskId'])){
            $task = $this->taskRepository->getTask(htmlentities($_GET['deleteTaskId']));
            $this->taskRepository->remove($task, true);

        }

        // edit task
        if(isset($_POST['editTaskSubmitBtn']) && isset($_GET['editTaskId'])){
            $task = $this->taskRepository->getTask(htmlentities($_GET['editTaskId']));
            $this->taskRepository->editTask($task, htmlentities($_GET['newTaskName']));
        }


        // get the list of the logged in user's personal projects
        $projectList = $this->projectRepository->getPersonalProjectList($this->getUser());


        // display the view
        return $this->render('projects.html.twig', array(
            'colorScheme' => "info",
            'projectList' => $projectList,
            'pageTitle' => $pageTitle
        ));

    }

}