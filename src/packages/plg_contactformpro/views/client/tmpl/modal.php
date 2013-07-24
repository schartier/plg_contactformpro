<?php
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
defined('_JEXEC') or die;

$validate = $this->params->get('validate-on-blur', false) ? ' validate' : '';
?>
<div class="<?php echo $this->params->get('use_icons') ? 'cfp_icons' : ''; ?>">
    <div class="cfp_title"><?php echo $this->params->get('title'); ?></div>
    <?php //print_r($this->params); exit;  ?>
    <form method="post"
          action="<?php echo JURI::base() . 'index.php?contactformpro=send'; ?>"
          onSubmit="ContactFormBox.sendMessage(this, <?php echo $this->params->get('id'); ?>); return false;">
        <div class="cfp_field sender_name cfp_required">
            <div class="cfp_label">
                <label for="<?php echo $this->params->get('id'); ?>_sender_name"
                       class="hasTip"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOURNAME_LABEL') . '::' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOURNAME_DESC'); ?>">
                           <?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOURNAME_LABEL'); ?>
                </label>
            </div>
            <div class="cfp_input">
                <input class="inputbox required<?php echo $validate; ?>"
                       type="text"
                       id="<?php echo $this->params->get('id'); ?>_sender_name"
                       name="sender_name"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOURNAME_DESC'); ?>"
                       value="<?php echo htmlspecialchars($this->params->get('sender_name')); ?>"
                       maxlength="128" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <div class="cfp_field sender_email cfp_required">
            <div class="cfp_label">
                <label for="<?php echo $this->params->get('id') ?>_sender_email"
                       class="hasTip"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOUREMAIL_LABEL') . '::' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOUREMAIL_DESC'); ?>">
                           <?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOUREMAIL_LABEL'); ?>
                </label>
            </div>
            <div class="cfp_input">
                <input class="inputbox required validate-email<?php echo $validate; ?>"
                       type="text"
                       onblur="document.formvalidator.validate(this);"
                       id="<?php echo $this->params->get('id') ?>_sender_email"
                       name="sender_email"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_YOUREMAIL_DESC'); ?>"
                       maxlength="256"
                       value="<?php echo htmlspecialchars($this->params->get('sender_email', '')); ?>" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <div class="cfp_field cfp_required subject">
            <div class="cfp_label">
                <label class="hasTip"
                       for="<?php echo $this->params->get('id') ?>_subject"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_LABEL') . '::' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_DESC'); ?>">
                           <?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_LABEL'); ?>
                </label>
            </div>
            <div class="cfp_input">
                <input class="inputbox required<?php echo $validate; ?>"
                       type="text"
                       onblur="document.formvalidator.validate(this);"
                       id="<?php echo $this->params->get('id') ?>_subject"
                       name="subject"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_SUBJECT_DESC'); ?>"
                       maxlength="256"
                       value="<?php echo $this->params->get('subject', ''); ?>" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <div class="cfp_field cfp_required message">
            <div class="cfp_label">
                <label class="hasTip"
                       for="<?php echo $this->params->get('id') ?>_message"
                       title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_LABEL') . '::' . JText::_('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_DESC'); ?>">
                           <?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_LABEL'); ?>
                </label>
            </div>
            <div class="cfp_input">
                <textarea id="<?php echo $this->params->get('id') ?>_message"
                          onblur="document.formvalidator.validate(this);"
                          class="inputbox required<?php echo $validate; ?>"
                          name="message"
                          title="<?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_MESSAGE_DESC'); ?>"
                          ><?php
                           echo htmlspecialchars(trim($this->params->get('message', '')));
                           ?></textarea>
            </div>
            <div style="clear:both;"></div>
        </div>
        <div class="cfp_field cfp_required captcha">
            <?php echo CFPHelper::displayCaptcha($this->params->get('id'), $this->params->get('id'), 'required'); ?>
        </div>
        <div class="cfp_field copy">
            <input type="checkbox"
                   id="<?php echo $this->params->get('id') ?>_receive_copy"
                   name="receive_copy"
                   <?php echo $this->params->get('receive_copy', '0') ? 'checked="true"' : ''; ?>
                   value="1" />
            <label for="">
                <?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_COPY_LABEL'); ?>
            </label>
        </div>
        <input type="hidden"
               id="<?php echo $this->params->get('id') ?>_encoding"
               name="encoding"
               value="<?php echo $this->params->get('encoding', 'UTF-8'); ?>" />

        <?php echo JHtml::_('form.token'); ?>

        <input type="hidden"
               id="<?php echo $this->params->get('id') ?>_task"
               name="task"
               value="send" />

        <input type="hidden"
               id="<?php echo $this->params->get('id') ?>_mailto"
               name="mailto"
               value="<?php echo base64_encode($this->params->get('mailto', '')); ?>"
               <?php echo JDEBUG ? 'class="required" title="Configuration error::There is no address to send the message to." ':''; ?> />

        <?php foreach ($hiddenFields as $field => $value) { ?>
            <input type="hidden"
                   id="<?php echo $field; ?>"
                   name="<?php echo $field; ?>"
                   value="<?php echo htmlspecialchars($value); ?>" />
        <?php } ?>

        <div class="cfp_field submit">
            <div class="cfp_submit">
                <button type="submit"
                        id="submit"
                        name="submit"
                        class="button">
                    <span><?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_SEND'); ?></span>
                </button>
            </div>
        </div>
    </form>
    <?php echo $footer; ?>
    <div style="clear:both;"></div>
</div>