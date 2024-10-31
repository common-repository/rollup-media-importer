<?php    
    require(RCS_VIEWS_PATH.'/shared/libraries.php');
?>
<div class="wrap">
    <div id="icon-rcs-general" class="icon32"><br/></div>
    <h2>
        RUM Content Suite Logs
    </h2>  
<?php    
    require(RCS_VIEWS_PATH.'/shared/errors.php');    
    require(RCS_VIEWS_PATH . '/shared/nav.php');
?>
    <p class="about-description help-text">Please find the latest ingest activities listed below with a direct link to the content in WP.</p>
    <br class="clear"/>
    <form method="post" name="logs-query-form">
    <div class="tablenav top">
        <div class="alignleft actions">
            <select name="et">
                <option selected="selected" value="">Show all error types</option>
                <?php 
                foreach(RCSLogsHelper::$LOGTYPES as $key=>$value){
                    $sel = '';
                    if($error_type == $key){
                        $sel = 'selected="selected"';
                    }
                ?>
                <option value="<?php echo $key;?>" <?php echo $sel;?>><?php echo $value;?></option>
                <?php
                }
                ?>
            </select>
            <input type="hidden" name="page" value="<?php echo $current_page;?>">
            <input type="submit" name="" id="post-query-submit" class="button" value="Filter">
        </div>
        
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $pagination['total'] ?> items</span>
            <?php if($pagination['pages'] > 1){ ?>
            <span class="pagination-links"><a class="first-page disabled" title="Go to the first page" href="<?php echo admin_url( 'admin.php');?>?page=rcslogfile&p=1<?php if(!empty($error_type)){?>&et=<?php echo $error_type; }?>">«</a>
                <a class="prev-page disabled" title="Go to the previous page" href="<?php echo admin_url( 'admin.php');?>?page=rcslogfile&p=<?php echo $prev_page;?><?php if(!empty($error_type)){?>&et=<?php echo $error_type; }?>">‹</a>
                <span class="paging-input"><input class="current-page" title="Current page" type="text" name="p" value="<?php echo $actual_page;?>" size="1"> of <span class="total-pages"><?php echo $pagination['pages'];?></span></span>
                <a class="next-page" title="Go to the next page" href="<?php echo admin_url( 'admin.php');?>?page=rcslogfile&p=<?php echo $next_page;?><?php if(!empty($error_type)){?>&et=<?php echo $error_type; }?>">›</a>
                <a class="last-page" title="Go to the last page" href="<?php echo admin_url( 'admin.php');?>?page=rcslogfile&p=<?php echo $pagination['pages'];?><?php if(!empty($error_type)){?>&et=<?php echo $error_type; }?>">»</a>
            </span>
            <?php } ?> 
        </div>   
    </div>
    </form>    
    <table class="wp-list-table widefat">
        <thead>
            <tr>
            <th>ID</th>
            <th>Log Type</th>
            <th>Log Message</th>
            <th>RUM Content ID</th>
            <th>WP Post ID</th>
            <th>Date</th>
            </tr>
        </head>
        <tbody>
            <?php foreach($logs_list as $log){ ?>
        <tr>
            <td><?php echo $log->id;?></td>
            <td><?php echo $log->type;?></td>
            <td><?php echo stripslashes($log->message);?></td>
            <td><?php echo stripslashes($log->rum_content_id);?></td>
            <td><?php if($log->wp_post_id >0){?><a href="<?php echo get_permalink( $log->wp_post_id ); ?>" target="_blank" title="<?php echo get_the_title( $log->wp_post_id );?>"><?php echo $log->wp_post_id;?></a><?php }?></td>
            <td><?php echo $log->created_at;?></td>
        </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
