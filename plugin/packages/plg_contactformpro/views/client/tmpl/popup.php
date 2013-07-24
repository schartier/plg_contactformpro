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
?>

<a href="<?php echo JRoute::_("index.php?contactformpro=client"); ?>"
    class="cfpLink"
    rel='<?php echo $id; ?>'>
    <?php echo html_entity_decode($this->params->get('label'), ENT_QUOTES); ?>
</a>