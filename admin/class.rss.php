<?php

class PageLines_RSS {

	function __construct() {


	 	}



		/**
		 * Store RSS worker
		 *
		 * @package PageLines Framework
		 * @since   2.2
		 */
		public static function get_dash_rss( $args = array() ) {

			$defaults = array(

				'feed'		=>	'http://api.pagelines.com/rss/rss2.php',
				'items'		=>	3,
				'community'	=>	false
			);

			$args = wp_parse_args( $args, $defaults );

			$out = array();

			$items = $args['items'];
			$feed_url = $args['feed'];

	    $rss = fetch_feed( $feed_url );

			if ( is_wp_error($rss) ) {

			$out[] = array(
				'title'	=>	'RSS Error',
				'test'	=>	$rss->get_error_message()
			);
			unset($rss);
			return $out;
		}

		if ( !$rss->get_item_quantity() ) {

			$out[] = array(
				'title'	=>	'RSS',
				'test'	=>	'Apparently, there is nothing new yet!'
			);
			$rss->__destruct();
			unset($rss);
			return $out;
		}


		foreach ( $rss->get_items(0, $items) as $item ) {


			if ($enclosure = $item->get_enclosure())
				$image =  $enclosure->get_link();
			else
				$image = false;

			$link = '';
			$content = '';
			$date = $item->get_date();

			$link = esc_url( $item->get_link() );
			$title = $item->get_title();
			$content = $item->get_content();
			if( $args['community'] ) {

				$d = self::com_url( $item->get_description() );
				$link = $d[0];
				$content = $d[1];
			}

		$out[] = array(
			'title'	=>	$title,
			'text'	=>	$content,
			'link'	=>	$link,
			'img'	=>	$image
		);

		}
		$rss->__destruct();
		unset($rss);

		return $out;
	}

	public static function com_url( $d ) {

		preg_match( '#<p>(http://[^<]*)</p>#', $d, $out );
		$d = str_replace( $out[0], '', $d );
		return array( $out[1], $d );
	}
}
