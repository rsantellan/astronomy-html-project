<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{

    public function indexAction()
    {
        return $this->render('AppBundle:Admin:index.html.twig', array());
    }
    
    public function galleriesAction()
    {
      return $this->render('AppBundle:Admin:galleries.html.twig', array(
                
            )); 
    }    

}
