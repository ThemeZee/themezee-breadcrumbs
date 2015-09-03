=== Lean Custom Content Types ===
Contributors: themezee
Tags: lean slider, lean slider post type, slider post type, slideshow post type, slider custom post type, custom post type, post type, post types, custom post types, slides, slideshow, slideshow posts, slider posts
Requires at least: 3.5
Tested up to: 3.8.1
Stable tag: 1.0

Registers a lean custom post type for slideshow posts and allow theme developers to built custom slideshows on top of it.

== Description ==

The *Lean Slider* plugin creates a simple custom post type which allows you to easily add slideshow posts to your WordPress website. Depending on the used theme the lean slider post type supports featured images, slide order and custom fields for linked slides.
[Click here to learn how you can install and use the plugin](http://wordpress.org/extend/plugins/lean-slider/installation/)

= About Usage =
Please note that this plugin is NOT a complete slideshow solution. It only provides support to create slideshow posts on your WordPress backend. It's up to your used theme to turn your created slideshow posts in a beautiful custom slideshow on the frontend.

= Benefits =
Most likely you were referred to this plugin by your used theme. So why do your theme use *Lean Slider*? Using a plugin to register a custom post type for slides rather than a theme has one major benefit. You will not lose your created slides when you switch to another theme. Content loss will always happen when your theme has created any custom post types. Because of this WordPress themes should not register custom post types by themselves.

By using the *Lean Slider* plugin you will not lose your created slides when you switch themes - and if your new theme supports *Lean Slider* as well, your slideshow will just work right away in your new theme.

= Support for theme developers =
The main purpose of the *Lean Slider* post type is to provide theme developers an easy solution to create custom slideshows based on an own slider custom post type within their themes. See the section above why theme developers should not register custom post types in their themes. Especially for business wordpress themes it can be useful to create an extra post type for a homepage slider rather than using the default WordPress post type.
[Check out the FAQ page to learn how to integrate Lean Slider into your themes](http://wordpress.org/extend/plugins/lean-slider/faq/)

== Installation ==

1. Upload the `lean-slider` plugin folder to the `/wp-content/plugins/` directory of your website.
1. Activate the plugin through the *"Plugins"* menu in WordPress. A new item *"Lean Slider"* will appear your WordPress admin panel (under "Posts").
1. Go to *"Lean Slider" > "Add New Slide"* and create your slides. Do not forget to set featured images since most sliders rely on them.
1. Configure the slideshow settings within your used WordPress theme. Please note: *Lean Slider* is not a complete slideshow solution and has to be used in combination with a theme which supports *Lean Slider*
1. That's it. Your website should now display the slider at the location chosen by your theme (e.g. homepage template).

== Frequently Asked Questions ==

= How can I let display my slideshow? =
Please note that *Lean Slider* only registers the custom post type which allows you to create slides in your backend. You need to use a compatible WordPress theme which turns your slides into a custom slideshow on the frontend.

= Can I create multiple slideshows? =
Depends on the theme implementation. Theme developers can easily extend *Lean Slider* and realize multiple slideshows with categories and tags, for example.

== Changelog ==

= 1.0 =

* Initial Release