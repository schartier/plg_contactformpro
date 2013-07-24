<?php

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
// No direct access
defined('_JEXEC') or die;

jimport('joomla.form.form');

/**
 * HTML View class for the ContactFormProForm
 * Used by the ContactFormPro Editor Button
 *
 * @package	Joomla.Plugin
 * @subpackage	plg_contactformpro
 * @since 1.0
 */
class CFPViewFormHtml {

    protected $params;

    public function CFPViewFormHtml($params) {
        $this->params = $params;

        JHTML::_('behavior.framework');
        JHTML::_('behavior.formvalidation');

        $document =& JFactory::getDocument();
        $document->addScript(CFP_URI . 'js/popup-contactformpro.js');
        $document->addStyleSheet(CFP_URI . 'css/popup-contactformpro.css');

        $options = array(
            'titleSelector' => 'dt.tabs',
            'descriptionSelector' => 'dd.tabs'
        );

        $document->addScriptDeclaration(
            "var MAILTO_REQUIRED = '" . str_replace('\'', '\\\'', JText::_('PLG_SYSTEM_CONTACTFORMPRO_MAILTO_MISSING')) ."';"
        );
    }

    function display($tmpl='default', $return = false) {
        //JHTMLBehavior::formvalidation();
        $form = & JForm::getInstance('plg_contactformpro.form', dirname(__FILE__) . '/../../forms/form.xml');

        if (!($form instanceof JForm)) {
            $this->_subject->setError('JERROR_NOT_A_FORM');
            return false;
        }

        $dispatcher = JDispatcher::getInstance();

        // CHANGE FOR A COPY OF POST + CUSTOM FIELDS
        // Trigger the form preparation event.
        $dispatcher->trigger('onContentPrepareForm', array($form, $this->params));
        // Trigger the data preparation event.
        $dispatcher->trigger('onContentPrepareData', array('plg_contactformpro.form', $this->params));

        // Load the data into the form after the plugins have operated.
        $form->bind($this->params);
        $config = JFactory::getConfig();

        $site_url = $config->get('site_url');
        if (trim($site_url) == '') {
            $uri = JURI::getInstance();
            $site_url = $uri->toString(array('scheme', 'host', 'port'));
        }

        $footer = defined('JF_PREMIUM_PATH')
            ? ''
            : '<p class="jf-copy" style="display:block !important;position:absolute;bottom:0;right:0;background: none #ffffff;margin:0;padding:0;"><small><a href="http://joomfever.com/extensions-joomla/viewcategory/24-banner-rotator">Joomla! Banner Rotator</a></small></p>';

        if($return)
            ob_start();

        include 'tmpl' . DS . $tmpl . '.php';

        if($return)
            return ob_get_clean();
    }

}
