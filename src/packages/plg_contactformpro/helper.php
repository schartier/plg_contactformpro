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

/**
 * Helper class for ContactFormPro
 *
 * @author Sebastien Chartier
 */
class CFPHelper {

    private static $_params = null;

    public static function getParams(){
        if(self::$_params == null){
            $plugin = JPluginHelper::getPlugin('system', 'contactformpro');
            self::$_params = new JRegistry();
            self::$_params->loadString($plugin->params); //new JObject(json_decode($plugin->params));
        }

        return self::$_params;
    }

    static function displayCaptcha($name, $id, $class) {

        $params = self::getParams();

        if($params->get('captcha_system', false)){

            $captcha    = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

            if ($captcha === 0 || $captcha === '0' || $captcha === '' || $captcha === null)
            {
                return '';
            }

            if (($captcha = JCaptcha::getInstance($captcha, array('namespace' => $name))) == null)
            {
                return '';
            }

            return $captcha->display($name, $id, $class);

        }else{
            return self::_displayMathGuard();
        }
    }

    static function validateCaptcha() {
        $params = self::getParams();

        if($params->get('captcha_system', false)){
            $captcha    = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

            if ($captcha === 0 || $captcha === '0' || $captcha === '' || $captcha === null)
            {
                return '';
            }

            if (($captcha = JCaptcha::getInstance($captcha, array('namespace' => $name))) == null)
            {
                return '';
            }

            return $captcha->checkAnswer('');
        }else{
            return self::_validateMathGuard();
        }
    }

    private static function _displayMathGuard() {
        require_once(dirname(__FILE__) . DS . 'captcha' . DS . 'mathGuard.class.php');

        $attributes = array();
        $attributes['title'] = JText::_('PLG_SYSTEM_CONTACTFORMPRO_CAPTCHA_REQUIRE');
        $attributes['id']
            = $attributes['name']
            = 'captcha_code' . rand(1, 99999);

        $html = '';
        $html .= '<label class="mathguard-label" for="'.$attributes['name'].'">'
                . JText::_("PLG_CONTACTFORMPRO_MATHGUARD_SECURITY_QUESTION") . ' : </label> ';
        $html .= mathGuardImproved::returnQuestion($attributes);

        return $html;
    }

    private static function _validateMathGuard() {
        require_once(dirname(__FILE__) . DS . 'captcha' . DS . 'mathGuard.class.php');

        foreach(JRequest::get('POST') as $key => $value){
            if(preg_match('/^captcha_code.*/', $key)){
                return mathGuardImproved::checkResult($key, $value);
            }
        }

        return false;
    }

    /**
     * Evaluate the css dimension from user entry.
     *
     * @param string $dimension Can be auto, px or %
     * @return String  A valid css representation of the dimension
     * (ie px is appended if missing).
     */
    public static function fixCssDimension($dimension){
        return $dimension ?
                    preg_replace('/\d+$/', '$0px', $dimension): false;
    }

    /**
     * Sends email for ContactFormPro.
     *
     * Message information is extract from Post vars.
     *
     * @return object
     */
    static function sendmail() {
        jimport('joomla.mail.helper');

        $params = self::getParams();

        $response->status = 1001;
        $response->message = "";

        if (JRequest::getString("error_message"))
            $response->message .= html_entity_decode(urldecode(JRequest::getString("error_message")));
        else
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_GENERIC_ERROR') . '</p>';


        if (!JRequest::checkToken()) {
            $respons->status = 9999;
            $response->message .= '<p>' . JText::_('JINVALID_TOKEN') . '</p>';
        }

        // Check for a valid session cookie
        if($params->get('validate_session', 0)) {
                if(JFactory::getSession()->getState() != 'active'){
                        $respons->status = 9999;
                        $response->message .= '<p>' . JText::_('JINVALID_TOKEN') . '</p>';
                }
        }

        $debug = JRequest::getVar('debug');

        $mailto = JRequest::getVar('mailto');

        if ($mailto) {
            $mailto = base64_decode($mailto);
            $mailto = explode(';', $mailto);
        }else{
            $response->status = 1101;
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_MAILTO_MISSING') . '</p>';
        }

        $sender_email = JRequest::getVar('sender_email');
        if (!$sender_email || !JMailHelper::isEmailAddress($sender_email)) {
            $response->status = 1201;
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_SENDER_EMAIL_MISSING') . '</p>';
        }

        $message = stripslashes(JRequest::getVar('message'));
        if (!$message || $message == '') {
            $response->status = 1301;
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_MISSING') . '</p>';
        }

        $sender_name = stripslashes(JRequest::getVar('sender_name'));
        if (!$sender_name || $sender_name == '') {
            $response->status = 1401;
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_SENDER_NAME_MISSING') . '</p>';
        }

        $subject = stripslashes(JRequest::getVar('subject'));
        if (!$subject || $subject == '') {
            $response->status = 1501;
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_MISSING') . '</p>';
        }

        if (!CFPHelper::validateCaptcha()) {
            $response->status = 1601;
            $response->message .= '<p>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_CAPTCHA_REQUIRE') . '</p>';
        }

