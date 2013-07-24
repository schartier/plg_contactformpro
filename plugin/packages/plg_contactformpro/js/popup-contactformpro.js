/**
 * ------------------------------------------------------------------------
 * Plugin ContactFormPro for Joomla! 1.7 - 2.5
 * ------------------------------------------------------------------------
 * @copyright   Copyright (C) 2011-2012 joomfever.com - All Rights Reserved.
 * @license     GNU/GPL, http://www.gnu.org/copyleft/gpl.html
 * @author:     Sebastien Chartier
 * @link:     http://www.joomfever.com
 * ------------------------------------------------------------------------
 *
 * @package	Joomla.Plugin
 * @subpackage  ContactFormPro
 * @version     1.12
 * @since	1.7
 */

(function() {
    var CFPManager = this.CFPManager = {

        _getUriObject: function(u){
		var bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		return (bits)
			? bits.associate(['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'])
			: null;
	},
        initialize: function()
        {
            o = this._getUriObject(window.self.location.href);
            q = new Hash(this._getQueryObject(o.query));
            this.editor = decodeURIComponent(q.get('e_name'));

            // Fields list
            this.fields = new Array(
                "mailto",
                "sender_name",
                "subject",
                "title",
                "label",
                "error_message",
                "success_message",
                "display",
                "style");

        },

        onok: function()
        {
            mailto = document.id('mailto').get('value');
            if(!document.formvalidator.validate(document.id('mailto')))
            {
                alert(MAILTO_REQUIRED);
                return false;
            }
            document.id('mailto').set('value', mailto.replace('@', '#'));

            extra = '';

            replace = {'"': '&quot;', '<':'&lt;', '>':'&gt;', '\'' : '&#39;', '&' : '&amp;'};

            // build the tag
            for( i = 0; i< this.fields.length; i++ ){
                f = document.id(this.fields[i]).get('value');
                if(this._isset(f)){
                    for(key in replace)
                        f = f.replace(new RegExp(key, "g"), replace[key]);

                    extra += ' ' + this.fields[i] + '="' + f + '"';
                }
            }

            tag = '{contactformpro ' + extra + ' /}';

            window.parent.jInsertEditorText(tag, this.editor);
            return true;
        },

        _isset: function(x)
        {
            return x != undefined && x != '';
        },

        showMessage: function(text)
        {
            var message  = document.id('message');
            var messages = document.id('messages');

            if(message.firstChild)
                message.removeChild(message.firstChild);

            message.appendChild(document.createTextNode(text));
            messages.style.display = "block";
        },

        parseQuery: function(query)
        {
            var params = new Object();
            if (!query) {
                return params;
            }
            var pairs = query.split(/[;&]/);
            for ( var i = 0; i < pairs.length; i++ )
            {
                var KeyVal = pairs[i].split('=');
                if ( ! KeyVal || KeyVal.length != 2 ) {
                    continue;
                }
                var key = unescape( KeyVal[0] );
                var val = unescape( KeyVal[1] ).replace(/\+ /g, ' ');
                params[key] = val;
            }
            return params;
        },
	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.each(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	}
    };
})(document.id);

window.addEvent('domready', function(){
    CFPManager.initialize();
});
