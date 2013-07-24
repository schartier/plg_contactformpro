<?php
/**
 * ------------------------------------------------------------------------
 * Editor Button for ContactFormPro - Joomla! 1.7 - 2.5
 * ------------------------------------------------------------------------
 * @copyright   Copyright (C) 2011-2012 Chartiermedia.com - All Rights Reserved.
 * @license     GNU/GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @author:     Sebastien Chartier
 * @link:       http://www.chartiermedia.com
 * ------------------------------------------------------------------------
 *
 * @package	Joomla.Plugin
 * @subpackage  plg_editors-xtd_contactformpro
 * @version     1.00 (February 20, 2012)
 * @since	1.7
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Editor ContactFormPro button
 *
 * @package	Joomla.Plugin
 * @subpackage	plg_editors-xtd_contactformpro
 * @since       1.7
 */
class plgButtonContactformpro extends JPlugin {

    /**
     * Constructor
     *
     * @access      protected
     * @param       object  $subject The object to observe
     * @param       array   $config  An array that holds the plugin configuration
     * @since       1.5
     */
    public function __construct(& $subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Display the button
     *
     * @return array A four element array of (article_id, article_title, category_id, object)
     */
    function onDisplay($name) {
        $user = JFactory::getUser();
        JHtml::_('behavior.modal');

            //global $mainframe;
            $mainframe =& JFactory::getApplication();
            if($mainframe->isAdmin())
                $link = '../';

            $link .= 'index.php?contactformpro=form&amp;' . JSession::getFormToken() . '=1&amp;e_name=' . $name;

            $base_url = JURI::base();
            $app = JFactory::getApplication();
            if($app->isAdmin())
                $base_url = dirname($base_url);

            $editor_form = $name . "_ifr";

            $js = $jaJS = "
		function isBrowserIEJA() {
			return navigator.appName==\"Microsoft Internet Explorer\";
		}

		function IeCursorFixJA() {
			if (isBrowserIEJA()) {
				tinyMCE.execCommand('mceInsertContent', false, '');
				global_ie_bookmark = tinyMCE.activeEditor.selection.getBookmark(false);
			}
			return true;
		}";

            $document = JFactory::getDocument();
            $document->addScriptDeclaration($js);
            $document->addStyleSheet($base_url . "/plugins/editors-xtd/contactformpro/style.css");

            $button = new JObject();
            $button->set('onclick', 'IeCursorFixJA();  return false;');
            $button->set('modal', true);
            $button->set('link', $link);
            $button->set('text', JText::_('PLG_EDITORS-XTD_CONTACTFORMPRO_BUTTON'));
            $button->set('name', 'contactformpro');
            $button->set('options', "{handler: 'iframe', size: {x: 450, y: 500}}");

            return $button;
    }

}
