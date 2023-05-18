<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;

class Utility
{
    private static $instance = null;
    private function __construct() {}
//    private function __clone() {}
    public static function getInstance(): ?Utility
    {
        if(!isset(self::$instance)) {
            self::$instance = new Utility();
        }
        return self::$instance;
    }

    public static function deleteProject($entityManager)
    {
        $projectId = $_GET['deleteProjectId'];
//            $project = $entityManager->getRepository(Project::class)->findOneBy(array('id' => $projectId));
        $project = $entityManager->getRepository(Project::class)->find($projectId);

        // delete all tasks in the project
        $tasks = $entityManager->getRepository(Task::class)->findBy([
            'project' => $project
        ]);

        foreach ($tasks as $task){
            $entityManager->remove($task);
        }

        $entityManager->flush();

        // delete the project
        $entityManager->remove($project);
////
        $entityManager->flush();
    }

}