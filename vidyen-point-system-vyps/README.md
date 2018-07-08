=== VidYen Point System ===
Contributors: vidyen
Donate link: https://vidyen.com
Tags: games, user, gamify, monetization, Adscend, Coinhive, WooCommerce Wallet
Requires at least: 4.9.5
Tested up to: 4.97
Requires PHP: 7.0
Stable tag: 4.9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VidYen Point System [VYPS] allows you to gamify monetization by giving your users a reason to turn off adblockers in return for rewards and recognition.

== Description ==

This is a multipart system - similar to WooCommerce - which allows WordPress administrators to track points for rewards in monetization systems. The key problem with existing advertising models and Coinhive plugins is that they do not track activity by users in a quantifiable way (or at all) so users have no self interest in doing those activities for the owner of the WordPress site. By showing users they are earning points and that by either gaining recognition or some type of reward via WooCommerce, they are incentivized to do those types of activities instead of just turning on an adblocker and using your content.

This plugin allows you to create points and assign them to users based off monetization activities such as Adscend advertising or Coinhive mining via those systems API.

The goal is to allow you to have your users do all sorts of activities that can be used in games and to purchase items on WooCommerce.

== Frequently Asked Questions ==

=Can I delete point types=

No. In order to prevent a more open and fair system, admins can only change the name and icon of the points rather than allowing the wiping of entire balances. You can simply change the name and then remove all possibility of users interacting with that point type going forward. You cannot wipe the history though.

=Can I delete a point transaction?=

No. In order to have a system similar tp (but not) a blockchain, to decrease a users balance you must have a negative transaction of that point type so everyone can see in the log that it happened.

=Can I use point types I create with VYPS to give credit to users on WooCommerce?=

Yes. You can install WooCommerce Wallet and use the point transfer shortcode to transfer points at various rates and then the user can use the wallet credit to make purchases.

=Can users transfer points between themselves=

As, WooCommerce Wallet already has a feature to transfer between users, we did not add that feature for VYPS points directly. If there is a demand, it can be added though.

=Can people buy points directly through WooCommerce?=

No. It was not intended as an RMT or an virtual currency exchange, but if we get enough demand for it, it wouldn't be too hard in theory. You could simply sell a lot of points in WooCommerce and then manually add them through the admin panel for now.

== Changelog ==

= 00.02.06 =

Added support for Adscend and Coinhive API Tracking
Various bug fixes.

= 00.02.04 =

Official release of base program
WooCommerce Wallet bridge.
Multiple point types
User viewable balances with icons
Admin option in users to add or subtract points from users
Public point transaction logo
Point transfer exchange shortcodes.

== Future Plans ==

WordPress based RNG game
Leaderboard
Better public log system
Online game API transfer system (Word of Tanks API etc)
