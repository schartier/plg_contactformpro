<?php

class plgSystemJoomfeverInstallerScript {
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) {
        $database = JFactory::getDBO();

        $query = "UPDATE #__extensions SET enabled=1 WHERE name='plg_system_joomfever'";
        $database->setQuery($query);

        if ($database->query())
            echo '<p>' . JText::_('ContactFormPro system plugin enabled succesfully') . '</p>';
        else
            echo '<p>' . JText::_('Enable ContactFormPro in the plugins manager') . '</p>';
    }
}