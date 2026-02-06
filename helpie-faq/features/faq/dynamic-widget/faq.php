<?php

namespace HelpieFaq\Features\Faq\Dynamic_Widget;

if (!class_exists('\HelpieFaq\Features\Dynamic_Widget\Faq')) {
    class Faq
    {

        public $model;
        public $view;

        public function __construct()
        {
            // Models
            $this->model = new \HelpieFaq\Features\Faq\Dynamic_Widget\Faq_Model();

            // Views
            $this->view = new \HelpieFaq\Features\Faq\Faq_View();
        }

        // For using only with Elementor Widget: Helpie FAQ - Dynamically Added FAQ
        public function get_view($args)
        {
            $html = '';

            $style = array();

            if (isset($args['style'])) {
                $style = $args['style'];
            }

            $html = $this->get_viewProps_elementor($args, $style);

            // error_log('html: ' . $html);

            return $html;
        }

        public function get_viewProps_elementor($args, $style = [])
        {
            $viewProps = array();

            $viewProps['collection'] = [
                'title' => "FAQ Added Via Elementor",
                'display_mode' => 'simple_accordion',
                'display_mode_group_by' => 'none',
            ];

            $viewProps = $this->model->get_viewProps($args);

            $viewProps['items'] = [];

            // Generate a unique widget instance identifier to ensure uniqueness across multiple widgets
            // This ensures FAQs from different widget instances on the same page get unique IDs
            // Use deterministic hash of FAQ content as fallback (not uniqid) for cache compatibility
            $widget_instance_id = isset($args['widget_id']) ? $args['widget_id'] : 'elementor-' . substr(md5(wp_json_encode($args['faqs'])), 0, 12);
            
            foreach ($args['faqs'] as $key => $field) {
                // Generate unique ID for each FAQ item
                // Create a hash-based positive integer that's unique per widget instance and FAQ item
                // Using crc32 to generate a numeric hash, then ensure it's positive and unique
                $hash_string = $widget_instance_id . '-' . $key . '-' . $field['tab_title'] . '-' . md5($field['tab_content']);
                $unique_id = abs(crc32($hash_string));
                
                // Ensure the ID is large enough to avoid conflicts with real post IDs
                // WordPress post IDs typically start from 1, so we'll use a range starting from a high number
                // If the hash is too small, add a large offset
                if ($unique_id < 1000000) {
                    $unique_id = 1000000 + ($unique_id % 1000000);
                }
                
                $single_field = [
                    'title' => $field['tab_title'],
                    'content' => $field['tab_content'],
                    'post_id' => $unique_id,
                    'count' => [],
                ];

                $viewProps['items'][] = $single_field;
            }

            if (isset($viewProps['items']) && !empty($viewProps['items'])) {
                $html = $this->view->get($viewProps, $style);
                // error_log('get_viewProps_elementor $html: ' . $html);
            }

            apply_filters('helpie_faq_schema_generator', $viewProps);

            return $html;
        }
    }
}
