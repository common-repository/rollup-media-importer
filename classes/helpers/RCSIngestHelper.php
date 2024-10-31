<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSIngestHelper{
    public $ingested_id = 0;
    public $wp_post_id = 0;
    public $message = '';
    function RCSIngestHelper(){
        $this->ingested_id = 0;
        $this->wp_post_id = 0;
        $this->message = '';
    }
    
    //Return false if error, true if success it sets the wp_post_id, the ingested_id with the ingested rum article id
    function rumsuite_post($article) {
        global $rcs_apikey_helper,$rcs_mapping_helper;
        $this->ingested_id = $article->id;
        $this->wp_post_id = 0;
        $this->message = '';
        error_log(print_r('Ingesting ID:'.$article->id, true));
        $post_status = $rcs_apikey_helper->get_setting_value('ingest_state');
        $content_config = $rcs_mapping_helper->get_mapping_config(strtolower($article->contentType));
        
        if (!empty($content_config)) {

            //get category ids from category names
            $categories = explode(",", $article->category);
            $post_category = array();
            foreach ($categories as $category) {
                //check if category exists in system and create if it does not
                $categoryName = strtolower(trim($category));
                $categoryName = str_replace(' ','-',$categoryName);
                $this->check_category_exists($categoryName);
                if (!get_category_by_slug($categoryName)) {
                    $post_category[] = 1;
                } else {
                    $post_category[] = get_category_by_slug($categoryName)->term_id;
                }
            }

            //Matching Meta Tags
            $fields = array();
            $fields['rum_content_id'] = $article->id;
            $fields['rum_suggested_url'] = $article->suggestedUrl;
            foreach ($content_config['fields'] as $rum_slug => $match) {
                if ($match['wp_slug'] != 'post_title' && $match['wp_slug'] != 'post_content') {
                    $fields[$match['wp_slug']] = $article->$rum_slug;
                }
            }
            
            //check if content exists already
            $post_id = $this->check_content_exists($article->id);
            if (!empty($post_id)) {
                $content = $this->add_tracking_code($article->text);
                
                //Update Post
                $new_post = array(
                    'ID' => $post_id,
                    'post_title' => $article->title,
                    'post_content' => $content,
                    'post_status' => $post_status,
                    'post_type' => $content_config['post_type'],
                    'comment_status' => 'open',
                    'post_category' => $post_category
                );
            } else {
                $content = $this->add_tracking_code($article->text);
                
                //Insert new
                $new_post = array(
                    'post_title' => $article->title,
                    'post_content' => $content,
                    'post_status' => $post_status,
                    'post_type' => $content_config['post_type'],
                    'comment_status' => 'open',
                    'post_category' => $post_category
                );
            }

            $post_id = wp_insert_post($new_post);
            if ($post_id != 0) {
                $this->wp_post_id = $post_id;
                wp_set_post_tags($post_id, $article->tags, false);
                //add content template/attachment information as meta
                $this->create_custom_fields($post_id, $fields);
                error_log(print_r('Succesfully Inserted:' . $post_id, true));
                $image_id = $this->save_media($article->mainImage, $post_id);
                if ($image_id !== false) {
                    $this->set_featured_image($post_id, $image_id);
                }
                return true;
            } else {
                $this->message = 'Error inserting post in wordpress database';
                return false;
            }
        } else {
            $this->message = 'Not configured RUM Content Type '.$article->contentType;
            return false;
        }
    }
    
    //Returns the id for the saved media post or false if error
    private function save_media($data){
        $image_url = !empty($data->url)?$data->url:'';
        $image_alt = !empty($data->alt)?$data->alt:'';
        $image_caption = !empty($data->caption)?$data->caption:'';
        $image_name = '';
        $uploads = wp_upload_dir();
        if(!empty($image_url)){
            $ext = pathinfo( basename($image_url) , PATHINFO_EXTENSION);
            $image_name = basename($image_url);
            $filename = wp_unique_filename( $uploads['path'], $image_name, $unique_filename_callback = null );
            $wp_filetype = wp_check_filetype($filename, null );
	    $name = "wpid-{$filename}";
            error_log(print_r('File Name: '.$filename,true));
            
            if (!substr_count($wp_filetype['type'], "image")) {
                error_log(print_r(basename($image_url) . ' is not a valid image. ' . $wp_filetype['type'] . '',true));
            }
            $bits = file_get_contents($image_url,true);
            if($bits!==false){
                $upload = wp_upload_bits($name, NULL, $bits);
                if (!empty($upload['error'])) {
                    $errorString = sprintf(__('Could not write file %1$s (%2$s)'), $name, $upload['error']);
                    error_log(print_r($errorString,true));
                    return false;
                }
                // Construct the attachment array
                // attach to post_id 0
                $post_id = 0;
                $attachment = array(
                    'post_title' => $name,
                    'post_content' => '',
                    'post_type' => 'attachment',
                    'post_mime_type' => $wp_filetype['type'],
                    'post_excerpt' => $image_caption,
                    'post_content' => $image_caption,
                    'guid' => $upload['url']
                );

                // Save the data
                $id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                if (!$id) {
                    error_log(print_r("Failed to save record into database.",true));
                }
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
               
                $attach_data = wp_generate_attachment_metadata( $id, $upload['file'] );
                wp_update_attachment_metadata( $id, $attach_data );
                //adds alt text as meta
                add_post_meta($id, "_wp_attachment_image_alt", $image_alt, false);
                apply_filters('wp_handle_upload', array('file' => $filename, 'url' => $upload['url'], 'type' => $wp_filetype['type']), 'upload');
                error_log(print_r("Image saved id:".$id,true));
                return $id;
            }else{
                return false;
            }
        }
    }
    
    //Check if category exist using the slug
    private function check_category_exists($catname) {
        $cat_values = array('cat_name' => trim($catname));
        if (!function_exists('get_category_by_slug')){require_once(ABSPATH . "wp-admin" . '/includes/category.php');}
        if (!function_exists('wp_insert_category')){require_once(ABSPATH . "wp-admin" . '/includes/taxonomy.php');}
        if (!get_category_by_slug($catname)) {
            wp_insert_category($cat_values);
        }
    }
    
    private function check_content_exists($rumId) {
        $query = array(
            'meta_key' => 'rum_content_id',
            'meta_value' => $rumId,
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
            'post_type' => 'any'
        );
        query_posts($query);
        if (have_posts()) :
            while (have_posts()) : the_post();
                $str = get_the_ID();
                return $str;
            endwhile;
        endif;
    }
    
    private function create_custom_fields($post_id, $fields){
        foreach ($fields as $key => $value) {
            delete_post_meta($post_id, $key);
            add_post_meta($post_id, $key, $value, false);
        }
    }
    
    private function set_featured_image($post_id, $file_id) {
        global $wpdb;
        $wpdb->update($wpdb->posts, array('post_parent' => $post_id), array('ID' => $file_id));
        delete_post_meta($post_id, '_thumbnail_id');
        add_post_meta($post_id, '_thumbnail_id', $file_id, false);
    }
    
    private function add_tracking_code($article_content){
        return $article_content.' [rumt]rum_content_id[/rumt]';
    }
}
?>
