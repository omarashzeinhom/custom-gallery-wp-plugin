<?php
/**
 * Plugin Name: Mini Gallery
 * Description: A WordPress plugin to display a simple custom gallery.
 * Version: 1.0
 * Author: Omar Ashraf Zeinhom AbdElRahman | ANDGOEDU
 * License: GPLv2
 */

if (!defined('ABSPATH')) exit;

// Unique prefix for all functions and hooks
function mgwpp_register_post_type() {
    $args = array(
        'public' => true,
        'label' => 'Gallery Image',
        'description' => 'Manage your galleries here',
        'show_in_rest' => false,
        'show_in_menu'=> false,
        'rest_base' => 'gallery-image',
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'capability_type' => 'mgwpp_soora',
        'map_meta_cap' => true,
        'capabilities' => array(
            'edit_post' => 'edit_mgwpp_soora',
            'read_post' => 'read_mgwpp_soora',
            'delete_post' => 'delete_mgwpp_soora',
            'edit_posts' => 'edit_mgwpp_sooras',
            'edit_others_posts' => 'edit_others_mgwpp_sooras',
            'publish_posts' => 'publish_mgwpp_sooras',
            'read_private_posts' => 'read_private_mgwpp_sooras',
            'delete_posts' => 'delete_mgwpp_sooras',
            'delete_private_posts' => 'delete_private_mgwpp_sooras',
            'delete_published_posts' => 'delete_published_mgwpp_sooras',
            'delete_others_posts' => 'delete_others_mgwpp_sooras',
            'edit_private_posts' => 'edit_private_mgwpp_sooras',
            'edit_published_posts' => 'edit_published_mgwpp_sooras',
            'create_posts' => 'create_mgwpp_sooras',
        )
    );
    register_post_type('mgwpp_soora', $args);
}
add_action('init', 'mgwpp_register_post_type');

// Enqueue front-end scripts and styles
function mgwpp_enqueue_assets() {
    // Register scripts and styles
    wp_register_script('mg-carousel', plugin_dir_url(__FILE__) . 'public/js/carousel.js', array(), '1.0', true);
    wp_register_style('mg-styles', plugin_dir_url(__FILE__) . 'public/css/styles.css', array(), '1.0');

    // Enqueue for front-end only
    if (!is_admin()) {
        wp_enqueue_script('mg-carousel');
        wp_enqueue_style('mg-styles');
    }
}
add_action('wp_enqueue_scripts', 'mgwpp_enqueue_assets');

// Enqueue admin assets
function mgwpp_enqueue_admin_assets() {
    // Register scripts and styles
    wp_register_script('mg-admin-carousel', plugin_dir_url(__FILE__) . 'public/admin/js/mg-scripts.js', array('jquery'), '1.0', true);
    wp_register_style('mg-admin-styles', plugin_dir_url(__FILE__) . 'public/admin/css/mg-styles.css', array(), '1.0');

    // Enqueue for admin pages
    wp_enqueue_script('mg-admin-carousel');
    wp_enqueue_style('mg-admin-styles');
}
add_action('admin_enqueue_scripts', 'mgwpp_enqueue_admin_assets');

// Activation & Deactivation Hooks
function mgwpp_plugin_activate() {
    mgwpp_register_post_type();
    mgwpp_add_marketing_team_role();
    mgwpp_capabilities();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mgwpp_plugin_activate');

function mgwpp_plugin_deactivate() {
    unregister_post_type('mgwpp_soora');
    remove_role('marketing_team');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'mgwpp_plugin_deactivate');

// Uninstall Hook
function mgwpp_plugin_uninstall() {
    $sowar = get_posts(array(
        'post_type' => 'mgwpp_soora',
        'numberposts' => -1,
        'post_status' => 'any'
    ));
    foreach ($sowar as $gallery_image) {
        wp_delete_post(intval($gallery_image->ID), true);
    }
    remove_role('marketing_team');
}
register_uninstall_hook(__FILE__, 'mgwpp_plugin_uninstall');

// Roles
function mgwpp_add_marketing_team_role() {
    if (get_role('marketing_team') === null) {
        add_role('marketing_team', 'Marketing Team', array(
            'read' => true,
            'upload_files' => true,
            'edit_files' => true,
            'edit_mgwpp_soora' => true,
            'read_mgwpp_soora' => true,
            'delete_mgwpp_soora' => true,
            'edit_mgwpp_sooras' => true,
            'edit_others_mgwpp_sooras' => true,
            'publish_mgwpp_sooras' => true,
            'read_private_mgwpp_sooras' => true,
            'delete_mgwpp_sooras' => true,
            'delete_private_mgwpp_sooras' => true,
            'delete_published_mgwpp_sooras' => true,
            'delete_others_mgwpp_sooras' => true,
            'edit_private_mgwpp_sooras' => true,
            'edit_published_mgwpp_sooras' => true,
            'create_mgwpp_sooras' => true,
        ));
    }
}
add_action('init', 'mgwpp_add_marketing_team_role');

// Capabilities
function mgwpp_capabilities() {
    $roles = ['administrator', 'marketing_team'];
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('edit_mgwpp_soora');
            $role->add_cap('read_mgwpp_soora');
            $role->add_cap('delete_mgwpp_soora');
            $role->add_cap('edit_mgwpp_sooras');
            $role->add_cap('edit_others_mgwpp_sooras');
            $role->add_cap('publish_mgwpp_sooras');
            $role->add_cap('read_private_mgwpp_sooras');
            $role->add_cap('delete_mgwpp_sooras');
            $role->add_cap('delete_private_mgwpp_sooras');
            $role->add_cap('delete_published_mgwpp_sooras');
            $role->add_cap('delete_others_mgwpp_sooras');
            $role->add_cap('edit_private_mgwpp_sooras');
            $role->add_cap('edit_published_mgwpp_sooras');
            $role->add_cap('create_mgwpp_sooras');
        }
    }
}
add_action('admin_init', 'mgwpp_capabilities');

