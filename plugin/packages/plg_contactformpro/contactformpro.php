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
// No direct access.
defined('_JEXEC') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('CFP_URI') or define('CFP_URI', JURI::base() . 'plugins/system/contactformpro/');
defined('CFP_PATH') or define('CFP_PATH', implode(DS, array(JPATH_SITE, 'plugins', 'system', 'contactformpro')));

jimport('joomla.plugin.plugin');
jimport('joomla.session.session');

class plgSystemContactformpro extends JPlugin {

    private static $_jsLoaded = false;

    function plgSystemContactformpro(& $subject, $params) {
        parent::__construct($subject, $params);
        $this->loadLanguage();

        $premiumPath = implode(DS, array(JPATH_SITE, 'plugins', 'system', 'joomfever'));
        if(is_dir($premiumPath)){
            defined('JF_PREMIUM_URI') or define('JF_PREMIUM_URI', JURI::base() . 'plugins/system/joomfever/');
            defined('JF_PREMIUM_PATH') or define('JF_PREMIUM_PATH', $premiumPath);
        }
    }

    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        if (is_object($row)) {
            return $this->_replaceTags($row->text, $params);
        }
        return $this->_replaceTags($row, $params);
    }

    public function onAfterInitialise() {
        $sview = JRequest::getString('contactformpro');

        if ($sview) {
            include_once "helper.php";

            $lang = JFactory::getLanguage();
            $lang->load('plg_system_contactformpro', JPATH_BASE . DS . 'administrator');

            $sformat = JRequest::getString('format', 'html');
            $fview = dirname(__FILE__) . '/views/' . $sview . '/view.' . $sformat . '.php';

            if ($sview && file_exists($fview)) {
                include_once $fview;

                $cname = 'CFPView' . ucfirst($sview) . ucfirst($sformat);

                $this->params->loadArray(JRequest::get('default', 4));
                $view = new $cname($this->params);

                $content = $view->display(JRequest::getString('display', 'default'), 1);

                $application = JFactory::getApplication();

                CFPHelper::renderPage($content);
                $application->close();

            } else if ($sformat == 'json') {
                $response->status = 0;
                $response->message = JText::_('PLG_SYSTEM_CONTACTFORMPRO_GENERIC_ERROR');
                echo json_encode($response);
                exit;
            }
        }
    }

    private function _loadJs(){
        if(!self::$_jsLoaded){
            // Perform javascript initialisation
            JHtml::_('behavior.framework');
            JHtml::_('behavior.formvalidation');
            JHtml::_('behavior.tooltip');

            $document = JFactory::getDocument();
            $document->addScript(CFP_URI . 'js/contactformbox.js');

            JText::script('PLG_SYSTEM_CONTACTFORMPRO_SENDER_EMAIL_INVALID');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_MISSING');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_MISSING');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_SENDER_NAME_MISSING');
            //JText::script('PLG_SYSTEM_CONTACTFORMPRO_CAPTCHA_REQUIRE');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_CORRECT_ERRORS');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_SUCCESS');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_SENDING_MSG');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_BACK_LINK');
            //JText::script('PLG_SYSTEM_CONTACTFORMPRO_MAILTO_MISSING');
            JText::script('PLG_SYSTEM_CONTACTFORMPRO_GENERIC_ERROR');

            $document->addScriptDeclaration("
                window.addEvent('domready', function(){
                    $$('a.cfpLink').each(function(el){
                        el.addEvent('click', function(evnt){
                            evnt.stop();
                            ContactFormBox.ajax(this.get('href'), window[this.get('rel')]);
                        });
                    });

                    if(Browser.name == 'ie' && Browser.version < 9){
                        $$('div.cfp_contact_form').each(function(el){
                            el.addClass('ie8');
                        });
                    }

                    if(typeof JTooltips == 'undefined'){
                        var JTooltips = new Tips($$('.hasTip'),
                        { maxTitleChars: 50, fixed: false});
                    }
                });
            ");

            self::$_jsLoaded = true;
        }

        return self::$_jsLoaded;
    }

    protected function _replaceTags(&$text, &$params) {
        /*
         * Check for presence of {contactformpro=off} which is explicits disables this
         * bot for the item.
         */
        if (JString::strpos($text, '{contactformpro=off}') !== false) {
            $text = JString::str_ireplace('{contactformpro=off}', '', $text);
            return true;
        }

        // Simple performance check to determine whether bot should process further.
        if (JString::strpos($text, '{contactformpro ') === false) {
            return true;
        }

        // We can now include required javascript
        $this->_loadJs();

        /** @todo try to autocorrect user input (ex. formatting tags) **/
        $pattern = '/{contactformpro\s([^\}]*)(?|(?:\/\})|(?:\}((?:(?!\{\/contactformpro\}).)*)\{\/contactformpro\}))/i';
        while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
            $formparams = new JRegistry();
            $formparams->loadArray(JUtility::parseAttributes(html_entity_decode($regs[1][0])));

            foreach ($formparams->toArray() as $key => $value)
                $formparams->set($key, html_entity_decode($value));

            if ($formparams->get('mailto'))
                $formparams->set('mailto', str_replace('#', '@', $formparams->get('mailto')));

            if($formparams->get('display', false) == 'form')
                $formparams->set('display', 'default');

            if (isset($regs[2][0]))
                $formparams->set('message', $regs[2][0]);

            $replacement = $this->showContactForm($formparams, true);

            // Replace the found address with the contact form
            $text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
        }

        return true;
    }

    /**
     *
     * @param JRegistry      $params
     * @param boolean      $ret
     * @return string   The contact from
     */
    private function showContactForm($params, $return = true) {

        include_once implode(DS, array(dirname(__FILE__), 'views', 'client', 'view.html.php'));
        $view = new CFPViewClientHtml($this->params);

        return $view->display($params, $return);
    }

}