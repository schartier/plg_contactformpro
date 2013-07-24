<?php

/**
 * ------------------------------------------------------------------------
 * Plugin Joomfever for Joomla! 1.7 - 2.5
 * ------------------------------------------------------------------------
 * @copyright   Copyright (C) 2011-2012 joomfever.com - All Rights Reserved.
 * @license     GNU/GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @author:     Sebastien Chartier
 * @link:       http://www.joomfever.com
 * ------------------------------------------------------------------------
 *
 * @package	Joomla.Plugin
 * @subpackage  Joomfever
 * @version     1.12 (February 20, 2012)
 * @since	1.7
 */
// No direct access.
defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');

class plgSystemJoomfever extends JPlugin {

    public function onAfterInitialise(){
        $document = JFactory::getDocument();
        $document->addScriptDeclaration("$(window).addEvent('domready', function(){
            $$('p.jf-copy').setStyle('display', 'none');
        })");
    }

}