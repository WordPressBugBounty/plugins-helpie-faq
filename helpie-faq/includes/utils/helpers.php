<?php

namespace HelpieFaq\Includes\Utils;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\HelpieFaq\Includes\Utils\Helpers')) {
    class Helpers
    {

        public function content_setup()
        {
            $taxonomy = 'helpie_faq_category';
            $post_info = array();
            $post_info['term-1'] = $this->insert_term_with_post('helpie_faq', 'a-term-1', 'helpie_faq_category', 'Term1 Post Title');
            $post_info['term-2'] = $this->insert_term_with_post('helpie_faq', 'd-term-2', 'helpie_faq_category', 'Term2 Post Title');
            $post_info['term-3'] = $this->insert_term_with_post('helpie_faq', 'c-term-3', 'helpie_faq_category', 'Term3 Post Title');
            $post_info['term-4'] = $this->insert_term_with_post('helpie_faq', 'b-term-4', 'helpie_faq_category', 'Term4 Post Title');
            $term_info_5 = wp_insert_term('f-term-5', $taxonomy);
            $post_info['term-5'] = array(
                0 => '',
                1 => $term_info_5['term_id'],
            );

            return $post_info;
        }

        public function create_new_user($role = 'subscriber', $username = 'subman', $password = 'subpass', $email = 'submail@pauple.com')
        {
            $user_id = wp_create_user($username, $password, $email);
            $userdata = array('ID' => $user_id, 'role' => $role);
            wp_update_user($userdata);
            // error_log('create_new_user: ' . $role);

            return $user_id;
        }

        public function insert_term_with_post($post_type, $term_value, $taxonomy, $post_title = 'random', $post_content = 'demo text', $parent_term_id = 0)
        {
            if (!term_exists($term_value, $taxonomy, $parent_term_id)) {
                // echo "parent_term_id: " . $parent_term_id;
                $term_info = wp_insert_term($term_value, $taxonomy, array('parent' => $parent_term_id));
                $term_id = $term_info['term_id'];
            } else {
                $term = get_term_by('slug', $term_value, $taxonomy);
                $term_id = $term->term_id;
            }

            $post_id = wp_insert_post(array('post_title' => $post_title, 'post_type' => $post_type, 'post_content' => $post_content, 'post_status' => 'publish'));

            $question_type = add_post_meta($post_id, 'question_types', array('faq'), true);

            $cat_ids = array_map('intval', (array) $term_id);
            $cat_ids = array_unique($cat_ids);
            wp_set_object_terms($post_id, $cat_ids, $taxonomy);
            return [$post_id, $term_id];
        }

        public function insert_post_to_child_term($post_type, $term_value, $taxonomy, $parent_term)
        {
            $term_info = wp_insert_term($term_value, $taxonomy, array('parent' => $parent_term));
            $term_id = $term_info['term_id'];
            $post_id = wp_insert_post(array('post_title' => 'random', 'post_type' => $post_type, 'post_content' => 'demo text', 'post_status' => 'publish'));

            $cat_ids = array_map('intval', (array) $term_id);
            $cat_ids = array_unique($cat_ids);
            wp_set_object_terms($post_id, $cat_ids, $taxonomy);

            return [$post_id, $term_id];
        }

        public function insert_post_with_term($post_type, $term_id, $taxonomy, $post_title = 'random', $post_content = 'demo text')
        {
            $post_id = wp_insert_post(array('post_title' => $post_title, 'post_type' => $post_type, 'post_content' => $post_content, 'post_status' => 'publish'));
            $cat_ids = array_map('intval', (array) $term_id);
            $cat_ids = array_unique($cat_ids);
            wp_set_object_terms($post_id, $cat_ids, $taxonomy);
            return $post_id;
        }

        public function css_to_string($css)
        {
            $inline = '';
            foreach ($css as $key => $value) {
                $inline .= $key . ":" . $value . ";";
            }
            return $inline;
        }

        public function insert_faq_group_metadata($post, $props)
        {
            $category_id = $props['category_id'];
            $faq_groups = array(
                array(
                    'faq_item' => array(
                        'post_id' => $post->ID,
                        'title' => $post->post_title,
                        'content' => $post->post_content,
                        'categories' => [$category_id],
                    ),
                ),
            );
            update_term_meta($props['group_id'], 'helpie_faq_group_items', ['faq_groups' => $faq_groups]);
            return $props['group_id'];
        }

        public function create_uncategorized_faq_term()
        {
            $name = HELPIE_FAQ_DEFAULT_CATEGORY;
            $taxonomy = 'helpie_faq_category';
            $parent_term_id = 0;

            if (!term_exists($name, $taxonomy, $parent_term_id)) {
                $term_info = wp_insert_term($name, $taxonomy, array('parent' => $parent_term_id));
                $term_id = $term_info['term_id'];
            } else {
                $term = get_term_by('slug', $name, $taxonomy);
                $term_id = $term->term_id;
            }
            /** update the term into option table */
            update_option(HELPIE_FAQ_DEFAULT_CATEGORY_OPTION, $term_id);

            return $term_id;
        }

        public function get_default_category_term_id()
        {
            /*** Get the faq default category term id */
            $term_id = get_option(HELPIE_FAQ_DEFAULT_CATEGORY_OPTION);

            /** create the uncategory faq term if not the HELPIE_FAQ_DEFAULT_CATEGORY_OPTION */
            if (empty($term_id)) {
                $term_id = $this->create_uncategorized_faq_term();
            }
            return $term_id;
        }

        public function get_the_category_term_by_id($term_id)
        {
            $term = get_term($term_id, 'helpie_faq_category');
            return $term;
        }

        public function sanitizing_the_array_values($data)
        {
            if (!is_array($data)) {
                return $data;
            }
            if (empty($data)) {
                return [];
            }
            $sanitized_data = [];
            foreach ($data as $array_key => $value) {
                $sanitize_key = sanitize_text_field($array_key);
                if (is_array($value)) {
                    $sanitized_data[$sanitize_key] = $this->sanitizing_the_array_values($value);
                } else {
                    $sanitized_data[$sanitize_key] = sanitize_textarea_field($value);
                }
            }
            return $sanitized_data;
        }

        /**
         * Convert term names/slugs to term IDs
         * Supports both numeric term IDs and term names/slugs
         *
         * @param array $terms Array of term values (can be term IDs or names/slugs)
         * @param string $taxonomy Taxonomy name
         * @return array Array of term IDs
         */
        public static function convert_terms_to_ids($terms, $taxonomy)
        {
            if (!is_array($terms)) {
                return array();
            }

            if (!taxonomy_exists($taxonomy)) {
                return array();
            }

            $term_ids = array();

            foreach ($terms as $term_value) {
                // Trim whitespace
                $term_value = trim($term_value);

                if (empty($term_value)) {
                    continue;
                }

                // If it's already a numeric term ID, use it directly
                if (is_numeric($term_value)) {
                    $term_ids[] = (int) $term_value;
                    continue;
                }

                // Try to find term by name first
                $term = get_term_by('name', $term_value, $taxonomy);

                // If not found by name, try by slug
                if (!$term || is_wp_error($term)) {
                    $term = get_term_by('slug', $term_value, $taxonomy);
                }

                // If term found, add its ID
                if ($term && !is_wp_error($term)) {
                    $term_ids[] = (int) $term->term_id;
                }
            }

            return array_unique($term_ids);
        }

        /**
         * Check if an IP address is in CloudFlare's IP ranges
         *
         * @param string $ip IP address to check
         * @return bool True if IP is in CloudFlare ranges
         */
        private static function is_cloudflare_ip($ip)
        {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                return false;
            }

            // CloudFlare IPv4 ranges (updated as of 2024)
            $cloudflare_ranges = array(
                '173.245.48.0/20',
                '103.21.244.0/22',
                '103.22.200.0/22',
                '103.31.4.0/22',
                '141.101.64.0/18',
                '108.162.192.0/18',
                '190.93.240.0/20',
                '188.114.96.0/20',
                '197.234.240.0/22',
                '198.41.128.0/17',
                '162.158.0.0/15',
                '104.16.0.0/13',
                '104.24.0.0/14',
                '172.64.0.0/13',
                '131.0.72.0/22',
            );

            // CloudFlare IPv6 ranges
            $cloudflare_ranges_v6 = array(
                '2400:cb00::/32',
                '2606:4700::/32',
                '2803:f800::/32',
                '2405:b500::/32',
                '2405:8100::/32',
                '2a06:98c0::/29',
                '2c0f:f248::/32',
            );

            // Check IPv4 ranges
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                foreach ($cloudflare_ranges as $range) {
                    if (self::ip_in_range($ip, $range)) {
                        return true;
                    }
                }
            }

            // Check IPv6 ranges
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                foreach ($cloudflare_ranges_v6 as $range) {
                    if (self::ipv6_in_range($ip, $range)) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * Check if an IPv4 address is within a CIDR range
         *
         * @param string $ip IPv4 address to check
         * @param string $range CIDR range (e.g., '192.168.1.0/24')
         * @return bool True if IP is in range
         */
        private static function ip_in_range($ip, $range)
        {
            if (strpos($range, '/') === false) {
                return $ip === $range;
            }

            list($subnet, $mask) = explode('/', $range);

            // IPv4 CIDR check
            $ip_long = ip2long($ip);
            $subnet_long = ip2long($subnet);
            
            if ($ip_long === false || $subnet_long === false) {
                return false;
            }
            
            $mask_long = -1 << (32 - (int)$mask);
            return ($ip_long & $mask_long) === ($subnet_long & $mask_long);
        }

        /**
         * Check if IPv6 address is in CIDR range
         *
         * @param string $ip IPv6 address to check
         * @param string $range CIDR range (e.g., '2400:cb00::/32')
         * @return bool True if IP is in range
         */
        private static function ipv6_in_range($ip, $range)
        {
            if (strpos($range, '/') === false) {
                return $ip === $range;
            }

            list($subnet, $mask) = explode('/', $range);
            $mask = (int)$mask;

            // Convert IPs to binary
            $ip_bin = inet_pton($ip);
            $subnet_bin = inet_pton($subnet);

            if ($ip_bin === false || $subnet_bin === false) {
                return false;
            }

            // Calculate number of full bytes and remaining bits
            $full_bytes = intval($mask / 8);
            $remaining_bits = $mask % 8;

            // Compare full bytes
            for ($i = 0; $i < $full_bytes; $i++) {
                if ($ip_bin[$i] !== $subnet_bin[$i]) {
                    return false;
                }
            }

            // Compare remaining bits in the next byte
            if ($remaining_bits > 0 && $full_bytes < 16) {
                $mask_byte = (0xFF << (8 - $remaining_bits)) & 0xFF;
                $ip_byte = ord($ip_bin[$full_bytes]) & $mask_byte;
                $subnet_byte = ord($subnet_bin[$full_bytes]) & $mask_byte;
                if ($ip_byte !== $subnet_byte) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Get client IP address for rate limiting
         * Security: Validates proxy headers to prevent IP spoofing attacks
         *
         * @return string Client IP address or '0.0.0.0' if not determinable
         */
        public static function get_client_ip()
        {
            // Always start with REMOTE_ADDR - most secure, cannot be spoofed
            $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
            
            if (!empty($remote_addr) && filter_var($remote_addr, FILTER_VALIDATE_IP)) {
                // Check if request is from CloudFlare
                if (self::is_cloudflare_ip($remote_addr)) {
                    // Only trust CloudFlare header if request actually comes from CloudFlare
                    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                        $cf_ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP']));
                        // Handle comma-separated IPs
                        if (strpos($cf_ip, ',') !== false) {
                            $cf_ip = trim(explode(',', $cf_ip)[0]);
                        }
                        if (filter_var($cf_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                            return $cf_ip;
                        }
                    }
                }

                // Allow sites behind known proxies to configure trusted proxy IPs
                // Use filter to allow customization: apply_filters('helpie_faq_trusted_proxy_ips', array())
                $trusted_proxies = apply_filters('helpie_faq_trusted_proxy_ips', array());
                
                if (!empty($trusted_proxies) && is_array($trusted_proxies) && in_array($remote_addr, $trusted_proxies, true)) {
                    // Request is from a trusted proxy, check X-Forwarded-For or X-Real-IP
                    $forwarded_headers = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP');
                    
                    foreach ($forwarded_headers as $header) {
                        if (!empty($_SERVER[$header])) {
                            $ip = sanitize_text_field(wp_unslash($_SERVER[$header]));
                            // Handle comma-separated IPs (X-Forwarded-For)
                            if (strpos($ip, ',') !== false) {
                                $ip = trim(explode(',', $ip)[0]);
                            }
                            // Only accept public IPs (not private/reserved ranges)
                            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                                return $ip;
                            }
                        }
                    }
                }

                // Default: return REMOTE_ADDR (most secure)
                return $remote_addr;
            }

            return '0.0.0.0';
        }

    } // END CLASS
}
