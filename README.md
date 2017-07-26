# Image Redirect Extension

This is the repository for the Image Redirect Extension.for phpBB

## Quick Install
You can install this on the latest release of phpBB 3.1/3.2 by following the steps below:

1. In the `ext` directory of your phpBB board, create a new directory named `v12mike` (if it does not already exist) and navigate to it
1. `git clone git@github.com:v12mike/imageredirect.git` (the extension tree should now be under ext/v12mike/imageredirect)
1. Navigate in the ACP to `Customise -> Manage extensions`.
1. Look for `Image Redirect` under the Disabled Extensions list, and click its `Enable` link.
1. To use the local hositn of external images feature
 * Navigate in the ACP to 'Extensions -> Image Redirect -> Local Image Store Settings'.
 * Enter the path to the local image file store e.g. images/ext/
 * Enable local store mode
1. To use the secure image proxy feature
 * Navigate in the ACP to 'Extensions -> Image Redirect -> Proxy Settings'.
 * Enter the proxy address (without protocol specifier or trailing /) e.g. mydomain.com/camo
 * Enter the camo API key (if using a camo server) 
 * Add at least your sites domain(s) to the Directly Mapped Domains list (without protocol specifier or trailing /) e.g. mydomain.com
 * Select 'Camo Mode' or 'Simple Mode' and that 'Image Proxy Enable' is selected
 * Note that proxy mode requires [Camo](https://github.com/atmos/camo) or other proxy server to have been setup previously.

## Support

* Report bugs and other issues via github.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

## Todo

* Add ability to automatically harvest external images
