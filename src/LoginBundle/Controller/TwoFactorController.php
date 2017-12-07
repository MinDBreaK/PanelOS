<?php

namespace LoginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Form\Extension\Core\Type\TextType;


class TwoFactorController extends Controller
{
	/**
	 * @Route("/security/2fa/enable", name="security_2fa_enable")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function enableAction()
	{
		if ($this->getUser()->getGoogleAuthenticatorCode() != null ) 
		{
			return $this->redirectToRoute("security_2fa_enabled");
		}
		$data["title"] = "Enable 2FA";
		return $this->render('LoginBundle:TwoFactor:enable.html.twig', $data);
	}

	/**
	 * @Route("/security/2fa/enable/2", name="security_2fa_enable_2")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function enable2Action(Request $request)
	{
		if ($this->getUser()->getGoogleAuthenticatorCode() != null ) 
		{
			return $this->redirectToRoute("security_2fa_enabled");
		}

		$session = $request->getSession();	
		$authHelper = $this->get('login_user.twofactor.google.provider');
		$user = $this->getUser();
		
		if ( !$session->has("temporaryKey") )
		{
			$session->set("temporaryKey", $authHelper->generateSecret() );
		}
		
		$user->setGoogleAuthenticatorCode( $session->get("temporaryKey") );
		$url = $authHelper->getUrl( $user );

		$formChecker = $this->createFormBuilder()
							->add("code", TextType::class, [
								"label" => "Generated Code"
								])
							->getForm();

		$formChecker->handleRequest( $request );

		if ( $formChecker->isSubmitted() )
		{
			$code = $formChecker->getData()["code"];
			if ($formChecker->isValid() && $authHelper->checkCode( $user, $code ))
			{
				$em = $this->getDoctrine()->getManager();
				$em->persist( $user );
				$em->flush();

				return $this->redirectToRoute( "security_2fa_enabled" );
			}
		}

		
		$data["key"]   	= $session->get("temporaryKey");
		$data["url"]   	= $url;
		$data["form"]  	= $formChecker->createView();
		$data["server"]	= $authHelper->getServer();
		$data["title"] 	= "Enable 2FA";
		return $this->render('LoginBundle:TwoFactor:enable.2.html.twig', $data);
	}

	/**
	 * @Route("/security/2fa/enabled", name="security_2fa_enabled")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function enabledAction(Request $request)
	{
		return $this->render('LoginBundle:TwoFactor:enabled.html.twig');
	}


	/**
	 * @Route("/security/2fa/disable", name="security_2fa_disable")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function disableAction(Request $request)
	{
		return $this->render('LoginBundle:TwoFactor:disable.html.twig');
	}

	/**
	 * @Route("/security/2fa/disabled", name="security_2fa_disabled")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function disabledAction(Request $request)
	{
		$user = $this->getUser();
		$user->setGoogleAuthenticatorCode( null );

		$em = $this->getDoctrine()->getManager();
		$em->persist( $user );
		$em->flush();
		return $this->render('LoginBundle:TwoFactor:disabled.html.twig');
	}
}