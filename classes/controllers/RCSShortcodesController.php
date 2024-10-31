<?php
class RCSShortcodesController{
     private static $TRACKING_URL = 'http://tt.rollupmedia.com/assets/js/rum.js';
     
     function RCSShortcodesController(){
         @add_shortcode('rumt', array($this, 'rumtracking_shortcode'));
     }
    
     function rumtracking_shortcode($atts, $text){
         global $post;
         $rcsid = get_post_meta($post->ID, 'rum_content_id', true);
         return '<script type="text/javascript" src="'.RCSShortcodesController::$TRACKING_URL.'?articleId='.$rcsid.'"></script>';
     }
}
?>