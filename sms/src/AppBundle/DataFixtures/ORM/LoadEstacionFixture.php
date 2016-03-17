<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Entity\Estacion;
/**
 * Description of LoadUserFixture
 *
 * @author Rodrigo Santellan
 */
class LoadEstacionFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function getOrder() {
        return 2;
    }

    public function load(ObjectManager $manager) {
        $estacion0 = new Estacion();
        $estacion0->setPosition(0);
        $estacion0->setLocation('Ubicada en el Laboratorio de Energía Solar (LES). LAT: -31° 16.9630’ / LON: -57° 55.0930’ / ALT (AMSL): 53.2 m');
        $estacion0->setName('LES');
        
        $estacion1 = new Estacion();
        $estacion1->setPosition(1);
        $estacion1->setLocation('Ubicada en la Escuela No 70 “Salto Grande”. LAT: -31° 15.4524’ / LON: -57° 53.4037’ /ALT (AMSL): 64.8 m');
        $estacion1->setName('Escuela 70 "Salto Grande"');
        
        $estacion2 = new Estacion();
        $estacion2->setPosition(2);
        $estacion2->setLocation('Ubicada en la Escuela No 91 “Portugal”. LAT: -31° 19.0293’ / LON: -57° 53.0127’ / ALT (AMSL): 55.2 m');
        $estacion2->setName('Escuela 91”Portugal');
        
        $estacion3 = new Estacion();
        $estacion3->setPosition(3);
        $estacion3->setLocation('Descripción: Ubicada en el predio de la Comisión Técnica Mixta de Salto Grande (CTM). LAT: A definir. LON: A definir. ALT: A definir.');
        $estacion3->setName('CTM');
        
        $estacion4 = new Estacion();
        $estacion4->setPosition(4);
        $estacion4->setLocation('Descripción: Ubicada en la Escuela No 26 “Osimani Llerena”. LAT: -31° 18.9205’ LON: -57° 57.1291’ ALT (AMSL): 55.2 m');
        $estacion4->setName('Escuela 26 “Osimani Llerena”');
        
        
        $manager->persist($estacion0);
        $manager->persist($estacion1);
        $manager->persist($estacion2);
        $manager->persist($estacion3);
        $manager->persist($estacion4);
        
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}


