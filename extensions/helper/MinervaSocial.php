<?php
/**
 * Minerva: a CMS based on the Lithium PHP framework
 *
 * @copyright Copyright 2010-2011, Shift8Creative (http://www.shift8creative.com)
 * @license http://opensource.org/licenses/bsd-license.php The BSD License
 * 
 * Minerva's Social Media Helper
 * Adds social sharing features and some shortcuts for Facebook that relate to Minerva.
 *
 * @author Tom Maiaroto
 * @website http://www.shift8creative.com
 *
*/
namespace minerva\extensions\helper;

use lithium\storage\Session;
use lithium\core\Libraries;
use li3_facebook\extensions\FacebookProxy;

class MinervaSocial extends MinervaHtml {
    
    public function _init() {
        parent::_init();
        
        // Default
        $this->facebook = null;
		$this->facebook->app_id = false;
		$this->facebook->locale = 'en_US';
        
        // If using Facebook
        $facebook_config = Libraries::get('li3_facebook');
        if(isset($facebook_config)) {
            $this->facebook->login_url = FacebookProxy::getLoginUrl(array('next' => $facebook_config['login_url']));
			$this->facebook->logout_url = FacebookProxy::getLogoutUrl(array('next' => $facebook_config['logout_url']));
            $this->facebook->app_id = $facebook_config['appId'];
			$this->facebook->locale = (isset($facebook_config['locale'])) ? $facebook_config['locale']:$this->facebook->locale;
		}
    }
    
    /**
	 * Displays a basic Facebook Connect login button.
	 * Works with the PHP SDK to get the login URL.
	 *
	 * @param $options Array
	 * @return String
	*/
	public function facebook_login(array $options = array()) {
		$defaults = array(
			'div' => 'fb_login',
			'button_image' => '/minerva/img/fb-login-button.png',
			'button_alt' => 'Login with Facebook',
			'additional_copy' => null,
			'link_options' => array('escape' => false)
		);
		$options += $defaults;
		$output = '';
		
		if(!empty($this->facebook->login_url)) {
			if($options['div'] !== false) {
				$output .= '<div id="' . $options['div'] . '">' . $options['additional_copy'];
			}
			
			$output .= $this->_context->html->link('<img src="' . $options['button_image'] . '" alt="' . $options['button_alt'] .'" />', $this->facebook->login_url, $options['link_options']);
			
			if($options['div'] !== false) {
				$output .= '</div>';
			}
		}
		
		return $output;
	}
	
	/**
	 * Embeds the Facebook JavaScript SDK
	 * Facebook app id, locale, etc. is set in app/bootstrap/libraries.php
	 * with configuration options for Libraries::add('minerva').
	 * ex.
	 * Libraries::add('minerva', array(
	 *     'facebook' => array(
	 *         'appId' => 0000,
	 *         'secret' => 0000,
	 *         'locale' => 'en_US'
	 *     )
	 * ))
	 *
	 * TODO: add other options to be passed... like "status", "cookie" and "xfbml"
	 *
	 * @param $async Boolean Whether or not to embed it so it loads asynchronously
	 * @param $debug Boolean Whether or not to use the debug version
	 * @return String The HTML embed code
	*/
	public function facebook_init($async=true, $debug=false) {
		$script = 'all.js';
		if($debug === true) {
			$script = 'core.debug.js';
		}
		$output = '';
		if($this->facebook->app_id) {
			if($async) {
				$output = "<div id=\"fb-root\"></div><script>window.fbAsyncInit = function() { FB.init({appId: '".$this->facebook->app_id."', status: true, cookie: true, xfbml: true}); }; (function() { var e = document.createElement('script'); e.async = true; e.src = document.location.protocol + '//connect.facebook.net/".$this->facebook->locale."/".$script."'; document.getElementById('fb-root').appendChild(e); }());</script>";
			} else {
				$output = "<div id=\"fb-root\"></div><script src=\"http://connect.facebook.net/".$this->facebook->locale."/".$fb_script."\"></script><script>FB.init({ appId  : '".$this->facebook->app_id."', status : true, cookie : true, xfbml : true });</script>";
			}
		}
		return $output;
	}
    
}
?>