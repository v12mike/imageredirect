<?php
/**
*
* @package imageredirect
* @copyright (c) 2017 v12Mike
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace v12mike\imageredirect\acp;

class imageredirect_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix;

		$this->config = $config;
		$this->request = $request;
		$this->template = $template;
		$this->db = $db;
		$this->cache = $cache;

		$user->add_lang('acp/common');
		$user->add_lang_ext('v12mike/imageredirect', 'info_acp_imageredirect');
		$this->tpl_name = 'acp_imageredirect';
		$this->page_title = $user->lang['IMAGE_REDIRECT'];
		add_form_key('acp_imageredirect');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('acp_imageredirect'))
			{
				trigger_error('FORM_INVALID');
			}
			if (!function_exists('validate_data'))
			{
				include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			}

			$check_row = array('imageredirect_proxyaddress' => $request->variable('imageredirect_proxyaddress', 0));
			$validate_row = array('imageredirect_proxyaddress' => array('string', false, 0, 5));
			$error = validate_data($check_row, $validate_row);

			// Replace "error" strings with their real, localised form
			$error = array_map(array($user, 'lang'), $error);

			// more data validation could be inserted here...

			if (!sizeof($error))
			{
				$config->set('imageredirect_enabled',      	$request->variable('imageredirect_enabled', 0));
				$config->set('imageredirect_simplemode',	$request->variable('imageredirect_simplemode', 0));
				$config->set('imageredirect_proxyaddress',  $request->variable('imageredirect_proxyaddress', "", true));
				$config->set('imageredirect_proxyapikey', 	$request->variable('imageredirect_proxyapikey', "", true));
			}
		}
		elseif ($request->is_set_post('delete_domain_') && $request->variable('delete_domain_', array(0 => '')))
		{
			// deletion of configured domain has been requested
			$domain_id = array_keys($request->variable('delete_domain_', array(0 => '')));
			$sql = 'DELETE FROM ' . $table_prefix . 'imageredirect_no_proxy_domains' . ' WHERE domain_id = ' . $domain_id[0];
			$this->db->sql_query($sql);
			$this->cache->destroy('sql', $table_prefix . 'imageredirect_no_proxy_domains');
		}
		elseif ($request->is_set_post('delete_location_') && $request->variable('delete_location_', array(0 => '')))
		{
			// deletion of configured location has been requested
			$location_id = array_keys($request->variable('delete_location_', array(0 => '')));
			$sql = 'DELETE FROM ' . $table_prefix . 'imageredirect_link_locations' . ' WHERE location_id = ' . $location_id[0];
			$this->db->sql_query($sql);
		}
		elseif ($request->is_set_post('add_domain'))
		{
			// add a new domain to the db
			$sql = 'INSERT INTO ' . $table_prefix . 'imageredirect_no_proxy_domains' . $this->db->sql_build_array('INSERT', array(
				'domain'		=> $request->variable('imageredirect_adddomain', "", true),
				'subdomains'	=> $request->variable('imageredirect_subdomains', 1),
			));
			$this->db->sql_query($sql);
			$this->cache->destroy('imageredirect_no_proxy_domains');
		}
		elseif ($request->is_set_post('add_location'))
		{
			// add a new location to the db
			$sql = 'INSERT INTO ' . $table_prefix . 'imageredirect_link_locations' . $this->db->sql_build_array('INSERT', array(
				'location'		=> $request->variable('imageredirect_addlocation', "", true),
				'field'	=> $request->variable('imageredirect_addfield', "", true),
				'comment'	=> $request->variable('imageredirect_addcomment', "", true),
			));
			$this->db->sql_query($sql);
		}

		// display the list of configured domains
	   	$sql = 'SELECT domain_id, domain, subdomains FROM ' . $table_prefix . 'imageredirect_no_proxy_domains ';
		$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page']);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('directurlrewrites', array(
				'DOMAIN_ID'		=> $row['domain_id'],
				'DOMAIN'		=> $row['domain'],
				'SUBDOMAINS'	=> $row['subdomains'],
				));
		}
		$this->db->sql_freeresult($result);

		// display the list of configured locations
	   	$sql = 'SELECT location_id, location, field, comment, type FROM ' . $table_prefix . 'imageredirect_link_locations ';
		$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page']);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('imageredirect_link_locations', array(
				'LOCATION_ID'	=> $row['location_id'],
				'LOCATION'		=> $row['location'],
				'FIELD'			=> $row['field'],
				'COMMENT'		=> $row['comment'],
				'TYPE'	=> $row['type'],
				));
		}
		$this->db->sql_freeresult($result);

		// fill-in the template
		$template->assign_vars(array(
			'ENABLED'			=> (!empty($this->config['imageredirect_enabled'])) ? true : false,
			'SIMPLE_MODE'		=> (!empty($this->config['imageredirect_simplemode'])) ? true : false,
			'PROXY_ADDRESS'		=> (!empty($this->config['imageredirect_proxyaddress'])) ? $this->config['imageredirect_proxyaddress'] : "",
			'PROXY_API_KEY'		=> (!empty($this->config['imageredirect_proxyapikey'])) ? $this->config['imageredirect_proxyapikey'] : "",
			'IR_VERSION'		=> $this->config['imageredirect_version'],
			'IR_ERROR'	        => isset($error) ? ((sizeof($error)) ? implode('<br />', $error) : '') : '',
			'U_ACTION'			=> $this->u_action,
		));

	}
}
