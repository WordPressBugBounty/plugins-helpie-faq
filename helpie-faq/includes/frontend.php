<?php

namespace HelpieFaq\Includes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\HelpieFaq\Includes\Frontend')) {
    class Frontend
    {

        public $options = [];

        public function convert_youtube_urls_to_embeds($content)
        {
            // Define a pattern to match YouTube URLs
            $pattern = '/(https?:\/\/www\.youtube\.com\/watch\?v=[\w-]+)/i';

            // Replace each YouTube URL with the oEmbed HTML with specified size
            $content = preg_replace_callback($pattern, function ($matches) {
                // Get the URL from the matches
                $url = esc_url($matches[1]);

                // Get the oEmbed HTML for the URL
                $embed_code = wp_oembed_get($url);

                // If oEmbed HTML was retrieved
                if ($embed_code) {
                    // Set desired width and height
                    $width = 560; // Width in pixels
                    $height = 315; // Height in pixels

                    // Modify the embed code to include width and height
                    $embed_code = str_replace(
                        array('width="1200"', 'height="675"'),
                        array('width="' . $width . '"', 'height="' . $height . '"'),
                        $embed_code
                    );

                    return $embed_code;
                }

                // Return the original URL if embedding failed
                return $matches[1];
            }, $content);

            return $content;
        }

        public function get_the_faq_content($collections_with_props)
        {
            $props = isset($collections_with_props['props']) ? $collections_with_props['props'] : [];
            $this->options = isset($collections_with_props['collectionProps']) ? $collections_with_props['collectionProps'] : [];

            $content = isset($props['content']) ? $props['content'] : '';

            /** define the variable for checking the elementor plugin has active on site */
            $is_elementor_active = is_plugin_active('elementor/elementor.php');

            $content = $this->convert_youtube_urls_to_embeds($content);

            // error_log('content: ' . $content);
            if ($is_elementor_active) {

                // \Elementor\Plugin::instance()->frontend->remove_content_filter();

                $content = apply_filters('elementor/frontend/the_content', $content);

            } else {

                if (!isset($this->options['integration']) || $this->options['integration'] != 'lms') {
                    /**
                     * The FAQ content here is *partial* content extracted from a post.
                     * Running the FULL `the_content` filter chain over it lets arbitrary
                     * third-party filters (email obfuscators especially) corrupt it --
                     * they leave decoder placeholder tokens that never resolve and render
                     * as garbled output (GH #706).
                     *
                     * Fix: temporarily swap the global `the_content` WP_Hook object for a
                     * restricted one carrying only WordPress core formatting filters, then
                     * always restore the original in a finally block. This supersedes the
                     * old Beaver Builder remove/re-add -- Beaver's filter (like any other
                     * third-party filter) simply will not be present in the restricted set.
                     *
                     * NOTE: $wp_filter['the_content'] holds closures and is NOT
                     * serializable -- it must be saved/restored by plain reference.
                     */
                    global $wp_filter;

                    $saved_the_content = isset($wp_filter['the_content']) ? $wp_filter['the_content'] : null;

                    // Drop the existing hook so only core formatting filters remain.
                    unset($wp_filter['the_content']);

                    try {
                        // oEmbed: run BEFORE wpautop/shortcodes so URLs on their own line
                        // are converted. Matches WP core's wp-includes/embed.php priorities.
                        if (isset($GLOBALS['wp_embed']) && is_object($GLOBALS['wp_embed'])) {
                            add_filter('the_content', array($GLOBALS['wp_embed'], 'run_shortcode'), 8);
                            add_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);
                        }

                        // WordPress core formatting filters (see wp-includes/default-filters.php).
                        // do_blocks (priority 9) must run so block-authored FAQ answers,
                        // including dynamic blocks, render correctly.
                        if (function_exists('do_blocks')) {
                            add_filter('the_content', 'do_blocks', 9);
                        }
                        add_filter('the_content', 'wptexturize');
                        add_filter('the_content', 'wpautop');
                        add_filter('the_content', 'shortcode_unautop');
                        add_filter('the_content', 'prepend_attachment');
                        add_filter('the_content', 'do_shortcode', 11);

                        $content = apply_filters('the_content', $content);
                    } finally {
                        // ALWAYS restore the original global hook, even on exception,
                        // so other content on the request keeps its filters.
                        if ($saved_the_content !== null) {
                            $wp_filter['the_content'] = $saved_the_content;
                        } else {
                            unset($wp_filter['the_content']);
                        }
                    }
                }
            }

            $show_excerpt = ($this->is_excerpt_enabled() && hf_fs()->can_use_premium_code__premium_only() && isset($props['post_id'])) ? true : false;
            if ($show_excerpt) {
                $content = $this->get_excerpt_more_content($content, $props);
            }
            return $content;
        }

        public function is_excerpt_enabled()
        {
            $enabled_excerpt = isset($this->options['enable_excerpt']) && $this->options['enable_excerpt'] == 1 ? true : false;
            return $enabled_excerpt;
        }

        public function get_excerpt_length()
        {
            $excerpt_length = isset($this->options['excerpt_word_length']) ? $this->options['excerpt_word_length'] : 55;
            if (intval($excerpt_length) <= 0) {
                $excerpt_length = 55;
            }
            return $excerpt_length;
        }

        public function get_excerpt_more_content($content, $props)
        {
            $excerpt_content = isset($props['excerpt']) ? $props['excerpt'] : '';
            $read_more_link = $this->get_readmore_link($props);

            if (!empty($excerpt_content)) {
                $content = $excerpt_content . $read_more_link;
            } else {
                $length = $this->get_excerpt_length();
                $content = $this->trim_content_preserve_links($content, $length, $read_more_link);
            }
            return $content;
        }

        public function get_readmore_link($props)
        {

            $post_link = isset($props['post_id']) ? get_permalink($props['post_id']) : '#';
            $read_more_text = isset($this->options['read_more_link_text']) && !empty($this->options['read_more_link_text']) ? $this->options['read_more_link_text'] : 'Read More';
            $open_new_window = (isset($this->options['open_new_window']) && $this->options['open_new_window'] == 1) ? 'target="_blank"' : '';
            $read_more_content = '&hellip;<a class="read-more-link" href="' . esc_url($post_link) . '"  ' . $open_new_window . '>' . esc_html($read_more_text) . '</a>';
            return $read_more_content;
        }

        /**
         * Trim content to a word limit while preserving <a> tags.
         * wp_trim_words() strips ALL HTML, so links in excerpts are lost.
         */
        public function trim_content_preserve_links($content, $num_words, $more)
        {
            // Strip all tags except <a>
            $content = strip_tags($content, '<a>');
            $content = trim($content);

            $words = preg_split('/\s+/', $content, $num_words + 1);

            if (count($words) > $num_words) {
                array_pop($words);
                $content = implode(' ', $words);
                // Close any unclosed <a> tags
                if (substr_count($content, '<a') > substr_count($content, '</a>')) {
                    $content .= '</a>';
                }
                $content .= $more;
            }

            return $content;
        }

        public function get_read_more_content($collections_with_props)
        {
            $props = isset($collections_with_props['props']) ? $collections_with_props['props'] : [];
            $this->options = isset($collections_with_props['collectionProps']) ? $collections_with_props['collectionProps'] : [];
            $content = isset($props['content']) ? $props['content'] : '';

            $show_excerpt = ($this->is_excerpt_enabled() && hf_fs()->can_use_premium_code__premium_only() && isset($props['post_id'])) ? true : false;

            if (!$show_excerpt) {
                return $content;
            }
            return $this->get_excerpt_more_content($content, $props);
        }

    }
}
