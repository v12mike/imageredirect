<?php
/**
*
* @package imageredirect
* @copyright (c) 2017 v12Mike
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace v12mike\imageredirect\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v312');
	}

	public function update_data()
	{
		$update_data = array();

		$update_data[] = array('config.add', array('imageredirect_version', '1.0.0'),);
		$update_data[] = array('config.add', array('imageredirect_localimagesmode', "0"),);
		$update_data[] = array('config.add', array('imageredirect_localimagespath', "images/ext/"),);
		$update_data[] = array('config.add', array('imageredirect_proxymode', "0"),);
		$update_data[] = array('config.add', array('imageredirect_proxyaddress', ""),);
		$update_data[] = array('config.add', array('imageredirect_proxyapikey', ""),);

		$update_data[] = array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'IR_ACP'),);
		$update_data[] = array('module.add', array(
			'acp','IR_ACP', array(
				'module_basename'	=> '\v12mike\imageredirect\acp\imageredirect_module',
				'modes'				=> array('local_image_store', 'image_proxy', 'image_link_locations',),
				),
			),);
		return $update_data;
	}

	//lets create the needed tables
	public function update_schema()
	{
		$update_data = array();

		$update_data = array(
			'add_tables' => array(
				$this->table_prefix . 'imageredirect_no_proxy_domains' => array(
					'COLUMNS'		=> array(
						'domain_id'		=> array('UINT:8', NULL, 'auto_increment',),
						'domain'		=> array('VCHAR:50', '',),
						'subdomains'	=> array('UINT:8', 1,),
					),
					'PRIMARY_KEY' => 'domain_id',
				),
				$this->table_prefix . 'imageredirect_link_locations' => array(
					'COLUMNS'		=> array(
						'location_id'	=> array('UINT:8', NULL, 'auto_increment',),
						'location'		=> array('VCHAR:50', '',),
						'field'			=> array('VCHAR:50', '',),
						'comment'		=> array('VCHAR:255', '',),
						'type'			=> array('UINT:8', 0,),
					),
					'PRIMARY_KEY'    => 'location_id',
				),
			),
		);
		return $update_data;
	}

	public function revert_schema()
	{
		$update_data = array();

		$update_data = array(
			'drop_tables'		=> array(
				$this->table_prefix . 'imageredirect_no_proxy_domains',
				$this->table_prefix . 'imageredirect_link_locations',
			),
		);
		return $update_data;
	}
}

