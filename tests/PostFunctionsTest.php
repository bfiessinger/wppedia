<?php

namespace WPPedia\Tests;

use PHPUnit\Framework\TestCase;

class PostFunctionsTest extends TestCase {

	public function testGetPostAlternativeTermsReturnsValuesFromJsonMeta(): void {
		$post_id = self::factory()->post->create(
			[
				'post_type'   => 'wppedia_term',
				'post_title'  => 'Alternative Terms',
				'post_status' => 'publish',
			]
		);

		update_post_meta(
			$post_id,
			'wppedia_post_alt_tags',
			wp_json_encode(
				[
					[ 'value' => 'First' ],
					[ 'value' => 'Second' ],
				]
			)
		);

		$this->assertSame( [ 'First', 'Second' ], wppedia_get_post_alternative_terms( $post_id ) );
	}

	public function testGetPostAlternativeTermsReturnsNullForMissingMeta(): void {
		$post_id = self::factory()->post->create(
			[
				'post_type'   => 'wppedia_term',
				'post_title'  => 'No Meta',
				'post_status' => 'publish',
			]
		);

		$this->assertNull( wppedia_get_post_alternative_terms( $post_id ) );
	}

	public function testGetPostsInitialLetterListIncludesAllLettersWhenHideEmptyIsFalse(): void {
		$post_id = self::factory()->post->create(
			[
				'post_type'   => 'wppedia_term',
				'post_title'  => 'Beta Entry',
				'post_status' => 'publish',
			]
		);

		$this->assertIsInt( $post_id );

		$letters = wppedia_get_posts_initial_letter_list(
			[
				'hide_empty'       => false,
				'show_option_home' => true,
			]
		);

		$this->assertArrayHasKey( 'home', $letters );
		$this->assertArrayHasKey( 'a', $letters );
		$this->assertArrayHasKey( 'b', $letters );
		$this->assertSame( 'b', $letters['b'] );
	}


	public function testGetPostVersionHistoryReturnsRevisionsInDescendingOrder(): void {
		$post_id = self::factory()->post->create(
			[
				'post_type'   => 'wppedia_term',
				'post_title'  => 'Versioned Entry',
				'post_status' => 'publish',
			]
		);

		$this->assertIsInt( $post_id );

		wp_update_post(
			[
				'ID'           => $post_id,
				'post_content' => 'First revision content',
			]
		);

		wp_update_post(
			[
				'ID'           => $post_id,
				'post_content' => 'Second revision content',
			]
		);

		$history = wppedia_get_post_version_history( $post_id );

		$this->assertNotEmpty( $history );
		$this->assertArrayHasKey( 'id', $history[0] );
		$this->assertArrayHasKey( 'author_name', $history[0] );
		$this->assertArrayHasKey( 'modified_human', $history[0] );
	}

	public function testGetPostVersionHistoryReturnsEmptyArrayForInvalidPostId(): void {
		$this->assertSame( [], wppedia_get_post_version_history( 0 ) );
	}

}
