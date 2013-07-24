<?php

class plgSystemContactformproInstallerScript {

    private $YOUR_KEY = 's15l5i12497u';

    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent) {
        $database = JFactory::getDBO();

        $query = "UPDATE #__extensions SET enabled=1 WHERE name='plg_system_contactformpro'";
        $database->setQuery($query);

        if ($database->query())
            echo '<p>' . JText::_('ContactFormPro system plugin enabled succesfully') . '</p>';
        else
            echo '<p>' . JText::_('Enable ContactFormPro in the plugins manager') . '</p>';

        $query = "SELECT params from #__extensions WHERE name='plg_system_contactformpro'";
        $database->setQuery($query);

        $params = $database->loadResult();
        if ($params) {
            $params = json_decode($params);
            $params->appkey = $this->YOUR_KEY;
        } else {
            $params = array('appkey' => $this->YOUR_KEY);
        }

        $query = "UPDATE #__extensions SET params='" . json_encode($params) . "' WHERE name='plg_system_contactformpro'";
        $database->setQuery($query);

        if ($database->query())
            echo '<p>' . JText::_('Plugin registered succesfully') . '</p>';
        else
            echo '<p>' . JText::_('There was a problem with registration') . '</p>';
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) {
        // $parent is the class calling this method
        //echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent) {
        // $parent is the class calling this method
        //echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //echo '<p>' . JText::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
    }

}