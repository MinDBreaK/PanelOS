<?php

namespace LoginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Github\Client as GithubClient;
use Github\Exception\TwoFactorAuthenticationRequiredException;

use LoginBundle\Entity\GithubToken;

class GithubController extends Controller
{
	private const OAUTH2_CLIENT_ID 		= "5eb0ce96263b97859ab1";
	private const OAUTH2_CLIENT_SECRET 	= "e8c16b62b0154e91bfdb789bef369dd03ab984e4";

	private const authorizeURL 	= 'https://github.com/login/oauth/authorize';
	private const tokenURL 		= 'https://github.com/login/oauth/access_token';
	private const apiURLBase 	= 'https://api.github.com/';
	
	/**
	 * @Route("/github/connect", name="github_connect")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function enableAction( Request $request )
	{
		$params = "";
		$response = "";
		$urlParams = $request->query->all();
		$session = $request->getSession();
		$data["success"] = false;
		
		if ( !isset( $urlParams["code"] ) )
		{
			
			$session->set( "state", hash( 'sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR'] ) );

			$params = array(
				'client_id' => self::OAUTH2_CLIENT_ID,
				'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
				'scope' => 'user repo',
				'state' => $session->get('state')
			);

			return $this->redirect( self::authorizeURL . '?' . http_build_query($params) );

		} else {
			if ( $session->has("state") && ($urlParams["state"] != $session->get('state')) )
			{
				$this->redirectToRoute("github_connect");
			} else {
				$headers[] = 'Accept: application/json';
				$headers[] = 'User-Agent: BetaTest';
				$post["client_id"] 		= self::OAUTH2_CLIENT_ID;
				$post["client_secret"] 	= self::OAUTH2_CLIENT_SECRET;
				$post["redirect_uri"] 	= 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
				$post["state"]			= $session->get('state');
				$post["code"]			= $urlParams['code'];

				$ch = curl_init(self::tokenURL);
  				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				if( $post ){
    				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
				}
				if ( $session->has("access_token") ){
					$headers[] = 'Authorization: Bearer ' . $session->get('access_token');
				}

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$response = json_decode( curl_exec($ch) );
				if (isset( $response->access_token ))
				{
					$user = $this->getUser();
					$githubToken = $user->getGithubToken();
					if ( $githubToken == null )
					{
						$githubToken = new GithubToken();
					}
					$githubToken->setToken( $response->access_token );
					$githubToken->setCreationDate( new \DateTime("now") );
					$githubToken->setScope( explode(",", $response->scope) );
					$githubToken->setUser( $user );

					$em = $this->getDoctrine()->getManager();
					$em->persist( $githubToken );
					$em->flush();
					
					$data["success"] = true;
				}
			}
		}
		
		return  $this->render('LoginBundle:Github:githubJson.html.twig', $data);
	}

	/**
	 * @Route("/github/connected", name="github_connected")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function enabledAction( Request $request )
	{
		return $this->render('LoginBundle:Github:enabled.html.twig');
	}

	/**
	 * @Route("/github/disconnect", name="github_disconnect")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function disableAction( Request $request )
	{
		$token = $this->getUser()->getGithubToken();
		$em = $this->getDoctrine()->getManager();
		$em->remove( $token );
		$em->flush();

		return $this->render('LoginBundle:Github:disabled.html.twig');
	}
	
	/**
	 * @Route("/github/checkConnection", name="github_checkConnection")
	 * @Security("has_role('ROLE_USER')")
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function checkGHConnection( Request $request )
	{
		$data["state"] = false;
		$token = $this->getUser()->getGithubToken();
		if ($token !== null) {
			$data["state"] = true;
		}
		return $this->render("LoginBundle:Github:check.json.twig", $data);
	}
	
	/**
	 * @Route("/github/retrieveOAuthURL", name="github_retriveOAuthURL")
	 * @Security("has_role('ROLE_USER')")
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function retriveOAuthUTLAction( Request $request )
	{
		$session = $request->getSession();
		$session->set( "state", hash( 'sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR'] ) );
		
		$params = array(
			'client_id' => self::OAUTH2_CLIENT_ID,
			'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
			'scope' => 'user repo',
			'state' => $session->get('state')
		);
		
		$data["url"] = self::authorizeURL . '?' . http_build_query($params);
		return $this->render("LoginBundle:Github:retriveOAuthURL.json.twig", $data);
	}
}