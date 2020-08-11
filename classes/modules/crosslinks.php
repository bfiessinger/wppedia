<?php

/**
 * Modify the_content
 * 
 * @since 1.0.0
 */

namespace bf\wpPedia\modules;

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
			if ( stripos( wp_strip_all_tags( $content ), $post->title ) !== false ) {

				$link_phrase = $this->prepare_link_phrase( $post->title );
				if ( ! empty( $content ) && is_string( $content ) ) {
					$content = $this->parse_content_xml( $content, $link_phrase, $post );
				}

			}
			
		}

    return $content;

	}

	private function parse_content_xml( $content, $link_phrase, $post ) {

		$dom = new \DOMDocument();
		
		libxml_use_internal_errors(true);
		if ( ! $dom->loadHtml(mb_convert_encoding( $content, 'HTML-ENTITIES', "UTF-8") ) ) {
			libxml_clear_errors();
		}
		$xpath = new \DOMXPath($dom);

		$ignore_tags = [
			'a', 
			'script', 
			'style', 
			'code', 
			'pre', 
			'object',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'textarea'
		];

		$query = '//text()';
			foreach ( $ignore_tags as $tag ) {
				$query .= '[not(ancestor::' . $tag . ')]';
		}

		foreach( $xpath->query($query) as $node ) {

			if ( $this->require_full_words && 0 === preg_match( '/(^|\s|\>|\#|\@|\+)' . $link_phrase . '(\?|\!|\;|,|\.|\<|\s|$)/imsu', $node->wholeText ) )
				continue;

			$replaced = preg_replace_callback( '/' . $link_phrase . '/imsu', function( $match ) use ( $post ) {

				if ( ! empty( $match[0] ) ) {

					$post_title_link = get_permalink( $post->ID );

					return '<a href="' . $post_title_link . '" title="' . esc_html( $match[0] ) . '" class="wppedia-crosslink" data-post_id="' . $post->ID . '">' . $match[0] . '</a>';

				}

			}, htmlspecialchars($node->wholeText, ENT_COMPAT));

			if (!empty($replaced)) {
				$newNode = $dom->createDocumentFragment();
				$replacedShortcodes = strip_shortcodes($replaced);
				$result = $newNode->appendXML('<![CDATA[' . $replacedShortcodes . ']]>');

				if ($result !== false) {
						$node->parentNode->replaceChild($newNode, $node);
				}
			}

		}
		
		/*
		 *  get only the body tag with its contents, then trim the body tag itself to get only the original content
		 */
		$bodyNode = $xpath->query('//body')->item(0);

		if ($bodyNode !== NULL) {

			$newDom = new \DOMDocument();
			$newDom->appendChild($newDom->importNode($bodyNode, TRUE));

			$intermalHtml = $newDom->saveHTML();
			$content = mb_substr(trim($intermalHtml), 6, (mb_strlen($intermalHtml) - 14), "UTF-8");

			/*
			 * Fixing the self-closing which is lost due to a bug in DOMDocument->saveHtml() (caused a conflict with NextGen)
			 */
			$content = preg_replace('#(<img[^>]*[^/])>#Ui', '$1/>', $content);

		}

		return $content;

	}

	/**
	 * Modify Post Content
	 * 
	 * @since 1.0.0
	 */
	public function the_post_content_links( $content ) {

		// Bail early if the current post is not a wiki entry
		if ( 
			is_admin() || 
			! is_singular('wppedia_term') ||
			doing_action('wpseo_head')
		)
			return $content;

		return $this->the_content_linked( $content );

  }

}
