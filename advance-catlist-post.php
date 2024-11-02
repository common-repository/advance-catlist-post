<?php
/*
Plugin Name: Advance Catlist Post
Plugin URI: https://biharinfozone.in/advance-catlist-post
Description: A customizable plugin that displays posts from a user-defined category with options for date modification and the number of posts. If no category is provided, shows the latest posts. Elementor compatible.
Version: 1.6
Author: Pawan Jagriti
Author URI: https://biharinfozone.in/author/pawanjagriti
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: advance-catlist-post
Domain Path: /languages
*/

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Add Web Docs link in the plugin meta row
function advance_catlist_post_plugin_meta($links, $file) {
    if ($file === 'advance-catlist-post/advance-catlist-post.php') {
        $links[] = '<a href="https://github.com/pawanhost/Advance-Catlist-Post-Docs.git" target="_blank">Web Docs</a>';
    }
    return $links;
}
add_filter('plugin_row_meta', 'advance_catlist_post_plugin_meta', 10, 2);

// Enqueue the CSS file
function advance_catlist_post_enqueue_styles() {
    wp_enqueue_style('advance-catlist-post-style', plugin_dir_url(__FILE__) . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'advance_catlist_post_enqueue_styles');

// Shortcode Logic
function advance_catlist_post_shortcode($atts) {
    $default_number_of_posts = get_option('default_number_of_posts', 5);
    $show_date = get_option('show_date', 'no'); // Get the setting for showing date
    $show_new_gif = get_option('show_new_gif', 'no');
    $new_gif_post_count = get_option('new_gif_post_count', 3); // Default number of posts for New GIF

    $atts = shortcode_atts(array(
        'name' => '',
        'date_modified' => 'no',
        'date_class' => 'icp_date',
        'numberposts' => $default_number_of_posts,
    ), $atts, 'catlist');

    $args = array(
        'posts_per_page' => intval($atts['numberposts']),
    );

    if (!empty($atts['name'])) {
        $args['category_name'] = sanitize_text_field($atts['name']);
    }

    $query = new WP_Query($args);
    $output = '<ul class="advance-catlist-posts">';

    if ($query->have_posts()) {
        $post_count = 0; // Counter to display "New" GIF for top posts

        while ($query->have_posts()) {
            $query->the_post();
            $post_count++; // Increment counter per post

            $output .= '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a>';

            // Show "New" GIF for top posts as per the setting
            if ($show_new_gif === 'yes' && $post_count <= $new_gif_post_count) {
                $output .= ' <img src="' . plugin_dir_url(__FILE__) . 'assets/images/new.webp" alt="New" class="new-gif" style="width: 20px; height: auto; vertical-align: middle; mix-blend-mode: multiply;"/>';
            }

            // Show published or modified date as per settings
            if (strtolower($show_date) === 'yes') {
                $output .= '<span class="' . esc_attr($atts['date_class']) . '"> - ' . esc_html(get_the_date()) . '</span>';
            }
            if (strtolower(sanitize_text_field($atts['date_modified'])) === 'yes') {
                $output .= '<span class="' . esc_attr($atts['date_class']) . '"> - ' . esc_html(get_the_modified_date()) . '</span>';
            }

            $output .= '</li>';
        }
    } else {
        $output .= '<li>' . esc_html__('No posts found', 'advance-catlist-post') . '</li>';
    }

    $output .= '</ul>';

    wp_reset_postdata();
    return $output;
}
add_shortcode('catlist', 'advance_catlist_post_shortcode');

// Add Settings Page
function advance_catlist_post_add_admin_menu() {
    add_menu_page(
        esc_html__('Advance Catlist Post', 'advance-catlist-post'),
        esc_html__('Catlist Settings', 'advance-catlist-post'),
        'manage_options',
        'advance_catlist_post',
        'advance_catlist_post_options_page',
        'dashicons-admin-settings',
        20
    );
}
add_action('admin_menu', 'advance_catlist_post_add_admin_menu');

function advance_catlist_post_options_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Advance Catlist Post Settings', 'advance-catlist-post'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('advance_catlist_post_settings_group');
            do_settings_sections('advance_catlist_post');
            submit_button();
            ?>
        </form>

        <hr>

        <!-- Shortcode Generator Section -->
        <h2><?php esc_html_e('Shortcode Generator', 'advance-catlist-post'); ?></h2>
        <div id="shortcode-generator">
            <label for="catlist_category"><?php esc_html_e('Select Category', 'advance-catlist-post'); ?></label>
            <select id="catlist_category">
                <option value=""><?php esc_html_e('Latest Posts', 'advance-catlist-post'); ?></option>
                <?php
                $categories = get_categories();
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>

            <br><br>

            <label for="catlist_show_posts"><?php esc_html_e('Customize Number of Posts', 'advance-catlist-post'); ?></label>
            <input type="checkbox" id="catlist_show_posts" />

            <br><br>

            <div id="catlist_posts_input_container" style="display: none;">
                <label for="catlist_numberposts"><?php esc_html_e('Number of Posts', 'advance-catlist-post'); ?></label>
                <input type="number" id="catlist_numberposts" min="1" value="5" />
            </div>

            <br><br>

            <button id="generate_shortcode" class="button button-primary"><?php esc_html_e('Generate Shortcode', 'advance-catlist-post'); ?></button>
            <input type="text" id="generated_shortcode" readonly style="width: 100%; margin-top: 10px;" />
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Toggle Number of Posts Input
            $('#catlist_show_posts').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#catlist_posts_input_container').show();
                } else {
                    $('#catlist_posts_input_container').hide();
                }
            });

            // Generate Shortcode
            $('#generate_shortcode').on('click', function(e) {
                e.preventDefault();
                var category = $('#catlist_category').val();
                var showPosts = $('#catlist_show_posts').is(':checked');
                var numberPosts = $('#catlist_numberposts').val();
                var shortcode = '[catlist';

                if (category) {
                    shortcode += ' name="' + category + '"';
                }

                if (showPosts) {
                    shortcode += ' numberposts="' + numberPosts + '"';
                }

                shortcode += ']';

                $('#generated_shortcode').val(shortcode);
            });
        });
    </script>
    <?php
}

