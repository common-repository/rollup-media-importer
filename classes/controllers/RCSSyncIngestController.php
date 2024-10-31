<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(RCS_MODELS_PATH . '/JSONApiClient.php');
class RCSSyncIngestController{
    public static $DEFAULT_ITEMS = 20;
    
    function RCSSyncIngestController(){
        
    }
    
    function update_sync_time($cron_slug){
        wp_clear_scheduled_hook('rcs_ingest_sync');
        wp_schedule_event(time(), $cron_slug, 'rcs_ingest_sync');
    }
    
    function sync_ingest_post(){
        global $rcs_apikey_helper,$rcs_ingest_helper,$rcs_logs_helper;
        error_log(print_r('STARTING INGESTED ARTICLES FROM RUM CONTENT SUITE:', true));
        $rcs_logs_helper->insert_log('START_INGESTION','Ingestion Process has started');
        
        $api_key = $rcs_apikey_helper->get_setting_value('api_key');
        $shared_secret = $rcs_apikey_helper->get_setting_value('shared_secret');
        $web_property_id = $rcs_apikey_helper->get_setting_value('web_property_id');
        
        $client = new JSONApiClient($api_key,$web_property_id,$shared_secret);
        $resp = $client->getArticles(RCSSyncIngestController::$DEFAULT_ITEMS);
        if($resp!='ACCESS_DENIED' && $resp != 'UNKNOWN_PUBLISHER' && $resp != 'MISSING_PARAMETERS' && $resp != 'UNKNOWN_ERROR'){
            $savedIds= array();
            foreach($resp->articles as $article){
                $saved = $rcs_ingest_helper->rumsuite_post($article);
                if($saved){
                    $savedIds[] = $rcs_ingest_helper->ingested_id;
                    $rcs_logs_helper->insert_log('INGESTED_ARTICLE','Article ingestion success',$rcs_ingest_helper->ingested_id,$rcs_ingest_helper->wp_post_id);
                }else{
                    error_log(print_r('Error saving article', true));
                    $rcs_logs_helper->insert_log('ERROR_INSERTING',$rcs_ingest_helper->message,$rcs_ingest_helper->ingested_id);
                }
            }
            $rcs_logs_helper->insert_log('END_INGESTION','Ingestion Process End Successfully');
            if(!empty($savedIds)){
                $up_resp = $client->updateArticles($savedIds);
                if($up_resp=='ACCESS_DENIED' || $up_resp == 'UNKNOWN_PUBLISHER' || $up_resp == 'MISSING_PARAMETERS' || $up_resp == 'UNKNOWN_ERROR'){          
                    error_log(print_r('ERROR WHEN UPDATING INGESTED ARTICLES FROM RUM CONTENT SUITE:'.$up_resp, true));
                    $rcs_logs_helper->insert_log($up_resp,'An error occurs when trying to update RUM Content Suite status:'.$client->getErrorMessage());
                }
            }
        }else{
            error_log(print_r('ERROR WHEN START INGESTING ARTICLES FROM RUM CONTENT SUITE:'.$resp.' '.$client->getErrorMessage(), true));
            $rcs_logs_helper->insert_log($resp,'An error occurs when trying to start ingesting process:'.$client->getErrorMessage());
        }
    }
}
?>
