<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSMappingHelper{
    private $mapping;
    private $rum_wptype_mapping;
    
    function RCSMappingHelper(){
        global $wpdb;
        $this->mapping = $wpdb->prefix . "rcs_mapping";
        $this->rum_wptype_mapping = $wpdb->prefix . "rcs_rum_wptype_mapping";
    }
    
    function get_mappings(){
        global $wpdb;
        $query = 'SELECT * FROM ' . $this->mapping;
        
        $query .= ' ORDER BY id ASC';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function exist_mapping($rumtype){
        global $wpdb;
        $query = 'SELECT id FROM ' . $this->mapping;
        $query .= ' WHERE rum_type = \''.$rumtype.'\'';
        
        $results = $wpdb->get_results($query);
        if(empty($results)){
            return false;
        }else{
            return true;
        }
    }
    
    function get_exist_mapping($rumtype){
        global $wpdb;
        $query = 'SELECT id FROM ' . $this->mapping;
        $query .= ' WHERE rum_type = \''.$rumtype.'\'';
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function add_mapping($rum_type,$wp_type,$fields){
        global $wpdb;
        $result = false;
        if(!empty($rum_type) && !empty($wp_type)){
            $inserted = $wpdb->insert(
                $this->mapping, 
                array(
                'rum_type' => $rum_type,
                'post_type' => $wp_type),
                array(
                '%s',
                '%s')
            );
            if($inserted){
                $insertid = $wpdb->insert_id;
                $result = $insertid;
                if(!empty($fields)){
                    foreach($fields as $key => $value){
                        $insert_item = $wpdb->insert(
                            $this->rum_wptype_mapping, 
                            array(
                            'idmapping' => $insertid,   
                            'rum_slug' => $key,
                            'wp_slug' => $value),
                            array(
                            '%d',    
                            '%s',
                            '%s')
                        );
                    }
                }
            }
        }
        return $result;
    }
    
    function mapping_details($id){
        global $wpdb;
        $map_details = array();
        $query = 'SELECT * FROM ' . $this->mapping;
        $query .= ' WHERE id = '.$id;
        $map = $wpdb->get_results($query);
        if(!empty($map)){
            $map = $map[0];
            $map_details['id'] = $map->id;
            $map_details['rum_type'] = $map->rum_type;
            $map_details['post_type'] = $map->post_type;
            
            $query_details = 'SELECT rum_slug,wp_slug FROM ' . $this->rum_wptype_mapping;
            $query_details .= ' WHERE idmapping = '.$map->id;
            $fields = $wpdb->get_results($query_details);
            $clean_fields = array();
            foreach($fields as $f){
                $clean_fields[$f->rum_slug] = array('wp_slug'=>$f->wp_slug);
            }
            $map_details['fields'] = $clean_fields;
        }
        return $map_details;
    }
    
    function edit_mapping($id,$rum_type,$wp_type,$fields){
        global $wpdb;
        $result = false;
        $updated = $wpdb->update( 
                $this->mapping, 
                array( 
                'rum_type' => $rum_type,
                'post_type' => $wp_type), 
                array( 'id' => $id ), 
                array( 
                        '%s',	// value1
                        '%s'	// value2
                ), 
                array( '%d' ) 
        );
        if($updated!==false){
            $result = true;
            //Delete elements from Page Table;
            $del_query = 'DELETE FROM `'. $this->rum_wptype_mapping.'`';
            $del_query .= ' WHERE idmapping = '.$id;
            $wpdb->query($del_query);
            if (!empty($fields)) {
                foreach ($fields as $key => $value) {
                    $insert_item = $wpdb->insert(
                        $this->rum_wptype_mapping, array(
                        'idmapping' => $id,
                        'rum_slug' => $key,
                        'wp_slug' => $value), array(
                        '%d',
                        '%s',
                        '%s')
                    );
                }
            }
        }
        return $result;
    }
    
    function deleted_mapping($id){
        global $wpdb;
        $result = false;
        if(!empty($id)){
           $del_query = 'DELETE FROM `'. $this->rum_wptype_mapping.'`';
           $del_query .= ' WHERE idmapping = '.$id; 
           $r1 = $wpdb->query($del_query);
           
           $del_query = 'DELETE FROM `'. $this->mapping.'`';
           $del_query .= ' WHERE id = '.$id;
           $r2 = $wpdb->query($del_query);
           
           if($r1 !== false && $r2 !== false){
               $result= true;
           }
        }
        return $result;
    }
    
    function get_mapping_config($rum_type){
        global $wpdb;
        $map_details = array();
        $query = 'SELECT * FROM ' . $this->mapping;
        $query .= ' WHERE rum_type = \''.$rum_type.'\'';
        $map = $wpdb->get_results($query);
        if(!empty($map)){
            $map = $map[0];
            $map_details['id'] = $map->id;
            $map_details['rum_type'] = $map->rum_type;
            $map_details['post_type'] = $map->post_type;
            
            $query_details = 'SELECT rum_slug,wp_slug FROM ' . $this->rum_wptype_mapping;
            $query_details .= ' WHERE idmapping = '.$map->id;
            $fields = $wpdb->get_results($query_details);
            $clean_fields = array();
            foreach($fields as $f){
                $clean_fields[$f->rum_slug] = array('wp_slug'=>$f->wp_slug);
            }
            $map_details['fields'] = $clean_fields;
        }
        return $map_details;
    }
}
?>
