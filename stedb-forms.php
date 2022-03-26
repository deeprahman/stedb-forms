<?php
/**
 * Plugin Name: STEdb Forms
 * Plugin URI: https://stedb.com/stedb-forms/
 * Description: Drag and drop form builder, send button with social integration which guarantees no fake leads, no fake emails submitting your forms and FREE email marketing automation platforms.
 * Version: 2.0.0
 * Author: STEdb
 * Author URI: https://stedb.com
 * Text Domain: stedb-forms
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();
define( 'STEDB_FORMS_VERSION', 'develop-' . time() );
define( 'STEDB_FORMS_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'STEDB_FORMS_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Includes
 * classes
 */
include_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-api-client.php';
include_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-wordpress-base.php';
include_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-wordpress-admin.php';
include_once STEDB_FORMS_DIR_PATH . '/includes/class-stedb-forms-wordpress-public.php';

/**
 * Language
 * load
 */
load_plugin_textdomain( 'stedb-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Install STEdb Forms
 * - create wpdb table
 * - create api account
 */
function stedb_forms_install() {
	stedb_forms_create_wpdb_tables();
}

register_activation_hook( __FILE__, 'stedb_forms_install' );

/**
 * STEdb Forms
 * create wpdb tables
 */
function stedb_forms_create_wpdb_tables() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	/** create lists (ie forms) table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'stedb_forms_lists' . " (
		`id` bigint(20) NOT NULL auto_increment,
		`list_id` bigint(20) DEFAULT NULL COMMENT 'for stedb api',
		`user_id` bigint(20) DEFAULT NULL,
		`name` varchar(255) DEFAULT NULL,
		`social_links` longtext NOT NULL,
		`receiver` varchar(255) DEFAULT NULL,
		`form_builder_content` longtext,
		`date` date DEFAULT NULL,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";" );

	/** create list (ie form) reports table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'stedb_forms_list_reports' . " (
		`id` bigint(20) NOT NULL,
		`creation_date` datetime DEFAULT NULL,
		`last_subscription` datetime DEFAULT NULL,
		`count` bigint(20) DEFAULT NULL,
		`stat` longtext,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";" );

	/** create fields table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'stedb_forms_fields' . " (
		`id` bigint(20) NOT NULL auto_increment,
		`field_id` int(11) DEFAULT NULL COMMENT 'for stedb api',
		`form_id` int(11) DEFAULT NULL,
		`type`  varchar(255) DEFAULT NULL,
		`name`  varchar(255) DEFAULT NULL,
		`values` longtext DEFAULT NULL,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";" );

	/** create entries table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'stedb_forms_entries' . " (
		`id` bigint(20) NOT NULL auto_increment,
		`entry_id` bigint(20) NOT NULL 'for stedb api',
		`form_id` bigint(20) NOT NULL,
		`email` varchar(255) NOT NULL,
		`custom_fields` text,
		`date` date DEFAULT NULL,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";" );

	/** create entries temp table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'stedb_forms_entries_temp' . " (
		`id` bigint(20) NOT NULL auto_increment,
		`form_hash` varchar(255) NOT NULL,
		`form_id` bigint(20) NOT NULL,
		`token` varchar(255) NOT NULL,
		`data` text,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";" );

	/** create campaigns table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . 'stedb_forms_campaigns' . " (
		`id` bigint(20) NOT NULL auto_increment,
		`campaign_id` int(11) NOT NULL COMMENT 'for stedb api',
		`form_id` int(11) NOT NULL,
		`type` int(11) NOT NULL COMMENT '0 = regular email, 1 = autoresponder',
		`status` int(11) NOT NULL COMMENT '1 = draft, 4 = running, 3 = scheduled',
		`run_date` date DEFAULT NULL,
		`from_name` varchar(255) DEFAULT NULL,
		`from_email` varchar(255) DEFAULT NULL,
		`subject` varchar(255) DEFAULT NULL,
		`content` longtext,
		`date` date DEFAULT NULL,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";" );
}

/**
 * get stedb form shortcode
 *
 * @param $id
 * @param null $list_id
 *
 * @return false|string
 */
function stedb_forms_get_stedb_form_shortcode( $id, $list_id = null ) {
	global $wpdb;

	/** check id */
	if ( empty( $id ) ) {
		return false;
	}

	/** check list id */
	if ( empty( $list_id ) ) {
		$list_id = $wpdb->get_var( $wpdb->prepare( 'SELECT list_id FROM ' . $wpdb->prefix . 'stedb_forms_lists WHERE id = %d LIMIT 1', absint( $id ) ) );

		if ( empty( $list_id ) ) {
			return false;
		}
	}

	$shortcode_pairs = array( 'id' => '', 'list_id' => '' );
	$shortcode_atts  = array( 'id' => $id, 'list_id' => $list_id );

	return stedb_forms_get_stedb_form_shortcode_str( 'stedb_form', $shortcode_pairs, $shortcode_atts );
}

/**
 * get stedb form shortcode str
 *
 * @param $shortcode
 * @param array $pairs
 * @param array $atts
 * @param string $content
 *
 * @return string
 */
function stedb_forms_get_stedb_form_shortcode_str( $shortcode, $pairs = array(), $atts = array(), $content = '' ) {

	foreach ( $pairs as $name => $default ) {
		if ( array_key_exists( $name, $atts ) ) {
			$pairs[ $name ] = $atts[ $name ];
		} else {
			$pairs[ $name ] = $default;
		}
	}

	$out = '[' . $shortcode;

	if ( empty( $pairs ) ) {
		/** get default shortcode pairs */
		$pairs = array();
	}

	foreach ( $pairs as $name => $value ) {
		$out .= ' ' . $name . '="' . $value . '"';
	}

	if ( ! empty( $content ) ) {
		if ( ! is_string( $content ) ) {
			if ( is_bool( $content ) ) {
				$content = '';
			}
			$content = strval( $content );
		}

		$out .= ']';
		$out .= $content;
		$out .= '[/' . $shortcode . ']';
	} else {
		$out .= ']';
	}

	return $out;
}

/**
 * get campaign type
 *
 * @param $type
 *
 * @return string
 */
function stedb_forms_get_campaign_type( $type ) {
	$types = array(
		0 => esc_html__( 'Regular Email', 'stedb-forms' ),
		1 => esc_html__( 'Autoresponder', 'stedb-forms' ),
	);

	return $types[ intval( $type ) ];
}

/**
 * get campaign status
 *
 * @param $status
 *
 * @return string
 */
function stedb_forms_get_campaign_status( $status ) {
	$statuses = array(
		1 => esc_html__( 'Draft', 'stedb-forms' ),
		3 => esc_html__( 'Scheduled', 'stedb-forms' ),
		4 => esc_html__( 'Running', 'stedb-forms' ),
	);

	return $statuses[ intval( $status ) ];
}

/**
 * STEdb Forms
 * load classes
 */
function stedb_forms_load() {
	new STEDB_Forms_WordPress_Admin();
	new STEDB_Forms_WordPress_Public();
}

add_action( 'init', 'stedb_forms_load' );