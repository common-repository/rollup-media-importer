<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSAppController{
    function RCSAppController(){
        add_action('admin_menu', array( &$this, 'menu' ), 1);
        add_filter('rcs_nav_array', array( &$this, 'rcs_nav'), 1);
        add_filter('cron_schedules',array( &$this, 'rcs_cron_definer'));
        add_action('rcs_ingest_sync', array( &$this, 'rcs_ingest_post_sync'));
        add_filter( "plugin_action_links_rollup-media-importer/rollup-media-importer.php", array( &$this, 'rcs_plugin_add_settings_link'));
        
        register_activation_hook(RCS_PATH.'/rollup-media-importer.php', array( &$this, 'install' ));  
        register_deactivation_hook( RCS_PATH.'/rollup-media-importer.php', array( &$this, 'deactivate' ) );
        register_uninstall_hook( RCS_PATH.'/rollup-media-importer.php', array( &$this, 'uninstall' )); 
        
        wp_enqueue_style('rcs-style', RCS_URL . '/css/styles.css');
    }
    
    function menu() {
        global $rcs_settings;
        
        if(current_user_can('administrator') || current_user_can('editor') ){
            global $rcs_apikey_controller;
            add_object_page('RUM Content Suite',$rcs_settings->menu, 'edit_posts', 'rcscontentsuite', array($rcs_apikey_controller, 'route'),'div');
        }
    }
    
    function rcs_nav($nav=array()){
        if(current_user_can('administrator') || current_user_can('editor')){
            $nav['rcscontentsuite'] = "API Key";
            $nav['rcsmapping'] = "Mapping";
            $nav['rcslogfile'] = "Log File";
        }
        return $nav;
    }
    
    function install(){        
        global $rcsdb;
        $rcsdb->upgrade();
    }
    
    function uninstall(){
        if(current_user_can('administrator')){
            global $rcsdb;
            wp_clear_scheduled_hook('rcs_ingest_sync');
            $rcsdb->uninstall();
            return true;
            wp_die('RUM Content Suite was successfully uninstalled.');
        }else{
            global $rcs_settings;
            wp_die($rcs_settings->admin_permission);
        }
    }
    
    function deactivate(){
        wp_clear_scheduled_hook('rcs_ingest_sync');
        return true;
    }
    
    function rcs_cron_definer($schedules){
        $schedules['ten-minutes'] = array('interval' => 600, 'display' => __('Once Every 10 Minutes'));
        $schedules['thirty-minutes'] = array('interval' => 1800, 'display' => __('Once Every 30 Minutes'));
        $schedules['two-hourly'] = array('interval' => 7200, 'display' => __('Once Every 2 Hours'));
        $schedules['three-hourly'] = array('interval' => 10800, 'display' => __('Once Every 3 Hours'));
        $schedules['six-hourly'] = array('interval' => 21600, 'display' => __('Once Every 6 Hours'));
        $schedules['twelve-hourly'] = array('interval' => 43200, 'display' => __('Once Every 12 Hours'));
        return $schedules;
    }
    
    //Syncronize Ingest Post
    function rcs_ingest_post_sync() {
        global $rcs_sync_controller,$rcs_sync_start;
        if(!$rcs_sync_start){
            $rcs_sync_start = true;
            error_log('RUM CONTENT SUITE INGEST POSTS TASK:'.date("Y-m-d H:i:s"));
            global $rcs_sync_controller;
            $rcs_sync_controller->sync_ingest_post();
            $rcs_sync_start = false;
        }else{
            error_log('RUM CONTENT SUITE INGEST POSTS TASK ALREADY STARTED:'.date("Y-m-d H:i:s"));
        }
    }
    
    function rcs_plugin_add_settings_link( $links ) {
        $settings_link = '<a href="'.admin_url( 'admin.php').'?page=rcscontentsuite">Settings</a>';
        array_push( $links, $settings_link );
        return $links;
    }
}
?>
