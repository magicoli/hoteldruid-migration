=== HotelDruid migration tool to WooCommerce ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: <http://example.com/>
Tags: comments, spam Requires at least: 3.0.1
Requires at least: 4.8
Tested up to: 6.0.1
Stable tag: 0.1.0
License: AGPLv3 or later
License URI: <https://www.gnu.org/licenses/agpl-3.0.fr.html>

HotelDruid migration tool for WooCommerce Bookings

== Description ==

This plugin is unstable. It addresses a specific need and is not intended for general distribution. Do not use it unless you are a developer and know what you do. You need to read, verify and adjust the code according to your needs.

The intend of this plugin is to migrate booking data from an HotelDruid setup to a WordPress WooCommerce bookings solution.

= Background context =

HotelDruid is an hotel management program. It is open source and has been there for more than a decade, but lacks a lot of features and customer support.

* the software has a lot of limitations and only minor improvements were published during the last decade
* no, bad or very little use of modern programming standards (classes, css...)
* external calendar synchronisation is closed-source and limited to a few providers (booking.com and expedia)
* no iCal synchronisation, making impossible to sync with any other provider
* poor invoicing system
* in addition, the code is pretty obscure, making it very difficult to contribute

10 years ago, the offer was pretty low, so this solution seemed interesting, with the hope more features and improvements would appear in the future. And here we are, ten years later, the software received close to no improvements, while the general offer in this market has grown, with very efficient products like Lodgify, Maestrel HBook, MotoPress Hotel Booking or WooCommerce Bookings, to only cite a few.

The solution should be covered by an upcoming project https://github.com/magicoli/bookings-calendar-sync

For this, I need to convert all HotelDruid data to import them  in WordPress. And that's the goal of this project.

== Installation ==

1. In HotelDruid -> Configure -> Backup, create a new backup file and download it (it would be named "hoteld_backup.php")
2. Create a WordPress website, install and activate WooCommerce (required) and WooCommerce Bookings (optional)
3. Upload the backup file hoteld_backup.php on the webite server, in a folder accessible by the server, but outside the website structure.
3. Go to WordPress admin menu -> Tools -> HotelDruid migration
4. Paste the backup file location
5. Click "Start conversion" (not implemented yet)

== Frequently Asked Questions ==

= What's the answer? =

42

== Changelog ==

= 0.1.0 =

* Initial commit
