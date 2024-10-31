<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class RCSDb{
    private $frecuency;
    private $rum_content_type;
    private $platform_fields;
    private $ingest_state;
    private $basic_post_data;
    private $general_settings;
    private $mapping;
    private $rum_wptype_mapping;
    private $rum_log;
    
    function RCSDb(){
        global $wpdb;
        $this->frecuency = $wpdb->prefix . "rcs_frecuency";
        $this->rum_content_type = $wpdb->prefix . "rcs_rum_content_type";
        $this->platform_fields = $wpdb->prefix . "rcs_platform_fields";
        $this->ingest_state = $wpdb->prefix . "rcs_ingest_state";
        $this->basic_post_data = $wpdb->prefix . "rcs_basic_post_data";
        $this->general_settings = $wpdb->prefix . "rcs_general_settings";
        $this->mapping = $wpdb->prefix . "rcs_mapping";
        $this->rum_wptype_mapping = $wpdb->prefix . "rcs_rum_wptype_mapping";
        $this->rum_log = $wpdb->prefix . "rcs_rum_log";
    }
    
    function upgrade(){
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $this->frecuency_table();
        $this->rum_content_type_table();
        $this->rum_platform_fields();
        $this->ingest_state();
        $this->basic_post_data_table();
        $this->general_settings_table();
        $this->mapping_table();
        $this->logs_table();

        do_action('rcs_after_install');
    }
    
    function frecuency_table(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        //0 false 1 true
        $sql = "CREATE TABLE {$this->frecuency} (
                id bigint(20) NOT NULL auto_increment,
                name text NOT NULL,
                slug varchar(255) NOT NULL,
                pos bigint(20) NOT NULL default 0,
                isdefault tinyint(1) NOT NULL default 0,
                PRIMARY KEY  (id),
                KEY key_rcs_frec_slug (slug),
                UNIQUE KEY u_rcs_frec_slug (slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
        
        //Delete elements from Table;
        $del_query = 'DELETE FROM `'. $this->frecuency.'`';
        $wpdb->query($del_query);
        
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every 10 min', 'slug' => 'ten-minutes','pos'=>1));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every 30 min', 'slug' => 'thirty-minutes','pos'=>2));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every hour', 'slug' => 'hourly','pos'=>3));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every 2 hours', 'slug' => 'two-hourly','pos'=>4));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every 3 hours', 'slug' => 'three-hourly','pos'=>5));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every 6 hours', 'slug' => 'six-hourly','pos'=>6));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'every 12 hours', 'slug' => 'twelve-hourly','pos'=>7));
        $rows_affected = $wpdb->insert( $this->frecuency, array( 'name' => 'daily', 'slug' => 'daily','pos'=>8 ,'isdefault'=>1));
    }
    
    function rum_content_type_table(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->rum_content_type} (
                id bigint(20) NOT NULL auto_increment,
                name text NOT NULL,
                slug varchar(255) NOT NULL,
                pos bigint(20) NOT NULL default 0,
                PRIMARY KEY  (id),
                KEY key_rcs_rct_slug (slug),
                UNIQUE KEY u_rcs_rct_slug (slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
        
        //Delete elements from Table;
        $del_query = 'DELETE FROM `'. $this->rum_content_type.'`';
        $wpdb->query($del_query);
        
        $rows_affected = $wpdb->insert( $this->rum_content_type, array( 'name' => 'Article', 'slug' => 'article','pos'=>1));
        $rows_affected = $wpdb->insert( $this->rum_content_type, array( 'name' => 'Blog Post', 'slug' => 'blog-post','pos'=>2));
    }
    
    function rum_platform_fields(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->platform_fields} (
                id bigint(20) NOT NULL auto_increment,
                name text NOT NULL,
                slug varchar(255) NOT NULL,
                pos bigint(20) NOT NULL default 0,
                PRIMARY KEY  (id),
                KEY key_rcs_rpf_slug (slug),
                UNIQUE KEY u_rcs_rpf_slug (slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
        
        //Delete elements from Table;
        $del_query = 'DELETE FROM `'. $this->platform_fields.'`';
        $wpdb->query($del_query);
        
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'Header', 'slug' => 'header','pos'=>1));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'Title', 'slug' => 'title','pos'=>2));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'Author', 'slug' => 'byLine','pos'=>3));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'Content', 'slug' => 'text','pos'=>4));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'SEO Title', 'slug' => 'seotitle','pos'=>5));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'SEO Meta-Description', 'slug' => 'seometaDescription','pos'=>6));   
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'SEO Keywords', 'slug' => 'seokeywords','pos'=>7));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'OG Description', 'slug' => 'ogDescription','pos'=>8));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'Google Description', 'slug' => 'googlePlusDescription','pos'=>9));
        $rows_affected = $wpdb->insert( $this->platform_fields, array( 'name' => 'Google Plus Link', 'slug' => 'googlePlusLink','pos'=>10));
        
    }
    
    function ingest_state(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->ingest_state} (
                id bigint(20) NOT NULL auto_increment,
                name text NOT NULL,
                slug varchar(255) NOT NULL,
                pos bigint(20) NOT NULL default 0,
                isdefault tinyint(1) NOT NULL default 0,
                PRIMARY KEY  (id),
                KEY key_rcs_ris_slug (slug),
                UNIQUE KEY u_rcs_ris_slug (slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
                
        //Delete elements from Table;
        $del_query = 'DELETE FROM `'. $this->ingest_state.'`';
        $wpdb->query($del_query);
        
        $rows_affected = $wpdb->insert( $this->ingest_state, array( 'name' => 'Publish', 'slug' => 'publish','pos'=>1));
        $rows_affected = $wpdb->insert( $this->ingest_state, array( 'name' => 'Pending', 'slug' => 'pending','pos'=>2, 'isdefault' => 1));
        $rows_affected = $wpdb->insert( $this->ingest_state, array( 'name' => 'Draft', 'slug' => 'draft','pos'=>3));
        $rows_affected = $wpdb->insert( $this->ingest_state, array( 'name' => 'Auto Draft', 'slug' => 'auto-draft','pos'=>4));
        $rows_affected = $wpdb->insert( $this->ingest_state, array( 'name' => 'Future', 'slug' => 'future','pos'=>5));
        $rows_affected = $wpdb->insert( $this->ingest_state, array( 'name' => 'Private', 'slug' => 'private','pos'=>6));
    }
    
    function basic_post_data_table(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->basic_post_data} (
                id bigint(20) NOT NULL auto_increment,
                name text NOT NULL,
                slug varchar(255) NOT NULL,
                PRIMARY KEY  (id),
                KEY key_rcs_bpd_slug (slug),
                UNIQUE KEY u_rcs_bpd_slug (slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
        
        //Delete elements from Table;
        $del_query = 'DELETE FROM `'. $this->basic_post_data.'`';
        $wpdb->query($del_query);
        
        $rows_affected = $wpdb->insert( $this->basic_post_data, array( 'name' => 'Post Title', 'slug' => 'post_title'));
        $rows_affected = $wpdb->insert( $this->basic_post_data, array( 'name' => 'Post Content', 'slug' => 'post_content'));
    }
    
    function general_settings_table(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->general_settings} (
                id bigint(20) NOT NULL auto_increment,
                slug varchar(255) NOT NULL,
                value varchar(255) NOT NULL,
                PRIMARY KEY  (id),
                KEY key_rcs_pgs_slug (slug),
                UNIQUE KEY u_rcs_pgs_slug (slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
    }
    
    function mapping_table(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->mapping} (
                id bigint(20) NOT NULL auto_increment,
                rum_type varchar(20) NOT NULL,
                post_type varchar(20) NOT NULL,
                PRIMARY KEY  (id),
                KEY key_rcs_prt_slug (rum_type),
                KEY key_rcs_ppt_slug (post_type),
                UNIQUE KEY u_rcs_prt_slug (rum_type)
              ) {$charset_collate};";
                    
        dbDelta($sql);
        
        $sql = "CREATE TABLE {$this->rum_wptype_mapping} (
                id bigint(20) NOT NULL auto_increment,
                idmapping bigint(20) NOT NULL,
                rum_slug varchar(255) NOT NULL,
                wp_slug varchar(255) NOT NULL,
                PRIMARY KEY  (id),
                KEY key_rcs_fim_slug (idmapping),
                KEY key_rcs_prs_slug (rum_slug),
                KEY key_rcs_pws_slug (wp_slug)
              ) {$charset_collate};";
                    
        dbDelta($sql);
    }
    
    function logs_table(){
        global $wpdb;
        $charset_collate = '';
        if( $wpdb->has_cap( 'collation' ) ){
            if( !empty($wpdb->charset) )
                $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
            if( !empty($wpdb->collate) )
                $charset_collate .= " COLLATE $wpdb->collate";
        }
        
        $sql = "CREATE TABLE {$this->rum_log} (
                id bigint(20) NOT NULL auto_increment,
                type varchar(20) NOT NULL,
                message text NULL default '',
                rum_content_id varchar(50) NULL default '',
                wp_post_id bigint(20) NULL default 0,
                created_at timestamp DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY key_rcs_lt_slug (type)
              ) {$charset_collate};";
                    
        dbDelta($sql);
    }


    function uninstall(){
        if(!current_user_can('administrator')){
            global $rcs_settings;
            wp_die($rcs_settings->admin_permission);
        }
        
        global $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS '. $this->frecuency);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->rum_content_type);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->platform_fields);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->platform_fields);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->ingest_state);        
        $wpdb->query('DROP TABLE IF EXISTS '. $this->basic_post_data);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->general_settings);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->mapping);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->rum_wptype_mapping);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->rum_log);
        
        do_action('rcs_after_uninstall');
    }
}
?>
