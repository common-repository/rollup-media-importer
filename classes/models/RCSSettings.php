<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSSettings{
    // Page Setup Variables
    public $menu;
    public $admin_permission;


    function RCSSettings(){
        $this->set_default_options();
    }

    function set_default_options(){
        if(!isset($this->menu))
            $this->menu = 'RUM Content Suite';
        
        if(!isset($this->admin_permission))
            $this->admin_permission = "You do not have permission to do that";
        $this->admin_permission = stripslashes($this->admin_permission);
    }
}
?>
