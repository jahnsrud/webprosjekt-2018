=== Advanced Custom Fields: Gallery Field ===
Contributors: elliotcondon
Requires at least: 3.6.0
Tested up to: 4.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create beautiful image galleries, sliders and more at lightning speed!

== Description ==

The Gallery field creates a simple and intuitive interface for managing a collection of images. The interface features 2 different views for clients to better manage the data

http://www.advancedcustomfields.com/add-ons/gallery-field/


== Installation ==

This software can be treated as both a WP plugin and a theme include.
However, only when activated as a plugin will updates be available/

= Plugin =
1. Copy the 'acf-gallery' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1. Copy the 'acf-gallery' folder into your theme folder (can use sub folders)
   * You can place the folder anywhere inside the 'wp-content' directory
2. Edit your functions.php file and add the following code to include the field:

`
include_once('acf-gallery/acf-gallery.php');

`

3. Make sure the path is correct to include the acf-gallery.php file
4. Remove the acf-gallery-update.php file from the folder.


== Changelog ==

= 2.0.1 =
* Minor fixes and improvements

= 2.0.0 =
* Added support for ACF version 5

= 1.1.1 =
* Fixed Bug where upload popup would appear when editing an image

= 1.1.0 =
* IMPORTANT: ACF Gallery Field now requires a minimum WordPress version of 3.5.0
* IMPORTANT: If you are using this add-on within a premium theme / plugin, you MUST remove the update file
* Added uploadedTo option to match image / file fields
* Major re-write of the JS to comply with newer jQuery versions
* Improved value returned by get_field: Files and images will now return slightly different and more relevant data

= 1.0.0 =
* Official Release

= 0.0.6 =
* [Fixed] Fix JS error causing images not to update (metadata)

= 0.0.5 =
* [IMPORTANT] This update requires the latest ACF v4 files available on GIT - https://github.com/elliotcondon/acf4
* [Added] Added category to field to appear in the 'Content' optgroup
* [Updated] Updated dir / path code to use acf filter

= 0.0.4 =
* [Fixed] Fix wrong str_replace in $dir

= 0.0.3 =
* [IMPORTANT] This update requires the latest ACF v4 files available on GIT - https://github.com/elliotcondon/acf4
* [Updated] Updated format_value filters for new 3rd parameter

= 0.0.2 =
* [Fixed] Fix wrong css / js urls on WINDOWS server.

= 0.0.1 =
* Initial Release.
