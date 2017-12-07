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

class UserType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class)
				->add('email', EmailType::class  )
				->add('password', RepeatedType::class, [
						"type"				=> PasswordType::class,
						"invalid_message"	=> "The passwords must match !",
						"first_options"		=> ["label" => "Password"],
						"second_options"	=> ["label" => "Confirm Password"]
					])
				->add('salt', TextType::class, [
					"label" => "Salt (Only letters and numbers)",
					"required" => false,
					"attr"  => [
							"pattern"		=> "^[a-zA-Z0-9]+$",
							"class"			=> "validate tooltipped",
							"data-position"	=> "bottom",
							"data-delay"	=> "50",
							"data-tooltip"	=> "You can define here your own salt if you don't trust our. Promise, we won't be mad at you !"
						] 
					])
				->add('name', TextType::class, [
						"label" => "Name"
					])
				->add('surname', TextType::class, [
						"label" => "Surname"
					])
				->add('birthday', birthdayType::class, [
						'widget' => 'single_text',
						'format' => 'dd-mm-yyyy', 
						'attr'	 => [
							"class" => "datepicker"
						]
					])
				;
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
		return 'loginbundle_user';
	}


}
