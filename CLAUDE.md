# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A single-purpose WordPress plugin (`EDH Recent Category Manager`) that automatically keeps the latest N published posts assigned to one chosen category or tag, in real time. There is no build system, package manager, or test suite — it's plain PHP + one vanilla JS file, developed and deployed as a standard WordPress plugin directory.

## Development workflow

There are no build/lint/test commands — this is not a Node/Composer project. To work on it:

- Install the plugin folder under `wp-content/plugins/` of a WordPress install (or symlink it there) and activate it via the **Plugins** screen to test changes.
- Verify changes manually through **Posts → Recent Posts** in wp-admin, since there is no automated test harness.
- PHP syntax can be sanity-checked with `php -l edh-recent-category-manager.php`.
- When bumping the version, update it in three places kept in sync: the `Version:` and `@version` headers in `edh-recent-category-manager.php`, the `wp_enqueue_script()` version argument for `assets/js/edh-rcm-admin.js`, and `Stable tag:` plus a new `== Changelog ==` entry in `readme.txt`.

## Architecture

Everything lives in `edh-recent-category-manager.php`, organized into sections (search for the `// ---` banners):

1. **Admin settings page** (`edh_rcm_add_settings_page`, `edh_rcm_render_settings_page`) — registered under the Posts menu. Renders a form (taxonomy choice of category/tag, the managed term dropdown, and post count 1–15) submitted via `options.php` using the Settings API (`register_setting`/`settings_fields`). The settings page's toggle-row UI (show category dropdown vs. tag dropdown) is handled by `assets/js/edh-rcm-admin.js`, enqueued only on this admin page via a closure-captured `$hook_suffix` check inside `admin_enqueue_scripts` — do not reintroduce inline `<script>` tags here, that was an explicit WordPress.org review rejection reason (see changelog 1.3.1).
2. **Settings sanitization** (`edh_rcm_sanitize_taxonomy`, `edh_rcm_sanitize_count`) — registered as `sanitize_callback`s; taxonomy is constrained to `category`/`post_tag`, count to 1–15.
3. **Reactive option-change cleanup** — the plugin hooks both `updated_option` (`edh_rcm_on_option_updated`) and `added_option` (`edh_rcm_on_option_added`), because WordPress fires different hooks depending on whether the option row already exists in `wp_options`. Both paths must independently detect a taxonomy/term switch and call `edh_rcm_clear_term()` to strip the plugin's term assignment from posts under the *previous* term before recalculating, or stale assignments leak. Any new managed setting must be added to the `$managed_options` array in *both* functions.
4. **Post lifecycle hooks** — `transition_post_status` and `deleted_post` trigger recalculation any time a `post`-type post's publish state changes or it's deleted.
5. **Core recalculation** (`edh_rcm_update_recent_articles_category`) — the single source of truth for membership. It fetches the latest `$post_count` published posts, diffs them against posts currently carrying the managed term, removes the term from posts no longer in the latest set, and adds it to newly-qualifying posts. No-ops if no term is configured (`$term_id` falsy).

### Key invariants to preserve

- The plugin only ever touches **one** managed taxonomy term at a time (either a category or a tag, never both simultaneously) — `edh_recent_posts_taxonomy` is the switch between modes.
- Switching the managed taxonomy type, or the selected term within a type, must always clean up assignments on the *old* term first (via `edh_rcm_clear_term`) before applying the new configuration — this is what prevents orphaned plugin-managed term assignments.
- The site's default category (`get_option('default_category')`) is deliberately excluded from the category dropdown since WordPress auto-assigns it and it can't be meaningfully "managed."
