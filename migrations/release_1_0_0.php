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
		return array(
			array('config.add', array('imageredirect_version', '1.0.0')),
			array('config.add', array('imageredirect_localimagesmode', 0)),
			array('config.add', array('imageredirect_localimagespath', "images/ext/")),
			array('config.add', array('imageredirect_proxymode', 0)),
			array('config.add', array('imageredirect_proxyaddress', "")),
			array('config.add', array('imageredirect_proxyapikey', "")),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'IR_ACP'
			)),
			array('module.add', array(
				'acp',
				'IR_ACP',
				array(
					'module_basename'	=> '\v12mike\imageredirect\acp\imageredirect_module',
					'modes'				=> array('local_image_store',
												 'image_proxy',
												 'image_link_locations')
				),
			)),
			array('custom', array(array($this, 'insert_url_location_data'))),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('imageredirect_version')),
			array('config.remove', array('imageredirect_localimagesmode')),
			array('config.remove', array('imageredirect_localimagespath')),
			array('config.remove', array('imageredirect_proxymode')),
			array('config.remove', array('imageredirect_proxyaddress')),
			array('config.remove', array('imageredirect_proxyapikey')),

			array('module.remove', array(
				'acp',
				'IR_ACP',
				array(
					'module_basename'	=> '\v12mike\imageredirect\acp\imageredirect_module',
					'modes'				=> array('local_image_store',
												 'image_proxy',
												 'image_link_locations')
				),
			)),
			array('module.remove', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'IR_ACP'
			))
		);
	}

	//lets create the needed tables
	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'imageredirect_no_proxy_domains' => array(
					'COLUMNS'		=> array(
						'domain_id'		=> array('UINT:8', NULL, 'auto_increment'),
						'domain'		=> array('VCHAR:50', ''),
						'subdomains'	=> array('UINT:8', 1)
					),
					'PRIMARY_KEY'    => 'domain_id',
				),
				$this->table_prefix . 'imageredirect_link_locations' => array(
					'COLUMNS'		=> array(
						'location_id'	=> array('UINT:8', NULL, 'auto_increment'),
						'location'		=> array('VCHAR:50', ''),
						'field'			=> array('VCHAR:50', ''),
						'comment'		=> array('VCHAR:255', ''),
						'type'			=> array('UINT:8', 0)
					),
					'PRIMARY_KEY'    => 'location_id',
				)
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'imageredirect_no_proxy_domains',
				$this->table_prefix . 'imageredirect_link_locations'
			),
		);
	}

	// add the default location data
	public function insert_url_location_data()
	{
		$initial_data = array(
			array('location' => 'forumrow',		'field' => 'FORUM_DESC',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'AUTHOR_AVATAR',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'AVATAR',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'AVATAR_IMG',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'CURRENT_USER_AVATAR',		'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'FORUM_DESC',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'MESSAGE',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'POST_PREVIEW',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'PREVIEW_MESSAGE',			'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'PREVIEW_SIGNATURE',			'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'SIGNATURE',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'headers',		'field' => 'SIGNATURE_PREVIEW',			'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'history_row',	'field' => 'MESSAGE',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'searchresults','field' => 'MESSAGE',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'notifications','field' => 'AVATAR',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'notification_list','field' => 'AVATAR',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'poll_option',	'field' => 'POLL_OPTION_CAPTION',		'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'postrow',		'field' => 'MESSAGE',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'postrow',		'field' => 'SIGNATURE',					'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'postrow',		'field' => 'POSTER_AVATAR',				'comment' => 'phpBB Core',	'type' => '1'),
			array('location' => 'topic_review_row','field' => 'MESSAGE',				'comment' => 'phpBB Core',	'type' => '1'),

			array('location' => 'topicrow',		'field' => 'TOPIC_PREVIEW_FIRST_AVATAR','comment' => 'Topic Preview extension','type' => '0'),
			array('location' => 'topicrow',		'field' => 'TOPIC_PREVIEW_LAST_AVATAR',	'comment' => 'Topic Preview extension','type' => '0'),
			array('location' => 'postrow',		'field' => 'TOPIC_PREVIEW_FIRST_AVATAR','comment' => 'Topic Preview extension','type' => '0'),
			array('location' => 'postrow',		'field' => 'TOPIC_PREVIEW_LAST_AVATAR',	'comment' => 'Topic Preview extension','type' => '0'),
			array('location' => 'searchresults','field' => 'TOPIC_PREVIEW_FIRST_AVATAR','comment' => 'Topic Preview extension','type' => '0'),
			array('location' => 'searchresults','field' => 'TOPIC_PREVIEW_LAST_AVATAR',	'comment' => 'Topic Preview extension','type' => '0'),

			array('location' => 'top_five_donors','field' => 'AVATAR',					'comment' => 'Donations extension','type' => '0'),
			array('location' => 'last_five_donors','field' => 'AVATAR',					'comment' => 'Donations extension','type' => '0'),
			array('location' => 'donorlist',	'field' => 'AVATAR',					'comment' => 'Donations extension','type' => '0'),

			array('location' => 'topicrow',		'field' => 'LAST_POST_AUTHOR_FULL',		'comment' => 'Last Post Avatar extension','type' => '0'),
			array('location' => 'forumrow',		'field' => 'AVATAR_IMG',				'comment' => 'Last Post Avatar extension','type' => '0'),

			array('location' => 'memberrow',	'field' => 'AVATAR_IMG',				'comment' => 'Senkys Avatars on Memberlist extension','type' => '0'),
			);
		$this->db->sql_multi_insert($this->table_prefix.'imageredirect_link_locations', $initial_data);
	}
}

