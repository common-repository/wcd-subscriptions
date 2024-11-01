<?php
/**
 * Provide a admin settings page
 *
 * This file is used to markup the admin settings of the plugin.
 *
 * @since 1.0.0
 * @package  wcd-subscriptions
 * @subpackage wcd-subscriptions/admin/partials/templates
 */

$current_tab = isset( $_GET['sys_status_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sys_status_tab'] ) ) : 'wordpress-configuration';

$wordpress_version = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( '-', 'wcd-subscriptions' );

$wp_active_plugins = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( '-', 'wcd-subscriptions' );

$wp_multisite = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'wcd-subscriptions' ) : __( 'No', 'wcd-subscriptions' );

$wp_debug_enabled = defined( 'WP_DEBUG' ) ? __( 'Yes', 'wcd-subscriptions' ) : __( 'No', 'wcd-subscriptions' );

$wp_cache_enabled = defined( 'WP_CACHE' ) ? __( 'Yes', 'wcd-subscriptions' ) : __( 'No', 'wcd-subscriptions' );

$wp_users = function_exists( 'count_users' ) ? count_users() : __( '-', 'wcd-subscriptions' );

$wp_posts = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : 0;

$php_version = function_exists( 'phpversion' ) ? phpversion() : __( '-', 'wcd-subscriptions' );

$server_ip = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

$server_port = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

$web_server = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

$server_path = defined( 'ABSPATH' ) ? ABSPATH : __( '-', 'wcd-subscriptions' );

$server_hostname = function_exists( 'gethostname' ) ? gethostname() : __( '-', 'wcd-subscriptions' );

$os = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( '-', 'wcd-subscriptions' );

$mail_server_url = get_option( 'mailserver_url', 'NA' );
?>
<?php
if ( 'wordpress-configuration' == $current_tab ) {
	?>
<a class="sys_st_tab sys_st_active_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=wordpress-configuration">Wordpress Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=server-configuration">Server Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=database-configuration">Database Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=plugin-requirements">Plugin Requirements</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=site-details">Site Details</a>
	<?php
}
?>
<?php
if ( 'server-configuration' == $current_tab ) {
	?>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=wordpress-configuration">Wordpress Configuration</a>
<a class="sys_st_tab sys_st_active_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=server-configuration">Server Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=database-configuration">Database Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=plugin-requirements">Plugin Requirements</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=site-details">Site Details</a>
	<?php
}
?>
<?php
if ( 'database-configuration' == $current_tab ) {
	?>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=wordpress-configuration">Wordpress Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=server-configuration">Server Configuration</a>
<a class="sys_st_tab sys_st_active_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=database-configuration">Database Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=plugin-requirements">Plugin Requirements</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=site-details">Site Details</a>
	<?php
}
?>
<?php
if ( 'plugin-requirements' == $current_tab ) {
	?>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=wordpress-configuration">Wordpress Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=server-configuration">Server Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=database-configuration">Database Configuration</a>
<a class="sys_st_tab sys_st_active_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=plugin-requirements">Plugin Requirements</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=site-details">Site Details</a>
	<?php
}
?>
<?php
if ( 'site-details' == $current_tab ) {
	?>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=wordpress-configuration">Wordpress Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=server-configuration">Server Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=database-configuration">Database Configuration</a>
<a class="sys_st_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=plugin-requirements">Plugin Requirements</a>
<a class="sys_st_tab sys_st_active_tab" href="admin.php?page=wcd_woo_subs_page&wcd_tab=wcd-woo-subs-system-status&sys_status_tab=site-details">Site Details</a>
	<?php
}
?>
<?php
if ( 'wordpress-configuration' == $current_tab ) {
	?>
<table class="system_status_table">
	<tr class="system_status_tr">
	<th class="system_status_td">WP entities</th>
	<th class="system_status_td">WP values</th>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">wp_version</td>
		<td class="system_status_td"><?php echo esc_html( $wordpress_version ); ?> </td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">wp_debug_enabled</td>
		<td class="system_status_td"><?php echo esc_html( $wp_debug_enabled ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">active_plugins</td>
		<td class="system_status_td"><?php echo esc_html( $wp_active_plugins ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">is site multisite?</td>
		<td class="system_status_td"><?php echo esc_html( $wp_multisite ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">wp_cache_enabled</td>
		<td class="system_status_td"><?php echo esc_html( $wp_cache_enabled ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">wp_posts</td>
		<td class="system_status_td"><?php echo esc_html( $wp_posts ); ?></td>
	</tr>
</table>
	<?php
}
?>

<?php
if ( 'server-configuration' == $current_tab ) {
	?>
<table class="system_status_table">
	<tr class="system_status_tr">
		<th class="system_status_th">Server entities</th>
		<th class="system_status_th">Server values</th>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">php_version</td>
		<td class="system_status_td"><?php echo esc_html( $php_version ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">server_ip</td>
		<td class="system_status_td"><?php echo esc_html( $server_ip ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">server_port</td>
		<td class="system_status_td"><?php echo esc_html( $server_port ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">web_server</td>
		<td class="system_status_td"><?php echo esc_html( $web_server ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">server_path</td>
		<td class="system_status_td"><?php echo esc_html( $server_path ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">system_os</td>
		<td class="system_status_td"><?php echo esc_html( $os ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">server_hostname</td>
		<td class="system_status_td"><?php echo esc_html( $server_hostname ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">mail_server_url</td>
		<td class="system_status_td"><?php echo esc_html( $mail_server_url ); ?></td>
	</tr>
</table>
	<?php
}
?>

<?php
if ( 'database-configuration' == $current_tab ) {
	global $wpdb;
	$size = 0;
	$rows = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );

	if ( $wpdb->num_rows > 0 ) {
		foreach ( $rows as $row ) {
			$size += $row['Data_length'] + $row['Index_length'];
		}
	}
	$decimals = 2;
	$mbytes = number_format( $size / ( 1024 * 1024 ), $decimals );
	?>
<table class="system_status_table">
	<tr class="system_status_tr">
		<th class="system_status_th">Database entities</th>
		<th class="system_status_th">Database values</th>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">db_version</td>
		<td class="system_status_td"><?php echo esc_html( $wpdb->db_version() ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">Total database size</td>
		<td class="system_status_td"><?php echo esc_html( $mbytes . 'MB' ); ?></td>
	</tr>
</table>
	<?php
}
?>

<?php
if ( 'plugin-requirements' == $current_tab ) {
	?>
<table class="system_status_table">
	<tr class="system_status_tr">
		<th class="system_status_th">Entity</th>
		<th class="system_status_th">Should be</th>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">WordPress version</td>
		<td class="system_status_td">6.1.1</td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">WooCommerce versison</td>
		<td class="system_status_td">7.4.1</td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">PHP Version</td>
		<td class="system_status_td">7.2 or higher</td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">Wordpress Cron</td>
		<td class="system_status_td">Enabled</td>
	</tr>
</table>
	<?php
}
?>

<?php
if ( 'site-details' == $current_tab ) {

	$response = wp_remote_get( 'https://ifconfig.co/ip', array() );
	$ip = $response['body'];
	?>
<table class="system_status_table">
	<tr class="system_status_tr">
		<th class="system_status_th">Site variables</th>
		<th class="system_status_th">Values</th>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">Site URL</td>
		<td class="system_status_td"><?php echo esc_html( get_site_url() ); ?></td>
	</tr>
	<tr class="system_status_tr">
		<td class="system_status_td">Output IP</td>
		<td class="system_status_td"><?php echo esc_html( $ip ); ?></td>
	</tr>
</table>
	<?php
}
?>
