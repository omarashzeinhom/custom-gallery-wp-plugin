<?php

/**
 * Plugin Name: Mini Gallery
 * Description: A WordPress plugin to display a simple custom gallery.
 * Version: 1.0
 * Author: Omar Ashraf Zeinhom AbdElRahman | ANDGOEDU
 * License: GPLv2 
 */

if (!defined('ABSPATH')) exit;

// Register Gallery Post Type
function mg_post()
{
    $args = array(
        'public' => true,
        'label' => 'Gallery Image',
        'description' => 'Manage your galleries here',
        'show_in_rest' => true,
        'rest_base' => 'gallery-image',
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive' => true,
        'show_in_menu' => false,
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
add_action('init', 'mg_post');


// Enqueue front-end and admin scripts and styles
// Enqueue front-end and admin scripts and styles
function mg_enqueue_assets()
{
    // Register scripts and styles
    wp_register_script('mg-carousel', plugin_dir_url(__FILE__) . 'public/js/carousel.js', array('jquery'), '1.0', true);
    wp_register_style('mg-styles', plugin_dir_url(__FILE__) . 'public/css/styles.css', array(), '1.0');

    // Enqueue for admin pages
    if (is_admin()) {
        wp_enqueue_script('mg-carousel');
        wp_enqueue_style('mg-styles');
    } else {
        // Enqueue for front-end
        wp_enqueue_script('mg-carousel');
        wp_enqueue_style('mg-styles');

        // Pass post ID to JavaScript only on single post pages
        if (is_single()) {
            $post_id = get_the_ID();
            wp_localize_script('mg-carousel', 'mg_gallery_data', array(
                'post_id' => $post_id,
            ));
        }
        if (!is_admin() && is_single()) {
            $post_id = get_the_ID();
            wp_localize_script('mg-carousel', 'mg_gallery_data', array(
                'post_id' => $post_id,
            ));
        }
    }
}
add_action('wp_enqueue_scripts', 'mg_enqueue_assets');
add_action('admin_enqueue_scripts', 'mg_enqueue_assets');
add_action('admin_head', 'mg_enqueue_assets');



// Activation & Deactivation Hooks
function mg_plugin_act()
{
    mg_post();
    marketing_team_role();
    mg_capabilities();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mg_plugin_act');

function mg_plugin_deact()
{
    unregister_post_type('galleryimage');
    remove_role('marketing_team');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'mg_plugin_deact');

// Uninstall Hook
function mg_plugin_uninstall()
{
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
function marketing_team_role()
{
    if (get_role('marketing_team') === null) {
        add_role(
            'marketing_team',
            'Marketing Team',
            array(
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
            )
        );
    }
}
add_action('init', 'marketing_team_role');

// Capabilities
function mg_capabilities()
{
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
function mg_menu()
{
    if (current_user_can('edit_galleryimages')) {
        add_menu_page(
            'Add New Gallery Image',
            'Gallery',
            'edit_galleryimages',
            'mini-gallery',
            'mg_plugin_page',
            'dashicons-format-gallery',
            6
        );
    }
}
add_action('admin_menu', 'mg_menu');

// Handle File Uploads
function mg_upload()
{
    if (!isset($_POST['mg_upload_nonce']) || !wp_verify_nonce($_POST['mg_upload_nonce'], 'mg_upload_nonce')) {
        wp_die('Security check');
    }
    if (!empty($_FILES['gallery_images']) && !empty($_POST['image_title'])) {
        $files = $_FILES['gallery_images'];
        $title = sanitize_text_field($_POST['image_title']);
        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_type' => 'galleryimage',
            'post_status' => 'publish'
        ]);
        if ($post_id) {
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = [
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    ];
                    $file_type = wp_check_filetype($file['name']);
                    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (in_array($file_type['type'], $allowed_types)) {
                        $uploaded = wp_handle_upload($file, ['test_form' => false]);
                        if (isset($uploaded['file'])) {
                            $file_path = $uploaded['file'];
                            $file_url = $uploaded['url'];
                            $attachment_id = wp_insert_attachment([
                                'guid' => $file_url,
                                'post_mime_type' => $file_type['type'],
                                'post_title' => $title,
                                'post_content' => '',
                                'post_status' => 'inherit'
                            ], $file_path, $post_id);
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
// Enqueue scripts and styles
// Handle Gallery Deletion
function mg_delete_gallery()
{
    if (!isset($_GET['gallery_id']) || !isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'mg_delete_gallery')) {
        wp_die('Security check failed');
    }

    $gallery_id = intval($_GET['gallery_id']);
    
    // Check if the user has the capability to delete the gallery
    if (!current_user_can('delete_galleryimage', $gallery_id)) {
        wp_die('You do not have permission to delete this gallery');
    }

    // Delete the gallery
    wp_delete_post($gallery_id, true);

    // Redirect back to the gallery management page
    wp_redirect(admin_url('admin.php?page=mini-gallery'));
    exit;
}
add_action('admin_post_mg_delete_gallery', 'mg_delete_gallery');
function mg_plugin_page()
{
    echo '<h1>Mini Gallery</h1>';

    // Form to upload new gallery images
    echo '<h2>Upload New Images</h2>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
    echo '<input type="hidden" name="action" value="mg_upload">';
    echo '<input type="hidden" name="mg_upload_nonce" value="' . esc_attr(wp_create_nonce('mg_upload_nonce')) . '">';
    echo '<label for="gallery_images">Select Images:</label>';
    echo '<input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" required multiple>';
    echo '<br><br>';
    echo '<label for="image_title">Gallery Title:</label>';
    echo '<input type="text" id="image_title" name="image_title" required>';
    echo '<br><br>';
    echo '<input type="submit" class="button button-primary" value="Upload Images">';
    echo '</form>';

    // Display existing galleries with their IDs and shortcodes
    echo '<h2>Existing Galleries</h2>';
    $galleries = get_posts(['post_type' => 'galleryimage', 'numberposts' => -1]);
    if ($galleries) {
        foreach ($galleries as $gallery) {
            echo '<div>';
            echo '<h3 class="text-center">' . esc_html($gallery->post_title) . ' (ID: ' . esc_html($gallery->ID) . ')</h3>';
            echo '<p>' . esc_html($gallery->post_content) . '</p>';
            // Optionally, display the carousel preview using the shortcode
            echo do_shortcode('[mg_gallery id="' . $gallery->ID . '"]');
            echo '<hr>';
            // Display the shortcode dynamically with the post ID
            echo '<p>' . esc_html__('Shortcode to display this gallery:', 'mini-gallery') . '</p>';
            echo '<pre>' . esc_html('[mg_gallery id="' . $gallery->ID . '"]') . '</pre>';
            // Add delete link
            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=mg_delete_gallery&gallery_id=' . $gallery->ID), 'mg_delete_gallery');
            echo '<p><a href="' . esc_url($delete_url) . '" class="button button-secondary">' . esc_html__('Delete Gallery', 'mini-gallery') . '</a></p>';
            echo '</div>';
            echo '<hr>';
        }
    } else {
        echo '<p>No galleries found.</p>';
    }
}


// Shortcode to display gallery
function mg_gallery_shortcode($atts)
{
    $atts = shortcode_atts(['id' => ''], $atts);
    $post_id = intval($atts['id']);
    $output = '';
    if ($post_id) {
        $images = get_attached_media('image', $post_id);
        if ($images) {
            $output .= '<div id="mg-carousel" class="mg-gallery">';
            foreach ($images as $image) {
                $img_url = wp_get_attachment_image_src($image->ID, 'medium');
                $output .= '<div id="carousel-slide" class="carousel-slide"><img src="' . esc_url($img_url[0]) . '" alt="' . esc_attr($image->post_title) . '" class="carousel-slide-img"></div>';
            }
            $output .= '</div>';
        } else {
            $output .= '<p>No images found for this gallery.</p>';
        }
    } else {
        $output .= '<p>Invalid gallery ID.</p>';
    }
    return $output;
}
add_shortcode('mg_gallery', 'mg_gallery_shortcode');
