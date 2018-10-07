# Image Redirect Extension

This is the repository for the Image Redirect Extension.for phpBB

## Quick Install
You can install this on phpBB 3.2.x by following the steps below:

1. In the `ext` directory of your phpBB board, create a new directory named `v12mike` (if it does not already exist) and navigate to it
2. Copy the 'imageredirect' directory into the 'v12mike' directory (the extension tree must now be under ext/v12mike/imageredirect)
3. Navigate in the ACP to `Customise -> Manage extensions`.
4. Look for `Image Redirect` under the phpBB Disabled Extensions list, and click its `Enable` link.
5. To use the (optional) local hosting of external images feature
 * You MUST have already downlodaed the external image files using the scripts https://github.com/v12mike/fetch-external-images
 * Navigate in the phpBB ACP to 'Extensions -> Image Redirect -> Local Image Store Settings'.
 * Enter the path to the local image file store (e.g. images/ext/)
 * Enable local store mode
6. To use the (optional) secure image proxy feature
 * You must already have a Camo proxy server (https://github.com/atmos/camo) or other secure proxy server available.
 * Navigate in the ACP to 'Extensions -> Image Redirect -> Proxy Settings'.
 * Enter the proxy address (without protocol specifier or trailing /) e.g. mydomain.com/camo
 * Enter the camo API key (if using a camo server) 
 * Select 'Camo Mode' or 'Simple Mode' and that 'Image Proxy Enable' is selected.

## Upgrading from v1.0.x
 * Version 2.x of this extension is NOT compatible with phpBB v3.1.x
 * Disable the v1.0.x extension before deleting the extension files
 * Delete all of the directories and files in the 'ext/v12mike/imageredirect' directory
 * Copy all of the v2.0.x files into their respective directories under 'ext/v12mike/imageredirect'
 * Enable the Image Redirect extension in the ACP.  The settings should not need to be adjusted.
 
## Support

* Report bugs and other issues via github.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

