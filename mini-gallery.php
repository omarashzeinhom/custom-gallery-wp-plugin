<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function mg_styles() {
    wp_register_style('mg_alpha', get_template_directory_uri() . '/public/css/mg_alpha.css', array(), '1.0', 'all');
    wp_enqueue_style('mg_alpha');

    wp_register_style('mg_bravo', get_template_directory_uri() . '/public/css/mg_bravo.css', array(), '1.0', 'all');
    wp_enqueue_style('mg_bravo');
}
add_action('wp_enqueue_scripts', 'mg_styles');

function mg_js() {
    wp_register_script('mg_scripts', get_template_directory_uri() . '/public/js/mg_scripts.js', array(), '1.0', true);
    wp_enqueue_script('mg_scripts');
}
add_action('wp_enqueue_scripts', 'mg_js');

function mg_post() {
    $args = [
        'public' => true,
        'label' => 'Gallery Image',
        'description'=> 'Manage your galleries here',
        'show_in_rest'=> true,
        'rest_base'=> 'gallery-image',
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive'=> true,
        'show_in_menu'=> false,
        'supports'=> array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'capability_type' => 'galleryimage',
        'map_meta_cap' => true,
        'capabilities' => [
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
        ]
    ];

    register_post_type('galleryimage', $args );
}
add_action('init', 'mg_post');

function mg_plugin_act() {
    mg_post();
    marketing_team_role();
    mg_capabilities();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mg_plugin_act');

function mg_plugin_deact() {
    unregister_post_type('galleryimage');
    remove_role('marketing_team');
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'mg_plugin_deact');

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

function marketing_team_role() {
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
add_action('init', 'marketing_team_role');

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
        }
    }
}
add_action('admin_init', 'mg_capabilities');

function mg_menu() {
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

function mg_upload() {
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

function mg_plugin_page() {
    echo '<h1>' . esc_html__('Mini Gallery', 'mini-gallery') . '</h1>';
    echo '<h2>' . esc_html__('Upload New Images', 'mini-gallery') . '</h2>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" enctype="multipart/form-data">';
    wp_nonce_field('mg_upload_nonce', 'mg_upload_nonce');
    echo '<input type="hidden" name="action" value="mg_upload">';
    echo '<label for="gallery_images">' . esc_html__('Select Images:', 'mini-gallery') . '</label>';
    echo '<input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" required multiple>';
    echo '<br><br>';
    echo '<label for="image_title">' . esc_html__('Gallery Title:', 'mini-gallery') . '</label>';
    echo '<input type="text" id="image_title" name="image_title" required>';
    echo '<br><br>';
    echo '<input type="submit" class="button button-primary" value="' . esc_attr__('Upload Images', 'mini-gallery') . '">';
    echo '</form>';
    echo '<h2>' . esc_html__('Existing Galleries', 'mini-gallery') . '</h2>';
    $galleries = get_posts(['post_type' => 'galleryimage', 'numberposts' => -1]);

    if ($galleries) {
        foreach ($galleries as $gallery) {
            $gallery_title = esc_html($gallery->post_title);
            $gallery_content = esc_html($gallery->post_content);
            $gallery_id = intval($gallery->ID);

            echo '<div>';
            echo '<h3>' . esc_html($gallery_title) . ' (ID: ' . esc_html($gallery_id) . ')</h3>';
            echo '<p>' . esc_html($gallery_content) . '</p>';
            echo '<p>' . esc_html__('Shortcode to display this gallery:', 'mini-gallery') . '</p>';
            echo '<pre>[gallery_carousel id="' . esc_attr($gallery_id) . '"]</pre>';
            echo do_shortcode('[gallery_carousel id="' . esc_attr($gallery_id) . '"]');
            echo '</div>';
        }
    } else {
        echo '<p>' . esc_html__('No galleries found.', 'mini-gallery') . '</p>';
    }
}

function mg_gallery_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $id = intval($atts['id']);

    if ($id) {
        $images = get_attached_media('image', $id);

        if ($images) {
            $output = '<div class="gallery-carousel">';

            foreach ($images as $image) {
                $img_url = wp_get_attachment_url($image->ID);
                $output .= '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($image->post_title) . '">';
            }

            $output .= '</div>';
            return $output;
        }
    }

    return '<p>' . esc_html__('No images found in this gallery.', 'mini-gallery') . '</p>';
}
add_shortcode('gallery_carousel', 'mg_gallery_shortcode');
?>
