<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\ArticleCategory;
use AppBundle\Form\ArticleType;
use AppBundle\Form\ArticleCategoryType;

/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{

    /**
     * Lists all Article entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $request->get('category');
        $dql = 'select a, c from AppBundle:Article a left outer join a.categories c';
        $params = array();
        if($category !== null)
        {
          $dql = 'select a, c from AppBundle:Article a left outer join a.categories c where c.id = :category';
          $params['category'] = $category;
        }
        $entities = $em->createQuery($dql)
                    ->setParameters($params)
                    ->getResult();
        $categories = $em->getRepository('AppBundle:ArticleCategory')->findAll();

        return $this->render('AppBundle:Article:index.html.twig', array(
            'entities' => $entities,
            'categories' => $categories,
        ));
    }
    /**
     * Creates a new Article entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Article();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_article_edit', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Article entity.
     *
     * @param Article $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Article $entity)
    {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('admin_article_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Article entity.
     *
     */
    public function newAction()
    {
        $entity = new Article();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Article entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Article:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Article entity.
    *
    * @param Article $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Article $entity)
    {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('admin_article_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Article entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_article_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Article entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Article')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Article entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_article'));
    }

    /**
     * Creates a form to delete a Article entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_article_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    
    /**
     * Creates a form to create a Article entity.
     *
     * @param Article $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateCategoryForm(ArticleCategory $entity)
    {
        $form = $this->createForm(new ArticleCategoryType(), $entity, array(
            'action' => $this->generateUrl('admin_article_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
    
    /**
     * Displays a form to create a new Article entity.
     *
     */
    public function newCategoryAction()
    {
        $entity = new ArticleCategory();
        $form   = $this->createCreateCategoryForm($entity);

        $formView = $this->renderView('AppBundle:Article:_newCategory.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
        $response = new JsonResponse();
        $response->setData(array('result' => true, 'html' => $formView, 'title' => 'Agregar categoria'));
        return $response;
    }
    
    
    public function createCategoryAction(Request $request)
    {
      $entity = new ArticleCategory();
      $form = $this->createCreateCategoryForm($entity);
      $form->handleRequest($request);
      $html = '';
      $result = false;
      $message = 'Error al guardar la categoria';
      if ($form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($entity);
          $em->flush();
          // 
          $html = $this->renderView('AppBundle:Article:_articleCategoryRow.html.twig', array(
            'category' => $entity,
          ));
          $result = true;
          $message = 'Categoria guardada';
      }
      else
      {
        $html = $this->renderView('AppBundle:Article:_newCategory.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
      }
      $response = new JsonResponse();
      $response->setData(array('result' => $result, 'html' => $html, 'update' => false, 'message' => $message));
      return $response;
    }
    
    /**
     * Displays a form to edit an existing Article entity.
     *
     */
    public function editCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ArticleCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article Category entity.');
        }

        $editForm = $this->createCreateCategoryForm($entity);
        $formView =  $this->renderView('AppBundle:Article:_editCategory.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
        $response = new JsonResponse();
        $response->setData(array('result' => true, 'html' => $formView, 'title' => 'Editar categoria'));
        return $response;
        
    }

    /**
     * Edits an existing Article entity.
     *
     */
    public function updateCategoryAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ArticleCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article Category entity.');
        }

        $editForm = $this->createCreateCategoryForm($entity);
        $editForm->handleRequest($request);
        $html = '';
        $result = false;
        $message = 'Error al actualizar';
        if ($editForm->isValid()) {
            $em->flush();
            $html = $this->renderView('AppBundle:Article:_articleCategoryRow.html.twig', array(
              'category' => $entity,
            ));
            $result = true;
            $message = 'Categoria guardada';
            //return $this->redirect($this->generateUrl('admin_article_edit', array('id' => $id)));
        }else{
            $html =  $this->renderView('AppBundle:Article:_editCategory.html.twig', array(
                'entity'      => $entity,
                'form'   => $editForm->createView(),
            ));
        }
        $response = new JsonResponse();
        $response->setData(array('result' => true, 'html' => $html, 'message' => $message, 'id' => $id,  'update' => true));
        return $response;
    }    
    
    public function deleteCategoryAction(Request $request, $id)
    {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository('AppBundle:ArticleCategory')->find($id);

      if (!$entity) {
          throw $this->createNotFoundException('Unable to find Article Category entity.');
      }

      $em->remove($entity);
      $em->flush();
      $response = new JsonResponse();
      $response->setData(array('result' => true, 'message' => 'Categoria borrada con exito', 'id' => $id));
      return $response;
    }
    
}
