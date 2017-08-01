<?php
/**
*
* @package Image Redirect
* @copyright (c) 2014 phpBB Group
* @copyright (c) 2017 v12mike
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace v12mike\imageredirect\event;

define('ONE_MONTH', '2500000'); //seconds (approximately)
define('REGEX_STRING', '#<img [^>]*src="(http(s?):\/\/[^"]+)"[^>]*>#'); //define it once here, in case it needs adjusting

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_footer_after'	=> 'rewrite_assets',
		);
	}

	/**
	 * Rewrites an image tag into a version that can be used by a Camo asset server
	 *
	 * @param	array	$object	The array containing the data to rewrite
	 * @param	string	$key	The key into the array. The element to rewrite.
	 * @return	void
	 */
	private function rewrite_images(&$object, $key, $domains, $local_images_mode, $local_images_path)
	{
		global $phpbb_root_path;

		if (!empty($object[$key]))
		{
			if (preg_match_all(REGEX_STRING, $object[$key], $matches))
			{
				foreach ($matches[1] as $index=>$url)
				{
					// maybe we want to serve a local copy of the image?
					if ($local_images_mode > 0)
					{
						// if we have a locally hosted copy of the file, we can find it
						$local_file_name = md5("$url");
						$file_path = $phpbb_root_path  . $local_images_path . $local_file_name;
						if (file_exists($file_path))
						{
							// we will link to the local file
							$object[$key] = str_replace($url, generate_board_url() . '/' . $local_images_path . $local_file_name, $object[$key]);

							// next please!
							continue;
						}
					}
					
					// skip unless proxy mode enabled
					if ($this->config['imageredirect_enabled'] == 0) 
						continue;
					
					foreach ($domains as $row)
					{
						$domain = $row['domain'] . '/' ;
						$subdomains = $row['subdomains'];
						$match = stripos($url, $domain);
						if ($match !== false)
						{
							if (($subdomains != 0) || ($match == 7)) // 7 chars in "http://"
							{
								// just rewrite http:// to https:// for domains (including this one) that should support it
								$object[$key] = preg_replace('#http:#', 'https:', $object[$key]);
								break;
							}
						}
					}
					// only redirect to proxy if the url is not https://
					if ($matches[2][$index] == '')
					{
						// rewite others for  "simple mode" proxy (if so configured)
						if ($this->config['imageredirect_proxysimplemode'])
						{
							// the substr($url, 7) trims the leading http:// from the url
							$object[$key] = str_replace($url, 'https://' . $this->config['imageredirect_proxyaddress'] . substr($url, 7) . $this->config['imageredirect_proxyapikey'], $object[$key]);
						}
						//  rewrite url for Camo proxy server
						else
						{
							$digest = hash_hmac('sha1', $url, $this->config['imageredirect_proxyapikey']);
							$object[$key] = str_replace($url, 'https://' . $this->config['imageredirect_proxyaddress'] . '/' . $digest . '/' . bin2hex($url), $object[$key]);
						}
					}
				}
			}
		}
	}

	/**
	 * Adds an unhandled insecure link location to the database
	 *
	 * @param	string	$key	The template name.
	 * @param	string	$location	The element name.
	 * @param	array	$locations	The array containing the 
	 *  			configured set of locations
	 * @return	void
	 */
	private function unhandled_insecure_link($location, $field, &$locations)
	{
		global $cache;

		foreach ($locations as $configured_location)
		{
			if (($configured_location['location'] == $location) && ($configured_location['field'] == $field))
			{
				// already in the database, re-enable it if disabled, otherwise ignore it
				if ($configured_location['type'] == 0)
				{
					$sql = 'UPDATE ' . $this->table_prefix . 'imageredirect_link_locations SET type=2 WHERE location_id=' . $configured_location['location_id'];
					$this->db->sql_query($sql);
					$cache->destroy('sql', $this->table_prefix . 'imageredirect_proxylocations');
					// refresh the locations array to pick up the one we have just updated
					$sql = 'SELECT location, field, type, location_id FROM ' . $this->table_prefix . 'imageredirect_link_locations';
					$result = $this->db->sql_query($sql);
					$locations = $this->db->sql_fetchrowset($result);
					$this->db->sql_freeresult($result);
				}
				return;
			}
		}
		// not found, so add it to the database
		$this->user->add_lang('acp/common');
		$this->user->add_lang_ext('v12mike/imageredirect', 'info_acp_imageredirect');
		$sql = 'INSERT INTO ' . $this->table_prefix . 'imageredirect_link_locations' . $this->db->sql_build_array('INSERT', array(
			'location'	=> $location,
			'field'		=> $field,
			'type'		=> 2,
			'comment'	=> $this->user->lang['IR_ADDED_BY_TRAINING'],
		));
		$this->db->sql_query($sql);
		$cache->destroy('sql', $this->table_prefix . 'imageredirect_link_locations');
		// refresh the locations array to pick up the one we have just added
		$sql = 'SELECT location, field, type, location_id FROM ' . $this->table_prefix . 'imageredirect_link_locations';
		$result = $this->db->sql_query($sql);
		$locations = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);


	}

	public function rewrite_assets($event)
	{
		global $request;
		global $phpbb_container;

		if (($this->config['imageredirect_proxymode'] == 0) && ($this->config['imageredirect_localimagesmode'] == 0))
			return;

		$context = $phpbb_container->get('template_context');
		$rootref = &$context->get_root_ref();
		$tpldata = &$context->get_data_ref();

		// get all the domains that are directly remapped
		// do it here for efficiency
		$sql = 'SELECT domain, subdomains FROM ' . $this->table_prefix . 'imageredirect_no_proxy_domains';
		// cache the query for a while 
		$result = $this->db->sql_query($sql, ONE_MONTH);
		$domains = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		$local_images_mode = $this->config['imageredirect_localimagesmode'];
		$local_images_path = $this->config['imageredirect_localimagespath'];

		// get all the fields that need to be patched
		$sql = 'SELECT location, field, type, location_id FROM ' . $this->table_prefix . 'imageredirect_link_locations';
		// cache the query for a while 
		$result = $this->db->sql_query($sql, ONE_MONTH);
		$locations = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		foreach ($locations as $row)
		{
			if ($row['type'] == 0)
			{
				// this one is disabled
				continue;
			}
			$location = $row['location'];
			if ($location == 'headers')
			{
				// patch header fields
				$this->rewrite_images($rootref, $row['field'], $domains, $local_images_mode, $local_images_path);
			}
			else
			{
				// patch all other required fields
				if (isset($tpldata[$location]))
				{
					foreach ($tpldata[$location] as &$tplrow)
					{
						$this->rewrite_images($tplrow, $row['field'], $domains, $local_images_mode, $local_images_path);
					}
				}
			}
		}

		// catch any http:// image links and add to database
		// only if in 'learning mode' and user is admin
		if (($this->config['imageredirect_proxymode'] == 2) && $this->auth->acl_get('a_'))
		{
			foreach ($tpldata as $key=>$object)
			{
				foreach ($object as $item)
				{
					foreach ($item as $field=>$string)
					{
						if (gettype($string) == 'string')
						{
							if (preg_match(REGEX_STRING, $string))
							{
								// we have found an http:// image link (after rewriting all configured locations)
								$this->unhandled_insecure_link(($key == '.')?'headers':$key, $field, $locations);
							}
						}
					}
				}
			}
		}

	}

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_prefix, $auth, $user)
	{
	   $this->config = $config;
	   $this->db = $db;
	   $this->table_prefix = $table_prefix;
	   $this->auth = $auth;
	   $this->user = $user;
	}
}
