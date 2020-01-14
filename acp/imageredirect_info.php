<?php

/**
*
* @package Image Redirect
* @copyright (c) 2016-2020 v12Mike
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace v12mike\imageredirect\acp;

/**
* @package module_install
*/
class imageredirect_info
{
	function module()
	{
		return array(
			'filename'	=> 'v12mike/imageredirect/acp/imageredirect_module',
			'title'		=> 'Image Redirect',
			'version'	=> '2.0.1-b4',
			'modes'		=> array(
				'local_image_store'	=> array(
					'title' => 'IR_LOCAL_STORE_CONFIG',
					'auth' => 'ext_v12mike/imageredirect && acl_a_board',
					'cat'	=> array('IR_EXT')),
				'image_proxy'	=> array(
					'title' => 'IR_PROXY_CONFIG',
					'auth' => 'ext_v12mike/imageredirect && acl_a_board',
					'cat'	=> array('IR_EXT')),
				/* although not used in v2.0, 'image_link_locations' must be present here so that the migrator can delete it! */
				'image_link_locations'	=> array(
					'title' => 'IR_LOCATIONS_CONFIG',
					'auth' => 'ext_v12mike/imageredirect && acl_a_board',
					'cat'	=> array('IR_EXT')),
			),
		);
	}
}