// Register Settings
function advance_catlist_post_register_settings() {
    register_setting('advance_catlist_post_settings_group', 'default_category');
    register_setting('advance_catlist_post_settings_group', 'default_number_of_posts');
    register_setting('advance_catlist_post_settings_group', 'show_date'); // New setting for showing date
    register_setting('advance_catlist_post_settings_group', 'show_new_gif');
}
add_action('admin_init', 'advance_catlist_post_register_settings');

function advance_catlist_post_settings_init() {
    add_settings_section(
        'advance_catlist_post_section',
        esc_html__('Default Settings', 'advance-catlist-post'),
        'advance_catlist_post_section_callback',
        'advance_catlist_post'
    );

    add_settings_field(
        'default_category',
        esc_html__('Default Category', 'advance-catlist-post'),
        'default_category_render',
        'advance_catlist_post',
        'advance_catlist_post_section'
    );

    add_settings_field(
        'default_number_of_posts',
        esc_html__('Default Number of Posts', 'advance-catlist-post'),
        'default_number_of_posts_render',
        'advance_catlist_post',
        'advance_catlist_post_section'
    );

    add_settings_field(
        'show_date',
        esc_html__('Show Published Date', 'advance-catlist-post'),
        'show_date_render',
        'advance_catlist_post',
        'advance_catlist_post_section'
    );
    
    add_settings_field(
        'show_new_gif',
        esc_html__('Show "New" GIF after Title', 'advance-catlist-post'),
        'show_new_gif_render',
        'advance_catlist_post',
        'advance_catlist_post_section'
    );
    
    
add_settings_field(
    'new_gif_post_count',
    esc_html__('Number of Posts to Show "New" GIF', 'advance-catlist-post'),
    'new_gif_post_count_render',
    'advance_catlist_post',
    'advance_catlist_post_section'
);

}
add_action('admin_init', 'advance_catlist_post_settings_init');

function default_category_render() {
    $default_category = get_option('default_category');
    ?>
    <input type="text" name="default_category" value="<?php echo esc_attr($default_category); ?>">
    <?php
}

function default_number_of_posts_render() {
    $default_number_of_posts = get_option('default_number_of_posts');
    ?>
    <input type="number" name="default_number_of_posts" value="<?php echo esc_attr($default_number_of_posts); ?>" min="1">
    <?php
}

// Render the new show date setting
function show_date_render() {
    $show_date = get_option('show_date', 'no');
    ?>
    <select name="show_date">
        <option value="yes" <?php selected($show_date, 'yes'); ?>><?php esc_html_e('Yes', 'advance-catlist-post'); ?></option>
        <option value="no" <?php selected($show_date, 'no'); ?>><?php esc_html_e('No', 'advance-catlist-post'); ?></option>
    </select>
    <?php
}

// Render the new "New GIF" setting
function show_new_gif_render() {
    $show_new_gif = get_option('show_new_gif', 'no');
    ?>
    <select name="show_new_gif">
        <option value="yes" <?php selected($show_new_gif, 'yes'); ?>><?php esc_html_e('Yes', 'advance-catlist-post'); ?></option>
        <option value="no" <?php selected($show_new_gif, 'no'); ?>><?php esc_html_e('No', 'advance-catlist-post'); ?></option>
    </select>
    <?php
}

