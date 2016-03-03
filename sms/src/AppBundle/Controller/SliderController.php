<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Slider;

/**
 * Slider controller.
 *
 */
class SliderController extends Controller
{

    /**
     * Lists all Slider entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = array();
        $names = array('inicial');
        foreach($names as $name)
        {
          $entity = $em->getRepository('AppBundle:Slider')->findOneBy(array('name' => $name));
          if(!$entity)
          {
            $entity = new Slider();
            $entity->setName('inicial');
            $em->persist($entity);
            $em->flush();
          }
          $entities[] = $entity;
        }
        
        return $this->render('AppBundle:Slider:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Slider entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Slider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Slider entity.');
        }

        return $this->render('AppBundle:Slider:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
}
