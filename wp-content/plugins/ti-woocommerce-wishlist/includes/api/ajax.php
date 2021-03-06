<?php

// Init
// -----------------------------------------------------------------------
define( 'DOING_AJAX', true );
define( 'SHORTINIT', true );

// WP Load
// -----------------------------------------------------------------------

define( 'TINV_WP_ROOT', '../../../../../' );

require( '../../../../../wp-load.php' );
wp_plugin_directory_constants();
require_once( TINV_WP_ROOT . WPINC . '/class-wp-user.php' );
require_once( TINV_WP_ROOT . WPINC . '/class-wp-roles.php' );
require_once( TINV_WP_ROOT . WPINC . '/class-wp-role.php' );
require_once( TINV_WP_ROOT . WPINC . '/class-wp-session-tokens.php' );
require_once( TINV_WP_ROOT . WPINC . '/class-wp-user-meta-session-tokens.php' );
require_once( TINV_WP_ROOT . WPINC . '/formatting.php' );
require_once( TINV_WP_ROOT . WPINC . '/capabilities.php' );
//require_once ( TINV_WP_ROOT . WPINC . '/query.php' ); // - might be useful
require_once( TINV_WP_ROOT . WPINC . '/user.php' );
require_once( TINV_WP_ROOT . WPINC . '/meta.php' );

wp_cookie_constants();

require_once( TINV_WP_ROOT . WPINC . '/vars.php' );
require_once( TINV_WP_ROOT . WPINC . '/kses.php' );
require_once( TINV_WP_ROOT . WPINC . '/rest-api.php' );
require_once( TINV_WP_ROOT . WPINC . '/pluggable.php' );

// Get the nonce.
if ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
	$nonce = $_SERVER['HTTP_X_WP_NONCE'];
} else {
	wp_send_json( array( 'error' => 'Forbidden: unauthorized request.' ), 403 );
}

// Check the nonce.
$result = wp_verify_nonce( $nonce, 'wp_rest' );

if ( ! $result ) {
	wp_send_json( array( 'error' => 'Forbidden: wrong nonce.' ), 403 );
}

// Response headers
@header( 'X-Robots-Tag: noindex' );
send_nosniff_header();
nocache_headers();

// DB query.
global $wpdb;

$table       = sprintf( '%s%s', $wpdb->prefix, 'tinvwl_items' );
$table_lists = sprintf( '%s%s', $wpdb->prefix, 'tinvwl_lists' );

$data = $products = $wishlists = $results = array();

$share_key = false;

if ( is_user_logged_in() ) {
	$data['author'] = get_current_user_id();
} else {
	$share_key = filter_input( INPUT_COOKIE, 'tinv_wishlistkey' );
}

if ( ( isset( $data['author'] ) && $data['author'] ) || $share_key ) {

	$default = array(
		'count'    => 99999,
		'field'    => null,
		'offset'   => 0,
		'order'    => 'DESC',
		'order_by' => 'date',
		'external' => true,
		'sql'      => '',
	);

	foreach ( $default as $_k => $_v ) {
		if ( array_key_exists( $_k, $data ) ) {
			$default[ $_k ] = $data[ $_k ];
			unset( $data[ $_k ] );
		}
	}

	$default['offset'] = absint( $default['offset'] );
	$default['count']  = absint( $default['count'] );

	if ( is_array( $default['field'] ) ) {
		$default['field'] = '`' . implode( '`,`', $default['field'] ) . '`';
	} elseif ( is_string( $default['field'] ) ) {
		$default['field'] = array( 'ID', $default['field'] );
		$default['field'] = '`' . implode( '`,`', $default['field'] ) . '`';
	} else {
		$default['field'] = $table . '.*, ' . $table_lists . '.ID as wishlist_id, ' . $table_lists . '.status as wishlist_status, ' . $table_lists . '.title as wishlist_title, ' . $table_lists . '.share_key as wishlist_share_key';
	}

	$sql = "SELECT {$default[ 'field' ]} FROM `{$table}` INNER JOIN `{$table_lists}` ON `{$table}`.`wishlist_id` = `{$table_lists}`.`ID` AND `{$table_lists}`.`type` = 'default'";

	if ( $share_key ) {
		$sql .= " AND `{$table_lists}`.`share_key` = '{$share_key}'";
	}

	$where = '1';

	if ( ! empty( $data ) && is_array( $data ) ) {

		if ( array_key_exists( 'meta', $data ) ) {
			$product_id = $variation_id = 0;
			if ( array_key_exists( 'product_id', $data ) ) {
				$product_id = $data['product_id'];
			}
			if ( array_key_exists( 'variation_id', $data ) ) {
				$variation_id = $data['variation_id'];
			}
			$data['formdata'] = '';
			unset( $data['meta'] );
		}

		foreach ( $data as $f => $v ) {
			$s = is_array( $v ) ? ' IN ' : '=';
			if ( is_array( $v ) ) {
				foreach ( $v as $_f => $_v ) {
					$v[ $_f ] = $wpdb->prepare( '%s', $_v );
				}
				$v = implode( ',', $v );
				$v = "($v)";
			} else {
				$v = $wpdb->prepare( '%s', $v );
			}
			$data[ $f ] = sprintf( $table . '.' . '`%s`%s%s', $f, $s, $v );
		}

		$where = implode( ' AND ', $data );

		$sql .= ' WHERE ' . $where;
	}

	$sql .= sprintf( ' ORDER BY `%s` %s LIMIT %d,%d;', $default['order_by'], $default['order'], $default['offset'], $default['count'] );

	if ( ! empty( $default['sql'] ) ) {
		$replacer    = $replace = array();
		$replace[0]  = '{table}';
		$replacer[0] = $table;
		$replace[1]  = '{where}';
		$replacer[1] = $where;

		foreach ( $default as $key => $value ) {
			$i = count( $replace );

			$replace[ $i ]  = '{' . $key . '}';
			$replacer[ $i ] = $value;
		}

		$sql = str_replace( $replace, $replacer, $default['sql'] );
	}

	$results = $wpdb->get_results( $sql, ARRAY_A );

	if ( ! empty( $results ) ) {
		foreach ( $results as $product ) {
			$wishlists[ $product['wishlist_id'] ] = array(
				'ID'        => (int) $product['wishlist_id'],
				'title'     => $product['wishlist_title'],
				'status'    => $product['wishlist_status'],
				'share_key' => $product['wishlist_share_key'],
			);

		}

		foreach ( $wishlists as $wishlist ) {

			foreach ( $results as $product ) {
				if ( array_key_exists( $product['product_id'], $products ) ) {
					$products[ $product['product_id'] ][ $wishlist['ID'] ]['in'][] = (int) $product['variation_id'];
				} else {
					$products[ $product['product_id'] ][ $wishlist['ID'] ]         = $wishlist;
					$products[ $product['product_id'] ][ $wishlist['ID'] ]['in'][] = (int) $product['variation_id'];
				}

			}
		}
	}

}

$count = is_array( $results ) ? count( $results ) : 0;

$response = array(
	'products' => $products,
	'counter'  => $count,
);

wp_send_json( $response );
