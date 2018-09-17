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
	public function __construct(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.text_formatter_s9e_render_before' => 'adjust_xml_before_rendering',
			'core.get_avatar_after' => 'adjust_avatar_link',
			);
	}

	private function adjust_img_src_url($url)
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
				$url = generate_board_url() . '/' . $this->config['imageredirect_localimagespath'] . $local_file_name;
				return $url;
			}
			// drop through to proxy mode
		}

		// skip unless proxy mode enabled
		if ($this->config['imageredirect_proxymode'] > 0)
		{
			// only redirect to proxy if the protocol is http:// (not https:// )
			if (strpos($url, 'http://') === 0)
			{
				// if the image is hosted on this server, assume that the url is valid and just update the protocol
				if (strpos($url, 'http://' . $this->config['server_name']) === 0)
				{
					$url = preg_replace('#http://#', 'https://', $url);
					return $url;
				}

				// rewite urls for  "simple mode" proxy (if so configured)
				if ($this->config['imageredirect_proxysimplemode'])
				{
					// the substr($url, 7) trims the leading http:// from the url
					$url = 'https://' . $this->config['imageredirect_proxyaddress'] . substr($url, 7) . $this->config['imageredirect_proxyapikey'];
					return $url;
				}

				// if all else fails... rewrite url for Camo proxy server
				$digest = hash_hmac('sha1', $url, $this->config['imageredirect_proxyapikey']);
				$url = 'https://' . $this->config['imageredirect_proxyaddress'] . '/' . $digest . '/' . bin2hex($url);
			}
		}
		return $url;
	}

	public function adjust_xml_before_rendering($event)
	{
		$event['xml'] = \s9e\TextFormatter\Utils::replaceAttributes($event['xml'], 'IMG', function (array $attributes)
			{
				global $phpbb_root_path;

				if (isset($attributes['src']))
				{
					$attributes['src'] = $this->adjust_img_src_url($attributes['src']);
				}
				return $attributes;
			}
		);
	}

	public function adjust_avatar_link($event)
	{
		$matches = array();
		preg_match('#src="(http(s?)://[^"]+)"#', $event['html'], $matches);
		if (strlen($matches[1]))
		{
			$new_url = $this->adjust_img_src_url($matches[1]);
			$event['html'] = str_replace($matches[1], $new_url, $event['html']);
		}
	}
}

