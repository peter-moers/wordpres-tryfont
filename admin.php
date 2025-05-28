<?php
add_action('admin_menu', function() {
    add_options_page('TryFont Settings', 'TryFont', 'manage_options', 'tryfont', 'tryfont_admin_page');
});

add_action('admin_init', function() {
    register_setting('tryfont_settings', 'tryfont_defaults');
});

function tryfont_admin_page() {
    $defaults = get_option('tryfont_defaults', [
        'size' => 32,
        'show_size' => 'yes',
        'show_spacing' => 'yes',
        'show_lineheight' => 'yes'
    ]);

    // Handle font upload
    if (isset($_POST['submit_font']) && current_user_can('upload_files')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        
        $upload = wp_handle_upload($_FILES['tryfont_file'], ['test_form' => false]);
        if (isset($upload['error'])) {
            echo '<div class="error"><p>' . esc_html($upload['error']) . '</p></div>';
        } else {
            $font_ext = pathinfo($upload['file'], PATHINFO_EXTENSION);
            if (!in_array(strtolower($font_ext), ['ttf', 'woff', 'woff2'])) {
                unlink($upload['file']);
                echo '<div class="error"><p>Unsupported font format.</p></div>';
            } else {
                // Move to fonts folder
                $fonts_dir = wp_upload_dir()['basedir'] . '/fonts';
                if (!is_dir($fonts_dir)) mkdir($fonts_dir);
                $dest = $fonts_dir . '/' . basename($upload['file']);
                rename($upload['file'], $dest);
                echo '<div class="updated"><p>Font uploaded successfully.</p></div>';
            }
        }
    }

    // Get fonts list
    $font_dir = wp_upload_dir()['basedir'] . '/fonts';
    $font_url = wp_upload_dir()['baseurl'] . '/fonts';
    $fonts = file_exists($font_dir) ? array_diff(scandir($font_dir), ['.', '..']) : [];

    ?>
    <div class="wrap">
        <h1>TryFont Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('tryfont_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Default Font Size</th>
                    <td><input type="number" name="tryfont_defaults[size]" value="<?php echo esc_attr($defaults['size']); ?>" min="10" max="100" /></td>
                </tr>
                <tr>
                    <th scope="row">Show Font Size Slider</th>
                    <td><input type="checkbox" name="tryfont_defaults[show_size]" value="yes" <?php checked($defaults['show_size'], 'yes'); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">Show Letter Spacing Slider</th>
                    <td><input type="checkbox" name="tryfont_defaults[show_spacing]" value="yes" <?php checked($defaults['show_spacing'], 'yes'); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">Show Line Height Slider</th>
                    <td><input type="checkbox" name="tryfont_defaults[show_lineheight]" value="yes" <?php checked($defaults['show_lineheight'], 'yes'); ?> /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <hr>

        <h2>Upload Font</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="tryfont_file" accept=".ttf,.woff,.woff2" required />
            <?php submit_button('Upload Font', 'primary', 'submit_font'); ?>
        </form>

        <hr>

        <h2>Uploaded Fonts</h2>
        <table class="widefat">
            <thead>
                <tr><th>Font File</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($fonts)) : ?>
                    <?php foreach ($fonts as $font) : ?>
                        <tr>
                            <td><code>[tryfont font="<?php echo esc_html($font); ?>"]</code></td>
                            <td>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                                <input type="hidden" name="action" value="tryfont_delete_font">
                                <input type="hidden" name="font_file" value="<?php echo esc_attr($font); ?>">
                                <?php wp_nonce_field('tryfont_delete_' . $font); ?>
                                <button type="submit" class="button-link" onclick="return confirm('Delete font <?php echo esc_html($font); ?>?')">Delete</button>
                            </form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="2">No fonts uploaded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
