<?php

/**
 * Modify the_content
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia;

// Make sure this file runs only from within WordPress.
defined( 'ABSPATH' ) or die();

class crosslinks {

	public $crosslink_activated = true;
	public $prefer_single_words = false;
	public $require_full_words = true;
	public $post_types = ['wppedia_term'];

	public function __construct( bool $crosslink_activated = null, bool $prefer_single_words = null, bool $require_full_words = null, array $post_types = null ) {

		if ( $prefer_single_words !== null )
			$this->prefer_single_words = $prefer_single_words;

		if ( $post_types !== null )
			$this->post_types = $post_types;

		if ( $require_full_words !== null )
			$this->require_full_words = $require_full_words;

		if ( $crosslink_activated !== null )
			$this->crosslink_activated = $crosslink_activated;

		if ( $this->crosslink_activated )
			add_filter( 'the_content', [$this, 'the_post_content_links'] );

  }

  /**
   * Get Posts available for crosslink content
   * 
   * @since 1.0.0
   */
  public function get_crosslink_posts() {

    // Query all available posts
    $posts_query = wppedia_utils()->get_wiki_entries([
      'post_type'     => $this->post_types,
      'post_status'   => 'publish',
      'post__not_in'  => [get_the_ID()]
    ]);

    // Get Posts
    $posts = $posts_query->posts;

    // Reduce to titles
    $post_titles = array_map(function ($posts) {

      $post_title = new \stdClass();
      $post_title->ID = $posts->ID;
      $post_title->title = $posts->post_title;

      return $post_title;

    }, $posts);

    return $post_titles;

  }

  /**
   * Prepare the link Phrase
   * 
   * @since 1.0.0
   */
  private function prepare_link_phrase( string $str ) {

    $str = trim($str);
    $str = wptexturize($str);
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    $str = htmlspecialchars($str);
    $str = preg_quote($str, '/');

    return $str;

  }

  /**
   * Filter the content and insert crosslinks
   * 
   * @since 1.0.0
   */
  public function the_content_linked( $content ) {

    $posts = $this->get_crosslink_posts();

    // Tags to ignore
    $ignore_tags = ['a', 'script', 'style', 'code', 'pre'];

    // Prepare regex for replacements
    $regex_flags = 'imsu';
    $replace_regex = '(?!(?:[^<\[]+[>\]]|[^>\]]+\<\/(?:';
    foreach ( $ignore_tags as $index => $tag ) {

      $replace_regex .= $tag;
      if ( $index !== count($ignore_tags) - 1 )
        $replace_regex .= '|';

    }
    $replace_regex .= ')\>))';

    // Sort available posts by title length
    usort($posts, function($a, $b) {

      $a_len = mb_strlen($a->title);
      $b_len = mb_strlen($b->title);

      if ( $this->prefer_single_words )
        return $a_len - $b_len;

      return $b_len - $a_len;

    });

    // Loop over available posts and add crosslinks
    foreach ( $posts as $post ) {
      
      // Check if a title exists in the current posts content
      if ( stripos( $content, $post->title ) !== false ) {

				$link_phrase = $this->prepare_link_phrase( $post->title );

				// Split $link_phrase by empty spaces 
				/*
				$link_phrase_splitted = explode( ' ', $link_phrase );
				if ( count( $link_phrase_splitted ) > 1 ) {

					$link_phrase = '';

					foreach ( $link_phrase_splitted as $chunk ) {
						$link_phrase .= '(<(\w[^>]+)>\s?)?' . $chunk . '(<(\/\w[^>]+)>\s?)?';
					}

				}

				echo '/' . $replace_regex . '(' . $link_phrase . ')/' . $regex_flags;
				echo '<br>';
				*/

				// TESTING
				// (?!(?:[^<\[]+[>\]]|[^>\]]+\<\/(?:a|script|style|code|pre))\>)((<(\w[^>]+)>\s?)?Lorem(<(\/\w[^>]+)>\s?)?(<(\w[^>]+)>\s?)?Ipsum(<(\/\w[^>]+)>\s?)?)


				if ( $this->require_full_words && 0 === preg_match( '/' . $replace_regex . '(^|\s|\>|\#|\@|\+)' . $link_phrase . '(\?|\!|\;|,|\.|\<|\s|$)/' . $regex_flags, $content ) )
					continue;

				$post_title_link = get_permalink( $post->ID );

        $content = preg_replace( '/' . $replace_regex . '(' . $link_phrase . ')/' . $regex_flags, '<a href="' . $post_title_link . '" title="$1" class="wppedia-crosslink" data-post_id="' . $post->ID . '">$1</a>', $content );

      }
      

    }

    return $content;

  }

  /**
   * Modify Post Content
   * 
   * @since 1.0.0
   */
  public function the_post_content_links( $content ) {

		$doing_seo = doing_action('wpseo_head');

    // Bail early if the current post is not a wiki entry
    if ( 
			is_admin() || 
			! is_singular('wppedia_term') ||
			$doing_seo
		)
      return $content;

    return $this->the_content_linked( $content );

  }

}
