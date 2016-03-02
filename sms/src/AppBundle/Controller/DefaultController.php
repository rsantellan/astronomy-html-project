<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            
        ));
    }
    
    public function menuFooterNewsAction(Request $request)
    {
        return $this->render('default/_menuFooterNews.html.twig', array(
            
        ));
    }
    public function menuFooterStationsAction(Request $request)
    {
        return $this->render('default/_menuFooterStations.html.twig', array(
            
        ));
    }
}
