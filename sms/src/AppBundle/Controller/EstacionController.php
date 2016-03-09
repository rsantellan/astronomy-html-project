<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Estacion;
use AppBundle\Form\EstacionType;

/**
 * Estacion controller.
 *
 */
class EstacionController extends Controller
{

    /**
     * Lists all Estacion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Estacion')->findAll();

        return $this->render('AppBundle:Estacion:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Estacion entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Estacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_estacion_edit', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Estacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Estacion entity.
     *
     * @param Estacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Estacion $entity)
    {
        $form = $this->createForm(new EstacionType(), $entity, array(
            'action' => $this->generateUrl('admin_estacion_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Estacion entity.
     *
     */
    public function newAction()
    {
        $entity = new Estacion();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Estacion:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Estacion entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Estacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Estacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Estacion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Estacion entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Estacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Estacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Estacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Estacion entity.
    *
    * @param Estacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Estacion $entity)
    {
        $form = $this->createForm(new EstacionType(), $entity, array(
            'action' => $this->generateUrl('admin_estacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Estacion entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Estacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Estacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_estacion_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:Estacion:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Estacion entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Estacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Estacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_estacion'));
    }

    /**
     * Creates a form to delete a Estacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_estacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
