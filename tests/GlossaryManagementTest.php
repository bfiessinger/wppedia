<?php

namespace WPPedia\Tests;

use PHPUnit\Framework\TestCase;

class GlossaryManagementTest extends TestCase
{
    /**
     * Test the creation of a WPPedia glossary entry
     *
     * @since 1.4.0
     *
     * @dataProvider provideGlossaryData
     *
     * @param array $post_data
     *
     * @return void
     */
    public function testCreateGlossaryEntry($post_data)
    {
        // Insert the post into the database
        $post_id = wp_insert_post($post_data);

        // Check if the post was created successfully
        $this->assertIsInt($post_id);
        $this->assertGreaterThan(0, $post_id);

        // Fetch the post from the database
        $post = get_post($post_id);

        // Check if the post was fetched successfully
        $this->assertInstanceOf(\WP_Post::class, $post);
        $this->assertEquals($post_data['post_title'], $post->post_title);
        $this->assertEquals($post_data['post_content'], $post->post_content);
        $this->assertEquals($post_data['post_status'], $post->post_status);
        $this->assertEquals($post_data['post_type'], $post->post_type);

        // check if has the term "wppedia_initial_letter" with value of the first letter of the post title
        $initial_char = substr($post_data['post_title'], 0, 1);
        $term = get_the_terms($post_id, 'wppedia_initial_letter');
        $this->assertIsArray($term);
        $this->assertCount(1, $term);
        $this->assertInstanceOf(\WP_Term::class, $term[0]);
        $this->assertEqualsIgnoringCase($initial_char, $term[0]->name);

        // Check if the post has the alternative terms
        $alt_terms = get_post_meta($post_id, 'wppedia_post_alt_tags', true);
        $this->assertIsArray($alt_terms);

        // Check if the alternative terms were saved correctly
        $this->assertCount(3, $alt_terms);
        $this->assertEquals('Test', $alt_terms[0]['value']);
        $this->assertEquals('Example', $alt_terms[1]['value']);
        $this->assertEquals('Sample', $alt_terms[2]['value']);
    }

    /**
     * Data provider for WPPedia glossary entries
     *
     * @since 1.4.0
     *
     * @return array
     */
    public static function provideGlossaryData()
    {
        return [
            [
                [
                    'post_title' => 'Sample WPPedia Entry',
                    'post_excerpt' => 'This is a test glossary entry',
                    'post_content' => '<!-- wp:paragraph -->
                        <p>Testing the datasets of a WPPedia Glossary entry</p>
                    <!-- /wp:paragraph -->',
                    'post_status' => 'publish',
                    'post_type' => 'wppedia_term',
                    'post_name' => 'sample-wppedia-entry',
                    'meta_input' => [
                        'wppedia_post_alt_tags' => [
                            ['value' => 'Test'],
                            ['value' => 'Example'],
                            ['value' => 'Sample'],
                        ],
                    ],
                ]
            ]
        ];
    }
}
