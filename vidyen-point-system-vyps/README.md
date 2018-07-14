=== VidYen Point System ===
Contributors: vidyen
Donate link: https://www.vidyen.com/donate/
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

No. In order to make a more open and fair system, admins can only change the name and icon of the points rather than allowing the wiping of entire balances. You can simply change the name and then remove all possibility of users interacting with that point type going forward. You cannot wipe the history though.

=Can I delete a point transaction?=

No. In order to have a system similar to a blockchain or a bank ledger, to decrease a user's balance you must have a negative transaction of that point type so everyone can see in the log that the change happened and that there is a history that everyone can see.

=Can I use point types I create with VYPS to give credit to users on WooCommerce?=

Yes. You can install WooCommerce Wallet and use the point transfer shortcode to transfer points at various rates and then the user can use the wallet credit to make purchases.

=Can users transfer points between themselves=

As WooCommerce Wallet already has a feature to transfer between users, we did not add that feature for VYPS points directly. If there is a demand, it can be added though.

=Can people buy points directly through WooCommerce?=

No. It was not intended as an RMT or a virtual currency exchange, but if we get enough demand for it, it would not be too hard to add in theory. In the meantime, you could simply sell points in WooCommerce as a virtual item and then manually add them through the admin panel.

== Screenshots ==

1. Create your own point types with their own name and icon.
2. You can name the point type anything you would like and use any image that would make a good icon.
3. Admins can manually add point transactions for their users through the WordPress user panel.
4. Using the point transfer shortcodes, users can exchange points at various rates to other points or WooCommerce credit.
5. Using the Coinhive simple miner shortcode, users can "Mine to Pay" for items on your WooCommerce store
6. Using the Adscend plugin, users can watch videos ads and do other activities to earn points and credit as well.

== Changelog ==

= 00.03.06 =

Official release of Threshold Raffle Game via shortcode. (Yes you can use Coinhive to do a RNG raffle now)
See VYPS menu page instruction on how to implement.
Minor fixes to formatting.
Prep for Public Log Pagination system.

= 00.03.03 =

Emergency hotfix with WP users panel. Nothing to see here.
Some minor security updates with POST catches.

= 00.02.09 =

Database fix :NOTE: It says less than 10 active installation on official so I'm taking a gamble that no one needs their tables upgraded. If I'm wrong reach out via support.
Otherwise you need a fresh install to upgrade.
Added Threshold Raffle, but no documentation as is Alpha testing. Shortcode is as such: [vyps-tr spid=3 dpid=3 samount=1000 damount=10000 tickets=10]
Otherwise a ton of fixes to the database calls making it more readable.

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

WordPress based combat game
Leaderboard
Better public log system
Online game API transfer system (Word of Tanks API etc.)
