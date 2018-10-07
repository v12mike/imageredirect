<?php
/**
*
* Image Redirect Extension [English]
*
* @package language Image Redirect
* @copyright (c)  2017-2018 v12mike
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// ACP
	'IR_ACP'						=> 'Image Redirect',
	'IR_TITLE'						=> 'Image Redirect Configuration',
	'IR_VERSION'					=> 'Version',
	'IR_LOCAL_STORE_CONFIG'			=> 'Local Image Store Settings',
	'IR_PROXY_CONFIG'				=> 'Proxy Settings',
	'IR_LOCAL_IMAGES_MODE'			=> 'Local Image Store Mode',
	'IR_LOCAL_IMAGES_MODE_EXPLAIN'	=> 'Allows previously harvested copies of exernal images to be served from local storage',
	'IR_LOCAL_IMAGES_MODE_ENABLED'	=> 'Enabled',
	'IR_LOCAL_IMAGES_MODE_DISABLED'	=> 'Disabled',
	'IR_SAVE_LOCAL_IMAGES'			=> 'Save Local Image Store Settings',
	'IR_LOCAL_IMAGES_PATH'			=> 'Local Image Store Path',
	'IR_LOCAL_IMAGES_PATH_EXPLAIN'	=> 'Path to the local image store (relative to board root) e.g. images/ext/',
	'IR_PROXY_MODE'					=> 'Proxy Mode',
	'IR_CAMO_MODE'					=> 'Camo Mode',
	'IR_SIMPLE_MODE'				=> 'Simple Mode',
	'IR_SIMPLE_MODE_EXPLAIN'		=> 'Allows an alternate mode using a commercial proxy service',
	'IR_PROXY_API_KEY'				=> 'Camo Proxy API Key',
	'IR_ENABLED_EXPLAIN'	  	  	=> 'Allows the extension to be disabled while the configuration page is still available',
	'IR_PROXY_ADDRESS'	   	 		=> 'Address of the image proxy server',
	'IR_PROXY_ADDRESS_EXPLAIN' 		=> 'No protocol specifier or trailing / (e.g.: my_site/camo)',
	'IR_PROXY_API_KEY'				=> 'Camo API key',
	'IR_PROXY_API_KEY_EXPLAIN' 		=> 'A secret key shared with the camo proxy server',
	'IR_SAVE_PROXY'	    			=> 'Save proxy configuration',
	'IR_ENABLE'						=> 'Enable',
	'IR_DISABLE'					=> 'Disable',
	'IR_ENABLED'					=> 'Enabled',
	'IR_DISABLED'					=> 'Disabled',
));
