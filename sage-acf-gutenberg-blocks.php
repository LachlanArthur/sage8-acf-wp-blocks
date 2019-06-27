<?php

// Adapted from Sage 9 version https://github.com/MWDelaney/sage-acf-wp-blocks

namespace Roots\Sage\ACF_WP_Blocks;

// Check whether WordPress and ACF are available; bail if not.
if (! \function_exists('acf_register_block')) { // Only available in ACF 5.8+
    return;
}
if (! \function_exists('add_filter')) {
    return;
}
if (! \function_exists('add_action')) {
    return;
}

/**
 * Create blocks based on templates found in Sage's "wp-blocks" directory
 */
\add_action('acf/init', function () {

    // Global $sage_error so we can throw errors in the typical sage manner
    global $sage_error;

    // Get an array of directories containing blocks
    $directories = \apply_filters('sage8-acf-wp-blocks-paths', ['wp-blocks']);

    // Check whether ACF exists before continuing
    foreach ($directories as $dir) {

        // Sanity check whether the directory we're iterating over exists first
        if (!\file_exists(\locate_template($dir))) {
            return;
        }

        // Iterate over the directories provided and look for templates
        $template_directory = new \DirectoryIterator(\locate_template($dir));

        foreach ($template_directory as $template) {
            if (!$template->isDot() && !$template->isDir()) {

                // Strip the file extension to get the slug
                $filename = \pathinfo($template->getFilename(), PATHINFO_FILENAME);

                // Sanitize slug
                $slug = \preg_replace('/[^a-z0-9-]/', '', $filename);

                // Get header info from the found template file(s)
                $file_path = \locate_template("{$dir}/{$filename}.php");
                $file_headers = \get_file_data($file_path, [
                    'title'       => 'Title',
                    'description' => 'Description',
                    'category'    => 'Category',
                    'icon'        => 'Icon',
                    'keywords'    => 'Keywords',
                    'post_types'  => 'Post Types',
                    'mode'        => 'Default Mode',
                    'align'       => 'Default Alignment',
                ]);

                if (empty($file_headers['title'])) {
                    $sage_error(__('This block needs a title: ' . $file_path, 'sage' ), __('Block title missing', 'sage'));
                }

                if (empty($file_headers['category'])) {
                    $sage_error(__('This block needs a category: ' . $file_path, 'sage'), __('Block category missing', 'sage'));
                }

                // Set up block data for registration
                $data = [
                    'name'            => $slug,
                    'path'            => $file_path,
                    'title'           => $file_headers['title'],
                    'description'     => $file_headers['description'],
                    'category'        => $file_headers['category'],
                    'icon'            => $file_headers['icon'],
                    'keywords'        => \explode(' ', $file_headers['keywords']),
                    'post_types'      => \explode(' ', $file_headers['post_types']),
                    'mode'            => $file_headers['mode'],
                    'align'           => $file_headers['align'],
                    'render_callback' => __NAMESPACE__.'\\sage_blocks_callback',
                ];

                // Register the block with ACF
                \acf_register_block($data);
            }
        }
    }
});

/**
 * Callback to register blocks
 *
 * @param array $block
 * @return void
 */
function sage_blocks_callback($block) {

    // Set up the slug to be useful
    $slug = \str_replace('acf/', '', $block['name']);

    // Set up the block data
    $block['slug']    = $slug;
    $block['classes'] = \implode(' ', [$block['slug'], $block['className'], 'align'.$block['align']]);

    \set_query_var('block', $block);
    \load_template($block['path'], false);
}
