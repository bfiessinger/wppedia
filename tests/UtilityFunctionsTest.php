<?php

namespace WPPedia\Tests;

use PHPUnit\Framework\TestCase;

class UtilityFunctionsTest extends TestCase {

	public function testSlugifyCreatesAsciiSlug(): void {
		$slug = wppedia_slugify( 'Crème brûlée & Café', 'fallback' );

		$this->assertSame( 'creme-brulee-cafe', $slug );
	}

	public function testSlugifyFallsBackForNonTransliterableString(): void {
		$slug = wppedia_slugify( '你好世界', 'fallback-value' );

		$this->assertSame( 'fallback-value', $slug );
	}

	public function testListCharsCanBeCustomizedWithFilter(): void {
		$filter = static function ( array $chars ): array {
			$chars['#'] = '#';
			return $chars;
		};

		add_filter( 'wppedia_list_chars', $filter );

		$chars = wppedia_list_chars();

		remove_filter( 'wppedia_list_chars', $filter );

		$this->assertArrayHasKey( 'a', $chars );
		$this->assertArrayHasKey( 'z', $chars );
		$this->assertArrayHasKey( '#', $chars );
	}
}
