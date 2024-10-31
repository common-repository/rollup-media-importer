<?php
/*
Plugin Name: RUM Content Suite
Description: Integration with the RollUp Media Content Suite
Version: 1.0
Plugin URI: http://suites.rollupmedia.com/
Author URI: http://www.rollupmedia.com/
Author: RollUp Media Ltd.
*/

define('RCS_PLUGIN_TITLE', 'RUM Content Suite');
define('RCS_PLUGIN_NAME', 'RUM Content Suite');
define('RCS_PATH', WP_PLUGIN_DIR.'/rollup-media-importer');
define('RCS_VIEWS_PATH', RCS_PATH.'/classes/views');
define('RCS_CONTROLLERS_PATH', RCS_PATH.'/classes/controllers');
define('RCS_MODELS_PATH', RCS_PATH.'/classes/models');
define('RCS_HELPERS_PATH', RCS_PATH.'/classes/helpers');

global $rcs_siteurl;
global $rcs_sync_start;
$rcs_sync_start = false;
$rcs_siteurl = get_bloginfo('url');
define('RCS_URL', WP_PLUGIN_URL.'/rollup-media-importer');
define('RCS_JS_PATH', RCS_URL.'/js');

// Instansiate Helpers
require_once(RCS_HELPERS_PATH. '/RCSAppHelper.php');
require_once(RCS_HELPERS_PATH. '/RCSApikeyHelper.php');
require_once(RCS_HELPERS_PATH. '/RCSMappingHelper.php');
require_once(RCS_HELPERS_PATH. '/RCSConfigHelper.php');
require_once(RCS_HELPERS_PATH. '/RCSWPHelper.php');
require_once(RCS_HELPERS_PATH. '/RCSIngestHelper.php');
require_once(RCS_HELPERS_PATH. '/RCSLogsHelper.php');

global $rcs_app_helper;
global $rcs_apikey_helper;
global $rcs_mapping_helper;
global $rcs_config_helper;
global $rcs_wp_helper;
global $rcs_ingest_helper;
global $rcs_logs_helper;

$rcs_app_helper = new RCSAppHelper();
$rcs_apikey_helper = new RCSApikeyHelper();
$rcs_mapping_helper = new RCSMappingHelper();
$rcs_config_helper = new RCSConfigHelper();
$rcs_wp_helper = new RCSWPHelper();
$rcs_ingest_helper = new RCSIngestHelper();
$rcs_logs_helper = new RCSLogsHelper();

/***** SETUP SETTINGS OBJECT *****/
require_once(RCS_MODELS_PATH.'/RCSSettings.php'); 
global $rcs_settings;
$rcs_settings = new RCSSettings();

// Instansiate Models
require_once(RCS_MODELS_PATH.'/RCSDb.php'); 

global $rcsdb;

$rcsdb = new RCSDb();

// Instansiate Controllers
require_once(RCS_CONTROLLERS_PATH . '/RCSAppController.php');
require_once(RCS_CONTROLLERS_PATH . '/RCSApikeyController.php');
require_once(RCS_CONTROLLERS_PATH . '/RCSMappingController.php');
require_once(RCS_CONTROLLERS_PATH . '/RCSLogFileController.php');
require_once(RCS_CONTROLLERS_PATH . '/RCSSyncIngestController.php');
require_once(RCS_CONTROLLERS_PATH . '/RCSShortcodesController.php');

global $rcs_app_controller;
global $rcs_apikey_controller;
global $rcs_mapping_controller;
global $rcs_logfile_controller;
global $rcs_sync_controller;
global $rcs_shortcodes_controller;

$rcs_app_controller = new RCSAppController();
$rcs_apikey_controller = new RCSApikeyController();
$rcs_mapping_controller = new RCSMappingController();
$rcs_logfile_controller = new RCSLogFileController();
$rcs_sync_controller = new RCSSyncIngestController();
$rcs_shortcodes_controller = new RCSShortcodesController();
?>
