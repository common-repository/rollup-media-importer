<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSLogsHelper{
    public static $PAGE_SIZE = 50;
    public static $LOGTYPES = array('ACCESS_DENIED'=>'Access Denied',
        'UNKNOWN_PUBLISHER'=>'Unknown Publisher',
        'MISSING_PARAMETERS'=>'Missing Parameters',
        'UNKNOWN_ERROR'=>'Unknown Error',
        'INGESTED_ARTICLE' => 'Article Ingested',
        'ERROR_INSERTING' => 'Error Ingesting Articles',
        'UPDATE_ERROR' => 'Updating Errors',
        'START_INGESTION' => 'Ingestion Process Requested',
        'END_INGESTION' => 'Ingestion Process Finished',
        'ERROR_INGEST_PROCESS' => 'Ingestion Process Error');
    private $available_filters = array('type'=>'s');
    private $rum_log;
    
    function RCSLogsHelper(){
        global $wpdb;
        $this->rum_log = $wpdb->prefix . "rcs_rum_log";
    }
    
    function insert_log($type,$message='',$rum_id='',$wp_id=0){
        global $wpdb;
        $validtype= $this->valid_log_type($type);
        if($validtype){
            $message = !empty($message)?$message:RCSLogsHelper::$LOGTYPES[$type];
            $wpdb->insert(
                $this->rum_log, array(
                'type' => $type,
                'message' => esc_sql($message),
                'rum_content_id' => esc_sql($rum_id),
                'wp_post_id' => $wp_id), array(
                '%s',
                '%s',
                '%s',
                '%d')
            );
        }
    }
    
    private function valid_log_type($type){
        foreach(RCSLogsHelper::$LOGTYPES as $key =>$value){
            if($key == $type){
                return true;
            }
        } 
        return false;
    }
    
    function get_logs($pagesize=50,$page=1,$filters=null){
        global $wpdb;
        if($pagesize< 1)
            $pagesize = 50;
        if($page<1)
            $page = 1;
        $init = ($page - 1) * $pagesize;
        $end = $init + $pagesize - 1;
        $query = 'SELECT * FROM ' . $this->rum_log;
        $query .= $this->apply_filters($filters);
        $query .= ' ORDER BY created_at DESC,id DESC';
        $query .= ' LIMIT '.$init.','.$end;
        
        $results = $wpdb->get_results($query);
        return $results;
    }
    
    function pagination_data($pagesize=50,$filters=null){
        global $wpdb;
        $pagination = array();
        $pages = 0;
        if($pagesize< 1)
            $pagesize = 50;
        $query = 'SELECT COUNT(id) as total FROM ' . $this->rum_log;
        $query .= $this->apply_filters($filters);
        $results = $wpdb->get_results($query);
        $total = $results[0]->total;
        if($total > 0){
            $pages = $total / $pagesize;
            $pages = floor($pages);
            if($pagesize * $pages < $total){
                $pages += 1;
            }
        }
        $pagination['total'] = $total;
        $pagination['pages'] = $pages;
        return $pagination;
    }
    
    //Return WHERE CLAUSE FOR QUERY
    private function apply_filters($args){
        $filters = '';
        if(!empty($args)){
            $count = 0;
            foreach($this->available_filters as $f => $type){
                if(!empty($args[$f])){
                    if($count == 0){
                        $filters .= ' WHERE ';
                    }
                    if($type == 's'){
                        $filters .= $f. ' = \''.$args[$f].'\'';
                    }
                    $count++;
                }
            }
        }
        return $filters;    
    }
}
?>
