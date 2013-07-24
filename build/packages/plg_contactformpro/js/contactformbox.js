/**
 * ------------------------------------------------------------------------
 * Plugin ContactFormPro for Joomla! 1.7 - 2.5
 * ------------------------------------------------------------------------
 * @copyright   Copyright (C) 2011-2012 joomfever.com - All Rights Reserved.
 * @license     GNU/GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @author:     Sebastien Chartier
 * @link:       http://www.joomfever.com
 * ------------------------------------------------------------------------
 *
 * @package	Joomla.Plugin
 * @subpackage  ContactFormPro
 * @version     1.12 (February 20, 2012)
 * @since	1.7
 */

var ContactFormBox;

(function($) {

    var statuses = {
        opening: 'opening',
        opened: 'opened',
        closed: 'closed'
    };

    // ContactFormBox specific vars
    // (see defaultOptions for available options...)
    var mediaWidth,
    mediaHeight,
    options;

    // Global variables, accessible to ContactFormBox only
    var defaultOptions,
    top,
    mTop,
    left,
    mLeft,
    fx,
    style,
    status;

    // DOM Elements
    var overlay,
    center,
    container,
    bottom,
    media,
    preload,
    closeLink,
    mainForm;

    /*	Initialization	*/
    window.addEvent("domready", function() {

        status = statuses.closed;
        defaultOptions = {
            style: 'light',
            close_button: '&times;',
            keyboard: true,					// Enables keyboard control; escape key, left arrow, and right arrow
            keyboardAlpha: false,			// Adds 'x', 'c', 'p', and 'n' when keyboard control is also set to true
            keyboardStop: false,			// Stops all default keyboard actions while overlay is open (such as up/down arrows)
            // Does not apply to iFrame content, does not affect mouse scrolling
            overlayOpacity: 0.8,			// 1 is opaque, 0 is completely transparent (change the color in the CSS file)
            resizeOpening: true,			// Determines if box opens small and grows (true) or starts at larger size (false)
            resizeDuration: 240,			// Duration of each of the box resize animations (in milliseconds)
            initialWidth: 340,				// Initial width of the box (in pixels)
            initialHeight: 300,
            sending_message: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_SENDING_MSG', 'The message is being sent'), //'Sending message',
            correct_errors: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_CORRECT_ERRORS', 'Please correct errors'), //'Please correct error(s)',
            go_back: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_BACK_LINK', 'Back'),
            success_message: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_SUCCESS', 'The message has been sent successfully'),
            error_message: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_GENERIC_ERROR', 'There was an error when sending message'),
            sender_email_invalid: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_SENDER_EMAIL_INVALID', 'Please enter a valid email address'), //'Please correct error(s)',
            sender_name_missing: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_SENDER_NAME_MISSING', 'Please enter your name'),
            message_missing: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_MISSING', 'Please enter a subject'),
            subject_missing: Joomla.JText._('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_MISSING', 'Please enter a message')

        };

        // Create and append the ContactFormBox HTML code at the bottom of the document
        $(document.body).adopt(
            $$([
                overlay = new Element("div", {
                    id: "cfpOverlay",
                    styles: {
                        position: 'fixed',
                        top: '0',
                        left: '0',
                        'z-index': '9998',
                        cursor: 'pointer'
                    }
                }).addEvent("click", close),
                center = new Element("div", {
                    id: "cfpCenter"
                })
                ]).setStyle("display", "none")
            );


        container = new Element("div", {
            "class": "inner2"
        }).inject(
            new Element("div", {
                "class": "inner1"
            }).inject(center));
        media = new Element("div", {
            id: "cfpMedia"
        }).inject(
            container
            );
        bottom = new Element("div", {
            id: "cfpBottom"
        }).inject(container).adopt(
            closeLink = new Element("a", {
                id: "cfpCloseLink",
                href: "#"
            }).addEvent("click", close)
            );

        fx = {
            overlay: new Fx.Tween(overlay, {
                property: "opacity",
                duration: 360
            }).set(0),
            media: new Fx.Tween(media, {
                property: "opacity",
                duration: 360
            })
        };

        if (Browser.Platform.ios) {
            defaultOptions.keyboard = false;
            defaultOptions.resizeOpening = false;	// Speeds up interaction on small devices (mobile) or older computers (IE6)
            overlay.className = 'cfpMobile';
            bottom.className = 'cfpMobile';
        }

        if (Browser.name == 'ie' && Browser.version < 9) {
            defaultOptions.resizeOpening = false;	// Speeds up interaction on small devices (mobile) or older computers (IE6)
            overlay.className = 'cfpOverlayAbsolute';
            center.addClass('ie8');
        }
    });

    /*	API		*/

    ContactFormBox = {
        ajax: function(_url, _options) {
            setOptions(_options);
            overlay.addClass('cfpLoading');

            if(status == statuses.closed){
                status = statuses.opening;
                center.setStyles({'height': options.initialHeight, 'width': options.initialWidth});
                //document.getWidth() = document.body.offsetWidth; //window.getWidth();
                //document.getHeight() = document.body.offsetHeight; //window.getHeight();
                overlay.setStyles({
                    width: document.getWidth(),
                    height: document.getHeight()
                });

                setup(true);

                fx.overlay.start(options.overlayOpacity);
            }
            newEl
            = mainForm
            = new Element('div').setStyle('display', 'none');
            $(document.body).adopt(newEl);
            new Request.HTML({
                url: _url,
                data: options,
                onSuccess: function(responseTree){
                    newEl.adopt(responseTree);
                    overlay.removeClass('cfpLoading');
                    ContactFormBox.open(newEl, false);
                }
            }).send();
        },

        close: function(){
            close();	// Thanks to Yosha on the google group for fixing the close function API!
        },

        recenter: function(){	// Thanks to Garo Hussenjian (Xapnet Productions http://www.xapnet.com) for suggesting this addition
            if (center && !Browser.Platform.ios) {
                left = window.getScrollLeft() + (window.getWidth()/2);
                center.setStyles({
                    left: left,
                    marginLeft: -(mediaWidth/2)
                });
            }
        },

        open: function(_media, _options) {
            setOptions(_options);
            if(status == statuses.opened)
                return this.changeMedia(_media, false);

            //document.getWidth() = document.body.offsetWidth; //window.getWidth();
            //document.getHeight() = document.body.offsetHeight; //window.getHeight();
            overlay.setStyles({
                width: document.getWidth(),
                height: document.getHeight()
            });

            setup(true);

            top = window.getScrollTop() + (window.getHeight()/2);
            left = window.getScrollLeft() + (window.getWidth()/2);

            center.setStyles({
                top: top,
                left: left,
                width: container.offsetWidth,
                height: container.offsetHeight,
                marginTop: -(options.initialWidth/2),
                marginLeft: -(options.initialHeight/2),
                display: ""
            });

            fx.resize = new Fx.Morph(center, {
                duration: options.resizeDuration,
                onComplete: mediaAnimate
            });
            fx.overlay.start(options.overlayOpacity);

            status = statuses.opened;

            return this.changeMedia(_media, false);
        },

        changeMedia: function(_media, _options) {
            stop();

            setOptions(_options);

            center.addClass('cfpLoading');
            if(typeof preload != 'undefined') preload.adopt(media.getChildren());	// prevents loss of adopted data
            media.set('html', '');
            /*
            mediaWidth = container.offsetWidth;

            if(typeof options.width != 'undefined'){
                if(is_int(options.width)){
                    mediaWidth = options.width;
                }else if(options.width.match("%")){
                    mediaWidth = window.getWidth() * (options.width.replace("%", "")*0.01);
                }else if(options.width.match("px")){
                    mediaWidth = parseInt( options.width.replace("px", "") );
                }
            }

            mediaHeight = null;
            if(typeof options.height != 'undefined'){
                if(is_int(options.height)){
                    mediaHeight = options.height;
                }else if(options.height.match("%")){
                    mediaHeight = window.getHeight()*(options.height.replace("%", "")*0.01);
                }else if(options.height.match("px")){
                    mediaHeight = parseInt( options.height.replace("px", "") );
                }
            }*/

            preload = _media;
            startEffect();
            return false;
        },

        sendMessage: function(_form, _options){
            var form = $(_form);

            setOptions(_options);

            var messages = [options.correct_errors],
            valid = true;

            form.getElements('.inputbox[class|=validate], .inputbox.required').each(function(el){
                // Remove all icon-* class
                if( el.getParent().className
                    && el.getParent().className.test(/icon-([a-zA-Z0-9\_\-]+)/)){
                    el.getParent()
                    .className
                    .match(/icon-([a-zA-Z0-9\_\-]+)/)
                    .each(function(singleclass){
                        el.getParent().removeClass(singleclass);
                    });
                }

                if(document.formvalidator.validate(el)){
                    el.getParent().addClass('icon-valid');
                }else{
                    valid = false;
                    el.get('title')?messages.push(el.get('title')):'';

                    el.get('value')
                    ?el.getParent().addClass('icon-invalid')
                    :el.getParent().addClass('icon-required');
                }
            });

            if (valid) {
                showMessages([options.sending_message], {
                    'class': 'cfpLoading'
                });
                new Request.JSON({
                    url: _form.get('action') + '&format=json',
                    data: _form,
                    onSuccess: function(response){
                        showMessages([response.message], {
                            success: response.status == 1
                        });
                    },
                    onError: function(text, error){
                        showMessages([error], {
                            success: false
                        });
                    }
                }).send();
            }
            else {
                alert(messages[0]);
            }

            return false;
        }


    };

    function startEffect() {
        media.adopt(preload.getChildren());

        mediaWidth = media.offsetWidth + center.getStyle('padding-left').toInt() + center.getStyle('padding-right').toInt();
        mediaHeight = media.offsetHeight + center.getStyle('padding-top').toInt() + center.getStyle('padding-bottom').toInt();

        if (mediaHeight >= window.getHeight()) {
            mTop = -(window.getHeight() / 2);
        } else {
            mTop = -(mediaHeight/2);
        }

        if (mediaWidth >= window.getWidth()) {
            mLeft = -(window.getWidth() / 2);
        } else {
            mLeft = -(mediaWidth/2);
        }

        if (options.resizeOpening) {
            fx.resize.cancel();
            fx.resize.start({
                width: mediaWidth,
                height: mediaHeight,
                marginTop: mTop,
                marginLeft: mLeft
            });
        } else {
            center.setStyles({
                width: mediaWidth,
                height: mediaHeight,
                marginTop: mTop,
                marginLeft: mLeft
            });
        }
        mediaAnimate();
    }

    /*	Internal functions	*/
    function onScroll() {

        if (options.resizeOpening) {
            fx.resize.cancel();
            fx.resize.start({
                width: mediaWidth,
                height: mediaHeight,
                marginTop: mTop,
                marginLeft: mLeft
            });
        } else {
            center.setStyles({
                width: mediaWidth,
                height: mediaHeight,
                marginTop: mTop,
                marginLeft: mLeft
            });
        }
    }

    function size() {
        //document.getWidth() = window.getWidth();
        //document.getHeight() = window.getHeight();
        overlay.setStyles({
            width: document.getWidth(),
            height: document.getHeight()
        });
    }

    function setup(open) {/*
        if (Browser.firefox) {
            ["object", window.ie ? "select" : "embed"].forEach(function(tag) {
                Array.forEach($$(tag), function(el) {
                    if (open) el._mediabox = el.style.visibility;
                    el.style.visibility = open ? "hidden" : el._mediabox;
                });
            });
        }*/

        overlay.style.display = open ? "" : "none";

        var fn = open ? "addEvent" : "removeEvent";
        /*window[fn]("scroll", ContactFormBox.onScroll);*/
        window[fn]("onresize", size);

        if (options.keyboard) document[fn]("keydown", keyDown);
    }

    function keyDown(event) {
        if (options.keyboardAlpha) {
            switch(event.code) {
                case 27:	// Esc
                case 88:	// 'x'
                case 67:	// 'c'
                    close();
                    break;
            /*                case 37:	// Left arrow
                case 80:	// 'p'
                    previous();
                    break;
                case 39:	// Right arrow
                case 78:	// 'n'
                    next();*/
            }
        } else {
            switch(event.code) {
                case 27:	// Esc
                    close();
                    break;
            /*                case 37:	// Left arrow
                    previous();
                    break;
                case 39:	// Right arrow
                    next();*/
            }
        }
    }

    function is_int(value){
        if((parseFloat(value) == parseInt(value)) && !isNaN(value)){
            return true;
        } else {
            return false;
        }
    }

    function showMessages(_messages, _options){
        var content = '<p>' + _messages.shift() + '</p>';
        if(_messages.length){
            content += '<ul>';
            _messages.each(function(error){
                content += '<li>' + error + '</li>';
            });
            content += '</ul>';
        }

        center.addClass('cfp_msg_container');

        _options.success = (typeof _options.success != 'undefined') ? _options.success:-1;

        var theClass = 'cfp_msg ' + (_options['class']?_options['class']:'');
        if(_options.success > -1){
            theClass += _options.success ? ' success':' error';
        }

        newEl = new Element('div', {
            html: '<div class="' + theClass + '"><div class="cfp_msg_content">'+content+'</div></div>',
            style: 'display: none;'
        }).inject($(document.body));

        if(mainForm!=null && !_options.success){
            newEl.grab(new Element('a', {
                html: options.go_back,
                'class': 'button',
                events: {
                    click: function(e){
                        ContactFormBox.open(mainForm, false);
                    }
                }
            }));
        }
        ContactFormBox.open(newEl, false);
    }



    function setOptions(_options){
        if(!_options || status != statuses.closed)
            return;

        options = Object.merge(Object.clone(defaultOptions), _options);

        overlay.set('class', '');
        center.set('class', 'cfp_contact_form');

        if(typeof options.style != 'undefined'){
            style = options.style;
            overlay.addClass(style);
            center.addClass(style);
        }

        closeLink.set('html', options.close_button);
    }

    function mediaAnimate() {
        overlay.removeClass('cfpLoading');
        fx.media.start(1);
    }

    function stop() {
        if (preload) {
            preload.adopt(media.getChildren());	// prevents loss of adopted data
            preload.onload = function(){}; // $empty replacement
        }
        fx.resize.cancel();
        fx.media.cancel().set(0);
    }

    function close() {
        preload.onload = function(){}; // $empty replacement
        media.empty();
        for (var f in fx) fx[f].cancel();
        center.setStyle("display", "none");
        fx.overlay.chain(setup).start(0);

        status = statuses.closed;

        return false;
    }
})(document.id);
