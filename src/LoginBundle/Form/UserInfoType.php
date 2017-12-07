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

class UserInfoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class  )
				->add('name', TextType::class, [
						"label" => "Name"
					])
				->add('surname', TextType::class, [
						"label" => "Surname"
					])
				->add('birthday', BirthdayType::class, [
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy', 
					'attr'	 => [
						"class" => "datepicker"
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
		return 'loginbundle_userinfo';
	}


}
