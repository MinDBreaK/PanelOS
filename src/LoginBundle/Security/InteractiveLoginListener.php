<?php 

namespace LoginBundle\Security;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use LoginBundle\Entity\User;
 
class InteractiveLoginListener
{
    /**
     * @var LoginBundle\Security\TwoFactor\Google\Helper $helper
     */
    private $helper;
    
    /**
     * @param LoginBundle\Security\TwoFactor\Google\Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }
    
    /**
     * Listen for successful login events
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     * @return
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if (!$event->getAuthenticationToken() instanceof UsernamePasswordToken)
        {
            return;
        }
        
        //Check if user can do two-factor authentication
        $ip = $event->getRequest()->getClientIp();
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        if (!$user instanceof User)
        {
            return;
        }
        if (!$user->getGoogleAuthenticatorCode())
        {
            return;
        }
        
        //Set flag in the session
        $event->getRequest()->getSession()->set($this->helper->getSessionKey($token), null);
    }
    
}