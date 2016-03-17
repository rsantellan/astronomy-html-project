<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use AppBundle\Entity\Integrante;
/**
 * Description of LoadUserFixture
 *
 * @author Rodrigo Santellan
 */
class LoadIntegrantesFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function getOrder() {
        return 3;
    }

    public function load(ObjectManager $manager) {
        $integrante1 = new Integrante();
        $integrante1->setDescription('Astrónomo, docente y divulgador científico. Sus áreas de interés son las Ciencias Planetarias, con énfasis en los procesos de impactos de asteroides y cometas. Ha producido espectáculos para Planetario y dado conferencias en escuelas y liceos de todo el país.');
        $integrante1->setName('Dr. Gonzalo Tancredi');
        $integrante1->setPosicionVisual(0);
        $integrante1->setPosition('Responsable');
        
        $manager->persist($integrante1);
        
        $integrante2 = new Integrante();
        $integrante2->setDescription('Ingeniero Eléctrico, especialización en radioastronomía y ciencias espaciales, Chalmers University of Technology. Maestría en Física, opción Astronomía, Facultad de Ciencias/Udelar. Asesor de la Dirección Nacional de Telecomunicaciones y Servicios de Comunicación Audiovisual (Dinatel), del Ministerio de Industria, Energía y Minería (MIEM).');
        $integrante2->setName('Mag. Ing. Manuel Caldas');
        $integrante2->setPosicionVisual(1);
        $integrante2->setPosition('Co-Responsable');
        
        $manager->persist($integrante2);
        
        
        $integrante3 = new Integrante();
        $integrante3->setDescription('Técnico en electrónica, UTU. Técnico en comunicaciones (telemetría, redes de datos,radiocomunicaciones y alarmas) en represa de Salto Grande. \r\nTareas en el presente proyecto: Responsable en Salto Grande del mantenimiento de las estaciones.');
        $integrante3->setName('Tec. El. Javier Capeche');
        $integrante3->setPosicionVisual(2);
        $integrante3->setPosition('Técnico');
        
        $manager->persist($integrante3);
        
        
        $integrante4 = new Integrante();
        $integrante4->setDescription('Técnico en electrónica.  Técnico en electrónica de la División Ingeniería del LATU. \r\nTareas en el presente proyecto: Encargado del diseño y la realización del hardware de prototipos.');
        $integrante4->setName('Alberto Ceretta');
        $integrante4->setPosicionVisual(3);
        $integrante4->setPosition('Técnico');
        
        $manager->persist($integrante4);
        
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}


