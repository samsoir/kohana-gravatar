<?php defined('SYSPATH') or die('No direct script access.');
/**
 * [ref-gravatar] Gravatars are universal avatars available to all web sites and services.
 * Users must register their email addresses with Gravatar before their avatars will be
 * usable with this module. Users with gravatars can have a default image of your selection.
 *
 * @see        http://en.gravatar.com/
 *
 * @package    Kohana
 * @category   Gravatar
 * @version    3.1.0
 * @author     Kohana Team
 * @copyright  (c) 2009-2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Gravatar_Xmlrpc {

	/**
	 * Gravatar Ratings constants
	 */
	const G   = 0;
	const PG  = 1;
	const R   = 2;
	const X   = 3;


	/**
	 * Create an instance of the Gravatar XMLRPC API client
	 *
	 * @param   array        $config 
	 * @return  Gravatar_Xmlrpc
	 * @access  public
	 */
	public static function factory($config = array())
	{
		return new Gravatar_Xmlrpc($config);
	}

	/**
	 * @var     array
	 */
	protected $_config;

	/**
	 * Constructor
	 *
	 * @param   array        $config
	 * @throws  Kohana_Gravatar_Xmlrpc_Exception
	 */
	public function __construct($config)
	{
		// Check for soap
		if ( ! extension_loaded('XMLRPC'))
		{
			throw new Kohana_Gravatar_Xmlrpc_Exception('XML-RPC extension must be loaded to use this class!');
		}

		// Configure this library
		$config += Kohana::config('gravatar.xmlrpc');
		$this->_config = $config;
	}

	/**
	 * Set or get the password
	 *
	 * @param   string       $password 
	 * @return  mixed
	 */
	public function password($password = NULL)
	{
		// If there is no API key supplied
		if ($password === NULL)
		{
			return $this->_config['password'];
		}

		// Else set the API key
		$this->_config['password'] = (string) $password;

		// Return this
		return $this;
	}

	/**
	 * Set or get the email address in question
	 *
	 * @param   string       $email [Optional]
	 * @return  mixed
	 */
	public function email($email = NULL)
	{
		// If no argument, return the email address
		if ($email === NULL)
		{
			return $this->_config['email'];
		}

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
	 */
	public function exists(array $hashes)
	{
		return $this->_xmlrpc_request('grav.exists', array('hashes' => $hashes));
	}

	/**
	 * Returns an array of email addresses
	 * registered to the account API key
	 *
	 * @return  array
	 */
	public function addresses()
	{
		return $this->_xmlrpc_request('grav.addresses');
	}

	/**
	 * Returns an array containing images
	 * registered to this user - and their
	 * respective rating
	 *
	 * @return  array
	 */
	public function user_images()
	{
		return $this->_xmlrpc_request('grav.userimages');
	}

	/**
	 * Save an image to the registered account.
	 * Images must be transferred in raw base64
	 * encoded format.
	 *
	 * @param   string       $image
	 * @param   int          $rating [Optional]
	 * @return  string|boolean
	 * @throws  Kohana_Gravatar_Xmlrpc_Exception
	 */
	public function save_data($image, $rating = NULL)
	{
		// Load the image
		if ( ! $resource = file_get_contents($image))
		{
			throw new Kohana_Gravatar_Xmlrpc_Exception(__METHOD__.' unable to open image : :file', array(':file' => $image));
		}

		// If no rating has been applied, use general
		if ($rating === NULL)
		{
			$rating = Gravatar_Xmlrpc::G;
		}

		// Encode the image resource
		$encoded_image = base64_encode($resource);

		// Save the image to Gravatar
		return $this->_xmlrpc_request('grav.saveData', array('data' => $encoded_image, 'rating' => $rating));
	}

	/**
	 * Save a image URL to the registered account.
	 *
	 * @param   string       $url 
	 * @param   int          $rating [Optional]
	 * @return  string|boolean
	 * @throws  Kohana_Gravatar_Xmlrpc_Exception
	 */
	public function save_url($url, $rating = NULL)
	{
		// If the URL supplied is not valid
		if ( ! Validate::url($url))
		{
			// Throw an exception
			throw new Kohana_Gravatar_Xmlrpc_Exception(__METHOD__.' invalid URL supplied : :url', array(':url' => $url));
		}

		// If no rating has been applied, use general
		if ($rating === NULL)
		{
			$rating = Gravatar_Xmlrpc::G;
		}

		// Save the image to Gravatar
		return $this->_xmlrpc_request('grav.saveData', array('url' => $url, 'rating' => $rating));
	}

	/**
	 * Assign a user image on Gravatar to the addresses
	 * supplied.
	 *
	 * @param   string   user_image 
	 * @param   array    addresses 
	 * @return  array
	 */
	public function use_user_image($user_image, array $addresses)
	{
		// Set the userimage to all supplied addresses
		return $this->_xmlrpc_request('grav.useUserimage', array('userImage' => $user_image, 'addresses' => $addresses));
	}

	/**
	 * Remove the userimage associated with one or more email addresses
	 *
	 * @param   array    addresses 
	 * @return  boolean
	 */
	public function remove_image(array $addresses)
	{
		// Remove the current image from the supplied addresses
		return $this->_xmlrpc_request('grav.removeImage', array('addresses' => $addresses));
	}

	/**
	 * Remove a user image from the account and any email addresses with which it is associated
	 *
	 * @param   string   user_image 
	 * @return  boolean
	 */
	public function delete_user_image($user_image)
	{
		// Delete an image from the account
		return $this->_xmlrpc_request('grav.deleteUserimage', array('userimage' => $user_image));
	}

	/**
	 * Processes an XML-RPC response based on the method and parameters passed
	 * to it. This method uses PHP streams over cURL, which makes it available
	 * to the vast majority of systems.
	 *
	 * @param   string   method 
	 * @param   array    parameters 
	 * @return  mixed
	 * @throws  Kohana_Gravatar_Xmlrpc_Exception
	 */
	protected function _xmlrpc_request($method, array $parameters = array())
	{
		// Create the endpoint
		$endpoint = $this->_prepare_service_endpoint();

		// Apply the Gravatar user password to the parameters
		$parameters['password'] = $this->_config['password'];

		// Create the XML-RPC request
		$xml_payload = xmlrpc_encode_request($method, $parameters);

		// Create a context stream, enforcing a POST request with correct header
		$context = stream_context_create(array(
			'http' => array(
				'method'   => 'POST',
				'header'   => 'Content-Type: text/xml',
				'content'  => $xml_payload
			)
		));

		try
		{
			// Process the XML-RPC request and decode the response
			$xmlrpc_response = xmlrpc_decode(file_get_contents($endpoint, FALSE, $context));

			// If there was an error
			if ($xmlrpc_response and xmlrpc_is_fault($xmlrpc_response))
			{
				// Throw an exception
				throw new Kohana_Gravatar_Xmlrpc_Exception($xmlrpc_response['faultString'], NULL, $xmlrpc_response['faultCode']);
			}
			else
			{
				// return the response
				return $xmlrpc_response;
			}

			// Shouldn't get this far
			throw new Kohana_Gravatar_Xmlrpc_Exception(__METHOD__.' something went wrong, xmlrpc_response was empty');
		}
		// Catch all unexpected exceptions
		catch (Exception $e)
		{
			throw new Kohana_Gravatar_Xmlrpc_Exception($e->getMessage(), NULL, $e->getCode());
		}
	}

	/**
	 * Execute the Xmlrpc request and return
	 * the result
	 *
	 * @return  mixed
	 * @throws  Kohana_Gravatar_Xmlrpc_Exception
	 */
	protected function _prepare_service_endpoint()
	{
		if ($this->_config['email'] === NULL)
		{
			throw new Kohana_Gravatar_Xmlrpc_Exception('Username must be supplied to perform Gravatar API requests!');
		}

		// Generate full uri with user id
		return $this->_config['service'].'?user='.md5($this->_config['email']);
	}
}