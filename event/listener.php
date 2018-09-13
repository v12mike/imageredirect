<?php
/**
*
* @package Image Redirect
* @copyright (c) 2017-2018 v12mike
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace v12mike\imageredirect\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.text_formatter_s9e_render_before' => 'adjust_xml_before_rendering',
		);
	}

	public function adjust_xml_before_rendering($event)
	{
		$event['xml'] = \s9e\TextFormatter\Utils::replaceAttributes($event['xml'], 'IMG', function (array $attributes)
			{
				global $phpbb_root_path;

				if (isset($attributes['src']))
				{
					// maybe we want to serve a local copy of the image?
					if ($this->config['imageredirect_localimagesmode'] > 0)
					{
						// if we have a locally hosted copy of the file, we can find it
						$local_file_name = md5("$url");
						$file_path = $phpbb_root_path . $this->config['imageredirect_localimagespath'] . $local_file_name;
						if (file_exists($file_path))
						{
							// we will link to the local file
							$attributes['src'] = generate_board_url() . '/' . $this->config['imageredirect_localimagespath'] . $local_file_name;
							return;
						}
						// drop through to proxy mode
					}

					// skip unless proxy mode enabled
					if ($this->config['imageredirect_proxymode'] > 0) 
					{
							// only redirect to proxy if the url is not https://
							if (strpos($attributes['src'], 'http:') == 0)
							{
								 // rewite others for  "simple mode" proxy (if so configured)
								if ($this->config['imageredirect_proxysimplemode'])
								{
									// the substr($url, 7) trims the leading http:// from the url
									$attributes['src'] = 'https://' . $this->config['imageredirect_proxyaddress'] . substr($attributes['src'], 7) . $this->config['imageredirect_proxyapikey'];
								}
								//  rewrite url for Camo proxy server
								else
								{
									$digest = hash_hmac('sha1', $attributes['src'], $this->config['imageredirect_proxyapikey']);
									$attributes['src'] = 'https://' . $this->config['imageredirect_proxyaddress'] . '/' . $digest . '/' . bin2hex($attributes['src']);
								}
							}
						}
				}
				return $attributes;
			}
		);
	}

	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_prefix, $auth, $user)
	{
		$this->config = $config;
		$this->db = $db;  //remove
		$this->table_prefix = $table_prefix; //remove
		$this->auth = $auth;  //remove
		$this->user = $user; //remove
	}
}

