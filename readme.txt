=== Advance Catlist Post ===
Contributors: PawanJagriti
Donate link: https://biharinfozone.in/advance-catlist-post
Tags: Advance Catlist Post, category, posts, customizable, Elementor
Requires at least: 5.2
Tested up to: 6.6.1
Requires PHP: 7.0
Stable tag: /tags/1.6/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Short Description ==
Display posts from specific categories using customizable shortcodes in Elementor.

== Description ==
Advance Catlist Post is a WordPress plugin that allows you to display posts from a user-defined category using a simple shortcode...

### Features:
- Display posts from any specified category.
- Show the post date modification.
- Customize the number of posts to display.
- Compatible with Elementor.
- If no category is specified, it shows the most recent posts.

== Installation ==
1. Upload the `advance-catlist-post` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the settings page under 'Catlist Settings' in your WordPress dashboard to configure defaults.
4. Use the `[catlist name="CATEGORY_NAME" date_modified="yes/no" numberposts="10"]` shortcode in your posts or pages.

== Frequently Asked Questions ==
= How do I use the shortcode? =
Simply add `[catlist name="CATEGORY_NAME" date_modified="yes/no" numberposts="10"]` to any post or page. If you don't specify a category, the plugin will show the latest posts.

= Can I customize the number of posts displayed? =
Yes, you can customize the number of posts by modifying the `numberposts` attribute in the shortcode. You can also set a default number of posts from the settings page.

= Is this plugin compatible with Elementor? =
Yes, the plugin is fully compatible with Elementor, allowing for seamless integration and instant updates.

= What happens if I don't specify a category? =
If no category is specified, the plugin will display the most recent posts based on the number you set.

== Screenshots ==
1. Example of the posts list in bullet points. ![Screenshot 1](assets/screenshot-1.png)
2. Settings page where you can configure default options. ![Screenshot 2](assets/screenshot-2.png)

![Plugin Logo](assets/icon-128x128.gif)

== Changelog ==
= 1.6 =
* Security Enhancements: Sanitized inputs and outputs, added nonces.
* Code Optimization: Combined style registration and settings sanitization.
* Bug Fixes: Corrected default options and error handling.

= 1.5 =
* Fixes Some Bugs
* Improve & Added UI Features
* New Features Added

= 1.4 =
* Fixes Some Bugs
* Improve UI
* Added dynamic plugin state check for FlexiShare Pro on the Add-ons page

= 1.3 =
* Added Shortcode Generator: Users can now generate custom shortcodes using a dropdown to select categories and specify the number of posts.
* Copy Shortcode Functionality: A button to copy the generated shortcode to the clipboard has been implemented with a confirmation alert.
* Improved UI: Enhanced the shortcode generation UI for better usability.
* Admin Notice: Added a dismissible admin notice to encourage users to leave a review for the plugin.
* Code Optimization: Refactored and cleaned up the code for better readability and maintenance.

= 1.2 =
* Added an option in the settings page to toggle the display of the published date.
* Updated the shortcode to reflect the new setting.

= 1.1 =
* Added date filtering feature to the shortcode.
* Updated the plugin settings page for better user experience.

= 1.0 =
* Initial release.

== Upgrade Notice ==
= 1.0 =
Initial release of Advance Catlist Post.

== License ==
This plugin is licensed under the GPLv2 or later. You are free to modify and distribute this plugin under the same license.

== Credits ==
Developed by Pawan Jagriti. For more information, visit [BiharInfoZone](https://biharinfozone.in).
