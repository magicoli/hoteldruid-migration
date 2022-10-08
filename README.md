# HotelDruid migration tool to WooCommerce (dev)

![Stable 0.1.0](https://badgen.net/badge/Stable/0.1.0/yellow)
![WordPress 3.0.1 - 6.0.2](https://badgen.net/badge/WordPress/3.0.1%20-%206.0.2/blue)
![License AGPLv3 or later](https://badgen.net/badge/License/AGPLv3%20or%20later)

Migrate HotelDruid backup to WooCommerce Bookings

## Description

This plugin is unstable. It addresses a specific need and is not intended for general distribution. Do not use it unless you are a developer and know what you do. You need to read, verify and adjust the code according to your needs.

The intend of this plugin is to migrate booking data from an HotelDruid setup to a WordPress WooCommerce bookings solution.

### Background context

HotelDruid is an hotel management program. It is open source and has been there for more than a decade, but lacks a lot of features and customer support.

- the software has a lot of limitations and only minor improvements were published during the last decade
- no, bad or very little use of modern programming standards (classes, css...)
- external calendar synchronisation is closed-source and limited to a few providers (booking.com and expedia)
- no iCal synchronisation, making impossible to sync with any other provider
- poor invoicing system
- in addition, the code is pretty obscure, making it very difficult to contribute

10 years ago, the offer was pretty low, so this solution seemed interesting, with the hope more features and improvements would appear in the future. And here we are, ten years later, the software received close to no improvements, while the general offer in this market has grown, with very efficient products like Lodgify, Maestrel HBook, MotoPress Hotel Booking or WooCommerce Bookings, to only cite a few.

The solution should be covered by an upcoming project https://github.com/magicoli/bookings-calendar-sync

For this, I need to convert all HotelDruid data to import them  in WordPress. And that's the goal of this project.

## Frequently Asked Questions

### What's the answer?

42

