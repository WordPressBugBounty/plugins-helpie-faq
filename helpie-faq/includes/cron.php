<?php

namespace HelpieFaq\Includes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Cron class - tracking removed.
 * Kept as stub to prevent errors from existing calls.
 */
if (!class_exists('\HelpieFaq\Includes\Cron')) {
    class Cron
    {
        public function init()
        {
            // Tracking removed - clear any existing scheduled events
            wp_clear_scheduled_hook('helpie_faq/track_events');
        }

        public function set_intervals($schedules)
        {
            return $schedules;
        }

        public function clear_schedule()
        {
        }

        public function start($args = array())
        {
        }

        public function action($type, $args = array())
        {
        }

        public function run()
        {
        }
    }
}
