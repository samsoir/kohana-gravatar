<?php defined('SYSPATH') or die('No direct script access.');
/**
 * [ref-gravatar] Gravatars are universal avatars available to all web sites and services.
 * Users must register their email addresses with Gravatar before their avatars will be
 * usable with this module. Users with gravatars can have a default image of your selection.
 *
 * [ref-gravatar]: http://en.gravatar.com/
 * 
 * @package     Gravatar XMLRPC API for Kohana PHP 3
 * @requires    PHP-SOAP
 * @author      Sam C. De Freyssinet
 * @copyright   (c) 2009 De Freyssinet
 * @version     3.0.0
 * @license     http://creativecommons.org/licenses/by-sa/2.0/uk/
 */
class Gravatar_Xmlrpc {

	/**
	 * Create an instance of the Gravatar XMLRPC API client
	 *
	 * @param   array        $config 
	 * @return  self
	 * @access  public
	 * @static
	 */
	public static function instance($config = array())
	{
		static $instance;

		empty($instance) and $instance = new Gravatar_Xmlrpc($config);

		return $instance;
	}

	/**
	 * Factory method for creating a new Xmlrpc object
	 *
	 * @param   array        $config 
	 * @return  self
	 * @access  public
	 * @static
	 */
	public static function factory($config = array())
	{
		return new Gravatar_Xmlrpc($config);
	}

	/**
	 * Configuration for this client
	 *
	 * @var     array
	 */
	protected $_config;

	/**
	 * Xml Writing Driver
	 *
	 * @var     mixed
	 */
	protected $_xmlWriter;

	/**
	 * SOAP Client
	 *
	 * @var     SoapClient
	 */
	protected $_soapClient;

	/**
	 * Constructor, maintains the singleton or factory pattern
	 *
	 * @param   array        $config
	 * @access  protected
	 */
	protected function __construct($config)
	{
		// Check for soap
		if ( ! extension_loaded('Soap'))
			throw new Gravatar_Exception('Soap extension must be loaded to use this class!');

		// Configure this library
		$config += Kohana::config('gravatar.xmlrpc');
		$this->_config = $config;

		// Setup SOAP Client
		$this->_soapClient = new SoapClient(null, array
		(
			'location' => request::instance()->uri,
			'uri'      => $this->_config['service'].$this->_config['email'],
		));
	}

	/**
	 * Set or get the api_key
	 *
	 * @param   string       $api_key 
	 * @return  string|self
	 * @access  public
	 */
	public function api_key($api_key = NULL)
	{
		// If there is no API key supplied
		if ($api_key === NULL)
			return $this->_config['api_key'];

		// Else set the API key
		$this->_config['api_key'] = (string) $api_key;

		// Return this
		return $this;
	}

	/**
	 * Set or get the email address in question
	 *
	 * @param   string       $email [Optional]
	 * @return  string|self
	 * @access  public
	 */
	public function email($email = NULL)
	{
		// If no argument, return the email address
		if ($email === NULL)
			return $this->_config['email'];

		// Set the email address
		$this->_config['email'];

		// Return this
		return $this;
	}

	/**
	 * Checks that a hash exists
	 *
	 * @param   array        $hashes
	 * @return  array
	 * @access  public
	 */
	public function check_hash($hashes)
	{
		
	}

	/**
	 * Returns an array of email addresses
	 * registered to the account API key
	 *
	 * @return  array
	 * @access  public
	 */
	public function addresses()
	{
		
	}

	/**
	 * Returns an array containing images
	 * registered to this user - and their
	 * respective rating
	 *
	 * @return  array
	 * @access  public
	 */
	public function user_images()
	{
		
	}

	/**
	 * Save an image to the registered account.
	 * Images must be transferred in raw base64
	 * encoded format.
	 *
	 * @param   string       $image
	 * @param   int          $rating [Optional]
	 * @return  string|boolean
	 * @access  public
	 */
	public function save_data($image, $rating = 0)
	{
		
	}

	/**
	 * Save a URL to the registered account.
	 *
	 * @param   string       $url 
	 * @param   int          $rating [Optional]
	 * @return  string|boolean
	 * @access  public
	 */
	public function save_url($url, $rating = 0)
	{
		
	}

	/**
	 * Assign a user image on Gravatar to the addresses
	 * supplied.
	 *
	 * @param   string       $user_image 
	 * @param   array        $addresses 
	 * @return  array
	 * @access  public
	 */
	public function use_image($user_image, $addresses)
	{
		
	}

	/**
	 * Test function
	 *
	 * @return  mixed
	 * @access  public
	 */
	public function test()
	{
		
	}

	/**
	 * Execute the Xmlrpc request and return
	 * the result
	 *
	 * @return  mixed
	 * @access  protected
	 */
	protected function _exec()
	{
		
		
	}
}