This is an early version of a MediaWiki extension that allows [Wolfram CDF](http://www.wolfram.com/cdf/) files to be shown in wiki pages.

Installing
----------

To install the extension, you must first obtain a copy of the wolfram_cdf extension (either by downloading a snapshot, or checking it out of git). Once you have the extension:

- Place the wolfram_cdf directory in your mediawiki/extensions directory
- add the following to the end of your LocalSettings.php
        require_once("$IP/extensions/wolfram_cdf/CDF.php");
- if you want to enable the ability to show external .cdf files for added security, add the following to your LocalSettings.php after the above
        $wgCDFAllowExternalURLs = true;

Note that, in order to actually use .cdf files, you will need to ensure that file uploads are enabled (`$wgEnableUploads` is `true`) and cdf extensions are allowed. For example:

    $wgEnableUploads  = true;
    $wgUseImageMagick = true;
    $wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg', 'txt', 'cdf' );

You may also need to either ensure that your webserver serves up .cdf files as `application/x-netcdf`, or modify `$IP/includes/mime.types` to contain

    text/plain txt cdf

Using
-----

The basic syntax for the cdf extension is:

    <cdf width="width in pixels" height="height in pixels">filename</cdf>

When specifying the filename, you do not need to include the `File:` namespace in the filename - it will work with or without it. You may also provide an absolute URL instead of a filename to show a CDF file from another server.

Examples:

    <cdf width="650" height="400">SomeExample.cdf</cdf>
    <cdf width="565" height="589">http://demonstrations.wolfram.com/HobermanCube/HobermanCube.cdf</cdf>

Note that the second example above will only work if `$wgCDFAllowExternalURLs` is set to `true`.
