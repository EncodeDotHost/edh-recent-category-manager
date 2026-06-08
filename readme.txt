=== EDH Recent Category Manager ===
Contributors: encodedothost, nbwpuk
Tags: categories, tags, posts, automation, recent posts
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.3.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically maintains a configurable number of your latest published posts in a chosen category or tag, in real-time.

== Description ==

EDH Recent Category Manager removes the manual work of curating a "recent articles" category or tag. Once configured, the plugin silently monitors your site and ensures that exactly the number of posts you choose are always assigned to your selected category or tag — no more, no less.

Whenever a post is published, unpublished, or permanently deleted, the membership is recalculated instantly. The oldest post drops out and the newest one takes its place.

**Features**

* Settings page nested under the **Posts** menu in the WordPress admin.
* Choose to manage either a **Category** or a **Tag**.
* Choose any existing category (excluding the site default) or any existing tag as the managed term.
* Select between 1 and 15 posts to maintain.
* Switching taxonomy type or changing the managed term cleanly removes the plugin's assignments from the previous one.
* Changing the post count takes effect immediately without waiting for the next publish event.
* No configuration files or code edits required after activation.

== Installation ==

1. Upload the `edh-recent-category-manager` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Posts → Recent Posts** to configure the managed category and post count.

== Frequently Asked Questions ==

= What does "EDH" stand for? =

EDH is short for EncodeDotHost — keeping the plugin name succinct and memorable while making it clearly identifiable as a plugin maintained by the EncodeDotHost team.

= Why is the "Uncategorized" category not shown in the dropdown? =

WordPress designates one category as the site default (usually "Uncategorized"). Posts are assigned to it automatically if no other category is chosen, so it is not suitable as a managed category. EDH Recent Category Manager excludes whichever category is currently set as the site default, even if it has been renamed.

= What happens to posts in the old category or tag when I switch to a different one? =

The plugin removes the old managed term from any posts it had previously assigned there. The category or tag itself is left intact — only the plugin-managed assignments are cleaned up. This also applies when switching between taxonomy types (e.g. from a category to a tag).

= Will manually added posts in the managed category or tag be removed? =

The managed term is maintained exclusively by this plugin. Any posts manually added to it may be removed during the next recalculation if they fall outside the configured post count. It is best to treat the managed category or tag as plugin-owned.

= Does the plugin create the category or tag for me? =

No. The category or tag must already exist before you can select it in the settings. Create categories via **Posts → Categories** and tags via **Posts → Tags** first.

= What happens if no category or tag is configured? =

The plugin takes no action until a term is selected and saved in the settings.

== Screenshots ==

1. The settings page under Posts → Recent Posts.

== Changelog ==

= 1.3.1 =
* Fixed: settings page JavaScript is now loaded via wp_enqueue_script() instead of an inline `<script>` tag, per WordPress plugin guidelines.

= 1.3 =
* Renamed plugin to EDH Recent Category Manager with new slug edh-recent-category-manager.

= 1.2.1 =
* Fixed: switching from a managed category to a tag (or vice versa) on a fresh install or after upgrading from v1.1 now correctly removes the previous term's assignments before applying the new one.

= 1.2 =
* Added support for managing a **Tag** in addition to a Category.
* New Taxonomy setting on the settings page — choose between Category and Tag via radio buttons.
* Switching taxonomy type cleanly removes plugin assignments from the previous term before reassigning under the new one.
* Managed tag dropdown lists all existing tags; managed category dropdown behaviour unchanged.

= 1.1 =
* Added settings page under Posts → Recent Posts.
* Managed category and post count (1–15) are now configurable via the WordPress admin.
* Switching to a different managed category cleanly removes plugin assignments from the previous one.
* Changing settings takes effect immediately without waiting for the next publish event.
* Site default category (Uncategorized) is excluded from the category dropdown.

= 1.0 =
* Initial release.
