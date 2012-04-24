/* Name:        CDF Plugin
 * Version:     3.1
 * Authors:     Sean Jordan and Bob Kuo, Wolfram Research
 * Authors:     Chris Page, APEcs
 * Description: Detects if user has the Mathematica Browser Plugin installed and embeds the document
 * License:     MIT License <http://www.wolfram.com/cdf-player/plugin/v2.1/LICENSE>
 * Copyright:   2011 Wolfram Research
 *
 * Modified by Chris Page to support MediaWiki ResourceLoader.
 */

( function( $ ) {

$.cdfplugin = {
    
    'default_content': '<a style="display:block; width:100%; height:100%;' +
                       ' background:#ddd url(\'http://www.wolfram.com/cdf-player/plugin/images/cdf-player-white.png\') no-repeat center center; "' +
                       ' href="http://www.wolfram.com/cdf-player"></a>',

    'pluginIsPresent':  function() {
        if(typeof ActiveXObject != 'undefined') {
            // IE compatible browsers
            try {
                var control = new ActiveXObject("Mathematica.Control");
                if(control) {
                    return true; 
                }
            } catch (e) { }
        } else if(navigator.plugins && navigator.plugins.length > 0) {
            // Mozilla and WebKit browsers
            for(var i = 0; i < navigator.plugins.length; i++) {
                if(navigator.plugins[i].name.indexOf("Wolfram Mathematica") !== -1) {
                    return true;
                }
            }
        }
        return false;
    },

    'embed': function(src,width,height,params) {
        var content = (this.has_plugin) ? this._getEmbedHtml(src,width,height,params) : this._getDefaultContent(width,height);
        document.write(content);
    },

    '_getEmbedHtml': function(src,width,height,params) {
        var paramTags = "";
        var embedExtras = "";
        for (var prop in params) {
            paramTags += "<param name='" + prop + "' value='" + params[prop] + "'>";
            embedExtras += prop + "='" + params[prop] + "' ";
        }
        
        return '<object classid="clsid:612AB921-E294-41AA-8E98-87E7E057EF33" width="' + width + '" height="' + height + '" type="application/vnd.wolfram.mathematica"><param name="src" value="' + src + '">' + paramTags + '<embed width="' + width + '" height="' + height + '" src="' + src + '" type="application/vnd.wolfram.mathematica" ' + embedExtras + '></object>';
    },
        
    '_getDefaultContent': function(width,height) {
        //returns the html for the default content, wrapped in a div with the appropriate width/height
        return '<div style="width:' + width + 'px; height:' + height + 'px; ">' + this.default_content + '</div>';
    }
}; })( jQuery );