        if ($response->status > 1001)
            return $response;

        $encoding = JRequest::getVar('encoding');
        $encoding || ($encoding = "UTF-8");

        // header injection test
        // An array of e-mail headers we do not want to allow as input

        $headers = array('Content-Type:',
            'MIME-Version:',
            'Content-Transfer-Encoding:',
            'bcc:',
            'cc:');

        // An array of the input fields to scan for injected headers

        $fields = array('mailto',
            'sender_name',
            'sender_email',
            'subject',
        );

        // iterate over variables and search for headers

        foreach ($fields as $field) {

            foreach ($headers as $header) {

                if (strpos(JRequest::getVar($field), $header) !== false) {

                    JError::raiseError(403, '');
                }
            }
        }

        unset($headers, $fields);

        $emailSubject = sprintf(JText::_('PLG_SYSTEM_CONTACTFORMPRO_EMAIL_SUBJECT'), $sender_name);

        // add header
        $emailBody = '
            <p><b>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_LABEL') . '</b>: ' . JMailHelper::cleanBody($subject) . '</p>
            <p></p>
            <p><b>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_LABEL') . ' : </b></p>
            <p>' . JMailHelper::cleanBody(nl2br($message)) . '</p>
            <p></p>
            <p>' . $sender_name . '
                <br />' . $sender_email . '</p>
            <p></p>
            <p></p>
            <p><small>' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_GENERATED_BY') .
                $_SERVER['HTTP_REFERER'] . '</small></p>';

        $emailBody = mb_convert_encoding($emailBody, 'HTML-ENTITIES', $encoding);

        $adminemail = $params->get('fixedaddress');
        if($adminemail){
            $bcc = explode(';', $adminemail);
        }else{
            $bcc = array();
        }

        if(JRequest::getBool('receive_copy')){
            $bcc[] = $sender_email;
        }

        $error_info = CFPHelper::_send_email($sender_name, $sender_email, $mailto, $emailSubject, $emailBody, $bcc, true);

        if ($error_info == '') {
            $response->status = 1;
            if (JRequest::getString("success_message"))
                $response->message = html_entity_decode(urldecode(JRequest::getString("success_message")));
            else
                $response->message = JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUCCESS');
        } else {
            $response->status = 1501;
            if (JRequest::getString("error_message"))
                $response->message = html_entity_decode(urldecode(JRequest::getString("success_message")));
            else if($debug)
                $response->message = $error_info;
            else
                $response->message = JText::_('PLG_SYSTEM_CONTACTFORMPRO_GENERIC_ERROR');
        }
        return $response;
    }

    /**
     * Internal function to send email
     *
     * @param String $sender_name
     * @param String $sender_email
     * @param String $recipient
     * @param String $subject
     * @param String $body
     * @param bool $isHTML
     *
     * @return string   Returns the error message, if any!
     */
    private static function _send_email($sender_name, $sender_email, $recipient, $subject, $body, $bcc, $isHTML=false) {

        $app = JFactory::getApplication();

        $sender = array(
            $app->getCfg('config.mailfrom'),
            $app->getCfg('config.fromname')
        );

        $replyto = array(
            $sender_email,
            $sender_name
        );

        // set mail data
        $mailer = JFactory::getMailer();

        $mailer->addReplyTo($replyto);

        $mailer->addRecipient($recipient);

        $mailer->addBCC($bcc);

        $mailer->setSender($sender);

        $mailer->isHTML($isHTML);

        $mailer->setSubject($subject);

        $mailer->setBody($body);

        // send email
        ob_start();
        $send = & $mailer->Send();
        $error = ob_get_clean();
        ob_end_clean();

        return $error;
    }

    public static function renderPage($content=""){
        include_once "head.php";
        $document = JFactory::getDocument();
        $rendererHead = new JDocumentRendererHead($document);
        ?>
        <html>
            <head>
              <?php echo $rendererHead->fetchHead($document); ?>
            </head>
            <body>
             <?php echo $content; ?>
            </body>
        </html>
        <?php
    }
}
