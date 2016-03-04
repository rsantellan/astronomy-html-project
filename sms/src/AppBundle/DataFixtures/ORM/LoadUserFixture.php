<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Maith\Common\UsersBundle\Entity\Role;
/**
 * Description of LoadUserFixture
 *
 * @author Rodrigo Santellan
 */
class LoadUserFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function getOrder() {
        return 1;
    }

    public function load(ObjectManager $manager) {
        /*
        $role = new Role();
        $role->setName("ROLE_ADMIN");
        $manager->persist($role);
        
        $userManager = $this->container->get('fos_user.user_manager');
        $admin = $userManager->createUser();
        $admin->setUsername('administrador');
        $admin->setEmail('admin@administrador.com');
        $admin->setPlainPassword('smssystem2016');
        $admin->setEnabled(true);
        $admin->setSuperAdmin(true);
        $admin->addRole($role);
        $manager->persist($admin);
        
        $manager->flush();
         * 
         */
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}


