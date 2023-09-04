// Helper function to fetch all astra-advanced-hook posts
function astra_get_advanced_hook_posts() {
    $args = array(
        'post_type' => 'astra-advanced-hook',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $posts = get_posts($args);
    return $posts;
}

// Add custom fields for the product_brand and product_cat taxonomies
function astra_add_taxonomy_custom_fields($term) {
    $top_content_id = get_term_meta($term->term_id, 'astra_top_advanced_hook', true);
    $bottom_content_id = get_term_meta($term->term_id, 'astra_bottom_advanced_hook', true);
    $advanced_hooks = astra_get_advanced_hook_posts();
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="astra_top_advanced_hook"><?php _e('Top Section Hook', 'astra'); ?></label>
        </th>
        <td>
            <select name="astra_top_advanced_hook" id="astra_top_advanced_hook">
                <option value=""><?php _e('Select Hook', 'astra'); ?></option>
                <?php foreach ($advanced_hooks as $hook) : ?>
                    <option value="<?php echo $hook->ID; ?>" <?php selected($top_content_id, $hook->ID); ?>><?php echo $hook->post_title; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php _e('Select the astra-advanced-hook post for the top section.', 'astra'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="astra_bottom_advanced_hook"><?php _e('Bottom Section Hook', 'astra'); ?></label>
        </th>
        <td>
            <select name="astra_bottom_advanced_hook" id="astra_bottom_advanced_hook">
                <option value=""><?php _e('Select Hook', 'astra'); ?></option>
                <?php foreach ($advanced_hooks as $hook) : ?>
                    <option value="<?php echo $hook->ID; ?>" <?php selected($bottom_content_id, $hook->ID); ?>><?php echo $hook->post_title; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php _e('Select the astra-advanced-hook post for the bottom section.', 'astra'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('product_cat_edit_form_fields', 'astra_add_taxonomy_custom_fields', 10, 2);
add_action('product_brand_edit_form_fields', 'astra_add_taxonomy_custom_fields', 10, 2);

// Save custom fields for the product_brand and product_cat taxonomies
function astra_save_taxonomy_custom_fields($term_id) {
    if (isset($_POST['astra_top_advanced_hook'])) {
        update_term_meta($term_id, 'astra_top_advanced_hook', sanitize_text_field($_POST['astra_top_advanced_hook']));
    }
    if (isset($_POST['astra_bottom_advanced_hook'])) {
        update_term_meta($term_id, 'astra_bottom_advanced_hook', sanitize_text_field($_POST['astra_bottom_advanced_hook']));
    }
}

add_action('edited_product_cat', 'astra_save_taxonomy_custom_fields', 10, 2);
add_action('edited_product_brand', 'astra_save_taxonomy_custom_fields', 10, 2);

// Function to display selected post content
function astra_display_hook_content($hook_id) {
    if (!empty($hook_id)) {
        $_content = get_post_field('post_content', $hook_id);
        echo do_shortcode($_content);
    }
}

function astra_display_taxonomy_top_content() {
    if (is_product_category() || is_tax('product_brand')) {
        $term_id = get_queried_object_id();
        $content_id = get_term_meta($term_id, 'astra_top_advanced_hook', true);
        astra_display_hook_content($content_id);
    }
}

function astra_display_taxonomy_bottom_content() {
    if (is_product_category() || is_tax('product_brand')) {
        $term_id = get_queried_object_id();
        $content_id = get_term_meta($term_id, 'astra_bottom_advanced_hook', true);
        astra_display_hook_content($content_id);
    }
}


add_action('woocommerce_archive_description', 'astra_display_taxonomy_top_content', 5);
add_action('woocommerce_after_shop_loop', 'astra_display_taxonomy_bottom_content', 20);
