<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Article;
use AppBundle\Entity\NewsletterEmail;
use AppBundle\Form\ContactType;
use AppBundle\Form\NewsletterType;
use Alchemy\Zippy\Zippy;

class DefaultController extends Controller
{
    const ARTICLESPERPAGE = 10;
    const VIDEONAME = 'video.mp4';
    
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('AppBundle:Slider')->findOneBy(array('name' => 'inicial'));
        $dbEstaciones = $this->retrieveEstaciones($em);
        $estacionesClasses = array(
            0 => 'portfolio-item pf-web-design',
            1 => 'portfolio-item pf-photography pf-branding-design',
            2 => 'portfolio-item pf-web-design pf-branding-design',
            3 => 'portfolio-item pf-web-design pf-digital-art',
            4 => 'portfolio-item pf-web-design pf-digital-art',
        );
        $estaciones = array();
        $counter = 0;
        foreach($dbEstaciones as $estacion)
        {
          $estaciones[] = array(
              'class' => $estacionesClasses[$counter],
              'estacion' => $estacion,
          );
          $counter++;
          if($counter == 4)
          {
            $counter = 0;
          }
        }
        return $this->render('AppBundle:default:index.html.twig', array(
            'slider' => $slider,
            'activemenu' => 'inicio',
            'estaciones' => $estaciones,
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
        $form = $this->createForm(new NewsletterType());
        return $this->render('AppBundle:default:_newsletterForm.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function saveNewsletterAction(Request $request)
    {
        $entity = new NewsletterEmail();
        $form = $this->createForm(new NewsletterType($entity));
        $form->handleRequest($request);
        $result = false;
        $formView = false;
        if ($form->isValid()) {
          $data = $form->getData();
          $entity->setEmail($data->getEmail());
          $em = $this->getDoctrine()->getManager();
          $em->persist($entity);
          $em->flush();
          $message = \Swift_Message::newInstance()
                    ->setSubject('[SNS] Usuario agregado al newsletter')
                    ->setFrom(array('info@sns.com.uy' => 'SNS'))
                    ->setReplyTo($form->get('email')->getData())
                    ->setTo('rsantellan@gmail.com')
                    ->setBody('Se agrego al newsletter');

          $this->get('mailer')->send($message);
          $result = true;
        }else{
          $formView = $this->renderView('AppBundle:default:_newsletterForm.html.twig', array(
            'form' => $form->createView(),
        ));
        }
        $response = new JsonResponse();
        $response->setData(array('result' => $result, 'html' => $formView));
        return $response;
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
    
    private function retrieveEstaciones($em)
    {
      $estaciones = $em->createQuery('select e from AppBundle:Estacion e order by e.position asc')->getResult();
      return $estaciones;
    }
    
    private function retrieveEstacion($em, $estacionId)
    {
      $estacion = $em->createQuery('select e from AppBundle:Estacion e where e.id = :id')->setParameters(array('id' => $estacionId))->getOneOrNullResult();
      return $estacion;
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
                  ->setSubject('[SNS] Contacto desde sitio web')
                  ->setFrom(array('info@sms.com.uy' => 'SNS'))
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
      $em = $this->getDoctrine()->getManager();
      $dbEstaciones = $this->retrieveEstaciones($em);
      $estacionesClasses = array(
          0 => '',
          1 => 'second',
          2 => 'tird',
          3 => 'a',
          4 => 'b',
      );
      $estaciones = array();
      $counter = 0;
      foreach($dbEstaciones as $estacion)
      {
        $liClass = 'col-sm-6 pull-right padding-left-100 margin-bottom-30';
        if($counter % 2 != 0)
        {
          $liClass = 'col-sm-6 pull-left text-right padding-right-100';
        }
        $estaciones[] = array(
            'class' => $estacionesClasses[$counter],
            'liClass' => $liClass,
            'estacion' => $estacion,
        );
        $counter++;
      }
      return $this->render('AppBundle:default:estaciones.html.twig', array(
            'activemenu' => 'sistema',
            'estaciones' => $estaciones,
        ));
    }
    
    private function createVideo($estacionId = 0)
    {
      $completePath = $this->getDateDirectory($estacionId);
      $files  = scandir($completePath, SCANDIR_SORT_DESCENDING);
      $now = new \DateTime();
      
      $counter = 0;
      $list = array();
      while($counter <= 180 && $counter < count($files))
      {
        if($files[$counter] != '.' && $files[$counter] != '..')
        {
          $list[] = $completePath.DIRECTORY_SEPARATOR.$files[$counter];
        }
        $counter++;
      }
      $reversed = array_reverse($list);
      
      //$video_path = $this->getCacheVideoDir().DIRECTORY_SEPARATOR.$estacionId.$now->format('Y-m-d-H-i').DIRECTORY_SEPARATOR;
      $video_path = $this->getCacheVideoDir().DIRECTORY_SEPARATOR.$estacionId.md5(serialize($reversed)).DIRECTORY_SEPARATOR;
      
      if(file_exists($video_path.self::VIDEONAME))
      {
        return $video_path.self::VIDEONAME;
      }
      if(!is_dir($video_path))
      {
        mkdir($video_path, 0755, true);
      }
      //var_dump(md5(serialize($reversed)));
      //die;
      $counter = 0;
      foreach($reversed as $file)
      {
        //var_dump($file);
        copy($file, $video_path.sprintf("img-%03d.jpg", $counter));
        $counter++;
      }
      
      // Changing the video to 50%
      $cmdImagemagick = 'for file in '.$video_path.'*; do convert $file -resize 50% $file; done';
      exec($cmdImagemagick);
      
      $cmd = $this->container->getParameter('ffmpeg_location').' -framerate 5 -i '.$video_path.'img-%03d.jpg -c:v libx264 -r 30 -pix_fmt yuv420p '.$video_path.self::VIDEONAME;
      //$cmd = $this->container->getParameter('ffmpeg_location').' -framerate 1 -i '.$video_path.'img-%03d.jpg -c:v mpeg4 -r 30 '.$video_path.self::VIDEONAME; 
      //var_dump($cmd);die;
      //$cmd = 'ffmpeg -framerate 1/1 -i '.$video_path.'img-%03d.jpg -r 30 '.$video_path.'video.avi';
      $return = exec($cmd);

      return $video_path.self::VIDEONAME;
    }
    
    private function getDateDirectory($estacionId, $datestring = null, $subFolder = 'Images', $counter = 0)
    {
      $path = $this->container->getParameter('images_files');
      // Get today folder
      $now = new \DateTime();
      if($datestring == null)
      {
        $datestring = $now->format('Y/m/d');
      }
      
      $completePath = $path.$subFolder.DIRECTORY_SEPARATOR.$estacionId.DIRECTORY_SEPARATOR.$datestring;
      if(!is_dir($completePath) && $counter == 0)
      {
        $now->sub(new \DateInterval('P1D'));
        return $this->getDateDirectory($estacionId, $now->format('Y/m/d'), $subFolder, $counter + 1);
      }
      /*
      $completePath = '/home/rodrigo/sns/Images/4/2016/12/08/';
      $completePath = '/home/rodrigo/sns/'.$subFolder.'/0/2015/09/04/';
      */
      return $completePath;
    }
    
    private function getCacheDir()
    {
      $cacheDir = $this->get('kernel')->getCacheDir().DIRECTORY_SEPARATOR.'snsimages';
      if(!is_dir($cacheDir))
      {
        mkdir($cacheDir, 0755, true);
      }
      return $cacheDir;
    }
    
    private function getCacheVideoDir()
    {
      $cacheDir = $this->get('kernel')->getCacheDir().DIRECTORY_SEPARATOR.'video';
      if(!is_dir($cacheDir))
      {
        mkdir($cacheDir, 0755, true);
      }
      return $cacheDir;
    }
    
    private function getOutputImagesForStation($estacionPosition)
    {
      $path = $this->container->getParameter('images_files').'Output'.DIRECTORY_SEPARATOR;
      $images = array(
          'Original' => $path.$estacionPosition.'_Original.jpg',
          'Decision' => $path.$estacionPosition.'_Decision.jpg',
          'DecisionPrev' => $path.$estacionPosition.'_DecisionPrevious.jpg',
          'Prediction180' => $path.$estacionPosition.'_prediction180.png',
          'Prediction300' => $path.$estacionPosition.'_prediction300.png',
          'Prediction600' => $path.$estacionPosition.'_prediction600.png',
          'Desplazamiento' => $path.$estacionPosition.'_skymap.png',
          'alturadebasedenubevstiempo' => $path.'CBH.png',
          'fracciondenubosidadvstiempo' => $path.'CF.png',
          'radianciaglobalvstiempo' => $path.'GHI.png',
          'desplazamientomedionubesvstiempo' => $path.'v_av.png',
      );
      return $images;
    }
    
    private function getLastImage($estacionId)
    {
      $completePath = $this->getDateDirectory($estacionId);
      try{
        $files  = scandir($completePath, SCANDIR_SORT_DESCENDING);
        if(count($files) > 2)
        {

          $info = new \SplFileInfo($completePath.$files[0]);
          
          
          $tmpFile = $this->getCacheDir().DIRECTORY_SEPARATOR.$info->getFilename();
          if(!file_exists($tmpFile))
          {
              copy($completePath.DIRECTORY_SEPARATOR.$files[0], $tmpFile);
          }
          return $tmpFile;
        }
      }catch(\Exception $e)
      {
        $this->get('logger')->debug(sprintf("The directory: %s don't exists. Error: %s", $completePath, $e->getMessage()));
      }
      return null;
    }
    
    public function imagesAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $dbEstaciones = $this->retrieveEstaciones($em);
      $estaciones = array();
      foreach($dbEstaciones as $estacion)
      {
        $estaciones[] = array(
            'image' => $this->getLastImage($estacion->getPosition()),
            'estacion' => $estacion,
        );
      }
      
      return $this->render('AppBundle:default:imagenes.html.twig', array(
            'activemenu' => 'imagenes',
            'estaciones' => $estaciones,
        ));
    }
    
    public function proyectoImagesAction(Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $dbEstaciones = $this->retrieveEstaciones($em);
      $estaciones = array();
      foreach($dbEstaciones as $estacion)
      {
        $estaciones[] = array(
            'image' => $this->getLastImage($estacion->getPosition()),
            'estacion' => $estacion,
        );
      }
      
      return $this->render('AppBundle:default:proyectoImagenes.html.twig', array(
            'activemenu' => 'imagenes',
            'estaciones' => $estaciones,
        ));
    }

    public function proyectoEstacionAction(Request $request, $estacionId)
    {
      $em = $this->getDoctrine()->getManager();
      $estacion = $this->retrieveEstacion($em, $estacionId);
      return $this->render('AppBundle:default:estacion.html.twig', array(
            'activemenu' => 'imagenes',
            'estacion' => $estacion,
            'images' => $this->getOutputImagesForStation($estacion->getPosition()),
            'data' => $this->retrieveTextFileData($estacionId),
      ));
    }
    
    private function retrieveTextFileData($estacionId)
    {
      $foundCounter = 100;
      $found = false;
      $now = new \DateTime();
      
      $fileFullPath = null;
      while(!$found && $foundCounter > 0){
        $directory = $this->getDateDirectory($estacionId, $now->format('Y/m/d'), 'Output', 1);
        $foundCounter--;
        $fileName = $now->format('Y-m-d').'.txt';
        $fileFullPath = $directory.DIRECTORY_SEPARATOR.$fileName;
        if(file_exists($fileFullPath)){
          $found = true;
        }else{
          $now->sub(new \DateInterval('P1D'));
        }
      }
      if(!$found)
      {
        return array(
            'desplazamiento' => array(
              'vN' => 0,
              'vNkm' => 0,
              'vE' => 0,
              'vEkm' => 0,
              'v' => 0,
              'vkm' => 0,
            ),
            'nubosidad' => array(
              'hlu' => 0,
              'fn' => 0,
              'sp' => 0,
            ),
          );
      }
      $lastRow = array();
      if (($handle = fopen($fileFullPath, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
              $lastRow = $data;
          }
          fclose($handle);
      }
      $data = array(
        'desplazamiento' => array(
          'vN' => $lastRow[4],
          'vNkm' => floatval($lastRow[4]) * 3.6,
          'vE' => $lastRow[5],
          'vEkm' => floatval($lastRow[5]) * 3.6,
          'v' => $lastRow[6],
          'vkm' => floatval($lastRow[6]) * 3.6,
        ),
        'nubosidad' => array(
          'hlu' => $lastRow[1],
          'fn' => $lastRow[2],
          'sp' => $lastRow[3],
        ),
      );
      return $data;
      var_dump($lastRow);
      echo '<hr/>';
      var_dump($data);
      die;
    }
    
    public function proyectoEstacionDescargarArchivoAction(Request $request, $estacionId)
    {
      $em = $this->getDoctrine()->getManager();
      $estacion = $this->retrieveEstacion($em, $estacionId);
      $now = new \DateTime();
      $date = $request->get('date', $now->format('Y-m-d'));
      $usedDate = \DateTime::createFromFormat('Y-m-d', $date);
      if(!$usedDate)
      {
        $usedDate = $now;
      }
      $completePath = $this->getDateDirectory($estacion->getPosition(), $usedDate->format('Y/m/d'), 'Output');
      $fileName = $usedDate->format('Y-m-d').'.txt';
      $fileFullPath = $completePath.DIRECTORY_SEPARATOR.$fileName;
      
      if(file_exists($fileFullPath))
      {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileName);

        $response->setContent(file_get_contents($fileFullPath));
        return $response;
      }
      
      $this->get('session')->getFlashBag()->add('notif-error', 'El archivo no existe para ese día.');
      return $this->redirect($this->generateUrl('proyecto-estacion', array('estacionId' => $estacionId)));
    }
    
    public function proyectoEstacionDescargarImagenesAction(Request $request, $estacionId)
    {
      $em = $this->getDoctrine()->getManager();
      $estacion = $this->retrieveEstacion($em, $estacionId);
      $now = new \DateTime();
      $date = $request->get('date', $now->format('Y-m-d'));
      $usedDate = \DateTime::createFromFormat('Y-m-d', $date);
      if(!$usedDate)
      {
        $usedDate = $now;
      }
      $time = $request->get('usr_time', $now->format('H:i'));
      if($time == '')
      {
        $time = $now->format('H:i');
      }
      $auxTime = explode(':', $time);
      $usedDate->setTime($auxTime[0], $auxTime[1]);
      
      
      $completePath = $this->getDateDirectory($estacion->getPosition(), $usedDate->format('Y/m/d'));
      $files  = scandir($completePath, SCANDIR_SORT_DESCENDING);
      $regex = $usedDate->format('Y-m-d-H');
      $zipFiles = array();
      foreach($files as $file)
      {
        if(substr_count($file, $regex))
        {
          $zipFiles[] = $completePath.DIRECTORY_SEPARATOR.$file;
        }
      }
      $zippy = Zippy::load();
      // creates
      $zipBaseName = 'images-'.$regex.'.zip';
      $zipName = sys_get_temp_dir().DIRECTORY_SEPARATOR.$zipBaseName;
      
      try{
        if(file_exists($zipName))
        {
          @unlink($zipName);
        }
        $zippy->create($zipName, $zipFiles);
      } catch (\Exception $ex) {
        $this->get('session')->getFlashBag()->add('notif-error', $ex->getMessage());
        return $this->redirect($this->generateUrl('proyecto-estacion', array('estacionId' => $estacionId)));
      }
      
      $response = new Response();
      $response->headers->set('Content-Type', 'application/zip');
      $response->headers->set('Content-Disposition', 'attachment;filename="'.$zipBaseName);

      $response->setContent(file_get_contents($zipName));
      return $response;
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
