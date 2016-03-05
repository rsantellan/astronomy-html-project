<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\Article;
use AppBundle\Form\ContactType;

class DefaultController extends Controller
{
    const ARTICLESPERPAGE = 10;
  
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('AppBundle:Slider')->findOneBy(array('name' => 'inicial'));
        return $this->render('AppBundle:default:index.html.twig', array(
            'slider' => $slider,
            'activemenu' => 'inicio',
        ));
    }
    
    public function lastArticlesAction(Request $request)
    {
        return $this->render('AppBundle:default:_lastArticles.html.twig', array(
            'articles' => $this->retrieveRecentArticles(),
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

        $queryArticles = $em->createQuery("select c from AppBundle:Article c order by c.createdAt desc")
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
    
    private function retrieveArticleCategories($em)
    {
      $categories = $em->createQuery('select ac from AppBundle:ArticleCategory ac')->getResult();
      return $categories;
    }
    
    private function retrieveArticleTags($em)
    {
      $tags = $em->createQuery('select at from AppBundle:ArticleTag at')->getResult();
      return $tags;
    }
    
    private function retrieveArticles($page)
    {
      $intPage = (int) $page;
      $start = $intPage * self::ARTICLESPERPAGE;
      $em = $this->getDoctrine()->getManager();
        
      $queryArticles = $em->createQuery("select c from AppBundle:Article c where c.active = 1 order by c.createdAt asc")
                          ->setFirstResult($start)
                          ->setMaxResults(self::ARTICLESPERPAGE);
      $articles = $queryArticles->getResult();
      return $articles;
    }
    
    private function doReturnArticleList($page)
    {
      return $this->render('AppBundle:default:articles.html.twig', array(
            'articles' => $this->retrieveArticles($page),
            'activemenu' => 'noticias',
            'perpage' => self::ARTICLESPERPAGE,
            'page' => $page,
        ));
    }
    public function articleListAction(Request $request)
    {
      
      return $this->doReturnArticleList(0);
    }
    
    public function articleListPagerAction(Request $request, $page)
    {
      return $this->doReturnArticleList($page);
    }
    
    
    public function articleAction(Request $request, $slug)
    {
      $em = $this->getDoctrine()->getManager();

      $article = $em->getRepository('AppBundle:Article')->findOneBy(array('slug' => $slug));
      return $this->render('AppBundle:default:article.html.twig', array(
            'article' => $article,
            'activemenu' => 'noticias',
        ));
    }
    
    
    public function articleSideBarAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      return $this->render('AppBundle:default:_articleSidebar.html.twig', array(
            'categories' => $this->retrieveArticleCategories($em),
            'tags' => $this->retrieveArticleTags($em),
            'recentArticles' => $this->retrieveRecentArticles(),
            'perMonths' => $this->retrievePerMonthOfLastYear($em),
        ));
    }
    
    public function contactoAction(Request $request)
    {
      $form = $this->createForm(new ContactType());
      return $this->render('AppBundle:default:contacto.html.twig', array(
            'activemenu' => 'contacto',
            'form' => $form->createView(),
        ));
    }
    
    public function contactoSendAction(Request $request)
    {
      $form = $this->createForm(new ContactType());
      $form->handleRequest($request);
      $result = false;
      $formView = false;
      if ($form->isValid()) {
        $message = \Swift_Message::newInstance()
                  ->setSubject('[SMS] Contacto desde sitio web')
                  ->setFrom(array('info@sms.com.uy' => 'SMS'))
                  ->setReplyTo($form->get('email')->getData())
                  ->setTo('rsantellan@gmail.com')
                  ->setBody('Mail de algo');

        $this->get('mailer')->send($message);
        $result = true;
      }else{
        $formView = $this->renderView('AppBundle:Article:_contactForm.html.twig', array(
            'form' => $form->createView(),
          ));
      }
      $response = new JsonResponse();
      $response->setData(array('result' => $result, 'html' => $formView));
      return $response;
    }
    
    public function aboutAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $integrantes = $em->createQuery('select e from AppBundle:Integrante e order by e.posicionVisual')->getResult();
      $documents = $em->createQuery('select e from AppBundle:Document e order by e.createdAt')->getResult();
      return $this->render('AppBundle:default:acerca.html.twig', array(
            'activemenu' => 'acerca',
            'integrantes' => $integrantes,
            'documents' => $documents,
        ));
    }
}
