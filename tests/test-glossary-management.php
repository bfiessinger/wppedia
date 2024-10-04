<?php

namespace WPPedia\Tests;

use PHPUnit\Framework\TestCase;

class GlossaryManagementTest extends TestCase
{
    public function testCreateGlossaryEntry()
    {
        // prepare sample post data
        $post_data = [
            'post_title' => 'Sample WPPedia Entry',
            'post_content' => 'This is a test glossary entry',
            'post_status' => 'publish',
            'post_type' => 'wppedia_term',
        ];

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
    }
}
