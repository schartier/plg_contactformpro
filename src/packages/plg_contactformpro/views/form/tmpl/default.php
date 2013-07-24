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

/*
 * <?php echo JHtml::_('tabs.start', 'com_mycomponent_tabs',
  array('useCookie'=>true));?>
  <?php echo JHtml::_('tabs.panel', JText::_('com_mycomponent_tab1'),
  'tab1');?>
  some content 1
  <?php echo JHtml::_('tabs.panel', JText::_('com_mycomponent_tab2'),
  'tab2');?>
  some content 2
  ...
  <?php echo JHtml::_('tabs.end');?>
 *
 */
?>
<p style="text-align:right;"><a href="http://joomfever.com/index.php/extensions-joomla/contact-forms/themes-for-contactformpro" target='_blank' title="Get new designs">Get NEW DESIGNS</a></p>
<form action="#" id="ContactFormProForm">
    <?php echo JHtml::_('tabs.start', 'btnContactformpro', array('useCookie' => false)); ?>
    <?php foreach ($form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.  ?>
        <?php echo JHtml::_('tabs.panel', JText::_($fieldset->label), $fieldset->name); ?>
        <?php $fields = $form->getFieldset($fieldset->name); ?>
        <?php if (count($fields)): ?>
            <fieldset id="<?php echo $fieldset->name; ?>">
                <?php foreach ($fields as $field):
                    // Iterate through the fields in the set and display them.  ?>
                    <?php if ($field->hidden):
                        // If the field is hidden, just display the input.?>
                        <?php echo $field->input; ?>
                    <?php else: ?>
                        <dt>
                        <?php echo $field->label; ?>
                        </dt>
                        <dd><?php echo $field->input; ?></dd>
                    <?php endif; ?>
                <?php endforeach; ?>
            </fieldset>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php echo JHtml::_('tabs.end'); ?>
    <div>
        <?php echo JHtml::_('form.token'); ?>
        <p id="buttons">
            <button type="button" onclick="if(CFPManager.onok())window.parent.SqueezeBox.close();"><?php echo JText::_('PLG_SYSTEM_CONTACTFORMPRO_INSERT') ?></button>
            <button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
        </p>
    </div>
</form>