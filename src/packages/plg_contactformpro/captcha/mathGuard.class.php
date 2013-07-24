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

if (!class_exists('mathGuardImproved')) {

    abstract class mathGuardImproved extends JObject {

        /** This function checks whether the answer and generated security code match
         * @param $mathguard_answer		answer the user has entered
         * @param $mathguard_code		hashcode the mathguard has generated
         */
        public static function checkResult($key, $value) {
            $session = & JFactory::getSession();
            return ($value == $session->get($key, null));
        }

        /** this function returns math expression into your form, the parameter is optional
         * quite simmilar to insertQuestion, but returns the output as a text instead of echoing
         */
        public static function returnQuestion($attributes = array(), $suffix = '') { //default prime is 37, you can change it when specifying the different parameter
            $a = rand() % 10; // generates the random number
            $b = rand() % 10; // generates the random number

            $code = $a + $b;
            if (!is_array($attributes)) {
                $attributes = array();
            }
            if (!isset($attributes['name'])) {
                $suffix = $suffix . rand(1, 99999);
                $attributes['name'] = 'captcha_code' . $suffix;
            }
            if (!isset($attributes['id'])) {
                $attributes['id'] = $attributes['name'];
            }
            if (!isset($attributes['size'])) {
                $attributes['size'] = '2';
            }
            if (!isset($attributes['class'])) {
                $attributes['class'] = 'inputbox mathguard-answer required';
            }

            $session = & JFactory::getSession();
            $session->set($attributes['name'], $code);

            return $a . ' + ' . $b . ' = ' . '<input type="text" ' . JArrayHelper::toString($attributes) . ' />';
        }

    }

}