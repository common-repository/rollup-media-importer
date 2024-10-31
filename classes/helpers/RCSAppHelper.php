<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSAppHelper{
    
    function get_param($param, $default=''){
        if(strpos($param, '[')){
            $params = explode('[', $param);
            $param = $params[0];    
        }

        $value = (isset($_POST[$param]) ? trim($_POST[$param]) : (isset($_GET[$param]) ? trim($_GET[$param]) : $default));
        
        if(isset($params) and is_array($value) and !empty($value)){
            foreach($params as $k => $p){
                if(!$k or !is_array($value))
                    continue;
                    
                $p = trim($p, ']');
                $value = (isset($value[$p])) ? trim($value[$p]) : $default;
            }
        }

        return $value;
    }

    
    function user_has_permission($needed_role){        
        if($needed_role == '' or current_user_can($needed_role))
            return true;
            
        $roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
        foreach ($roles as $role){
        	if (current_user_can($role))
        		return true;
        	if ($role == $needed_role)
        		break;
        }
        return false;
    }
}
?>
