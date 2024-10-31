<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSApikeyController{
    private $class_errors;
    private $message;
    
    function RCSApikeyController(){
        add_action('admin_menu', array( &$this, 'menu' ));
        add_action('admin_menu', array( &$this, 'lower_menu' ), 90);
        
        $this->class_errors = array();
    }
    
    function menu(){
        global $rcs_settings;
        add_submenu_page('rcscontentsuite', $rcs_settings->menu .' | '. 'API Key', 'API Key', 'edit_posts', 'rcscontentsuite', array(&$this, 'route'));        
    }
    
    function lower_menu(){
    }
    
    function route(){
        $action = isset($_REQUEST['rcs_action']) ? 'rcs_action' : 'action';
        $action = RCSAppHelper::get_param($action);
        if($action == 'process')
            return $this->process();
        else
            return $this->display_settings_list();
    }
    
    function display_settings_list(){ 
        global $rcs_apikey_helper;
        $ingest_list = $rcs_apikey_helper->get_ingest_states();
        $frecuency_list= $rcs_apikey_helper->get_frecuency();
        //Get Actual Values
        $apikey = $rcs_apikey_helper->get_setting_value('api_key');
        $sharedsecret = $rcs_apikey_helper->get_setting_value('shared_secret');
        $webid = $rcs_apikey_helper->get_setting_value('web_property_id');
        $frecuency = $rcs_apikey_helper->get_setting_value('check_frecuency');
        $ingeststatus = $rcs_apikey_helper->get_setting_value('ingest_state');
        if(!empty($this->class_errors)){
            $errors = $this->class_errors;
        }
        if(!empty($this->message)){
            $message = $this->message;
        }
        require(RCS_VIEWS_PATH.'/page-apikey.php');
    }
    
    function process(){
        global $rcs_apikey_helper,$rcs_sync_controller;
        $this->class_errors = array();
        $this->message = '';
        $errors=array();
        $apikey = RCSAppHelper::get_param('api_key');
        $sharedsecret = RCSAppHelper::get_param('shared_secret');
        $webid = RCSAppHelper::get_param('web_property_id');
        $frecuency = RCSAppHelper::get_param('check_frecuency');
        $ingeststatus = RCSAppHelper::get_param('ingest_state');
        
        if(empty($apikey)){
            $this->class_errors[]='Please enter your API Key.';
        }
        if(empty($sharedsecret)){
            $this->class_errors[]='Please enter your Shared Secret.';
        }
        if(empty($webid)){
            $this->class_errors[]='Please enter your Web Property Id.';
        }
        
        if(empty($this->class_errors)){
            $tosavevalues=array(
                'api_key'=>$apikey,
                'shared_secret'=>$sharedsecret,
                'web_property_id'=>$webid,
                'ingest_state'=>$ingeststatus,
                'check_frecuency'=>$frecuency,
                );
            
            $result = $rcs_apikey_helper->save_properties($tosavevalues);
            $rcs_sync_controller->update_sync_time($frecuency);
            if($result){
                $this->message = 'API Key Properties configured.';
            }else{
                $this->class_errors[]='Something went wrong. We couln\'t update the values. Please try later.';
            }
        }
        
        return $this->display_settings_list();
    }
}    
?>
