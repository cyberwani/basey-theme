<?php

/**
 * Change number of search results to show on page
 * @param  array $query
 * @return array
 */
function basey_search_size( $query ) {
	$post_types_list = array();
	$args = array(
		'public'   => true
	);
	$output = 'names'; // names or objects, note names is the default
	$operator = 'and'; // 'and' or 'or'
	$post_types = get_post_types( $args, $output, $operator );
	$count = count( $post_types);
	if ( $query->is_search && $query->is_main_query() && !isset( $_GET['post_type'] ) /*&& $query->query_vars['post_type'] != 'nav_menu_item'*/) // Make sure it is a search page
		$query->query_vars['posts_per_page'] = intval( get_option( 'posts_per_page' ) ) * $count; // Multiplies the setting for posts_page_page by the number of post types publicly queryable

	return $query; // Return our modified query variables
}
add_filter( 'pre_get_posts', 'basey_search_size' ); // Hook our custom function onto the request filter

/**
 * Allows pages to be publicly queryable
 * @return void
 */
function basey_page_query() {
	if ( post_type_exists( 'page' ) ) {
		global $wp_post_types;
		$wp_post_types['page']->publicly_queryable = true;
	}
}
add_action( 'init', 'basey_page_query', 1 );

/**
 * If only one result is found on search, go to that page
 * @return void
 */
function basey_one_match_redirect() {
	if (is_search() ) {
		global $wp_query;
		if ( $wp_query->post_count == 1) {
			wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
		}
	}
}
add_action( 'template_redirect', 'basey_one_match_redirect' );

/**
 * Redirects search results from /?s=query to /search/query/, converts %20 to +
 *
 * @link http://txfx.net/wordpress-plugins/nice-search/
 */
function basey_nice_search_redirect() {
	global $wp_rewrite;
	if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) {
		return;
	}

	$search_base = $wp_rewrite->search_base;
	if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
		wp_redirect(home_url("/{$search_base}/" . urlencode(get_query_var('s'))));
		exit();
	}
}
add_action('template_redirect', 'basey_nice_search_redirect');

/**
 * Fix for empty search queries redirecting to home page
 *
 * @link http://wordpress.org/support/topic/blank-search-sends-you-to-the-homepage#post-1772565
 * @link http://core.trac.wordpress.org/ticket/11330
 */
function basey_request_filter($query_vars) {
	if (isset($_GET['s']) && empty($_GET['s'])) {
		$query_vars['s'] = ' ';
	}

	return $query_vars;
}
add_filter('request', 'basey_request_filter');

/**
 * Tell WordPress to use searchform.php from the templates/ directory. Requires WordPress 3.6+
 */
function basey_get_search_form($form) {
	$form = '';
	locate_template('/templates/searchform.php', true, false);
	return $form;
}
add_filter('get_search_form', 'basey_get_search_form');