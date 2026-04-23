=== Stacy NB ===

Contributors: btolan
Requires at least: 4.5
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 5.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: one-column, two-columns, right-sidebar, custom-menu, custom-logo, featured-images, footer-widgets, sticky-post, threaded-comments, translation-ready, blog, e-commerce

Direct child theme of SpicePress. Brings Stacy-style customizations (header variants, blog layouts) into a single flat child-theme structure.

== Description ==

Stacy NB is a direct child theme of SpicePress. WordPress does not allow a child theme to itself have a child theme, so Stacy NB flattens the Stacy child theme's customizations into a single child level on top of SpicePress. This results in a stable, installable theme that carries over Stacy's extra header variations, blog layouts and section presentations, while being fully independent of the Stacy theme.

Features:

* Two header layouts: default (logo left/right/center) and centered navbar
* Two blog layouts: grid (3-column) and list (media-object)
* Customizer integration for colors, typography, header/blog variant and menu breakpoint
* Translation ready
* Compatible with WooCommerce, Contact Form 7, WPML and Polylang (via the SpicePress parent)

Parent theme: SpicePress, by SpiceThemes — https://wordpress.org/themes/spicepress/

== Installation ==

1. Install the parent theme SpicePress via *Appearance > Themes > Add New*.
2. Upload this theme's zip via *Appearance > Themes > Add New > Upload Theme*.
3. Activate Stacy NB.
4. Adjust settings in *Appearance > Customize*.

== Frequently Asked Questions ==

= Can I use Stacy NB without SpicePress? =

No. Stacy NB is a child theme; SpicePress must be installed (it does not need to be activated).

= How do I change the header layout? =

*Appearance > Customize > Header Layout Settings* — choose "default" or "center".

= How do I change the blog layout on the homepage? =

*Appearance > Customize > Blog Layout Settings* — choose "default" (grid) or "list".

= The accent color changes don't apply — why? =

Enable *Custom color enable* in the Customizer Colors section, then pick your color.

== Changelog ==

= 1.0.0 =
* Initial release.
* Direct child of SpicePress (flattened from Stacy 1.5.1).
* Removed promotional admin pages.
* Replaced query_posts() with WP_Query.
* Applied proper escaping (sanitize_hex_color, absint, esc_html).
* Removed hardcoded promotional footer credits.

== Credits ==

Stacy NB is inspired by and built on top of:

* SpicePress (parent theme) by SpiceThemes — GPL v3
  https://wordpress.org/themes/spicepress/
* Stacy (original child theme) by SpiceThemes — GPL v2
  https://wordpress.org/themes/stacy/

All code is released under GPL v2 or later.
