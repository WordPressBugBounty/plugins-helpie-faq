<?php

namespace HelpieFaq\Includes;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
if ( !class_exists( '\\HelpieFaq\\Includes\\Ajax_Handler' ) ) {
    class Ajax_Handler {
        public function __construct() {
        }

        public function action() {
            // Security check: verify nonce and user capability
            if ( !check_ajax_referer( 'helpie_faq_nonce', 'nonce', false ) ) {
                wp_send_json_error( array(
                    'message' => 'Invalid security token',
                ), 403 );
            }
            if ( !current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array(
                    'message' => 'Insufficient permissions',
                ), 403 );
            }
            $insights_controller = new \HelpieFaq\Features\Insights\Controller();
            $insights_controller->clear();
            wp_send_json_success( array(
                'message' => 'Insights cleared successfully',
            ) );
        }

    }

    // END CLASS
}
$ajax_hanlder = new \HelpieFaq\Includes\Ajax_Handler();
$click_tracker = new \HelpieFaq\Features\Insights\Trackers\Click_Tracker();
$search_tracker = new \HelpieFaq\Features\Insights\Trackers\Search_Tracker();
add_action( 'wp_ajax_helpie_faq_click_counter', array($click_tracker, 'action') );
add_action( 'wp_ajax_nopriv_helpie_faq_click_counter', array($click_tracker, 'action') );
add_action( 'wp_ajax_helpie_faq_search_counter', array($search_tracker, 'action') );
add_action( 'wp_ajax_nopriv_helpie_faq_search_counter', array($search_tracker, 'action') );
add_action( 'wp_ajax_helpie_faq_reset_insights', array($ajax_hanlder, 'action') );
// Removed: wp_ajax_nopriv_helpie_faq_reset_insights - reset insights requires admin authentication (CVE-2025-58659)
// Removed: wp_ajax_helpie_faq_track_shortcodes_and_widgets - Mixpanel tracking removed
add_action( 'wp_ajax_update_feature_notice_dismissal_data_via_ajax', array(new \HelpieFaq\Features\Feature_Notice(), 'update_feature_notice_dismissal_data_via_ajax') );