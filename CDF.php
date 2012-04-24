<?php
/**
 * Wolfram Mathematics CDF extension
 *
 * To activate this extension, add the following into your LocalSettings.php file:
 * require_once('$IP/extensions/wolfram_cdf/CDF.php');
 *
 * @note This is a very early alpha version of this extension. A more "correct" version 
 *       will be released as soon as possible.
 * 
 * @ingroup Extensions
 * @author Chris Page
 * @version 0.1
 * @link 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
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
    'path'         => __FILE__,
    'name'         => 'WolframCDF',
    'version'      => '0.1.0',
    'author'       => 'Chris Page', 
    'url'          => '',
    'description'  => 'This extension provides the <nowiki><cdf></nowiki> tag'
);

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
    $wgParser -> setHook("cdf", "renderCDF");

    return true;
}

function renderCDF($input, $argv, $parser) 
{
    global $wgUser;
    global $wgServer;

    // Grab and convert arguments set for the tag...
    $width  = isset($argv['width'])  ? intval($argv['width']) : 320;
    $height = isset($argv['height']) ? intval($argv['height']) : 240;

    $output = '';
    $style  = 'display: block;';

    // If we have any input, create the popup tags...
    if($input != '') {
        // If we have anything other than a URL, assume it's a filename
        if(!preg_match('/^https?:/', $input)) { 
            $file = wfFindFile($input);
            if($file) {
                $input = $file -> getViewURL(false);
            } else {
                $input ="Error in filename";
            }
        }

        $output .=  "<script type=\"text/javascript\">jQuery.cdfplugin.embed('$input', $width, $height);</script>\n";
    }

    return $output;
}
