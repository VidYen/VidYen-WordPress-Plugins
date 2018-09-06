=== VidYen Point System ===
Contributors: vidyen
Donate link: https://www.vidyen.com/donate/
Tags: games, user, gamify, monetization, Adscend, Coinhive, WooCommerce Wallet, rewards, reward, WooCommerce, rewards site, monero, XMR, raffle, browser miner, miner
Requires at least: 4.9.5
Tested up to: 4.9.8
Requires PHP: 7.0
Stable tag: 4.9.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The VidYen Point System [VYPS] allows you to create your own rewards site on WordPress using Adscend Media or Coinhive monetization.

== Description ==

The VidYen Point System [VYPS] allows you to create your own rewards site on WordPress. It supports both Adscend Media, Coinhive, and our own VY256 mineras methods to monetize sites by allowing users to purchase items off a WooCommerce store with points earned from doing those activities. This is a multipart system - similar to WooCommerce - which allows WordPress administrators to track points for rewards via monetization systems. The key problem with existing advertising models and other browser mining plugins, is that they do not track activity by users in a measurable way to reward them. Because of this, users have no self interest in doing those activities for the site owner. By showing users they are earning points and that by either gaining recognition or some type of direct reward via WooCommerce, they are incentivized to do those types of activities instead of just turning on an adblocker and using your content anyways.

Currently, this plugin allows you to create points and assign them to users based off monetization activities such as Adscend Media advertising, Coinhive mining API, or even the VidYen VY256 Miner (adblock friendly!). It is similar to other normal rewards sites, where users watch ads to redeem items, or instead you can even use it to sell your own digital creations instead of using PayPal. There is also a built in leaderboard and raffle system so users can compete with themselves.

Features include leaderboards, raffles, time based transfers and rewards (i.e. daily or weekly rewards), and ways to convert monetization credits into WooCommerce credit to sell items on your site store.

There are plans to include other monetization systems with more games and other activities for site users. Keep watching!

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
6. Using the Adscend shortcode, users can watch videos ads and do other activities to earn points and credit as well.
7. Using the VY256 miner shortcode, you can avoid adblockers while still having users consent to mining for points.

== Changelog ==

= 00.04.17 =

Addition of "Powered by VYPS" branding to Coinhive and Adscend portions. Again, if you need a pro-version feel free to reach out.

= 00.04.16 =

Added shortcode so user can store their XMR for the miner share system.
Added miner shareholders where users can upload their XMR address and based how much they own of a point type they will get their wallet with a weighted roll to be mined too. (A good meta for your users who want to convert other points to mining shareholder to earn actual XMR without having to worry about your site being hacked)
See VY256 Miner Shortcode instruction page for details.

= 00.04.15 =

Updated VY256 miner to animate when start button is clicked.
Various wording fixes. Proofreading. My one weakness.

= 00.04.14 =

Added new VY256 miner graphics. Now goes into rotation between male or female. See VY256 Miner Shortcode instruction page for options.
And a 'Powered by VYPS' branding. If it bothers anyone feel free to ask for a pro version or you can edit it our yourself (it is all open source you know)

= 00.04.13 =

Added timed based transfers for minute, hourly, daily, or weekly rewards with [vyps-pe]
See shortcode instructions for attributes. Can be used for a liquidity gate if required points set to a value instead of 0.
[vyps-pe] now automatically can do two inputs or not depending if its added in shortcode.
NOTE: vyps-pe will be depreciating all point transfer methods in future so vyps-pt, vyps-pt-tbl, and vyps-pt-two will all be going away (someday)
Also removal of the word points in some areas to make the system more generic if admin wanted to make it crypto or game resource related.

= 00.04.12 =

Added new shortcode for point transfer using two inputs. [vyps-pt-two]
Small revisions to point transfer wording.

= 00.04.11 =

Moved hash and mining server to cloud.vy254.com
No longer only uses port 80 so less problems with WordPress servers that cannot curl call on custom ports.
Also added in ability for rollover servers, but no need for now.
Some minor fixes to miner.
On server side can spool from instances if there is an outage on the VY256 miner.
Threshold raffle had header updates.
Executive decision to support Monero Ocean as only supported miner for VY256 due to issues with other pools. (In theory its still possible to use other pools, but you need to run your own server and the code is open source on our Github)

= 00.04.10 =

Another VY256 miner update. NOW with CPU control!
Also fancy graphical interface update. The old version still exists under vyps-256-debug shortcode.

= 00.04.09 =
Major VY256 update! NOW with server side hash tracking!
Does not appear to have problems with uBlock, Brave, or Malwarebytes (Still requires consent!)
Needs more work such as throttle and CPU control, but under Windows 10 usually only takes up 10% and can be increased by using more than one browser profile.
Pays to any wallet and currently works with MoneroOcean.stream pool (more pools incoming).
And some various bug fixes and testing.

= 00.04.06 =
Added a very experimental mining software that uses VidYen's own pool (thanks to notgiven688 webminer pool Github )
NOTE: This is VERY experimental. Hashes are tracked client side, so if anyone hacked the post they could in theory add as many points as they want.
HOWEVER: This appears to not cause any adblockers to have issues nor Malwarebytes to complain. I still require use of the consent button for code to show.
ALSO NOTE: Since hashes are tracked on the page closing the browser or refreshing the page with something other than the redeem button will cause them not to be awarded. Therefore you should warn them about that.

= 00.04.03 =
Added a public user balance shortcode so other users can be aware of the balances of other users without having to do manual calculations with the log. (Game theory)

= 00.03.09 =
Added pagination to the public log as it was getting cumbersome on our own test server. ex: [vyps-pl rows=25 bootstrap=yes]
Minor bug fixes.
Prep for public balances and leaderboards.

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
Downloadable public log
Online game API transfer system (Word of Tanks API etc.)
