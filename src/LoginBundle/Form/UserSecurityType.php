<?php

namespace LoginBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserSecurityType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', RepeatedType::class, [
					"type"				=> PasswordType::class,
					"invalid_message"	=> "The passwords must match !",
					"first_options"		=> [
						"label" 			=> "Password",
						"error_bubbling" 	=> true
					],
					"second_options"	=> ["label" => "Confirm Password"], 
					"error_bubbling"	=> true
				])
				->add('salt', TextType::class, [
					"label" => "Salt",
					"required" => false,
					"attr"  => [
							"pattern"		=> "^[a-zA-Z0-9]+$",
							"class"			=> "validate tooltipped",
							"data-position"	=> "bottom",
							"data-delay"	=> "50",
							"data-tooltip"	=> "You can define here your own salt if you don't trust our. Promise, we won't be mad at you !"
					] 
				]);
    }
    
	/**
	* {@inheritdoc}
	*/
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'LoginBundle\Entity\User'
		));
	}

	/**
	* {@inheritdoc}
	*/
	public function getBlockPrefix()
	{
		return 'loginbundle_usersecurity';
	}


}
