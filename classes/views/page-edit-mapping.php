<?php    
    require(RCS_VIEWS_PATH.'/shared/libraries.php');
?>
<div class="wrap">
    <div id="icon-rcs-general" class="icon32"><br/></div>
    <h2>
        Edit Mapping
    </h2>  
<?php    
    require(RCS_VIEWS_PATH.'/shared/errors.php');    
    require(RCS_VIEWS_PATH . '/shared/nav.php');
?>
    <form method="post" name="editmapping-form" class="edit-mapping-form">
        <input type="hidden" name="rcs_action" value="process" />
        <input type="hidden" name="mid" value="<?php echo $mid;?>" />
        <?php if(!empty($added)){ ?>
            <input type="hidden" name="added" value="1" />
        <?php } ?>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                <th scope="row"><label for="rum_type">RUM Content Type</label></th>
                <td>
                    <select name="rum_type" type="text" id="rum_type">
                        <?php foreach($rum_type_list as $rum_type){
                            $sel='';
                           if($mapping_details['rum_type']== $rum_type->slug){
                               $sel = 'selected="selected"';
                           } 
                           echo '<option value="'.$rum_type->slug.'" '.$sel.'>'.stripslashes($rum_type->name).'</option>'; 
                        }?>
                    </select>
                </td>
                </tr>
                <tr valign="top">
                <th scope="row"><label for="post_type">WP Post Type</label></th>
                <td>
                    <select name="post_type" type="text" id="post_type">
                        <?php
                        $args = array(
                            'public' => true
                        );
                        $output = 'objects'; // names or objects
                        $post_types = get_post_types($args, $output);
                        foreach ($post_types as $post_type) {
                            $sel='';
                            if($mapping_details['post_type']== $post_type->name){
                               $sel = 'selected="selected"';
                            }
                            echo '<option value="' . $post_type->name . '" ' . $sel . '>' . stripslashes($post_type->label) . '</option>';
                        }
                        ?>
                    </select>
                </td>
                </tr>
            </tbody>
        </table>  
        <div id="poststuff">
        <div id="fields-container" class="postbox-container">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                <div id="mapping-container" class="postbox ">
                    <h3 class="hndle"><span>Mapping Fields</span></h3>
                    <div class="inside">
                        <table class="form-table">
                        <tbody>
                            <?php foreach($platform_fields as $field){ ?>
                            <tr valign="top">
                                <th scope="row"><label for="<?php echo $field->slug;?>"><?php echo stripslashes($field->name);?></label></th>
                            <td>
                                <select name="<?php echo $field->slug;?>" type="text" id="<?php echo $field->slug;?>">
                                    <option value="">---- Select ----</option>
                                    <?php foreach($meta_fields as $meta){
                                        $sel='';
                                        if($field->slug == 'title' && empty($mapping_details['fields'][$field->slug]['wp_slug'])){
                                            if($meta == 'post_title')
                                                $sel = 'selected="selected"';
                                        }else if($field->slug == 'text' && empty($mapping_details['fields'][$field->slug]['wp_slug'])){
                                            if($meta == 'post_content')
                                                $sel = 'selected="selected"';
                                        }else if(!empty($mapping_details['fields'][$field->slug]['wp_slug']) && $mapping_details['fields'][$field->slug]['wp_slug']== $meta){
                                           $sel = 'selected="selected"';
                                        }
                                       echo '<option value="'.$meta.'" '.$sel.'>'.stripslashes($meta).'</option>'; 
                                    }?>
                                </select>
                            </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>    
        </div> 
        </div>  
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">&nbsp;&nbsp;<a class="button" href="<?php echo admin_url( 'admin.php');?>?page=rcsmapping">Cancel</a></p>
    </form>
</div>
