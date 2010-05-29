<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'default' => array(
		'service'           => 'http://www.gravatar.com/avatar.php', // The gravatar service URL
		'default_image'     => FALSE,        // The default image if Gravatar is not found, FALSE uses Gravatar default
		'size'              => 100,                                  // The size of the returned gravatar
		'view'              => 'gravatar/image',                     // The default view
		'rating'            => Gravatar::GRAVATAR_G,                 // The default rating
		'alt'               => FALSE,                                // Alternate image string, FALSE to omit, string to include
	),
	'xmlrpc' => array(
		'service'           => 'https://secure.gravatar.com/xmlrpc',
		'email'             => NULL,
		'password'          => NULL,
	),
);