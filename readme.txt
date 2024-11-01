=== Plugin Name ===
Contributors: GaryJ
Donate link: http://code.garyjones.co.uk/donate/
Tags: twivatar, twitter, avatar
Requires at least: 2.8.0
Tested up to: 3.5.0
Stable tag: 1.0.2
License: GPL-2.0+

Adds a shortcode that echos the current Twitter avatar for a username. Image URL is cached.

== Description ==

Adds twivatar shortcode. When also passed a Twitter handle as the name attribute, the plugin will get the current avatar from Twitter, and store the URL for 24 hours. It then echoes out an image.

Attributes (optional unless marked otherwise):

* `name` - Twitter handle. Required.
* `size` - One of 'mini', 'normal' or 'bigger' Default is 'normal'.
* `link` - Whether to link the image to the profile page on Twitter. Default is true.
* `linkclass` - class attribute value of the link. Default is none.
* `rel` - rel attribute value of the link. Default is none.
* `class` - class attribute value of the image. Default is none.
* `title` - title attribute value of the image. Default is none.

Please report all issue via [https://github.com/GaryJones/twivatar/issues](https://github.com/GaryJones/twivatar/issues).

== Installation ==

1. Unzip and upload `twivatar` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where should I report any problems? =

Please report all issue via [https://github.com/GaryJones/twivatar/issues](https://github.com/GaryJones/twivatar/issues).

= Can I amend how long the image URL is cached for? =

Not yet. In a future version of the plugin, you will be able to. Current default is 24 hours.

= Can I change the default Twitter image? =

Not yet. In a future version of the plugin, you will be able to. Current default is to use an image file called `no-twitter-image.png` in a folder called `images` in your active theme.

This image is only used if no image can be determined for the account. Either Twitter is down, or the account doesn't exist. If the account does exist, but has no image, then the default one that Twitter uses will be stored.

== Screenshots ==

1. Example of output, as seen at http://bsktweetup.info/

== Changelog ==

= 1.0.2 =
* Removed array type hint that may not be satisfied in edge cases.
* Moved main class into its own file.
* Made the variable that has the instance of the class assigned to it, explicitely global.
* Added .pot file.

= 1.0.1 =
* Tidy up of old code, including improved i18n support.

= 1.0.0 =
* First public version.

== Upgrade Notice ==

= 1.0.2 =
Non-critical update - minor code tweaks.

= 1.0.1 =
Non-critical update - tidy up of old code and documentation.

= 1.0.0 =
Update from nothingness. You will feel better for it.