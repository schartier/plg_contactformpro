<?php

defined('_JEXEC') or die;

jimport('joomla.form.fields.list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldDirList extends JFormFieldList {

    //The field class must know its own type through the variable $type.
    protected $type = 'DirList';

    protected function getOptions() {
        // Initialize variables.
        $options = array();

        // Initialize some field attributes.
        $filter = (string) $this->element['filter'];
        $exclude = (string) $this->element['exclude'];
        $hideNone = (string) $this->element['hide_none'];
        $hideDefault = (string) $this->element['hide_default'];

        // Get the path in which to search for file options.
        $path = (string) $this->element['directory'];
        if (!is_dir($path)) {
            $path = JPATH_ROOT . '/' . $path;
        }

        // Prepend some default options based on field attributes.
        if (!$hideNone) {
            $options[] = JHtml::_('select.option', '-1', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }
        if (!$hideDefault) {
            $options[] = JHtml::_('select.option', '', JText::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        // Get a list of files in the search path with the given filter.
        $files = JFolder::folders($path, $filter);

        // Build the options list from the list of files.
        if (is_array($files)) {
            foreach ($files as $file) {

                // Check to see if the file is in the exclude mask.
                if ($exclude) {
                    if (preg_match(chr(1) . $exclude . chr(1), $file)) {
                        continue;
                    }
                }

                $options[] = JHtml::_('select.option', $file, $file);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}