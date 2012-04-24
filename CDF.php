<?php
/**
 * Wolfram Mathematics CDF extension
 *
 * To activate this extension, add the following into your LocalSettings.php file:
 * require_once('$IP/extensions/wolfram_cdf/CDF.php');
 *
 * @ingroup Extensions
 * @author Chris Page
 * @version 0.1
 * @link
 * @license GNU General Public License 3.0 or later
 */

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if( !defined( 'MEDIAWIKI' ) ) {
    echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
    die( -1 );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
    'path'           => __FILE__,
    'name'           => 'CDF',
    'version'        => '0.2.0',
    'author'         => 'Chris Page',
    'url'            => '',
    'descriptionmsg' => 'wolfram_cdf_desc'
);

$wgExtensionMessagesFiles['CDF'] = dirname( __FILE__ ) . '/CDF.i18n.php';
$wgHooks['ParserFirstCallInit'][] = 'efCDFSetup';

$wgResourceModules['ext.cdf'] = array (
    'scripts' => 'cdfplugin.js',

    'localBasePath' => dirname( __FILE__ ),
    'remoteExtPath' => 'wolfram_cdf',

    'position' => 'top'
);

function efCDFSetup() {
    global $wgParser;
    global $wgOut;

    $wgOut -> addModules('ext.cdf');
    $wgParser -> setHook("cdf", "efRenderCDF");

    return true;
}

function efRenderCDF($input, $argv, $parser)
{
    global $wgUser;
    global $wgServer;

    // Grab and convert arguments set for the tag...
    $width  = isset($argv['width'])  ? intval($argv['width']) : 320;
    $height = isset($argv['height']) ? intval($argv['height']) : 240;

    $output = '';

    // If we have any input, create the embed marker
    if($input != '') {
        // If we have anything other than a URL, assume it's a filename
        if(!preg_match('/^https?:/', $input)) {
            // Remove File: if present...
            if(preg_match('/^File:/', $input)) {
                $input = substr($input, 5);
            }

            $file = wfFindFile($input);
            if($file) {
                $output = "<script type=\"text/javascript\">jQuery.cdfplugin.embed('".$file -> getViewURL(false)."', $width, $height);</script>\n";
            } else {
                $output = '<span class="error">'.wfMsg('cdf_badfilename').'<span>';
            }
        } else {
            $output = "<script type=\"text/javascript\">jQuery.cdfplugin.embed('$input', $width, $height);</script>\n";
        }
    }

    return $output;
}
