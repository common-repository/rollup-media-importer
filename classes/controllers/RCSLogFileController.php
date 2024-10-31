<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSLogFileController{
    private $class_errors;
    
    function RCSLogFileController(){
        add_action('admin_menu', array( &$this, 'menu' ));
        add_action('admin_menu', array( &$this, 'lower_menu' ), 90);
        
        $this->class_errors = array();
    }
    
    function menu(){
        global $rcs_settings;
        add_submenu_page('rcscontentsuite', $rcs_settings->menu .' | '. 'Log File', 'Log File', 'edit_posts', 'rcslogfile', array(&$this, 'route'));        
    }
    
    function lower_menu(){
    }
    
    function route(){
        return $this->display_log_list();
    }
    
    function display_log_list(){
        global $rcs_logs_helper;
        $filters = null;
        $actual_page = RCSAppHelper::get_param('p');
        $error_type = RCSAppHelper::get_param('et');
        if(empty($actual_page)){
            $actual_page = 1;
        }
        if(!empty($error_type)){
            $filters['type'] = $error_type;
        }
        $next_page = $actual_page + 1;
        $prev_page = $actual_page - 1;
        $logs_list = $rcs_logs_helper->get_logs(RCSLogsHelper::$PAGE_SIZE,$actual_page,$filters);
        $pagination = $rcs_logs_helper->pagination_data(RCSLogsHelper::$PAGE_SIZE,$filters);
        if($next_page > $pagination['pages']){
            $next_page = $pagination['pages'];
        }
        if($prev_page <= 0){
            $prev_page = 1;
        }
        require(RCS_VIEWS_PATH.'/page-log-list.php');
    }
}
?>
