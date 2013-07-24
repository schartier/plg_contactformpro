<?php

class plgEditorsxtdContactformproInstallerScript {

   function install($parent) {
       $database = JFactory::getDBO();

       $database->setQuery("UPDATE #__extensions SET enabled=1 WHERE name='plg_editors-xtd_contactformpro'");

       if($database->query())
            echo '<p>'. JText::_('ContactFormPro system plugin enabled succesfully') . '</p>';
       else
           echo '<p>' . JText::_('Enable ContactFormPro in the plugins manager') . '</p>';
   }
}