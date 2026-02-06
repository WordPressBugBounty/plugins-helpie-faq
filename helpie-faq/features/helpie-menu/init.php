<?php

namespace HelpieFaq\Features\Helpie_Menu;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
if ( !class_exists( '\\HelpieFaq\\Features\\Helpie_Menu\\Init' ) ) {
    class Init {
        public function __construct() {
            add_action( 'admin_menu', array($this, 'add_single_menu_post_page') );
            // add_action("edit_form_after_title", array($this, 'get_add_template_view'), 10, 1);
            // add_action("helpie_menu_edit_form", array($this, 'get_add_template_view'), 10, 2);
            // add_action("helpie_menu_add_form", array($this, 'hide_slug_and_description_rows'), 10, 2);
        }

        public function get_frontend_view( $shortcode_params = [] ) {
            // $menu = new Menu();
            // return $menu->get_view();
            $html = "<div id='helpie-menu-frontend-app'>";
            // $html .= '<h1>Helpie Menu - Shortcode</h1>';
            $html .= '<div id="helpie-menu-app"></div>';
            $html .= '</div>';
            $html .= "</div>";
            $Actions = new \HelpieFaq\Includes\Actions();
            $Actions->handle_helpie_menu_assets( 'helpie_menu_shortcode', $shortcode_params );
            return $html;
        }

        public function add_single_menu_post_page( $submenu_page ) {
            $params = $this->get_params_from_url();
            $edit_title = __( "Edit Menu", "tablesome" );
            $create_new_title = __( "Create New Menu", "helpie-faq" );
            $page_title = ( isset( $params['action'] ) && $params['action'] == 'edit' ? $edit_title : $create_new_title );
            $submenu_page = [
                'name'     => 'tablesome_admin_page',
                'title'    => "Add/Edit Menu",
                'menu'     => "Add/Edit Menu",
                'callback' => [
                    'controller' => $this,
                    'method'     => 'get_add_template_view',
                ],
            ];
            add_submenu_page(
                'edit.php?post_type=' . HELPIE_MENU_POST_TYPE,
                /* main menu slug */
                $submenu_page["title"],
                /* page title */
                $submenu_page["menu"],
                /* page submenu title */
                'manage_categories',
                /* page roles and capability needed*/
                $submenu_page["name"],
                /* page name */
                array($submenu_page["callback"]["controller"], $submenu_page["callback"]["method"])
            );
        }

        public function get_params_from_url() {
            return [];
        }

        /**
         * Get the current post type using multiple methods
         * 
         * @param object|null $post Post object if available
         * @return string The current post type or empty string if not found
         */
        private function get_current_post_type( $post = null ) {
            // Method 1: Try to get post_type from URL parameter
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading post_type for display context only
            $post_type = ( isset( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '' );
            // Method 2: If not in URL, try to get from global $typenow
            if ( empty( $post_type ) && isset( $GLOBALS['typenow'] ) ) {
                $post_type = sanitize_key( $GLOBALS['typenow'] );
            }
            // Method 3: If editing a post, get post type from post ID
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading post ID for display context only
            if ( empty( $post_type ) && isset( $_GET['post'] ) ) {
                $post_id = absint( $_GET['post'] );
                // Access WordPress functions through global scope
                global $wpdb;
                if ( $post_id > 0 ) {
                    // Direct database query to avoid namespace issues
                    $query = $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_id );
                    $post_type = $wpdb->get_var( $query );
                }
            }
            // Method 4: If we have a post object, use it
            if ( empty( $post_type ) && is_object( $post ) && isset( $post->post_type ) ) {
                $post_type = $post->post_type;
            }
            return $post_type;
        }

        public function get_add_template_view( $post = null ) {
            // Get the current post type
            $post_type = $this->get_current_post_type( $post );
            error_log( "get_add_template_view post_type: " . $post_type );
            // Check if we're on the correct post type
            if ( $post_type != HELPIE_MENU_POST_TYPE ) {
                return;
            }
            error_log( 'get_add_template_view - continuing with helpie_menu post type' );
            // echo "<styl> body{display:none;}</style>";
            $defaults = array(
                'table_mode'     => 'editor',
                'pagination'     => true,
                'last_record_id' => 0,
            );
            $params = $defaults;
            $insight_image = HELPIE_FAQ_URL . '/assets/img/insights.png';
            echo '<section id="content-tease">';
            hfaq_safe_echo( $this->faq_pro_buy_notice_info() );
            // echo '<img src="' . esc_url($insight_image) . '" alt="' . esc_html__("FAQ Insights", "helpie-faq") . '" title="' . esc_html__("FAQ Insights", "helpie-faq") . '">';
            echo '</section>';
        }

        public function faq_pro_buy_notice_info() {
            $html = '';
            $html = "<div class='helpie-notice notice notice-success'>";
            $html .= '<p style="font-weight:bold;">';
            $html .= __( 'In order use this feature you need to purchase and activate the <a href="' . esc_url( admin_url( 'edit.php?post_type=helpie_faq&page=helpie_faq-pricing' ) ) . '">Helpie FAQ Pro</a> plugin.', 'helpie-faq' );
            $html .= '</p>';
            $html .= '</div>';
            return $html;
        }

    }

    // END CLASS
}