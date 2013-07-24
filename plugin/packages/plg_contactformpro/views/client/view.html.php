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
include_once( implode(DS, array(dirname(__FILE__), '..', '..', 'helper.php')));

/**
 * HTML View class for the ContactFormProForm
 * Used by the ContactFormPro Editor Button
 *
 * @package	Joomla.Plugin
 * @subpackage	plg_contactformpro
 * @since 1.0
 */
class CFPViewClientHtml {
    /* @var params JObject */

    /**
     *
     * @var JRegistry
     */
    protected $params;

    /**
     * @param $params JRegistry
     *
     * @return CFPViewClientHtml
     */
    public function CFPViewClientHtml($params) {
        $this->params = new JRegistry();
        $this->params->merge($params);
    }

    /**
     * @return String
     */
    function display($params, $return = false) {
        /*echo '<p>Before</p><pre>';
        print_r($params);
        echo '</pre>';*/
        $this->params->merge($params);/*
        echo '<p>After</p><pre>';
        print_r($this->params);
        echo '</pre>';*/

        $tmpl = $this->params->get('display', 'form');
        $this->params->set('style', ($this->params->get('style') &&
            $this->params->get('style') != 'none' &&
            $this->params->get('style') != -1) ?
                    $this->params->get('style') : false);

        $hiddenFields = array();

        if ($tmpl != "popup") {
            $mailto = $this->params->get('mailto');

            if (!$mailto || !strlen($mailto)) {
                $this->params->set('message', JText::_('PLG_SYSTEM_CONTACTFORMPRO_MAILTO_MISSING'));
                $this->params->set('label', 'WARNING: contactformpro tag must have a mailto attribute');
            }

            if ($this->params->get('error_message'))
                $hiddenFields['error_message'] = $this->params->get('error_message');
            if ($this->params->get('success_message'))
                $hiddenFields['success_message'] = $this->params->get('success_message');

            if (!self::validateKey($this->params->get('appkey'))) {
                $this->params->set('copy', '<p style="text-align:center;font-size:80% !important;">ContactForm plugin by <a href="http://joomfever.com" class="cfp-copy" title="ContactForm plugin for Joomla!">uuebdesign.com</a></p>');
            }
        }else {  //$tmpl == "popup"
            $this->params->set('label', html_entity_decode($this->params->get('label', JText::_('PLG_SYSTEM_CONTACTFORMPRO_TEXT_FOR_POPUP'))));
        }

        $user = JFactory::getUser();
        if ($user->id && $tmpl != 'popup') {
            $this->params->set('sender_name', $user->name);
            $this->params->set('sender_email', $user->email);
        }

        if ($tmpl != 'modal') {
            $id = $this->params->get('id', 'cfpForm_' . rand(1, 99999));
            $this->params->set("id", $id);

            if ($tmpl == 'popup')
                $this->params->set("display", "modal");

            $json = $this->params->get('id') . '=' . json_encode($this->params->toArray());

            $document = JFactory::getDocument();
            $document->addScriptDeclaration($json);

			JHtml::_('behaviour.framework');

            if ($style = $this->params->get('style')) {
                $document->addStyleSheet(CFP_URI . 'styles/icons.css');
                $document->addStyleSheet(CFP_URI . 'styles/blank.css');
                if(is_dir(implode(DS, array(CFP_PATH, 'styles', $style))))
                    $document->addStyleSheet(CFP_URI . '/styles/' . $style . '/style.css');
                else if(is_dir(implode(DS, array(JF_PREMIUM_PATH, 'contactformpro', $style))))
                    $document->addStyleSheet(JF_PREMIUM_URI . 'contactformpro/' . $style . '/style.css');
            }

        }


        $footer = defined('JF_PREMIUM_PATH')
            ? ''
            : '<p class="jf-copy" style="display:block !important;position:absolute;bottom:0;left:0;background: none #ffffff;margin:0;padding:0;"><small><a href="http://joomfever.com/extensions-joomla/viewcategory/24-banner-rotator">Joomla! Banner Rotator</a></small></p>';

        $tmplPath = 'tmpl' . DS . $tmpl . '.php';
        if( !file_exists(dirname(__FILE__) . DS . $tmplPath) )
            $tmplPath = 'tmpl' . DS . 'form.php';

        if($return)
            ob_start();

            include $tmplPath;

        if($return)
            $content = ob_get_length() ? ob_get_clean() : '';

        return $content;
    }

    /**
     * @return bool
     */
    private static function validateKey($key) {
        if (strlen($key) == 12) {
            $total = 0;
            for ($i = 0; $i < strlen($key); $i++) {
                $c = $key[$i];

                if (is_numeric($c)) {
                    $total += $c;
                }
            }

            return ($total % 13) == 8;
        }

        return false;
    }

}
