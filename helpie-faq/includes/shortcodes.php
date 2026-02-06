<?php

namespace HelpieFaq\Includes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\HelpieFaq\Includes\Shortcodes')) {
    class Shortcodes
    {
        public function __construct()
        {
        }

        public static function shortcode_attributes_compatibility($atts)
        {
            $compatibility = new \HelpieFaq\Includes\Migrations\Shortcode_Compatibility();
            return $compatibility->get_attributes($atts);
        }

        public function helpie_menu_shortcode($atts, $content = null)
        {
            // $atts = self::shortcode_attributes_compatibility($atts);

            // error_log('helpie_menu_shortcode $atts: ' . print_r($atts, true));

            $menu = new \HelpieFaq\Features\Helpie_Menu\Init();
            return $menu->get_frontend_view($atts);
        }

        public static function basic($atts, $content = null)
        {

            $atts = self::shortcode_attributes_compatibility($atts);

            $faq_model = new \HelpieFaq\Features\Faq\Faq_Model();
            $defaults = $faq_model->get_default_args();
            $args = shortcode_atts($defaults, $atts);

            /**
             * Check the shorcode is faq_group shortcode or not.
             * If it's faq_shortcode then set default props value in $args.
             */

            if (isset($atts['group_id']) && !empty($atts['group_id']) && intval($atts['group_id'])) {
                $faq_group_controller = new \HelpieFaq\Features\Faq_Group\Controller();
                $faq_groups_args = $faq_group_controller->get_default_args($atts);
                $args = array_merge($args, $faq_groups_args);

                /** Apply Group level style settings */
                $can_apply_group_style = $faq_group_controller->can_apply_group_style($atts['group_id']);
                $args['can_apply_group_style'] = $can_apply_group_style;

                $style = '';
                if ($can_apply_group_style == 'yes') {
                    $style_controller = new \HelpieFaq\Features\Faq_Group\Style_Controller($args);
                    $style = $style_controller->get_styles();
                }

                $view = $faq_group_controller->get_view($args);
                return $style . $view;
            }

            $faq = new \HelpieFaq\Features\Faq\Faq();
            return $faq->get_view($args);
        }

        public function notices($atts, $content = null)
        {
            if (!current_user_can('manage_options')) {
                return;
            }
            $notices = new \HelpieFaq\Features\Notices\View();
            return $notices->get_content($atts);
        }
    }
}

$helpie_faq_shortcodes = new \HelpieFaq\Includes\Shortcodes();

add_shortcode('helpie_faq', array($helpie_faq_shortcodes, 'basic'));
add_shortcode('helpie_menu', array($helpie_faq_shortcodes, 'helpie_menu_shortcode'));
// add_shortcode('helpie_notices', array($helpie_faq_shortcodes, 'notices'));

/**
 * E2E-only shortcode for Playwright regression coverage.
 *
 * This simulates the Elementor "Helpie FAQ - Dynamically Added FAQ" widget render path
 * without requiring Elementor to be installed in the Docker/CI environment.
 *
 * Enabled only when HELPIE_FAQ_E2E is defined (set in docker-compose.yml).
 *
 * Usage:
 *   [helpie_faq_dynamic_e2e faqs="Q1::A1||Q2::A2" /]
 */
if (defined('HELPIE_FAQ_E2E') && HELPIE_FAQ_E2E) {
    add_shortcode('helpie_faq_dynamic_e2e', function ($atts) {
        $atts = shortcode_atts([
            'faqs' => '',
            // Keep url attribute on so schema + anchors can be validated.
            'faq_url_attribute' => 1,
        ], $atts);

        $raw = is_string($atts['faqs']) ? $atts['faqs'] : '';
        $raw = trim($raw);
        if (empty($raw)) {
            return '';
        }

        $faq_items = [];
        $pairs = explode('||', $raw);
        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (empty($pair)) {
                continue;
            }
            $parts = explode('::', $pair, 2);
            $title = isset($parts[0]) ? trim($parts[0]) : '';
            $content = isset($parts[1]) ? trim($parts[1]) : '';
            if ($title === '' || $content === '') {
                continue;
            }
            $faq_items[] = [
                'tab_title' => $title,
                'tab_content' => $content,
            ];
        }

        if (empty($faq_items)) {
            return '';
        }

        $args = [
            'faqs' => $faq_items,
            'faq_url_attribute' => $atts['faq_url_attribute'],
        ];

        $dynamic = new \HelpieFaq\Features\Faq\Dynamic_Widget\Faq();
        return $dynamic->get_view($args);
    });
}
