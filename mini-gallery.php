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
function mg_register_post_type() {
    $args = array(
        'public' => true,
        'label' => 'Gallery Image',
        'description' => 'Manage your galleries here',
        'show_in_rest' => true,
        'rest_base' => 'gallery-image',
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive' => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'capability_type' => 'galleryimage',
        'map_meta_cap' => true,
        'capabilities' => array(
            'edit_post' => 'edit_galleryimage',
            'read_post' => 'read_galleryimage',
            'delete_post' => 'delete_galleryimage',
            'edit_posts' => 'edit_galleryimages',
            'edit_others_posts' => 'edit_others_galleryimages',
            'publish_posts' => 'publish_galleryimages',
            'read_private_posts' => 'read_private_galleryimages',
            'delete_posts' => 'delete_galleryimages',
            'delete_private_posts' => 'delete_private_galleryimages',
            'delete_published_posts' => 'delete_published_galleryimages',
            'delete_others_posts' => 'delete_others_galleryimages',
            'edit_private_posts' => 'edit_private_galleryimages',
            'edit_published_posts' => 'edit_published_galleryimages',
            'create_posts' => 'create_galleryimages',
        )
    );
    register_post_type('galleryimage', $args);
}
add_action('init', 'mg_register_post_type');

// Enqueue front-end scripts and styles
function mg_enqueue_assets() {
    // Register scripts and styles
    wp_register_script('mg-carousel', plugin_dir_url(__FILE__) . 'public/js/carousel.js', array(), '1.0', true);
    wp_register_style('mg-styles', plugin_dir_url(__FILE__) . 'public/css/styles.css', array(), '1.0');

    // Enqueue for front-end only
    if (!is_admin()) {
        wp_enqueue_script('mg-carousel');
        wp_enqueue_style('mg-styles');
    }
}
add_action('wp_enqueue_scripts', 'mg_enqueue_assets');

// Enqueue admin assets
function mg_enqueue_admin_assets() {
    // Register scripts and styles
    wp_register_script('mg-admin-carousel', plugin_dir_url(__FILE__) . 'public/admin/js/mg-scripts.js', array('jquery'), '1.0', true);
    wp_register_style('mg-admin-styles', plugin_dir_url(__FILE__) . 'public/admin/css/mg-styles.css', array(), '1.0');

    // Enqueue for admin pages
    wp_enqueue_script('mg-admin-carousel');
    wp_enqueue_style('mg-admin-styles');
}
add_action('admin_enqueue_scripts', 'mg_enqueue_admin_assets');

// Activation & Deactivation Hooks
function mg_plugin_activate() {
    mg_register_post_type();
    mg_add_marketing_team_role();
    mg_capabilities();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mg_plugin_activate');

function mg_plugin_deactivate() {
    unregister_post_type('galleryimage');
    remove_role('marketing_team');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'mg_plugin_deactivate');

// Uninstall Hook
function mg_plugin_uninstall() {
    $gallery_images = get_posts(array(
        'post_type' => 'galleryimage',
        'numberposts' => -1,
        'post_status' => 'any'
    ));
    foreach ($gallery_images as $gallery_image) {
        wp_delete_post(intval($gallery_image->ID), true);
    }
    remove_role('marketing_team');
}
register_uninstall_hook(__FILE__, 'mg_plugin_uninstall');

// Roles
function mg_add_marketing_team_role() {
    if (get_role('marketing_team') === null) {
        add_role('marketing_team', 'Marketing Team', array(
            'read' => true,
            'upload_files' => true,
            'edit_files' => true,
            'edit_galleryimage' => true,
            'read_galleryimage' => true,
            'delete_galleryimage' => true,
            'edit_galleryimages' => true,
            'edit_others_galleryimages' => true,
            'publish_galleryimages' => true,
            'read_private_galleryimages' => true,
            'delete_galleryimages' => true,
            'delete_private_galleryimages' => true,
            'delete_published_galleryimages' => true,
            'delete_others_galleryimages' => true,
            'edit_private_galleryimages' => true,
            'edit_published_galleryimages' => true,
            'create_galleryimages' => true,
        ));
    }
}
add_action('init', 'mg_add_marketing_team_role');

// Capabilities
function mg_capabilities() {
    $roles = ['administrator', 'marketing_team'];
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('edit_galleryimage');
            $role->add_cap('read_galleryimage');
            $role->add_cap('delete_galleryimage');
            $role->add_cap('edit_galleryimages');
            $role->add_cap('edit_others_galleryimages');
            $role->add_cap('publish_galleryimages');
            $role->add_cap('read_private_galleryimages');
            $role->add_cap('delete_galleryimages');
            $role->add_cap('delete_private_galleryimages');
            $role->add_cap('delete_published_galleryimages');
            $role->add_cap('delete_others_galleryimages');
            $role->add_cap('edit_private_galleryimages');
            $role->add_cap('edit_published_galleryimages');
            $role->add_cap('create_galleryimages');
        }
    }
}
add_action('admin_init', 'mg_capabilities');

