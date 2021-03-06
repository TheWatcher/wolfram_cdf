<?php
/**
 * Wolfram Mathematics CDF extension.
 * This extension allows .cdf files generated by Wolfram Mathematica to be displayed
 * in wiki pages using a simple `cdf` tag.
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

/** Should http:// or https:// in cdf tags result in a player that will show .cdf files
 *  from other sites? Set this to true if this feature should be used, false otherwise.
 */
$wgCDFAllowExternalURLs = false; // disable external urls by default for security

/** Display a download link beneath the plugin box. Allows users to grab the .cdf and view
 *  it in a standalone player.
 */
$wgCDFShowDownloadLink = false;


// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
    'path'           => __FILE__,
    'name'           => 'CDF',
    'version'        => '0.2.1',
    'author'         => 'Chris Page',
    'url'            => '',
    'descriptionmsg' => 'wolfram_cdf_desc'
);


// Standard setup stuff...
$wgExtensionMessagesFiles['CDF'] = dirname( __FILE__ ) . '/CDF.i18n.php';
$wgHooks['ParserFirstCallInit'][] = 'efCDFSetup';


// ResourceLoader information. Note that this uses 'position', and hence needs 1.18+
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
    global $wgCDFAllowExternalURLs;
    global $wgCDFShowDownloadLink;

    // Grab and convert arguments set for the tag...
    $width  = isset($argv['width'])  ? intval($argv['width']) : 320;
    $height = isset($argv['height']) ? intval($argv['height']) : 240;

    $output = '<div class="cdf">';

    // If the tag has any contents, create the embed marker
    if($input != '') {
        // If the contents are not a probable URL, or external URLs are disabled, treat it as a filename
        // NB: Yes, I know that this is not an exhaustive URL match.
        if(!$wgCDFAllowExternalURLs || !preg_match('|^https?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $input)) {
            // Remove File: if present...
            if(preg_match('/^File:/', $input)) {
                $input = substr($input, 5);
            }

            $file = wfFindFile($input);
            if($file) {
                $output .= "<script type=\"text/javascript\">jQuery.cdfplugin.embed('".$file -> getViewURL(false)."', $width, $height);</script>\n";

                // Add the download link if needed
                if($wgCDFShowDownloadLink) {
                    $output .= '<div class="cdflink">[<a href="'.$file -> getViewURL(false).'">'.wfMsg('cdf_download').'</a>]</div>';
                }

            } else {
                $output .= '<span class="error">'.wfMsg('cdf_badfilename').'<span>';
            }
        } else {
            $output .= "<script type=\"text/javascript\">jQuery.cdfplugin.embed('$input', $width, $height);</script>\n";

            // Add the download link if needed
            if($wgCDFShowDownloadLink) {
                $output .= '<div class="cdflink"">[<a href="'.$input.'">'.wfMsg('cdf_download').'</a>]</div>';
            }
        }

    }
    $output .= "</div>";

    return $output;
}
