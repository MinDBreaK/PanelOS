<?php

namespace LoginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use LoginBundle\Entity\User;
use LoginBundle\Entity\Role;
use LoginBundle\Form\UserType;
use LoginBundle\Form\UserInfoType;
use LoginBundle\Form\UserSecurityType;


class AccountController extends Controller
{
	/**
	 * @Route("/account", name="account_index")
	 * @Route("/account/{tab}", name="account_index_tab")
	 * @Security("has_role('ROLE_USER')")
	 * @param Request $request
	 *
	 * @param int $tab Tab ID of the Account page
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request, $tab = 1)
	{
		$user  = $this->getUser();
		$roles = $this->getDoctrine()->getRepository( "LoginBundle:Role" )->findByRole( $user->getRoles() );

		$infoForm = $this->createForm( UserInfoType::class, $user );

		$securityForm = $this->createForm( UserSecurityType::class, $user );
		
		switch ($tab) {
			case "informations":
				$focus = 1;
				break;
			case "security":
				$focus = 2;
				break;
			case "linked-accounts":
				$focus = 3;
				break;
			default:
				$focus = 1;
				break;
		}
		
		if('POST' === $request->getMethod()) { 			
        	if ($request->request->has('loginbundle_userinfo')) {
            	$infoForm->handleRequest( $request );

				if ( $infoForm->isSubmitted() )
				{
					if ( $infoForm->isValid() )
					{
						$em = $this->getDoctrine()->getManager();
						$em->persist( $user );
						$em->flush();
					}
				}
        	}

	        if ($request->request->has('loginbundle_usersecurity')) {
	            $securityForm->handleRequest( $request );
				$focus = "2";
				if ( $securityForm->isSubmitted() )
				{
					if ( $securityForm->isValid() )
					{
						$plainPassword = $user->getPassword();
						$encoder = $this->container->get( 'security.password_encoder' );
						$encoded = $encoder->encodePassword( $user, $plainPassword );
						$user->setPassword($encoded);

						$em = $this->getDoctrine()->getManager();
						$em->persist( $user );
						$em->flush();
					}
				}
	        }
	    }
		


		$data["title"] = "Account";
		$data["roles"] = $roles;
		$data["focus"] = isset($focus) ? $focus : "first";
		$data["infoForm"] = $infoForm->createView();
		$data["securityForm"] = $securityForm->createView();
		return $this->render('LoginBundle:Account:account.html.twig', $data);
	}
}