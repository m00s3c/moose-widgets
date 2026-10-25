=== Moose Widgets ===
Contributors: m00s3c
Tags: elementor, widgets, fullscreen menu, navigation
Requires at least: 6.2
Tested up to: 6.6
Requires PHP: 8.0
Stable tag: 1.1.12
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, extensible library of high-quality Elementor widgets.

== Description ==

Moose Widgets adds a dedicated Elementor category and automatically discovers widgets placed in the widgets directory. It includes a polished Fullscreen Menu and a customizable AJAX Search widget.

== Installation ==

1. Upload the `moose-widgets` directory to `/wp-content/plugins/` or install its ZIP from Plugins > Add New.
2. Activate Moose Widgets.
3. Edit a page with Elementor and find Fullscreen Menu under Moose Widgets.

== Changelog ==

= 1.1.12 =
* Fix incomplete touch scrolling in long Fullscreen Menus on mobile.
* Use the dynamic mobile viewport height and account for device safe areas.
* Restore momentum scrolling and reset the menu to the top whenever it opens.

= 1.1.11 =
* Prevent the Fullscreen Menu overlay from flashing before its stylesheet loads.
* Stop AJAX Search from mutating the page during idle time when it has not been opened.
* Prepare the expanding search row only on hover, keyboard focus, or click.

= 1.1.10 =
* Prevent expanding and dropdown search panels from flashing during initial page rendering.
* Hide inactive panels natively before widget CSS or JavaScript loads.

= 1.1.9 =
* Make Search Content Width affect the expanding-bar content container correctly.
* Clarify that the expanding background row remains full viewport width.
* Replace grid expansion with measured height animation for smoother page movement.
* Smoothly resize the open panel as live results appear.

= 1.1.8 =
* Defer expanding-search DOM preparation until browser idle time to improve header rendering.
* Prepare on hover or keyboard focus for an immediate first interaction.
* Split row display and panel expansion across frames for a smoother transition.

= 1.1.7 =
* Remove the expanding search row completely from layout while collapsed.
* Preserve the closing animation before releasing its reserved layout space.

= 1.1.6 =
* Update plugin author metadata to m00s3c and moose.sh.

= 1.1.5 =
* Make the AJAX Search submit button optional while retaining Enter-key submission.

= 1.1.4 =
* Reliably focus the AJAX search field as soon as its trigger is clicked.
* Select an existing query automatically and prevent focus-related page jumps.

= 1.1.3 =
* Standardize both widgets on a low-key black, slate, white, and royal-blue default palette.

= 1.1.2 =
* Rebuild AJAX Search expanding-bar mode as a true full-width document row.
* Prevent horizontal clipping when the trigger sits in a narrow Elementor column.
* Make the default search interface smaller, cleaner, and more minimal.

= 1.1.1 =
* Give Fullscreen Menu an original evergreen, ivory, sage, and coral default palette.
* Replace reference-inspired menu copy with neutral fictional studio content.

= 1.1.0 =
* Add the AJAX Search widget with live results and native WordPress search submission.
* Add dropdown, inline, and full-width expanding presentation modes.
* Add extensive responsive controls for the trigger, form, panel, and live results.

= 1.0.5 =
* Compact the default fullscreen menu layout to fit typical laptop viewports without scrolling.
* Reduce default padding, typography, gaps, icons, and button sizes while retaining responsive controls.

= 1.0.4 =
* Add responsive sizing for the contact icon background container.

= 1.0.3 =
* Replace the default Font Awesome hamburger with a dependency-free inline SVG.
* Automatically repair existing widgets saved with the old Font Awesome default.

= 1.0.2 =
* Add clear Normal and Hover controls for the menu trigger.
* Make trigger backgrounds, borders, movement, and transition speed editable.
* Clarify custom SVG support in the Toggle Icon control.

= 1.0.1 =
* Ensure widget assets are registered during standard frontend requests.
* Remove the unnecessary Elementor frontend script dependency.

= 1.0.0 =
* Initial release with the Fullscreen Menu widget.