// Admin Menu
function mgwpp_menu() {
    if (current_user_can('edit_mgwpp_sooras')) {
        add_menu_page('Add New Gallery', 'Gallery', 'edit_mgwpp_sooras', 'mini-gallery', 'mgwpp_plugin_page', 'dashicons-format-gallery', 6);
    }
}
add_action('admin_menu', 'mgwpp_menu');

// Handle File Uploads
function mgwpp_upload() {
    if (!isset($_POST['mgwpp_upload_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mgwpp_upload_nonce'])), 'mgwpp_upload_nonce')) {
        wp_die('Security check');
    }

    if (!empty($_FILES['sowar']) && !empty($_POST['image_title']) && !empty($_POST['gallery_type'])) {
        $files = $_FILES['sowar'];
        $title = sanitize_text_field($_POST['image_title']);
        $gallery_type = sanitize_text_field($_POST['gallery_type']); // Get the gallery type

        // Create a new post for the gallery
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'mgwpp_soora',
            'post_status' => 'publish'
        ));

        if ($post_id) {
            // Save the gallery type as post meta
            update_post_meta($post_id, 'gallery_type', $gallery_type);

            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );

                    $file_type = wp_check_filetype($file['name']);
                    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
                    if (in_array($file_type['type'], $allowed_types)) {
                        $uploaded = wp_handle_upload($file, array('test_form' => false));
                        if (isset($uploaded['file'])) {
                            $file_path = $uploaded['file'];
                            $file_url = $uploaded['url'];
                            $attachment_id = wp_insert_attachment(array(
                                'guid' => $file_url,
                                'post_mime_type' => $file_type['type'],
                                'post_title' => $title,
                                'post_content' => '',
                                'post_status' => 'inherit'
                            ), $file_path, $post_id);

                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                            $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
                            wp_update_attachment_metadata($attachment_id, $attach_data);
                        }
                    }
                }
            }
        }
    }

    wp_redirect(admin_url('admin.php?page=mini-gallery'));
    exit;
}
add_action('admin_post_mgwpp_upload', 'mgwpp_upload');

// Handle Gallery Deletion
function mgwpp_delete_gallery() {
    if (!isset($_GET['gallery_id']) || !isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'mgwpp_delete_gallery')) {
        wp_die('Security check failed');
    }

    $gallery_id = intval($_GET['gallery_id']);
    
    if (!current_user_can('delete_mgwpp_soora', $gallery_id)) {
        wp_die('You do not have permission to delete this gallery');
    }

    wp_delete_post($gallery_id, true);
    wp_redirect(admin_url('admin.php?page=mini-gallery'));
    exit;
}
add_action('admin_post_mgwpp_delete_gallery', 'mgwpp_delete_gallery');


