<?php    
    require(RCS_VIEWS_PATH.'/shared/libraries.php');
?>
<div class="wrap">
    <div id="icon-rcs-general" class="icon32"><br/></div>
    <h2>
        Mapping
    </h2>  
<?php    
    require(RCS_VIEWS_PATH.'/shared/errors.php');    
    require(RCS_VIEWS_PATH . '/shared/nav.php');
?>
    <p class="about-description help-text">For each content type supported in the RollUp Media (RUM) Content Suite, you can define the ingest mapping for each element.</p>
    <p class="about-description help-text">A content item (article, blog posts, etc.) gets provided by the API when in status &QUOT;Ready to be published&QUOT; in the RUM Content Suite. Once successfully ingested into your WP instance, the status is changed to &QUOT;Published&QUOT; in the RUM Content Suite. This ensures each content item is ingested only once into WP.</p>
    <br class="clear"/>
    <div class="tablenav top">
        <a href="?page=rcsnewmapping" class="add-new-h2"><span>Add New</span></a>
    </div>
    <table class="wp-list-table widefat">
        <thead>
            <tr>
            <th>ID</th>
            <th>RUM Content Type</th>
            <th>WP Post Type</th>
            <th>Actions</th>
            </tr>
        </head>
        <tbody>
            <?php foreach($mapping_list as $mapping){ ?>
        <tr>
            <td><?php echo $mapping->id;?></td>
            <td><?php echo $mapping->rum_type;?></td>
            <td><?php echo $mapping->post_type;?></td>
            <td><a href="?page=rcseditmapping&mid=<?php echo $mapping->id;?>"><span>Edit</span></a>&nbsp;<a href="?page=rcsdelmapping&mid=<?php echo $mapping->id;?>"><span>Remove</span></a></td>
        </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
