<?php
/*
 * Plugin Name: Live Auto Refresh
 * Description: Instantly reloads the browser when any theme file code is edited during development or when a content edit is saved.
 * Plugin URI:  https://www.andrewperron.com/live-auto-refresh/
 * Version:     1.0
 * Author:      Andrew Perron
 * Author URI:  https://www.andrewperron.com/live-auto-refresh/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: perron
 * Domain Path: /languages
 */
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
function perron_live_auto_refresh() {
    wp_enqueue_script( 'perron-live-auto-refresh', plugins_url( 'live-auto-refresh.min.js', __FILE__ ), array(), false, true );
}
add_action( 'wp_enqueue_scripts', 'perron_live_auto_refresh' );

function perron_get_theme_files_hash() {
    //$theme_dir = get_template_directory(); /* theme dir */
    $theme_dir = WP_CONTENT_DIR; /* /wp-content/ */
	$dir  = new RecursiveDirectoryIterator($theme_dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);
	
    $hashes = array();
	foreach ($files as $file) {
		if ($file->isFile()) {
			$path = $file->getPathname();
			if (str_contains($path, '/wp-content/themes/'.get_template())) {
			//if ((str_contains($path, '/wp-content/themes/'.get_template())) || (str_contains($path, '/wp-content/plugins/'))) {
				$hashes[$path] = md5_file($path);
			}
		}
	}
	
    $changedFile = '';
    $oldHashes = get_option('perron_theme_files_hashes', array());
    foreach ($hashes as $filename => $hash) {
        if (!isset($oldHashes[$filename]) || $oldHashes[$filename] !== $hash) {
            $changedFile = $filename;
            break;
        }
    }
    
    update_option('perron_theme_files_hashes', sanitize_text_field($hashes));
    
    return array(
        'hash' => md5(implode('', $hashes)),
        'changedFile' => $changedFile,
		'postModifiedTime' => get_option('perron_post_modified_time'),
    );
}

function perron_auto_refresh_ajax() {
	$themeFilesHash = perron_get_theme_files_hash();
	echo wp_kses_data(json_encode($themeFilesHash));
	wp_die();
}
add_action( 'wp_ajax_auto_refresh', 'perron_auto_refresh_ajax' );
add_action( 'wp_ajax_nopriv_auto_refresh', 'perron_auto_refresh_ajax' );

function perron_auto_refresh_localize_script() {
	wp_localize_script(
		'perron-live-auto-refresh',
		'autoRefresh',
		array(
			'ajaxurl' => esc_attr(admin_url( 'admin-ajax.php' )),
			'status' => (int) esc_attr(get_option('perron_auto_refresh_status', 1)),
			'postModifiedTime' => esc_attr(get_option('perron_post_modified_time')),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'perron_auto_refresh_localize_script' );

function perron_auto_refresh_save_post($post_id) {
    update_option('perron_post_modified_time', time());
}
add_action( 'save_post', 'perron_auto_refresh_save_post' );
if (class_exists('ACF')) {
	add_action( 'acf/save_post', 'perron_auto_refresh_save_post', 20 );
}

function perron_auto_refresh_toolbar_link($wp_admin_bar) {
	if( !is_super_admin() || !is_admin_bar_showing() || is_admin() ) return;
	
	if (get_option('perron_auto_refresh_status', 1)) {
	    $title = __('Auto Refresh');
	    $status = 0;
		$autorefreshbuttonclass = "autorefreshbuttonenabled";
		$autorefreshalert = "Disabling Auto Refresh";
	} else {
	    $title = __('Auto Refresh');
	    $status = 1;
		$autorefreshbuttonclass = "autorefreshbuttondisabled";
		$autorefreshalert = "Enabling Auto Refresh";
	}
	
	$args = array(
	    'id' => 'autorefresh',
	    'title' => $title,
	    'href' => add_query_arg(array('perron_auto_refresh_status'=>$status)),
	    'meta' => array(
			'class' => $autorefreshbuttonclass,
			'onclick' => 'alert("'.$autorefreshalert.'");',
	        )
	    );
	    
	if (isset($_GET['perron_auto_refresh_status'])){
		update_option('perron_auto_refresh_status', sanitize_text_field($_GET['perron_auto_refresh_status']));
	}

	$wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'perron_auto_refresh_toolbar_link', 999);

function perron_auto_refresh_toolbar_style() {
	echo '<style>/*AUTO REFRESH*/.autorefreshbuttonenabled a{background:green !important;color:white !important;}.autorefreshbuttonpaused a{background:orange !important;color:white !important;}.autorefreshbuttondisabled a{background:red !important;color:white !important;}</style>';
}
add_action('wp_head', 'perron_auto_refresh_toolbar_style', 100);

function perron_action_links( $links ) {
	$links['donate'] = '<a href="https://paypal.me/perronuk/" target="_blank">Donate</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'perron_action_links', 10, 1 );




register_deactivation_hook(__FILE__, 'perron_auto_refresh_deactivate');
function perron_auto_refresh_deactivate() {
    delete_option('perron_theme_files_hashes');
    delete_option('perron_auto_refresh_status');
    delete_option('perron_post_modified_time');
}