<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSConfigHelper{
    private $rum_content_type;
    private $platform_fields;
    private $basic_post_data;
    
    function RCSConfigHelper(){
        global $wpdb;
        $this->rum_content_type = $wpdb->prefix . "rcs_rum_content_type";
        $this->platform_fields = $wpdb->prefix . "rcs_platform_fields";
        $this->basic_post_data = $wpdb->prefix . "rcs_basic_post_data";
    }
    
    function get_rum_content_types(){
        global $wpdb;
        $query = 'SELECT id,name,slug FROM ' . $this->rum_content_type;
        
        $query .= ' ORDER BY pos ASC';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function get_platform_fields(){
        global $wpdb;
        $query = 'SELECT id,name,slug FROM ' . $this->platform_fields;
        
        $query .= ' ORDER BY pos ASC';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function get_post_default_fields(){
        global $wpdb;
        $query = 'SELECT slug FROM ' . $this->basic_post_data;
        
        $results = $wpdb->get_results($query);
        return $results;
    }
}
?>
