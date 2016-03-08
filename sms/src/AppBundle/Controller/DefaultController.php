<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Article;
use AppBundle\Form\ContactType;

class DefaultController extends Controller
{
    const ARTICLESPERPAGE = 10;
    const VIDEONAME = 'video.mp4';
    
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
    
    public function prototipoAction(Request $request)
    {
      return $this->render('AppBundle:default:prototipo.html.twig', array(
            'activemenu' => 'sistema',
        ));
    }
    
    public function estacionesAction(Request $request)
    {
      return $this->render('AppBundle:default:estaciones.html.twig', array(
            'activemenu' => 'sistema',
        ));
    }
    
    private function createVideo($estacionId = 0)
    {
      $completePath = $this->getNowDirectory($estacionId);
      $files  = scandir($completePath, SCANDIR_SORT_DESCENDING);
      $now = new \DateTime();
      $video_path = sys_get_temp_dir().DIRECTORY_SEPARATOR.$estacionId.$now->format('Y-m-d-H-i').DIRECTORY_SEPARATOR;
      $video_path = sys_get_temp_dir().DIRECTORY_SEPARATOR.$estacionId.'2016-12-08-10-10'.DIRECTORY_SEPARATOR;
      
      if(file_exists($video_path.self::VIDEONAME))
      {
        return $video_path.self::VIDEONAME;
      }
      if(!is_dir($video_path))
      {
        mkdir($video_path, 0755, true);
      }
      $counter = 0;
      $list = array();
      while($counter <= 180 && $counter < count($files))
      {
        if($files[$counter] != '.' && $files[$counter] != '..')
        {
          $list[] = $completePath.$files[$counter];
        }
        $counter++;
      }
      $reversed = array_reverse($list);
      $counter = 0;
      foreach($reversed as $file)
      {
        copy($file, $video_path.sprintf("img-%03d.jpg", $counter));
        $counter ++;
      }
      $cmd = $this->container->getParameter('ffmpeg_location').' -framerate 1 -i '.$video_path.'img-%03d.jpg -c:v libx264 -r 30 -pix_fmt yuv420p '.$video_path.self::VIDEONAME;
      //$cmd = 'ffmpeg -framerate 1/1 -i '.$video_path.'img-%03d.jpg -r 30 '.$video_path.'video.avi';
      $return = exec($cmd);

      return $video_path.$video_path.self::VIDEONAME;
    }
    
    private function getNowDirectory($estacionId)
    {
      $path = $this->container->getParameter('images_files');
      // Get today folder
      $now = new \DateTime();
      $completePath = $path.'Images'.DIRECTORY_SEPARATOR.$estacionId.DIRECTORY_SEPARATOR.$now->format('Y/m/d');
      
      $completePath = '/home/rodrigo/sns/Images/4/2016/12/08/';
      return $completePath;
    }
    
    private function getLastImage($estacionId)
    {
      $completePath = $this->getNowDirectory($estacionId);
      $files  = scandir($completePath, SCANDIR_SORT_DESCENDING);
      if(count($files) > 2)
      {
        
        $info = new \SplFileInfo($completePath.$files[0]);
        $tmpFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.$info->getFilename();
        copy($completePath.$files[0], $tmpFile);
        return $tmpFile;
      }
      return null;
    }
    
    public function imagesAction(Request $request)
    {
      
      $images = array(
         0 => $this->getLastImage(0),  
         1 => $this->getLastImage(1),  
         2 => $this->getLastImage(2),  
         3 => $this->getLastImage(3),  
         4 => $this->getLastImage(4),  
      );
      return $this->render('AppBundle:default:imagenes.html.twig', array(
            'activemenu' => 'imagenes',
            'images' => $images,
            'video' => $this->createVideo(),
        ));
    }
    
    public function retrieveVideoAction(Request $request, $estacionId)
    {
        //ffmpeg -framerate 1/1 -i /tmp/02016-12-08-10-10/img-%03d.jpg -vcodec mpeg4 -r 30 /tmp/02016-12-08-10-10/video.mp4
      //https://trac.ffmpeg.org/wiki/CompilationGuide/Ubuntu
        $videoPath = $this->createVideo($estacionId);
        $response = new Response();
        $response->setPublic();
        $response->headers->add(array('Content-Type' => 'video/mp4'));
        $response->setContent(file_get_contents($videoPath));
        $response->setStatusCode(200);
        return $response;
    }
    
    public function downloadOriginalFileAction(Request $request, $id)
    {
      
      $document = new \AppBundle\Entity\Document();
      $em = $this->getDoctrine()->getManager();
      $query = $em->createQuery("select f from MaithCommonAdminBundle:mFile f join f.album a where a.object_id = :id and a.object_class = :object_class and a.name = :name order by f.orden ASC");
    //$query = $this->em->createQuery("select a from MaithCommonAdminBundle:mAlbum a where a.object_id = :id and a.object_class = :object_class and a.name = :name ");
      $query->setParameters(array('id' => $id, 'object_class' => $document->getFullClassName(), 'name' => 'documents'));
      $query->setMaxResults(1);
      $file = $query->getOneOrNullResult();
      if(!$file)
      {
        throw $this->createNotFoundException('No se encontro el archivo.');
      }
      //$file = $em->getRepository("MaithCommonAdminBundle:mFile")->find($fileId);
      $content = file_get_contents($file->getFullPath());
      $response = new Response();

      /* Figure out the MIME type (if not specified) */
      $known_mime_types = array(
          "pdf" => "application/pdf",
          "txt" => "text/plain",
          "html" => "text/html",
          "htm" => "text/html",
          "exe" => "application/octet-stream",
          "zip" => "application/zip",
          "doc" => "application/msword",
          "xls" => "application/vnd.ms-excel",
          "ppt" => "application/vnd.ms-powerpoint",
          "gif" => "image/gif",
          "png" => "image/png",
          "jpeg" => "image/jpeg",
          "jpg" => "image/jpg",
          "php" => "text/plain"
      );
      $mime_type = $file->getType();
      if(!in_array($file->getType(), $known_mime_types))
      {
        $file_extension = strtolower(substr(strrchr($file->getName(), "."), 1));
        if (array_key_exists($file_extension, $known_mime_types)) {
          $mime_type = $known_mime_types[$file_extension];
        }
      }
      
      $response->headers->set('Content-Type', $mime_type);
      $response->headers->set('Content-Disposition', 'attachment;filename="'.$file->getName());

      $response->setContent($content);
      return $response;
    }    
}
