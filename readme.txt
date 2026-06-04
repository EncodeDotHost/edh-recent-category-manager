=== EDH Recent Posts ===
Contributors: encodedothost, nbwpuk
Tags: categories, posts, automation, recent posts
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically maintains a configurable number of your latest published posts in a chosen category, in real-time.

== Description ==

EDH Recent Posts removes the manual work of curating a "recent articles" category. Once configured, the plugin silently monitors your site and ensures that exactly the number of posts you choose are always assigned to your selected category — no more, no less.

Whenever a post is published, unpublished, or permanently deleted, the category membership is recalculated instantly. The oldest post drops out and the newest one takes its place.

**Features**

* Settings page nested under the **Posts** menu in the WordPress admin.
* Choose any existing category (excluding the site default) as the managed category.
* Select between 1 and 15 posts to maintain.
* Switching to a different managed category cleanly removes the plugin's assignments from the previous one, leaving the category itself untouched.
* Changing the post count takes effect immediately without waiting for the next publish event.
* No configuration files or code edits required after activation.

== Installation ==

1. Upload the `edh-recent-posts` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Posts → Recent Posts** to configure the managed category and post count.

== Frequently Asked Questions ==

= Why is the "Uncategorized" category not shown in the dropdown? =

WordPress designates one category as the site default (usually "Uncategorized"). Posts are assigned to it automatically if no other category is chosen, so it is not suitable as a managed category. EDH Recent Posts excludes whichever category is currently set as the site default, even if it has been renamed.

= What happens to posts in the old category when I switch to a new one? =

The plugin removes the old managed category from any posts it had previously assigned there. The category itself is left intact — only the plugin-managed assignments are cleaned up.

= Will manually added posts in the managed category be removed? =

The managed category is maintained exclusively by this plugin. Any posts manually added to it may be removed during the next recalculation if they fall outside the configured post count. It is best to treat the managed category as plugin-owned.

= Does the plugin create the category for me? =

No. The category must already exist before you can select it in the settings. Create it via **Posts → Categories** first.

= What happens if no category is configured? =

The plugin takes no action until a category is selected and saved in the settings.

== Screenshots ==

1. The settings page under Posts → Recent Posts.

== Changelog ==

= 1.0 =
* Initial release.