function mgwpp_plugin_page() {
    echo '<h1>' . esc_html__('Mini Gallery', 'mini-gallery') . '</h1>';

    // Form to upload new gallery images
    echo '<h2>' . esc_html__('Upload New Images', 'mini-gallery') . '</h2>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
    echo '<input type="hidden" name="action" value="mgwpp_upload">';
    echo '<input type="hidden" name="mgwpp_upload_nonce" value="' . esc_attr(wp_create_nonce('mgwpp_upload_nonce')) . '">';
    
    echo '<label for="sowar">' . esc_html__('Select Images:', 'mini-gallery') . '</label>';
    echo '<input type="file" id="sowar" name="sowar[]" accept="image/*" required multiple>';
    echo '<br><br>';
    
    echo '<label for="image_title">' . esc_html__('Gallery Title:', 'mini-gallery') . '</label>';
    echo '<input type="text" id="image_title" name="image_title" required>';
    echo '<br><br>';
    
    // Dropdown for gallery type
    echo '<label for="gallery_type">' . esc_html__('Gallery Type:', 'mini-gallery') . '</label>';
    echo '<select id="gallery_type" name="gallery_type" required>';
    echo '<option value="single_carousel">' . esc_html__('Single Carousel', 'mini-gallery') . '</option>';
    echo '<option value="multi_carousel">' . esc_html__('Multi Carousel', 'mini-gallery') . '</option>';
    echo '<option value="grid">' . esc_html__('Grid Layout', 'mini-gallery') . '</option>';
    echo '</select>';
    echo '<br><br>';
    
    echo '<input type="submit" class="button button-primary" value="' . esc_attr__('Upload Images', 'mini-gallery') . '">';
    echo '</form>';

    // Display existing galleries with their IDs and shortcodes
    echo '<h2>' . esc_html__('Existing Galleries', 'mini-gallery') . '</h2>';
    $galleries = get_posts(['post_type' => 'mgwpp_soora', 'numberposts' => -1]);
    if ($galleries) {
        foreach ($galleries as $gallery) {
            echo '<div>';
            echo '<h3 class="text-center">' . esc_html($gallery->post_title) . ' (ID: ' . esc_html($gallery->ID) . ')</h3>';
            echo '<p>' . esc_html($gallery->post_content) . '</p>';

            // Display the gallery type
            $gallery_type = get_post_meta($gallery->ID, 'gallery_type', true);
            echo '<p>' . esc_html__('Gallery Type: ', 'mini-gallery') . esc_html(ucfirst($gallery_type)) . '</p>';

            // Display the carousel preview using the shortcode
            echo do_shortcode('[mgwpp_gallery id="' . esc_attr($gallery->ID) . '"]');
            echo '<hr>';
            // Display the shortcode dynamically with the post ID
            echo '<p>' . esc_html__('Shortcode to display this gallery:', 'mini-gallery') . '</p>';
            echo '<pre>' . esc_html('[mgwpp_gallery id="' . esc_attr($gallery->ID) . '"]') . '</pre>';
            // Add delete link
            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=mgwpp_delete_gallery&gallery_id=' . esc_attr($gallery->ID)), 'mgwpp_delete_gallery');
            echo '<p><a href="' . esc_url($delete_url) . '" class="button button-secondary">' . esc_html__('Delete Gallery', 'mini-gallery') . '</a></p>';
            echo '</div>';
            echo '<hr>';
        }
    } else {
        echo '<p>' . esc_html__('No galleries found.', 'mini-gallery') . '</p>';
    }
}



// Shortcode to display gallery
// Shortcode to display gallery
function mgwpp_gallery_shortcode($atts)
{
    $atts = shortcode_atts(['id' => ''], $atts);
    $post_id = intval($atts['id']);
    $output = '';

    if ($post_id) {
        // Retrieve the gallery type from post meta
        $gallery_type = get_post_meta($post_id, 'gallery_type', true);
        if (!$gallery_type) {
            $gallery_type = 'single_carousel'; // Fallback to default if not set
        }

        $images = get_attached_media('image', $post_id);
        if ($images) {
            if ($gallery_type === 'single_carousel') {
                $output .= '<div id="mg-carousel" class="mg-gallery-single-carousel">';
                foreach ($images as $image) {
                    $imgwpp_url = wp_get_attachment_image_src($image->ID, 'medium');
                    $output .= '<div class="carousel-slide"><img src="' . esc_url($imgwpp_url[0]) . '" alt="' . esc_attr($image->post_title) . '" loading="lazy"></div>';
                }
                $output .= '</div>';
            } elseif ($gallery_type === 'multi_carousel') {
                $output .= '<div id="mg-multi-carousel" class="mg-gallery multi-carousel">';
                foreach ($images as $image) {
                    $imgwpp_url = wp_get_attachment_image_src($image->ID, 'medium');
                    $output .= '<div class="mg-multi-carousel-slide"><img src="' . esc_url($imgwpp_url[0]) . '" alt="' . esc_attr($image->post_title) . '" loading="lazy"></div>';
                }
                $output .= '</div>';
            } elseif ($gallery_type === 'grid') {
                $output .= '<div class="grid-layout">';
                foreach ($images as $image) {
                    $imgwpp_url = wp_get_attachment_image_src($image->ID, 'medium');
                    $output .= '<div class="grid-item"><img src="' . esc_url($imgwpp_url[0]) . '" alt="' . esc_attr($image->post_title) . '" loading="lazy"></div>';
                }
                $output .= '</div>';
            }
        } else {
            $output .= '<p>No images found for this gallery.</p>';
        }
    } else {
        $output .= '<p>Invalid gallery ID.</p>';
    }
    return $output;
}
add_shortcode('mgwpp_gallery', 'mgwpp_gallery_shortcode');
