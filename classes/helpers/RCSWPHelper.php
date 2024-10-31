<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSWPHelper{
    function RCSConfigHelper(){
    }
    
    function get_metatags(){
        global $wpdb;
        $query = 'SELECT DISTINCT meta_key FROM ' . $wpdb->prefix . 'postmeta';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
}
?>
