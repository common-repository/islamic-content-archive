<?php
/**
 * Islamic Content Archive
 *
 * Plugin Name: Islamic Content Archive
 * Plugin URI:  https://wordpress.org/plugins/islamic-content-archive/
 * Description: Islamic Content Archive is a plugin that allows you to get the content (articles, videos, audios) of 27 Islamic websites in different languages using Json API. The data is stored directly in the database of your site according to the category and the language.
 * Version:     2.3.3
 * Author:      EDC TEAM (E-Da`wah Committee)
 * Author URI:  https://edc.org.kw
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.4
*/

define('ICA_PATH',plugin_dir_path( __FILE__ ));
define('ICA_URL',plugin_dir_url( __FILE__ ));
define('ICA_Page_SLUG','islamic_content_archive');
define('ICA_Input_SLUG','ica_');
define('ICA_LIB','lib');
define('ICA_DS','/');
define('ICA_CONTROLLERS','controllers');
define('ICA_MODELS','models');
define('ICA_VIEWS','views');
define('ICA_DB_Table', 'ica_categories');
define('ICA_VERSION','1.0');

define('ICA_Logourl',ICA_URL.'style'.ICA_DS.'images'.ICA_DS.'logo'.ICA_DS);
define('ICA_Iconpath',ICA_PATH.'style'.ICA_DS.'images'.ICA_DS.'icons'.ICA_DS);
define('ICA_Iconurl',ICA_URL.'style'.ICA_DS.'images'.ICA_DS.'icons'.ICA_DS);
define('ICA_Flagspath',ICA_PATH.'style'.ICA_DS.'images'.ICA_DS.'flags'.ICA_DS);
define('ICA_Flagsurl',ICA_URL.'style'.ICA_DS.'images'.ICA_DS.'flags'.ICA_DS);

define('ICA_Controlerspath',ICA_PATH.'controllers'.ICA_DS);
define('ICA_Modelspath',ICA_PATH.'models'.ICA_DS);
define('ICA_Viewspath',ICA_PATH.'views'.ICA_DS);
define('ICA_Layoutpath',ICA_PATH.'views'.ICA_DS.'layout'.ICA_DS);
define('ICA_Langpath',ICA_PATH.'views'.ICA_DS.'lang'.ICA_DS);

function ICA_plugin_install(){
	if(get_bloginfo('language') == "ar"){
		$default_lang = 'ara';
		$source = 'رابط المصدر';
	}else{
		$default_lang = 'eng';
		$source = 'Soucre Link';
	}
	add_option(ICA_Input_SLUG.'language', $default_lang);
	add_option(ICA_Input_SLUG.'source', $source);
	add_option(ICA_Input_SLUG.'cronjobtime', 'everyhour');
	add_option(ICA_Input_SLUG.'version', ICA_VERSION);
}

function ICA_plugin_uninstall(){
	$options = get_option(ICA_Input_SLUG.'language');
 	if( is_array($options) && $options['uninstall'] === true){
		delete_option(ICA_Input_SLUG.'language');
		delete_option(ICA_Input_SLUG.'source');
		delete_option(ICA_Input_SLUG.'cronjobtime');
		delete_option(ICA_Input_SLUG.'version');
	}
}

function createTable(){
	global $wpdb;
	$table_name = $wpdb->prefix . ICA_DB_Table;

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
			ica_key varchar(50) NOT NULL,
			ica_value longtext NOT NULL,
			PRIMARY KEY  (id)
			);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	}
}

function ica_plugin_styles() {
	$ulr =  ICA_URL.'style/css/style.css';
	echo "<link rel='stylesheet' href='$ulr' type='text/css' media='all' />";
}

register_activation_hook(__FILE__,'ICA_plugin_install');
register_deactivation_hook(__FILE__, 'ICA_plugin_uninstall');
add_action( 'plugins_loaded', 'createTable');
add_action( 'admin_head', 'ica_plugin_styles' );

include_once(ICA_PATH.ICA_DS.'load.php');
