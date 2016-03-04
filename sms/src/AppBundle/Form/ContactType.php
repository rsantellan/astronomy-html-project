<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Description of ContactType
 *
 * @author Rodrigo Santellan
 */
class ContactType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'text', array(
                'attr' => array(
                    'placeholder' => 'contact.form.name.placeholder',
                    'pattern'     => '.{2,}' //minlength
                ),
                'label' => 'contact.form.name'
            ))
            ->add('email', 'email', array(
                'attr' => array(
                    'placeholder' => 'contact.form.email.placeholder'
                ),
                'label' => 'contact.form.email'
            ))
            ->add('phone', 'text', array(
                'attr' => array(
                    'placeholder' => 'contact.form.phone.placeholder',
                ),
                'label' => 'contact.form.phone'
            ))
            ->add('message', 'textarea', array(
                'attr' => array(
                    //'cols' => 90,
                    'rows' => 5,
                    'placeholder' => 'contact.form.message.placeholder'
                ),
                'label' => 'contact.form.message'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'El nombre no puede estar vacio')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'El E-Mail no puede estar vacio')),
                new Email(array('message' => 'Invalid email address.'))
            ),
            'phone' => array(

            ),
            'message' => array(
                new NotBlank(array('message' => 'El mensaje no puede estar vacio')),
                new Length(array('min' => 5))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
      
    }
    /*
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $collectionConstraint = new Collection(array(
            'name' => array(
                new NotBlank(array('message' => 'El nombre no puede estar vacio')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'El E-Mail no puede estar vacio')),
                new Email(array('message' => 'Invalid email address.'))
            ),
            'phone' => array(

            ),
            'message' => array(
                new NotBlank(array('message' => 'El mensaje no puede estar vacio')),
                new Length(array('min' => 5))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }
    */
    public function getName()
    {
        return 'appbundle_contacttype';
    }
    
}


