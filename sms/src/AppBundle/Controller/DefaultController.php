<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('AppBundle:Slider')->findOneBy(array('name' => 'inicial'));
        return $this->render('AppBundle:default:index.html.twig', array(
            'slider' => $slider,
        ));
    }
    
    public function menuFooterNewsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $queryArticles = $em->createQuery("select c from AppBundle:Article c order by c.createdAt asc")
                            ->setFirstResult(0)
                            ->setMaxResults(3);
                            
        $articles = $queryArticles->useResultCache(true, 360)->getResult();

        return $this->render('AppBundle:default:_menuFooterNews.html.twig', array(
            'articles' => $articles,
        ));
    }
    
    public function menuFooterStationsAction(Request $request)
    {
        return $this->render('AppBundle:default:_menuFooterStations.html.twig', array(
            
        ));
    }
    
    public function newsletterFormAction(Request $request)
    {
        return $this->render('AppBundle:default:_newsletterForm.html.twig', array(
            
        ));
    }
    
    public function articleListAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
        
      $queryArticles = $em->createQuery("select c from AppBundle:Article c order by c.createdAt asc")
                          ->setFirstResult(0)
                          ->setMaxResults(3);

      $articles = $queryArticles->useResultCache(true, 360)->getResult();
      
      return $this->render('AppBundle:default:articles.html.twig', array(
            'articles' => $articles,
        ));
    }
}
