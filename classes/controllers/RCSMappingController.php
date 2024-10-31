<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSMappingController{
    private static $EXCLUDED_METAS = array('_edit_last','_edit_lock','head','_thumbnail_id','rum_content_id','rum_suggested_url');
    private $class_errors;
    private $message;
    
    function RCSMappingController(){
        add_action('admin_menu', array( &$this, 'menu' ));
        add_action('admin_menu', array( &$this, 'lower_menu' ), 90);
        
        $this->class_errors = array();
    }
    
    function menu(){
        global $rcs_settings;
        add_submenu_page('rcscontentsuite', $rcs_settings->menu .' | '. 'Mapping', 'Mapping', 'edit_posts', 'rcsmapping', array(&$this, 'route'));        
    }
    
    function lower_menu(){
        add_submenu_page('rcsmapping', 'Mapping | '. 'Add New Mapping', '<span style="display:none;">'. 'Add New Mapping' .'</span>', 'edit_posts', 'rcsnewmapping', array(&$this, 'route_add_mapping'));
        add_submenu_page('rcsmapping', 'Mapping | '. 'Edit Mapping', '<span style="display:none;">'. 'Edit Mapping' .'</span>', 'edit_posts', 'rcseditmapping', array(&$this, 'route_edit_mapping'));
        add_submenu_page('rcsmapping', 'Mapping | '. 'Delete Mapping', '<span style="display:none;">'. 'Delete Mapping' .'</span>', 'edit_posts', 'rcsdelmapping', array(&$this, 'route_del_mapping'));
    }
    
    function route(){
        return $this->display_mappings_list();
    }
    
    function display_mappings_list(){  
        global $rcs_mapping_helper;
        if(!empty($this->class_errors)){
            $errors = $this->class_errors;
        }
        if(!empty($this->message)){
            $message = $this->message;
        }
        $mapping_list=$rcs_mapping_helper->get_mappings();
        require(RCS_VIEWS_PATH.'/page-mappings-list.php');
    }
    
    function route_add_mapping(){
        $action = isset($_REQUEST['rcs_action']) ? 'rcs_action' : 'action';
        $action = RCSAppHelper::get_param($action);
        $added = RCSAppHelper::get_param('added');
        if($action == 'process' && empty($added)){
            return $this->process_add_mapping();
        }else if($action == 'process' && !empty($added)){
            $map_item_id = RCSAppHelper::get_param('mid');
            return $this->process_edit_mapping($map_item_id,true);
        }else{
            return $this->display_add_mapping();
        }    
    }
    
    function display_add_mapping(){
        global $rcs_config_helper;
        $rum_type_list = $rcs_config_helper->get_rum_content_types();
        $platform_fields = $rcs_config_helper->get_platform_fields();
        $meta_fields = $this->merge_meta_tags();
        
        if(!empty($this->class_errors)){
            $errors = $this->class_errors;
        }
        if(!empty($this->message)){
            $message = $this->message;
        }
        require(RCS_VIEWS_PATH.'/page-add-mapping.php');
    }
    
    function merge_meta_tags(){
        global $rcs_config_helper,$rcs_wp_helper;
        $meta_tags = array();
        $default_metas = $rcs_config_helper->get_post_default_fields();
        $custom_metas = $rcs_wp_helper->get_metatags();
        foreach($default_metas as $dm){
            $meta_tags[] = $dm->slug; 
        }
        foreach($custom_metas as $cm){
            if(!in_array($cm->meta_key, RCSMappingController::$EXCLUDED_METAS)){
                $meta_tags[] = $cm->meta_key; 
            }
        }
        
        if(!empty($meta_tags)){
            sort($meta_tags);
        }
        
        return $meta_tags;
    }
    
    function process_add_mapping(){
        global $rcs_mapping_helper,$rcs_config_helper;
        $this->class_errors = array();
        $this->message = '';
        $errors=array();
        $rum_type = RCSAppHelper::get_param('rum_type');
        $wp_type = RCSAppHelper::get_param('post_type');
        $added_mapping = null;
        
        if(empty($rum_type)){
            $this->class_errors[]='Please enter your RUM Content Type.';
        }
        if(empty($wp_type)){
            $this->class_errors[]='Please enter your Wordpress Post Type.';
        }
        if(empty($this->class_errors)){
            $exist = $rcs_mapping_helper->exist_mapping($rum_type);
            if($exist){
                $this->class_errors[]='You can\'t add the same mapping multiple times.';
            }
        }
        
        if(empty($this->class_errors)){
            $platform_fields = $rcs_config_helper->get_platform_fields();
            $fields = array();
            foreach($platform_fields as $pfield){
                $param = RCSAppHelper::get_param($pfield->slug);
                if(!empty($param)){
                   $fields[$pfield->slug] = $param;
                }
            } 

            $result = $rcs_mapping_helper->add_mapping($rum_type,$wp_type,$fields);
            if($result){
                $this->message = 'Mapping configured.';
                $added_mapping = $result;
            }else{
                $this->class_errors[]='Something went wrong. We couln\'t save your mapping values. Please try later.';
            }
        }
        if(empty($this->class_errors)){
            $this->display_edit_mapping($added_mapping,true);
        }else{
            $this->display_add_mapping();
        }
        
        
    }
    
    function display_edit_mapping($mapid,$added=false){
        global $rcs_config_helper,$rcs_mapping_helper;
        $mid = $mapid;
        $rum_type_list = $rcs_config_helper->get_rum_content_types();
        $platform_fields = $rcs_config_helper->get_platform_fields();
        $meta_fields = $this->merge_meta_tags();
        $mapping_details = $rcs_mapping_helper->mapping_details($mapid);
        if(!empty($this->class_errors)){
            $errors = $this->class_errors;
        }
        if(!empty($this->message)){
            $message = $this->message;
        }
        require(RCS_VIEWS_PATH.'/page-edit-mapping.php');
    }
    
    function route_edit_mapping(){
        $action = isset($_REQUEST['rcs_action']) ? 'rcs_action' : 'action';
        $action = RCSAppHelper::get_param($action);
        $map_item_id = RCSAppHelper::get_param('mid');
        if($action == 'process')
            return $this->process_edit_mapping($map_item_id);
        else
            return $this->display_edit_mapping($map_item_id);
    }
    
    function process_edit_mapping($mapid,$added=false){
        global $rcs_mapping_helper,$rcs_config_helper;
        $mid = $mapid;
        $this->class_errors = array();
        $this->message = '';
        $errors=array();
        $rum_type = RCSAppHelper::get_param('rum_type');
        $wp_type = RCSAppHelper::get_param('post_type');
        
        if(empty($rum_type)){
            $this->class_errors[]='Please enter your RUM Content Type.';
        }
        if(empty($wp_type)){
            $this->class_errors[]='Please enter your Wordpress Post Type.';
        }
        if(empty($this->class_errors)){
            $exist = $rcs_mapping_helper->exist_mapping($rum_type);
            if($exist){
                $exist_id = $rcs_mapping_helper->get_exist_mapping($rum_type);
                $exist_id = $exist_id[0]->id;
                if($exist_id!=$mid){
                    $this->class_errors[]='You can\'t add the same mapping multiple times.';
                }
            }
        }
        
        if(empty($this->class_errors)){
            $platform_fields = $rcs_config_helper->get_platform_fields();
            $fields = array();
            foreach($platform_fields as $pfield){
                $param = RCSAppHelper::get_param($pfield->slug);
                if(!empty($param)){
                   $fields[$pfield->slug] = $param;
                }
            } 

            $result = $rcs_mapping_helper->edit_mapping($mid,$rum_type,$wp_type,$fields);
            if($result){
                $this->message = 'Mapping configuration updated.';
            }else{
                $this->class_errors[]='Something went wrong. We couln\'t edit your mapping values. Please try later.';
            }
        }
        
        $this->display_edit_mapping($mid,$added);
    }
    
    function route_del_mapping(){
        $this->class_errors = array();
        $this->message = '';
        $map_item_id = RCSAppHelper::get_param('mid');
        $result = $this->process_del_mapping($map_item_id);
        if ($result) {
            $this->message = 'Mapping configuration deleted.';
        } else {
            $this->class_errors[] = 'Something went wrong. We couln\'t delete your mapping values. Please try later.';
        }
        return $this->display_mappings_list();
    }
    
    function process_del_mapping($mid){
        global $rcs_mapping_helper;
        return $rcs_mapping_helper->deleted_mapping($mid);
    }
}
?>
