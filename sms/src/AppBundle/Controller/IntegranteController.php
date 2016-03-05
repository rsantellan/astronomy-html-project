<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Integrante;
use AppBundle\Form\IntegranteType;

/**
 * Integrante controller.
 *
 */
class IntegranteController extends Controller
{

    private function flushCache()
    {
      $cache = $this->get('fscache');
      $cache->delete(Integrante::LISTINTEGRANTES);
    }
    
    /**
     * Lists all Integrante entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('AppBundle:Integrante')->findAll();
        $entities =  $em->createQuery('select e from AppBundle:Integrante e order by e.posicionVisual')->getResult();
        return $this->render('AppBundle:Integrante:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Integrante entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Integrante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->flushCache();
            return $this->redirect($this->generateUrl('admin_integrante_edit', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Integrante:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Integrante entity.
     *
     * @param Integrante $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Integrante $entity)
    {
        $form = $this->createForm(new IntegranteType(), $entity, array(
            'action' => $this->generateUrl('admin_integrante_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Integrante entity.
     *
     */
    public function newAction()
    {
        $entity = new Integrante();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Integrante:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Integrante entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Integrante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Integrante entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Integrante:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Integrante entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Integrante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Integrante entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Integrante:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Integrante entity.
    *
    * @param Integrante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Integrante $entity)
    {
        $form = $this->createForm(new IntegranteType(), $entity, array(
            'action' => $this->generateUrl('admin_integrante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Integrante entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Integrante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Integrante entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->flushCache();
            return $this->redirect($this->generateUrl('admin_integrante_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:Integrante:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Integrante entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Integrante')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Integrante entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->flushCache();
        }

        return $this->redirect($this->generateUrl('admin_integrante'));
    }

    /**
     * Creates a form to delete a Integrante entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_integrante_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
