<?php
/**
 * Plugin Name: WP Subtitle Support for Better Internal Link Search
 * Plugin URI:  https://github.com/ministryofweb/better-internal-link-search-wp-subtitle
 * Description: Adds support for the "WP Subtitle" plugin to "Better Internal Link Search".
 * Version:     0.1.0
 * Author:      Marcus Jaschen
 * Author URI:  https://www.marcusjaschen.de/
 * License:     GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: better-internal-link-search-wp-subtitle
 */

namespace MinistryOfWeb\WordPress\Plugin;

use WP_Post;

/**
 * Class BetterInternalLinkSearchWPSubtitle
 *
 * @package MinistryOfWeb\WordPress\Plugin
 */
class BetterInternalLinkSearchWPSubtitle {
	/**
	 * BetterInternalLinkSearchSubtitles constructor.
	 *
	 * Registers the filter which searches all subtitles.
	 */
	public function __construct() {
		add_filter( 'pre_better_internal_link_search_results', array( $this, 'searchSubtitles' ), 10, 2 );
	}

	/**
	 * Creates an array of posts which have a subtitle that
	 * matches the search query.
	 *
	 * @param array $results
	 * @param array $args
	 *
	 * @return array
	 */
	public function searchSubtitles( $results, $args ) {
		$fetch = array(
			'meta_key'     => 'wps_subtitle',
			'meta_value'   => $args['s'],
			'meta_compare' => 'LIKE',
		);

		$posts = get_posts( $fetch );

		if ( empty( $posts ) ) {
			return array();
		}

		return array_map( array( $this, 'transformPost' ), $posts );
	}

	/**
	 * Transforms a \WP_Post object to the array in the required format.
	 *
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	private function transformPost( WP_Post $post ) {
		$title = get_the_title( $post );
		if ( function_exists( 'get_the_subtitle' ) ) {
			$subtitle = get_the_subtitle( $post, '', '', false );
			if ( ! empty( $subtitle ) ) {
				$title = $subtitle . ' - ' . $title;
			}
		}

		return array(
			'ID'        => $post->ID,
			'title'     => $title,
			'permalink' => get_the_permalink( $post ),
			'info'      => get_the_date( 'Y-m-d', $post ),
		);
	}
}

new BetterInternalLinkSearchWPSubtitle();
