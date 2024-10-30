=== Plugin Name ===
Contributors: eyn
Donate link: http://www.channel-ai.com/blog/donate.php?plugin=image-caption
Tags: images, css, post, formatting
Requires at least: 2.0
Tested up to: 2.5
Stable tag: 0.2

Adds image caption under images that have their title or alt attribute defined.

== Description ==

Extracts the `title` or `alt` attribute from images within your blog post and generates a neat caption directly underneath those images. Supports custom CSS styling for captions.

Features:

* generates image caption using `img` tag’s `title` or `alt` attribute
* standards compliant: valid XHTML
* degrades gracefully when disabled
* supports images with links (e.g. to higher resolution of the same image)
* supports [MyCSS](http://www.channel-ai.com/blog/plugins/mycss/ "MyCSS WordPress Plugin") by disabling default CSS import
* supports custom CSS class for div image container
* automatically adds width and height attribute to your image
* does not write to your WordPress database

== Screenshots ==

1. Image Caption in action

== Usage ==

Once activated, you can start adding caption to your image by adding title attribute to it:

	<img src="/images/picture.jpg" alt="" class="center" title="A sample caption">

CSS classes supported "out of the box" are: `center`, `alignleft`, `alignright`.

If you prefer to use "alt" attribute for the generation of image caption, open the php file and change `$ic_att` to "alt" instead of "title".

More usage details can be found at the plugin's homepage.

== Installation ==

1. Download and extract the “image-caption” folder
1. Upload the “image-caption” folder to your WordPress plugin directory, usually “wp-content/plugins”
1. Activate the plugin in your WordPress admin panel

== History ==

0.2 [2008.05.22]

* Added: support for alt attribute
* Added: option to disable stripping of title attribute
* Changed: tweaked regex detection 

0.1 [2008.01.29]

* Initial release