function new_gif_post_count_render() {
    $new_gif_post_count = get_option('new_gif_post_count', 3);
    ?>
    <select name="new_gif_post_count">
        <option value="1" <?php selected($new_gif_post_count, 1); ?>>1</option>
        <option value="2" <?php selected($new_gif_post_count, 2); ?>>2</option>
        <option value="3" <?php selected($new_gif_post_count, 3); ?>>3</option>
        <option value="5" <?php selected($new_gif_post_count, 5); ?>>5</option>
        <option value="7" <?php selected($new_gif_post_count, 7); ?>>7</option>
    </select>
    <?php
}
register_setting('advance_catlist_post_settings_group', 'new_gif_post_count');


function advance_catlist_post_section_callback() {
    echo esc_html__('Configure the default settings for the Advance Catlist Post plugin.', 'advance-catlist-post');
}

// Add Submenu for FlexiShare Pro Addon
function advance_catlist_post_addon_menu() {
    add_submenu_page(
        'advance_catlist_post', // Parent slug
        __('Advance Catlist Addon', 'advance-catlist-post'), // Page title
        __('Add-Ons', 'advance-catlist-post'), // Menu title
        'manage_options', // Capability
        'addon', // Menu slug
        'advance_catlist_post_addon_page' // Callback function to render the page
    );
}
add_action('admin_menu', 'advance_catlist_post_addon_menu');

function advance_catlist_post_addon_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Popular Add-ons, New Possibilities.', 'advance-catlist-post'); ?></h1>
        <p><?php esc_html_e('Enhance your website with these add-ons. Increase productivity, creativity, and unlock new features.', 'advance-catlist-post'); ?></p>

        <div class="addons-grid">
            <div class="addon-card">
                <div class="addon-icon" style="display: flex; justify-content: space-between;">
                    <img src="https://biharinfozone.in/wp-content/uploads/2024/10/FlexiShare-pro.png" alt="FlexiShare Pro">
                    <div class="addon-new-text"><?php esc_html_e('New', 'advance-catlist-post'); ?></div>
                </div>
                <h3 style="text-align:left;"><?php esc_html_e('FlexiShare Pro', 'advance-catlist-post'); ?></h3>
                <p style="text-align:left;"><?php esc_html_e('Customizable Floating Share Button. Share content on social media with ease.', 'advance-catlist-post'); ?></p>

                <?php
                // Define the plugin slug and file
                $plugin_file = 'flexishare-pro/flexishare-pro.php';

                // Check if the plugin is installed
                if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
                    // Plugin is installed
                    if (is_plugin_active($plugin_file)) {
                        // Plugin is active
                        echo '<button class="button button-secondary" disabled>' . esc_html__('Installed', 'advance-catlist-post') . '</button>';
                    } else {
                        // Plugin is installed but not active
                        $activate_url = wp_nonce_url(admin_url('plugins.php?action=activate&plugin=' . $plugin_file), 'activate-plugin_' . $plugin_file);
                        echo '<a href="' . esc_url($activate_url) . '" class="button button-primary">' . esc_html__('Activate', 'advance-catlist-post') . '</a>';
                    }
                } else {
                    // Plugin is not installed, show the Install button
                    $install_url = wp_nonce_url(admin_url('update.php?action=install-plugin&plugin=flexishare-pro&_wpnonce=' . wp_create_nonce('install-plugin_flexishare-pro')), 'install-plugin_flexishare-pro');
                    echo '<a href="' . esc_url($install_url) . '" class="button button-primary">' . esc_html__('Install', 'advance-catlist-post') . '</a>';
                }
                ?>
            </div>

            <!-- You can continue adding more add-ons as needed here -->
        </div>
    </div>

    <style>
        /* Add-ons Grid */
        .addons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .view-addons-details {
            text-decoration: none;
        }

        .addon-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }

        .addon-new-text {
            background: #ecfdf5;
            color: #047857;
            border-radius: 100px;
            padding: 3px 8px;
            height: fit-content;
        }

        .addon-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .addon-icon img {
            width: 64px;
            height: 64px;
            margin-bottom: 15px;
        }

        .addon-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .addon-card p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .addon-card .button {
            font-size: 14px;
            transition: all 0.3s;
        }

        .addon-card .button-primary {
            background-color: #0073aa;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }

        .addon-card .button-primary:hover {
            background-color: #005177;
        }
    </style>

    <?php
}