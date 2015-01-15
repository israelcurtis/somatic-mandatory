Somatic Mandatory for Wordpress
=========

A library intended for mu-plugins and used in [somatic_framework](https://github.com/somaticstudios/somatic-framework) that allows a theme or plugin to filter the list of required plugins so that:
* The deactivate links are removed.
* Plugins are automatically activated (if they are in the plugins directory)
This is useful when dealing with structural declarations like custom post types and taxonomies that should exist no matter what theme is in use.

#### Example Usage:
```php
<?php
/**
 * Add required plugins to somatic_mandatory_list
 *
 * @param  array $required Array of required plugins in `plugin_dir/plugin_file.php` form
 *
 * @return array           Modified array of required plugins
 */
function somatic_mandatory_add( $required ) {

	$required = array_merge( $required, array(
		'jetpack/jetpack.php',
		'sample-plugin/sample-plugin.php',
	) );

	return $required;
}
add_filter( 'somatic_mandatory_list', 'somatic_mandatory_add' );
?>
```
#### Changelog
* 0.3
	* Init fork from WebDevStudios
