<?php defined('SYSPATH') or die('No direct script access.');
/**
 * [ref-gravatar] Gravatars are universal avatars available to all web sites and services.
 * Users must register their email addresses with Gravatar before their avatars will be
 * usable with this module. Users with gravatars can have a default image of your selection.
 *
 * [ref-gravatar]: http://en.gravatar.com/
 * 
 * @package     Gravatar for Kohana PHP 3
 * @author      Sam C. De Freyssinet
 * @copyright   (c) 2009 De Freyssinet
 * @version     3.0.0
 * @license     http://creativecommons.org/licenses/by-sa/2.0/uk/
 */
class Gravatar {

	/**
	 * Static instances
	 *
	 * @var     array
	 * @static
	 * @access  protected
	 */
	static protected $_instances = array();

	/**
	 * Instance constructor pattern
	 *
	 * @param   string       email   the Gravatar to fetch for email address
	 * @param   string       config  the name of the configuration grouping
	 * @param   array        config  array of key value configuration pairs
	 * @return  Gravatar
	 * @access  public
	 * @static
	 */
	public static function & instance($email, $config = NULL)
	{
		// Create an instance checksum
		$config_checksum = sha1(serialize($config));

		// Load the Gravatar instance for email and configuration
		if ( ! isset(self::$_instances[$email][$config_checksum]))
			self::$_instances[$email][$config_checksum] = new Gravatar($email, $config);

		// Return a the instance
		return self::$_instances[$email][$config_checksum];
	}

	/**
	 * Factory method
	 *
	 * @param   string       email   the Gravatar to fetch for email address
	 * @param   string       config  the name of the configuration grouping
	 * @param   array        config  array of key value configuration pairs
	 * @return  Gravatar
	 * @access  public
	 * @static
	 */
	public static function factory($email, $config = NULL)
	{
		return new Gravatar($email, $config);
	}

	/**
	 * Gravatar Ratings constants
	 */
	const GRAVATAR_G   = 'G';
	const GRAVATAR_PG  = 'PG';
	const GRAVATAR_R   = 'R';
	const GRAVATAR_X   = 'X';

	/**
	 * Configuration for this library, merged with the static config
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_config;

	/**
	 * Additional attributes to add to the image
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_attributes = array();

	/**
	 * The email address of the user
	 *
	 * @var     string
	 * @access  protected
	 */
	protected $_email;

	/**
	 * Gravatar constructor
	 *
	 * @param   string       email   the Gravatar to fetch for email address
	 * @param   string       config  the name of the configuration grouping
	 * @param   array        config  array of key value configuration pairs
	 * @access  public
	 * @throws  Gravatar_Exception
	 */
	protected function __construct($email, $config = NULL)
	{
		// Set the email address
		$this->email($email);

		if (empty($config))
			$this->_config = Kohana::config('gravatar.default');
		elseif (is_array($config))
		{
			// Setup the configuration
			$config += Kohana::config('gravatar.default');
			$this->_config = $config;
		}
		elseif (is_string($config))
		{
			if ($config = Kohana::config('gravatar.'.$config) === NULL)
				throw new Gravatar_Exception(printf('Gravatar.__construct() , Invalid configuration group name : %s', $config));

			$this->_config = $config;
		}
	}

	/**
	 * __get() magic method for accessing email value
	 *
	 * @param   mixed        key  the key to get
	 * @return  mixed        the returned value
	 * @access  public
	 */
	public function __get($key)
	{
		if ($key === 'email')
		{
			$key = '_'.$key;
			return $this->$key;
		}
		elseif ($this->config[$key] !== NULL)
			return $this->_config[$key];
		elseif ($this->attributes[$key] !== NULL)
			return $this->_attributes[$key];
		else
			return NULL;
	}

	/**
	 * __set() magic method for setting object properties
	 * If $var is a method then this will call it, else it will
	 * set the attribute $key = $value
	 *
	 * @param string $key 
	 * @param string $value 
	 * @return void
	 * @author Sam Clark
	 */
	public function __set($key, $value)
	{
		if ($key === 'email')
		{
			$key = '_'.$key;
			$this->$key;
		}
		else
			$result = $this->add_attribute($key, $value);

	}

