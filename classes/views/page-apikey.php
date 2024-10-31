<?php    
    require(RCS_VIEWS_PATH.'/shared/libraries.php');
?>
<div class="wrap">
    <div id="icon-rcs-general" class="icon32"><br/></div>
    <h2>
        API Key
    </h2>  
<?php    
    require(RCS_VIEWS_PATH.'/shared/errors.php');    
    require(RCS_VIEWS_PATH . '/shared/nav.php');
?>
    <p class="about-description help-text">Your RollUp Media account manager will provide your with the required key, shared secret and web property id. When testing the plugin, please ingest content in &QUOT;Draft&QUOT; status first.</p>
    <br class="clear"/>
    <form method="post" name="apikey-form" class="apikey-form">
        <input type="hidden" name="rcs_action" value="process" />
        <table class="form-table">
            <tbody>
                <tr valign="top">
                <th scope="row"><label for="api_key">Add API Key</label></th>
                <td><input name="api_key" type="text" id="api_key" value="<?php echo stripslashes($apikey);?>" class="regular-text"></td>
                </tr>
                <tr valign="top">
                <th scope="row"><label for="shared_secret">Add Shared Secret</label></th>
                <td><input name="shared_secret" type="text" id="shared_secret" value="<?php echo stripslashes($sharedsecret);?>" class="regular-text"></td>
                </tr>
                <tr valign="top">
                <th scope="row"><label for="web_property_id">Add Web Property Id</label></th>
                <td><input name="web_property_id" type="text" id="web_property_id" value="<?php echo stripslashes($webid);?>" class="regular-text"></td>
                </tr>
                <tr valign="top">
                <th scope="row"><label for="ingest_state">Default Ingest State</label></th>
                <td>
                    <select name="ingest_state" type="text" id="ingest_state">
                        <?php foreach($ingest_list as $ingest){
                           $sel = '';
                           if($ingest->isdefault == 1 && empty($ingeststatus)){
                               $sel = 'selected="selected"';
                           }
                           if(!empty($ingeststatus) && $ingeststatus==$ingest->slug){
                               $sel = 'selected="selected"';
                           }
                           echo '<option value="'.$ingest->slug.'" '.$sel.'>'.stripslashes($ingest->name).'</option>'; 
                        }?>
                    </select>    
                </td>
                </tr>
                <tr valign="top">
                <th scope="row"><label for="check_frecuency">Frequency for checking new content</label></th>
                <td>
                    <select name="check_frecuency" type="text" id="check_frecuency">
                        <?php foreach($frecuency_list as $frec){
                           $sel = '';
                           if($frec->isdefault == 1 && empty($frecuency)){
                               $sel = 'selected="selected"';
                           }
                           if(!empty($frecuency) && $frecuency==$frec->slug){
                               $sel = 'selected="selected"';
                           }
                           echo '<option value="'.$frec->slug.'" '.$sel.'>'.stripslashes($frec->name).'</option>'; 
                        }?>
                    </select> 
                </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
    </form>
</div>
