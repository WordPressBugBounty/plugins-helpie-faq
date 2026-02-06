<?php

namespace HelpieFaq\Features\Insights\Trackers;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\HelpieFaq\Features\Insights\Trackers\Click_Tracker')) {
    class Click_Tracker extends \HelpieFaq\Features\Insights\Trackers\Event_Tracker
    {
        public $meta_key;
        public $event_type;
        public $repo;
        public $current_date;
        public $current_timestamp;
        public $insights_helper;
        public $date_format;

        public function __construct()
        {
            $this->meta_key = 'click_counter';
            $this->event_type = 'post_meta';

            parent::__construct();
            $this->repo = new \HelpieFaq\Features\Insights\Click_Counter_Repo();
        }

        public function get_event_data($postData)
        {
            return $postData;
        }
        public function get_new_count($counter_data)
        {

            // Add to today's count
            $counter_data = $this->update_todays_count($counter_data);

            // Add to monthly count
            $counter_data = $this->update_current_month_count($counter_data);

            // Add to All Time count
            $counter_data = $this->update_all_time_count($counter_data);
            // error_log('new_counter_data : ' . print_r($counter_data, true));
            return $counter_data;
        }

        public function update_todays_count($counter_data)
        {
            if (!isset($counter_data[$this->current_date])) {
                $counter_data[$this->current_date] = 1;
            } else {
                $counter_data[$this->current_date]++;
            }
            return $counter_data;
        }

        public function update_current_month_count($counter_data)
        {
            $current_month = $this->insights_helper->get_current_month($this->current_timestamp);
            $counter_data[$current_month] = isset($counter_data[$current_month]) ? ($counter_data[$current_month] + 1) : 1;
            return $counter_data;
        }

        public function update_all_time_count($counter_data)
        {
            $counter_data['all-time'] = isset($counter_data['all-time']) ? ($counter_data['all-time'] + 1) : 1;
            return $counter_data;
        }

        public function update_count($new_counter_data, $event_data)
        {
            $data_type = isset($event_data['data_type']) ? $event_data['data_type'] : '';
            $id = isset($event_data['id']) ? $event_data['id'] : 0;

            if ($data_type === 'post') {
                update_post_meta($id, $this->meta_key, $new_counter_data);
            } else {
                update_term_meta($id, $this->meta_key, $new_counter_data);
            }
        }

        public function get_current_count($info)
        {
            $data_type = isset($info['data_type']) ? $info['data_type'] : '';
            $id = isset($info['id']) ? $info['id'] : 0;

            $count = array();

            if ($data_type === 'post') {
                $count = $this->repo->get_post_meta($id);
            } else {
                $count = $this->repo->get_term_meta($id);
            }

            return $count;
        }

        public function process_data()
        {

            $validation_map = array(
                "id" => "String",
            );
            $sanitized_data = hfaq_get_sanitized_data("POST", $validation_map);

            $ided_content = isset($sanitized_data['id']) ? $sanitized_data['id'] : '';
            if (empty($ided_content)) {
                return;
            }

            $info_indexed = explode("-", $ided_content);

            // Security: Validate data_type and id format to prevent arbitrary meta updates
            $allowed_data_types = array('post', 'term');
            $data_type = isset($info_indexed[0]) ? $info_indexed[0] : '';
            $id = isset($info_indexed[1]) ? absint($info_indexed[1]) : 0;

            // Validate data_type is in allowlist
            if (!in_array($data_type, $allowed_data_types, true)) {
                return null;
            }

            // Validate id is a positive integer
            if ($id <= 0) {
                return null;
            }

            $info = array();
            $info['data_type'] = $data_type;
            $info['id'] = $id;

            return $info;
        }
    } // END CLASS
}
