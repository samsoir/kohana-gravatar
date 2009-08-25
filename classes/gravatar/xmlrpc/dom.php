<?php defined('SYSPATH') or die('No direct script access.');

class Gravatar_Xmlrpc_Dom extends Gravatar_Xmlrpc {

	/**
	 * Construct the class and check for
	 * the DOMDocument extension
	 *
	 * @param string $config 
	 * @author Sam Clark
	 */
	protected function __construct($config)
	{
		// Setup the parent
		parent::__construct($config);

		// Check for dom document
		if ( ! class_exists('DOMDocument', FALSE))
			throw new Gravatar_Exception_Xmlrpc('DOMDocument is required to use this class');
	}

	/**
	 * Checks that a hash exists
	 *
	 * @param   array        $hashes
	 * @return  array
	 * @access  public
	 * @todo
	 */
	public function exists($hashes)
	{
		
	}

	/**
	 * Returns an array of email addresses
	 * registered to the account API key
	 *
	 * @return  array
	 * @access  public
	 * @todo
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
	 * @todo
	 */
	public function userimages()
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
	 * @todo
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
	 * @todo
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
	 * @todo
	 */
	public function use_userimage($user_image, $addresses)
	{
		
	}

	/**
	 * Test function
	 *
	 * @return  mixed
	 * @access  public
	 * @todo
	 */
	public function test()
	{
		
	}
} // End Gravatar_Xmlrpc_Dom extends Gravatar_Xmlrpc