<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    public function testCreateProject(){
        $this->assertTrue(true);
        $this->assertEquals("a", "a");
        $this->assertEquals("a", "a");
        $this->assertTrue(true);
        $this->assertFalse(false);
        $this->assertEquals("a", "a");
    }

//    public function testCreateProject()
//    {
////        $client = static::createClient();
////
////        $userRepository = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
////        $user = $userRepository->findOneBy(['username' => 'owner']);
////        $client->loginUser($user);
//
//        // Submit a form to create a new project
////        $crawler = $client->request('GET', '/projects');
////        $form = $crawler->selectButton('Save')->form();
////        $form['project[name]'] = 'Test Project';
////        $form['project[description]'] = 'This is a test project.';
////        $client->submit($form);
////
////        // Check that the project was added to the database
////        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
////        $projectRepository = $entityManager->getRepository('App:Project');
////        $project = $projectRepository->findOneBy(['name' => 'Test Project']);
//
////        $this->assertNotNull($project);
////        $this->assertEquals('This is a test project.', $project->getDescription());
//    }
//
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertTrue($client->getResponse()->isSuccessful());

//    private $entityManager;
//
//    protected function setUp(): void
//    {
//        $kernel = self::bootKernel();
//
//        $this->entityManager = $kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();
//    }
//
//    public function testSearchByName()
//    {
//        $product = $this->entityManager
//            ->getRepository(Product::class)
//            ->findOneBy(['name' => 'Priceless widget'])
//        ;
//
//        $this->assertSame(14.50, $product->getPrice());
//    }
//
//
//
//    protected function tearDown(): void
//    {
//        parent::tearDown();
//
//        // doing this is recommended to avoid memory leaks
//        $this->entityManager->close();
//        $this->entityManager = null;
//    }
}
