# HotelDruid migration tool to WooCommerce
* Contributors: (this should be a list of wordpress.org userid's)
* Donate link: <http://example.com/>
* Tags: comments, spam Requires at least: 3.0.1
* Tested up to: 6.0
* Stable tag: 4.3
* License: AGPLv3 or later
* License URI: <https://www.gnu.org/licenses/agpl-3.0.fr.html>

HotelDruid migration tool for WooCommerce Bookings

## Description

This plugin is unstable. It answers to a specific need and is not intended for general distribution. Do not use it unless you are a developer and know what you do. You need to read, verify and adjust the code according to your needs.

The intend of this plugin is to migrate booking data from an HotelDruid setup to a WordPress WooCommerce bookings solution.

HotelDruid is an hotel management program. It is open source and has been there for more than a decade, but lacks a lot of features and customer support.

* the software has a lot of limitations and only minor improvements were published during the last decade
* no, bad or very little use of modern programming standards (classes, css...)
* external calendar synchronisation is closed-source and limited to a few providers (booking.com and expedia)
* no iCal synchronisation, making impossible to sync with any other provider
* poor invoicing system
* in addition, the code is pretty obscure, making it very difficult to contribute

10 years ago, the offer was pretty low, so this solution seemed interesting, with the hope more features and improvements would appear in the future. And here we are, ten years later, the software received close to no improvements, while the general offer in this market has grown, with very efficient products like Lodgify, Maestrel HBook, MotoPress Hotel Booking or WooCommerce Bookings, to only cite a few.

The base of my websites is [WordPress](https://wordpress.org). Because it's free, it's modular, it's quite easy to comprehend, it has a lot of user (meaning a lot of people to answer questions), and a huge catalog of extensions. It is usually a good website management solution for small businesses.

### Simple holiday rental solution

For small holiday rentals, providing a few simple other services, I recommend one of these plugins

* [Maestrel HBook](https://maestrel.com/hbook/)
* [MotoPress Hotel Booking](https://motopress.com/products/hotel-booking/)

They both provide iCal sync from and to external calendars, online booking and payments. They also include features for additional services, but it could be limited for things like restaurant, host table or car rentals, requiring a more complex boking and billing system.

* [Lodgify](https://Lodgify1.referralrock.com/l/1OLIVIERVAN88/) OTA to centralize calendar synchronisations (booking.com, Expedia, Abritel, Airbnb, etc.)

### More complete holiday rental offer

Here is the actual reason of this project.

For business with a larger offer, I recommend WooCommerce as a base, its modular structure allows to add any kind of products.

While there are several WooCommerce offers for hotel booking, I didn't find any suiting all my needs, so here is a mixed setup:

* [WordPress](https://wordpress.org) for the website
* [WooCommerce](https://woocommerce.com/) for orders, online payment and billing
* [WooCommerce Bookings](https://woocommerce.com/products/woocommerce-bookings/) for online reservations As for now, WooCommerce Bookings lacks external calendar sync (except using a Google Calendar). However it seems the best solution to mix several types of services in an unified, coherent environment, so this will be the goal of ano
* [Lodgify](https://Lodgify1.referralrock.com/l/1OLIVIERVAN88/) OTA: external, real time calendar synchronisation as well as a complete solution for booking management.

## Frequently Asked Questions

### What's the answer?

42

