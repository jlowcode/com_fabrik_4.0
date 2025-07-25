<?php

function getElementName($id) {
    if($id) {
        // Inicializa variaveis para query
        $db       = JFactory::getDbo();
        $subQuery = $db->getQuery(true);
        $query    = $db->getQuery(true);
    
        $subQuery
            ->select(array('a.name' ,'b.form_id'))
            ->from($db->quoteName('#__fabrik_elements', 'a'))
            ->join(
                'INNER',
                $db->quoteName('#__fabrik_formgroup', 'b').
                ' ON ('. $db->quoteName('a.group_id'). ' = '.
                $db->quoteName('b.group_id') . ')')
            ->where($db->quoteName('a.id') . ' = '. $id);
    
        $query
            ->select(array('a.db_table_name', 'b.name'))
            ->from($db->quoteName('#__fabrik_lists', 'a'))
            ->join(
                'INNER',
                '('.$subQuery.') as b on a.form_id = b.form_id'
            );
    
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
    
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();
    
        return $results[0]->db_table_name . '___' . $results[0]->name;
    }
    if(JText::_('ELEMENTS_NOT_SELECTED') != 'ELEMENTS_NOT_SELECTED') {
        throw new Exception(JText::_('ELEMENTS_NOT_SELECTED'), 0);
    } else {
        throw new Exception('To use this list view first you need to select the thumb and title elements in the administration page', 0);
    }

    
}
// Error: To use this list view template first you need to create and set the elements title and thumbnail in Fabrik\'s administration page.


