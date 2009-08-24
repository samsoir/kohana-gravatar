<?php defined('SYSPATH') or die('No direct script access.');

class Gravatar_Exception_Xmlrpc extends Gravatar_Exception {

	public function __construct($message, $code = NULL)
	{
		parent::__construct($message, NULL, $code);
	}

}