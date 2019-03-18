# Sage 8 ACF Gutenberg Blocks
Generate ACF Gutenberg blocks just by adding templates to your Sage 8 theme. This package is forked from the [Sage 9 version](https://github.com/MWDelaney/sage-acf-wp-blocks) by [MWDelaney](https://github.com/MWDelaney) which is based heavily on [this article](https://medium.com/nicooprat/acf-blocks-avec-gutenberg-et-sage-d8c20dab6270) by [nicoprat](https://github.com/nicooprat).

## Installation
Run the following in your Sage 8-based theme directory:
```sh
composer require "lachlanarthur/sage8-acf-wp-blocks"
```

## Creating blocks
Add templates to `your-theme/wp-blocks` which get and use ACF data. Each template requires a metadata comment block with some data in it:
```php
<?php
/**
 * Title: 
 * Description: 
 * Category: 
 * Icon: 
 * Keywords: 
 * Post Types: 
 * Default Mode: 
 * Default Alignment: 
 */
```

### Example block template

```php
<?php
/**
 * Title: Testimonial
 * Description: Customer testimonial
 * Category: formatting
 * Icon: admin-comments
 * Keywords: testimonial quote
 * Post Types: post page
 * Default Mode: preview
 * Default Alignment: full
 */
?>

<blockquote data-<?= sanitize_html_class( $block['id'] ) ?> class="<?= esc_attr( $block['classes'] ) ?>">
  <p><?= get_field('testimonial') ?></p>
  <cite>
    <span><?= get_field('author') ?></span>
  </cite>
</blockquote>

<style type="text/css">
  [data-<?= sanitize_html_class( $block['id'] ) ?>] {
    background: <?= get_field('background_color') ?>;
    color: <?= get_field('text_color') ?>;
  }
</style>
```

### Block options

Option | Value
------------ | -------------
Category | `common`<br>`formatting`<br>`layout`<br>`widgets`<br>`embed`<br>[Create your own](https://wordpress.org/gutenberg/handbook/extensibility/extending-blocks/#managing-block-categories)
Icon | The name of a [Dashicon](https://developer.wordpress.org/resource/dashicons/)
Post Types | Space-delimited list of post types
Default Mode | `preview`<br>`edit`
Default Alignment | `left`<br>`center`<br>`right`<br>`wide`<br>`full`
## Creating ACF fields
Once a block is created you'll be able to assign ACF fields to it using the standard Custom Fields interface in WordPress. We recommend using [sage-advanced-custom-fields](https://github.com/MWDelaney/sage-advanced-custom-fields) to keep your ACF fields in version control with Sage.

## Changing the blocks directory
The `wp-blocks` directory can be changed with the filter `sage8-acf-wp-blocks-paths`:

```php
add_filter('sage8-acf-wp-blocks-paths', function ($paths) {
  return ['templates/blocks', 'another-path'];
}, 10, 1);
```
