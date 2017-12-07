<?php

namespace LoginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use LoginBundle\Entity\User;
use LoginBundle\Form\UserType;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="main_login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $data["title"] = "Connexion";
        $data["last_username"] = $lastUsername;
        $data["error"] = $error;
        return $this->render('LoginBundle:Login:login.html.twig', $data);
    }

	/**
	 * @Route("/register", name="main_register")
	 */
	public function registerAction(Request $request)
	{
		$data["title"] = "Création d'un compte";

		$user = new User();

		$form = $this->createForm( UserType::class, $user );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() )
		{
			if ( $form->isValid() )
			{
				$plainPassword = $user->getPassword();
				$encoder = $this->container->get( 'security.password_encoder' );
				$encoded = $encoder->encodePassword( $user, $plainPassword );
				$user->setPassword($encoded);
				
				$user->setRoles("ROLE_USER");
				$em = $this->getDoctrine()->getManager();
				$em->persist( $user );
				$em->flush();
				return $this->redirectToRoute('main_index');
			}
		}

		$data["form"] = $form->createView();
		return $this->render('LoginBundle:Register:register.html.twig', $data);
	}


}
