<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSApikeyHelper{
    private $frecuency;
    private $ingest_state;
    private $general_settings;
    
    function RCSApikeyHelper(){
        global $wpdb;
        $this->frecuency = $wpdb->prefix . "rcs_frecuency";
        $this->ingest_state = $wpdb->prefix . "rcs_ingest_state";
        $this->general_settings = $wpdb->prefix . "rcs_general_settings";
    }
    
    function get_frecuency(){
        global $wpdb;
        $query = 'SELECT name,slug,isdefault  FROM ' . $this->frecuency;
        
        $query .= ' ORDER BY pos ASC';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function get_ingest_states(){
        global $wpdb;
        $query = 'SELECT name,slug,isdefault  FROM ' . $this->ingest_state;
        
        $query .= ' ORDER BY pos ASC';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function save_properties($properties){
        $result = true;
        if(!empty($properties)){
            foreach($properties as $key=>$value){
                $saved = $this->save_property($key,$value);
                if($saved===false){
                    $result = false;
                }
            }
        }
        return $result;    
    }
    
    function save_property($slug,$value){
        global $wpdb;
        $check_query = 'SELECT id  FROM ' . $this->general_settings;
        $check_query .= ' WHERE slug = \''.$slug.'\'';
        $check_results = $wpdb->get_results($check_query);
        if(empty($check_results)){//Insert the new value
            $query = 'INSERT INTO `'.$this->general_settings.'` (`slug`,`value`) VALUES (';
            $query.= '\''.esc_sql($slug).'\',';
            $query.= '\''.esc_sql($value).'\'';
            $query.= ')';
            $saved = $wpdb->query($query);
            if($saved === false){return false;}
        }else{//Update the value
            $updated = $wpdb->update( 
                $this->general_settings, 
                array( 
                        'value' => $value	// string
                ), 
                array( 'slug' =>  $slug), 
                array( 
                        '%s'	// value1
                ), 
                array( '%s' ) 
            );
            if($updated===false){return false;}
        }
        return true;
    }
    
    function get_setting_value($slug){
        global $wpdb;
        $query = 'SELECT value FROM ' . $this->general_settings;
        $query .= ' WHERE slug = \''.$slug.'\'';
        
        $results = $wpdb->get_results($query);
        if(!empty($results)){
            return $results[0]->value;
        }
        return '';
    }
}
?>