// Admin Menu
function mg_menu() {
    if (current_user_can('edit_galleryimages')) {
        add_menu_page('Add New Gallery Image', 'Gallery', 'edit_galleryimages', 'mini-gallery', 'mg_plugin_page', 'dashicons-format-gallery', 6);
    }
}
add_action('admin_menu', 'mg_menu');

// Handle File Uploads
function mg_upload() {
    if (!isset($_POST['mg_upload_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mg_upload_nonce'])), 'mg_upload_nonce')) {
        wp_die('Security check');
    }
    if (!empty($_FILES['gallery_images']) && !empty($_POST['image_title'])) {
        $files = $_FILES['gallery_images'];
        $title = sanitize_text_field($_POST['image_title']);
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'galleryimage',
            'post_status' => 'publish'
        ));
        if ($post_id) {
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
add_action('admin_post_mg_upload', 'mg_upload');

// Handle Gallery Deletion
function mg_delete_gallery() {
    if (!isset($_GET['gallery_id']) || !isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'mg_delete_gallery')) {
        wp_die('Security check failed');
    }

    $gallery_id = intval($_GET['gallery_id']);
    
    if (!current_user_can('delete_galleryimage', $gallery_id)) {
        wp_die('You do not have permission to delete this gallery');
    }

    wp_delete_post($gallery_id, true);
    wp_redirect(admin_url('admin.php?page=mini-gallery'));
    exit;
}
add_action('admin_post_mg_delete_gallery', 'mg_delete_gallery');


function mg_plugin_page() {
    echo '<h1>' . esc_html__('Mini Gallery', 'mini-gallery') . '</h1>';

    // Form to upload new gallery images
    echo '<h2>' . esc_html__('Upload New Images', 'mini-gallery') . '</h2>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
    echo '<input type="hidden" name="action" value="mg_upload">';
    echo '<input type="hidden" name="mg_upload_nonce" value="' . esc_attr(wp_create_nonce('mg_upload_nonce')) . '">';
    echo '<label for="gallery_images">' . esc_html__('Select Images:', 'mini-gallery') . '</label>';
    echo '<input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" required multiple>';
    echo '<br><br>';
    echo '<label for="image_title">' . esc_html__('Gallery Title:', 'mini-gallery') . '</label>';
    echo '<input type="text" id="image_title" name="image_title" required>';
    echo '<br><br>';
    echo '<input type="submit" class="button button-primary" value="' . esc_attr__('Upload Images', 'mini-gallery') . '">';
    echo '</form>';

    // Display existing galleries with their IDs and shortcodes
    echo '<h2>' . esc_html__('Existing Galleries', 'mini-gallery') . '</h2>';
    $galleries = get_posts(['post_type' => 'galleryimage', 'numberposts' => -1]);
    if ($galleries) {
        foreach ($galleries as $gallery) {
            echo '<div>';
            echo '<h3 class="text-center">' . esc_html($gallery->post_title) . ' (ID: ' . esc_html($gallery->ID) . ')</h3>';
            echo '<p>' . esc_html($gallery->post_content) . '</p>';
            // Display the carousel preview using the shortcode
            echo do_shortcode('[mg_gallery id="' . esc_attr($gallery->ID) . '"]');
            echo '<hr>';
            // Display the shortcode dynamically with the post ID
            echo '<p>' . esc_html__('Shortcode to display this gallery:', 'mini-gallery') . '</p>';
            echo '<pre>' . esc_html('[mg_gallery id="' . esc_attr($gallery->ID) . '"]') . '</pre>';
            // Add delete link
            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=mg_delete_gallery&gallery_id=' . esc_attr($gallery->ID)), 'mg_delete_gallery');
            echo '<p><a href="' . esc_url($delete_url) . '" class="button button-secondary">' . esc_html__('Delete Gallery', 'mini-gallery') . '</a></p>';
            echo '</div>';
            echo '<hr>';
        }
    } else {
        echo '<p>' . esc_html__('No galleries found.', 'mini-gallery') . '</p>';
    }
}


// Shortcode to display gallery
function mg_gallery_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $post_id = intval($atts['id']);
    $output = '';

    if ($post_id) {
        $images = get_attached_media('image', $post_id);
        if ($images) {
            $output .= '<div id="mg-carousel" class="mg-gallery">';
            foreach ($images as $image) {
                $img_url = wp_get_attachment_image_src($image->ID, 'medium');
                if ($img_url) {
                    $output .= '<div class="carousel-slide"><img src="' . esc_url($img_url[0]) . '" alt="' . esc_attr($image->post_title) . '" class="carousel-slide-img" loading="lazy"></div>';
                }
            }
            $output .= '</div>';
        } else {
            $output .= '<p>' . esc_html__('No images found for this gallery.', 'mini-gallery') . '</p>';
        }
    } else {
        $output .= '<p>' . esc_html__('Invalid gallery ID.', 'mini-gallery') . '</p>';
    }

    return $output;
}
add_shortcode('mg_gallery', 'mg_gallery_shortcode');

