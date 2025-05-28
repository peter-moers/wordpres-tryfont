<?php
/*
Plugin Name: TryFont
Description: Embed a live font sampler using a shortcode and upload your fonts via the admin panel.
Version: 1.1
Author: You
*/

// Allow font uploads
add_filter('upload_mimes', function($mimes) {
    $mimes['ttf'] = 'font/ttf';
    $mimes['woff'] = 'font/woff';
    $mimes['woff2'] = 'font/woff2';
    return $mimes;
});

// Avoid wrong mime detection
add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
    $filetype = wp_check_filetype($filename, $mimes);
    return [
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename'],
    ];
}, 10, 4);


require_once plugin_dir_path(__FILE__) . 'admin.php';

// Font deletion handler
add_action('admin_init', function() {
    if (
        isset($_GET['page']) &&
        $_GET['page'] === 'tryfont' &&
        isset($_GET['delete_font']) &&
        current_user_can('delete_files')
    ) {
        $file = basename($_GET['delete_font']);
        if (wp_verify_nonce($_GET['_wpnonce'], 'tryfont_delete_' . $file)) {
            $path = wp_upload_dir()['basedir'] . '/fonts/' . $file;
            if (file_exists($path)) {
                unlink($path);
                wp_safe_redirect(admin_url('options-general.php?page=tryfont&deleted=1'));
                exit;
            }
        }
    }
});

add_action('admin_post_tryfont_delete_font', function() {
    if (!current_user_can('manage_options')) {
        wp_die('You are not allowed to delete fonts.');
    }

    if (!isset($_POST['font_file'], $_POST['_wpnonce'])) {
        wp_die('Missing data.');
    }

    $font_file = basename($_POST['font_file']);

    if (!wp_verify_nonce($_POST['_wpnonce'], 'tryfont_delete_' . $font_file)) {
        wp_die('Security check failed.');
    }

    $path = wp_upload_dir()['basedir'] . '/fonts/' . $font_file;
    if (file_exists($path)) {
        unlink($path);
        wp_redirect(admin_url('options-general.php?page=tryfont&deleted=1'));
        exit;
    } else {
        wp_die('Font file not found.');
    }
});


function tryfont_enqueue_assets() {
    wp_register_script('tryfont-js', plugins_url('/js/tryfont.js', __FILE__), [], null, true);
    wp_register_style('tryfont-css', plugins_url('/css/tryfont.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'tryfont_enqueue_assets');

add_shortcode('tryfont', function($atts) {
    wp_enqueue_script('tryfont-js');
    wp_enqueue_style('tryfont-css');

    $defaults = get_option('tryfont_defaults', [
        'size' => 26,
        'show_size' => 'no',
        'show_spacing' => 'no',
        'show_lineheight' => 'no'
    ]);

    $atts = shortcode_atts([
        'font' => '',
        'text' => 'The quick brown fox jumps over the lazy dog.',
        'size' => $defaults['size'],
        'show_size' => $defaults['show_size'],
        'show_spacing' => $defaults['show_spacing'],
        'show_lineheight' => $defaults['show_lineheight']
    ], $atts);

    if (empty($atts['font'])) {
        return '<p style="color:red;">Font file not specified.</p>';
    }

    $font_file = basename($atts['font']);
    $font_url = content_url('/uploads/fonts/' . $font_file);
    $font_id = sanitize_title(pathinfo($font_file, PATHINFO_FILENAME));
    $ext = strtolower(pathinfo($font_file, PATHINFO_EXTENSION));

    $format = match ($ext) {
        'ttf' => 'truetype',
        'woff2' => 'woff2',
        'woff' => 'woff',
        default => null,
    };
    if (!$format) return '<p style="color:red;">Unsupported font format.</p>';

    $container_id = 'tryfont-' . $font_id;

    ob_start();
    ?>
    <div class="tryfont-container" id="<?php echo esc_attr($container_id); ?>"
         data-font-family="<?php echo esc_attr($font_id); ?>"
         data-font-url="<?php echo esc_url($font_url); ?>"
         data-sample-text="<?php echo esc_attr($atts['text']); ?>"
         data-size="<?php echo esc_attr($atts['size']); ?>"
         data-show-size="<?php echo esc_attr($atts['show_size']); ?>"
         data-show-spacing="<?php echo esc_attr($atts['show_spacing']); ?>"
         data-show-lineheight="<?php echo esc_attr($atts['show_lineheight']); ?>">
        <style>
            @font-face {
                font-family: '<?php echo esc_attr($font_id); ?>';
                src: url('<?php echo esc_url($font_url); ?>') format('<?php echo esc_attr($format); ?>');
                font-display: swap;
            }
        </style>
        <div class="controls"></div>
        <div class="sample-text" contenteditable="true" spellcheck="false">
            <?php echo esc_html($atts['text']); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});