	/**
	 * Handles this object being cast to string
	 *
	 * @return  string       the resulting Gravatar
	 * @access  public
	 * @author  Sam Clark
	 */
	public function __toString()
	{
		return (string) $this->render();
	}

	/**
	 * Accessor method for setting email address
	 *
	 * @param   string       email  the valid email address of Gravatar user
	 * @return  self
	 * @access  public
	 * @author  Sam Clark
	 */
	public function email($email)
	{
		if (validate::email($email))
			$this->_email = strtolower($email);
		else
			throw new Gravatar_Exception(printf('The email address %s is incorrectly formatted', $email));

		return $this;
	}

	/**
	 * Accessor method for setting size of gravatar
	 *
	 * @param   int          size  the size of the gravatar image in pixels
	 * @return  self
	 * @access  public
	 * @author  Sam Clark
	 */
	public function size($size)
	{
		if (is_numeric($size) AND $size > 0)
			$this->_config['size'] = $size;
		else
			throw new Gravatar_Exception('The image size must be greater than zero');

		return $this;
	}

	/**
	 * Accessor method for the rating of the gravatar
	 *
	 * @param   string       rating  the rating of the gravatar
	 * @return  self
	 * @access  public
	 * @author  Sam Clark
	 */
	public function rating($rating)
	{
		if (in_array(strtoupper($rating), array('G', 'PG', 'R', 'X')))
			$this->_config['rating'] = strtoupper($rating);
		else
			throw new Gravatar_Exception(printf('The rating value %s is not valid. Please use G, PG, R or X. Also available through Class constants'), $rating);

		return $this;
	}

	/**
	 * Accessor method for setting the default image if the supplied email address or rating return an empty result
	 *
	 * @param   string       url  the url of the image to use instead of the Gravatar
	 * @return  self
	 * @access  public
	 * @author  Sam Clark
	 */
	public function default_image($url)
	{
		if (validate::url($url))
			$this->_config['default'] = $url;
		else
			throw new Gravatar(printf('The url %s is improperly formatted', $url));

		return $this;
	}

	/**
	 * Allows addition of custom HTML attributes such as 'id' or 'class'.
	 *
	 * @param   string       key  the attribute key
	 * @param   string       value  the attribute value
	 * @return  self
	 * @access  public
	 * @author  Sam Clark
	 */
	public function add_attribute($key, $value)
	{
		$this->_attributes[$key] = $value;

		return $this;
	}

	/**
	 * Renders the Gravatar using supplied configuration and attributes. Can use custom view.
	 *
	 * @param   string       view  [Optional] a kohana PHP
	 * @param   string       email  [Optional] the valid email of a Gravatar user
	 * @return  string       the rendered Gravatar output
	 * @access  public
	 * @author  Sam Clark
	 */
	public function render($view = FALSE, $email = NULL)
	{
		if (isset($email))
			$this->email($email);

		$data = array('src' => array('src' => $this->_generate_url()));

		if ($this->_attributes)
			$data['src'] += $this->_attributes;

		$data['alt'] = $this->_process_alt();

		return (string) $view ? new View($view, $data) : new View($this->_config['view'], $data);;
	}

	/**
	 * Process the alt attribute output
	 *
	 * @return  string
	 * @access  protected
	 * @author  Sam Clark
	 */
	protected function _process_alt()
	{
		$keys = array
		(
			'{$email}'      => $this->_email,
			'{$size}'       => $this->_config['size'],
			'{$rating}'     => $this->_config['rating'],
		);

		if ($this->_config['alt'])
			$alt = strtr($this->_config['alt'], $keys);
		else
			$alt = FALSE;

		return $alt;
	}

	/**
	 * Creates the Gravatar URL based on the configuration and email
	 *
	 * @return  string       the resulting Gravatar URL
	 * @access  protected
	 * @author  Sam Clark
	 */
	protected function _generate_url()
	{
		$string = $this->_config['service'].'?gravatar_id='.md5($this->_email).'&s='.$this->_config['size'].'&r='.$this->_config['rating'];

		if ( ! empty($this->_config['default']))
			$string .= '&d='.$this->_config['default'];
		
		return $string;
	}
}