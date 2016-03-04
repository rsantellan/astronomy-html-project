<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Article;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('AppBundle:Slider')->findOneBy(array('name' => 'inicial'));
        return $this->render('AppBundle:default:index.html.twig', array(
            'slider' => $slider,
            'activemenu' => 'inicio',
        ));
    }
    
    public function menuFooterNewsAction(Request $request)
    {
        return $this->render('AppBundle:default:_menuFooterNews.html.twig', array(
            'articles' => $this->retrieveRecentArticles(),
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
    
    private function retrieveRecentArticles()
    {
      $cache = $this->get('fscache');
      $data = $cache->fetch(Article::RECENTARTICLES);
      if($data == false)
      {
        $em = $this->getDoctrine()->getManager();

        $queryArticles = $em->createQuery("select c from AppBundle:Article c order by c.createdAt asc")
                            ->setFirstResult(0)
                            ->setMaxResults(3);

        $data = $queryArticles->getResult();
        $cache->save(Article::RECENTARTICLES, $data);
      }
      return $data;
    }
    
    private function retrievePerMonthOfLastYear($em)
    {
      $cache = $this->get('fscache');
      $data = $cache->fetch(Article::PASTYEARARTICLESDATA);
      if($data == false)
      {
        $sql = 'select count(id) as cantidad, MONTH(createdAt) as mes from sms_article where createdAt >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) group by MONTH(createdAt) order by createdat';
        $stmt = $em->getConnection()->prepare( $sql );
        $stmt->execute();
        $data = array();
        foreach ($stmt->fetchAll() as $row)
        {
          $data[] = array('nMonth' => (int)$row['mes'], 'month' => Article::retrieveMonthNameFromNumber((int)$row['mes']), 'cantidad' => $row['cantidad']);
        }
        $cache->save(Article::PASTYEARARTICLESDATA, $data);
      }
      
      
      return $data;
    }
    
    public function articleListAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
        
      $queryArticles = $em->createQuery("select c from AppBundle:Article c where c.active = 1 order by c.createdAt asc");
                          //->setFirstResult(0)
                          //->setMaxResults(3);
      
      $articles = $queryArticles->useResultCache(true, 360)->getResult();
      $categories = $em->createQuery('select ac from AppBundle:ArticleCategory ac')->getResult();
      $tags = $em->createQuery('select at from AppBundle:ArticleTag at')->getResult();
      return $this->render('AppBundle:default:articles.html.twig', array(
            'articles' => $articles,
            'activemenu' => 'noticias',
            'categories' => $categories,
            'tags' => $tags,
            'recentArticles' => $this->retrieveRecentArticles(),
            'perMonths' => $this->retrievePerMonthOfLastYear($em),
        ));
    }
}
