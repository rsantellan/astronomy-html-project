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
 * Description of NewsletterType
 *
 * @author Rodrigo Santellan
 */
class NewsletterType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('email', 'email', array(
                'attr' => array(
                    'placeholder' => 'Email'
                ),
                'label' => 'Email'
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $collectionConstraint = new Collection(array(
            'email' => array(
                new NotBlank(array('message' => 'El E-Mail no puede estar vacio')),
                new Email(array('message' => 'Invalid email address.'))
            ),
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
      
    }

    public function getName()
    {
        return 'appbundle_newslttertype';
    }
    
}


