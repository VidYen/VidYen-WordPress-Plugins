=== VidYen Point System ===
Contributors: vidyen
Donate link: https://www.vidyen.com/donate/
Tags: games, monetization, Adscend, Coinhive, rewards, WooCommerce, rewards site, monero, XMR, raffle, mine, cryptocurrency
Requires at least: 4.9.7
Tested up to: 4.9.8
Requires PHP: 7.0
Stable tag: 4.9.8
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VidYen Point System [VYPS] allows you to create a rewards site using video ads or browser mining.

== Description ==

The VidYen Point System [VYPS] allows you to create your own rewards site on WordPress. It supports both Adscend Media, Coinhive, and our own VY256 miner as methods to monetize sites by allowing users to purchase items off a WooCommerce store with points earned from doing those activities. This is a multipart system - similar to WooCommerce - which allows WordPress administrators to track points for rewards via monetization systems. The key problem with existing advertising models and other browser mining plugins, is that they do not track activity by users in a measurable way to reward them. Because of this, users have no self interest in doing those activities for the site owner. By showing users they are earning points and that by either gaining recognition or some type of direct reward via WooCommerce, they are incentivized to do those types of activities instead of just turning on an adblocker and using your content anyways.

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

=Can users buy points directly through WooCommerce?=

No. It was not intended as an RMT or a virtual currency exchange, but if we get enough demand for it, it would not be too hard to add in theory. In the meantime, you could simply sell points in WooCommerce as a virtual item and then manually add them through the admin panel.

=Is there anyway to reward users outside of WooCommerce?=

Yes, with the VY256 Miner, you can setup up shareholder mining so users get a chance to earn XMR hashes to a specified wallet based on the percentage of the designated points they own.

=My users want their rewards in crypto currency rather than in gift cards and virtual items. Can you add this?=

You do know you can do this yourself by adding a wallet field to a WooCommerce checkout and send the crypto currency manually like a regular eCommerce selling physical goods. Also, you can just setup paper wallets from valid wallet generator site and put a $1 in each of them and save the private keys in txt files so your users can just scrap them when purchased in your WooCommerce store. I cannot believe I am recommending Jaxx, but it works well enough or this if your users are not technical. Or you can put a $5 eBay gift card on your store, and your end users can buy it indirectly.

=Are you sure I cannot pay my users directly in crypto for points?=

All right. You can, but you need to setup [Dashed Slug's](https://wordpress.org/plugins/wallets/) wallet which is rather complex and go through the VYPS point exchange through a previously setup bank user to do a user to user off blockchain transfer and then use the aforementioned plugin to do the withdrawal.

== Screenshots ==

1. Create your own point types with their own name and icon.
2. You can name the point type anything you would like and use any image that would make a good icon.
3. Admins can manually add point transactions for their users through the WordPress user panel.
4. Using the point transfer shortcodes, users can exchange points at various rates to other points or WooCommerce credit.
5. Using the Coinhive simple miner shortcode, users can "Mine to Pay" for items on your WooCommerce store
6. Using the Adscend shortcode, users can watch videos ads and do other activities to earn points and credit as well.
7. Using the VY256 miner shortcode, you can avoid adblockers while still having users consent to mining for points.
8. You can use shortcodes to display leaderboards for user rank by point earnings.
9. Or you can display which user owns what percent of the current supply of points.

== Changelog ==

= 1.6.0 =

- Fix: WooCommerce Wallet (semi-hotfix) moved into [vyps-pe]
- Add: Removed the "You need to be logged" in on miner as let the admin use their own LG code in native language if need be (This will be done to more things)
- Note: vyps-pt-ww will remain but will be removed eventually as people update their WooWallet version.


= 1.5.11 =

- Add: User request. Added "timebar=yellow" and "workerbar=orange" to the [vyps-vy256] short code. Replace the color with the color of the choice for the bars.
- Executive Decision: Revert to old method of activity bar rather than a counter bar as it felt more alive.

= 1.5.10 =

- Add: VY256 Worker (Miner) now has a progress bar. Two actually like a boss. The yellow is for activity. If it isn't going you may have to refresh. The orange is the steady progress till the point payout which when it gets to 100% will reward the point. It is useful for high hash payout.
- Fix: I raised the default hash payout to 1024 as I believe this reduces the amount of zero by a great abundance. As always you can set with the hash= attribute in the shortcode.
- Some ground work for v2 XMR fork in October.

= 1.5.9 =

- Fix: Miner display issue. When you had really high has to point ratios, it would display 1 before you got there.
- Add: [vyps-ww] can use the same short code attributes as PE now or the old ones so it doesn't break the site when you update. I am going to revisit this and use a different method before bringing it in PE before depreciating vyps-ww to a better method.

= 1.5.8 =

- Revision of the VY256 hash to point method. I have always tried to avoid decimals but it was always pointed out to me that the current system still creates large amount of points and large amounts of digits on both sides of the decimal point get annoying. So going forward, by default the VY256 miner will show both hashes and points with the default payout being hashes divided by 256. You can change this ratio by hash=1024 if you want a scale. It will always round down.
- Added ability to have custom urls for the miners. This is undocumented pro level. So feel free to ask me directly if interested.
- Note: I feel the start up of the VY256 a bit clunky in feed back, but I need to get other opinions with the timer.
- Fixes to readme file.
- Dropped the [VYPS] the plugin name to match the repository to see if that fix an ongoing statistics issue.
- Mobile view for the [VYPS-PE] with mobile=true makes it friendly with sites with limited width.
- Fix: Seemed to misspelled height with hight in some of the HTML tables. Corrected.

= 1.5.7 =

- Retro-active version change due to WordPress Repository tracking issues. We now are version 1.0 going forward and we have always been at 1.0 not Oceania.
- [VYPS-PE refer=] fix. If symbol= is called it counts the input point as the refer reward and refer=10 will be the 10% of the firstamount= not the output as that will be in crypto not points and cannot be translated accordingly. Hopefully that will not be confusing.
- Readme now has bullets.
- Fix: Apparently, I did not actually include the vyps_is_refer_func() if you have been getting that error it was undefined.
- NOTE: This update was made with 4 hours of sleep. Let me know if there are any bugs, grammar, or spelling issues.

= 1.5.6 =

- Dashed Slug's wallet integration. Added the ability to use the point exchange to trade points for crypto on Dashed Slug's awesome wallet program. It is a rather complicated process and if you want to use it and cannot figure out how it works, you may have to contact me directly for support. I will make a video tutorial in the near future along with working on better documenation.
- Modified wording as there will be a shift from Coinhive to other miners as they now cost $200 or more to use it. Remember VY256 miner is free to use!


= 1.5.5 =

- More referral system additions.
- Added refer= to the [vyps-pe] system to where if they have a referral code entered it gives them points. This way admins can gate in earnings transfers through point exchanges. NOTE: This does not work on WooWallet transfers. Yet... There is a side plan to roll the WW shortcode into the PE one and use short code attribute to specify.
- Created [vyps-refer-bal] which shows log of the points earned by users referrals. NOTE: This is not who has their codes, but had their code and earned them points at some time in the past. You will have to specify the pid= like you would in the public balance.

= 1.5.4 =

- Update to the referral system. More difficult that I expected, but it does more checking and validation of codes and uses a hash system rather than the old method.
- NOTE: If your users started using it (all 9 of you), then they will have to re-update the refer code. That said, the new codes are now and hereby forever the same for each user in perpetuity.
- It is also capitalization sensitive. Just tell them to copy and paste it. I am moving the refer log and exchange bonus to next version. I may allow for a GET update for the code from URL, but it is not that important compared to the other things I need to do.
- Also, some minor wording changes to move away from the use of certain words to better words.

= 1.5.3 =

- Addition of referral system [vyps-refer] short code to display and enter other user's referral codes.
To reward people for referrals, add refer=10 on the [vyps-vy256] short code to give a 10% point award when the referral of that user uses the miner.
- NOTE: I did not add this to other systems, as I would prefer to shuffle people into using the VY256 miner. You may reach out to me directly through the usual means if you want it for Adscend or Coinhive.
- Fixed the [vyps-xmr-wallet] order of operations.
- Naming convention changes in function (for those who use the GitHub). Invisible to WordPress repository users.

= 1.5.2 =

- VY256 Miner focus:
- Miner (minor) counting bug with saying hashes mined but not mined. (I think I have it fixed?)
Executive decision to remove the term "hash" to end user on VY256 miner and use whatever the name and icon of the point it pays out to since that is a known. - I don't think end users need to know what a hash was and since it counts rejected hashes off the server, they often earn more points than what shows up on MoneroOcean.
- Added a XMR wallet validation check. Your wallet needs to start with a 4 or 8 and be at least 90 characters. Yeah. I know legacy wallets may be shorter, but you should really be generating new wallets every now and then when they update the Monero CLI.
- Added a 0:60 count down timer so that people have a false sense of something happening when the miner revs up. Unless they have a potato, it will show reward points way before then. It goes away as soon as hashes are worked on. Laggy but one day will have a real budget for resolving this.


= 1.5.1 =

- As requested. We are adding functionality for a pro-version to remove the branding on demand. Contact on Facebook for details as it takes time for Envato approval.
- This has little functionality except some built in ground word for new planned function for new functionality such as referrals and custom miner work.

= 1.4.18 =

- Added percent ownership to leaderboards as percent=yes in the shortcode attribute options. Now you can see percentage of point ownership without pulling out Excel.
- Update to the VY256 instructions, as needed to be more clear on how to use MoneroOcean.
Some minor house cleaning.

= 1.4.17 =

- Addition of "Powered by VYPS" branding to Coinhive and Adscend portions. Again, if you need a pro-version feel free to reach out.

= 1.4.16 =

- Added shortcode so user can store their XMR for the miner share system.
- Added miner shareholders where users can upload their XMR address and based how much they own of a point type they will get their wallet with a weighted roll to be mined too. (A good meta for your users who want to convert other points to mining shareholder to earn actual XMR without having to worry about your site being hacked)
- See VY256 Miner Shortcode instruction page for details.

= 1.4.15 =

- Updated VY256 miner to animate when start button is clicked.
- Various wording fixes. Proofreading. My one weakness.

= 1.4.14 =

- Added new VY256 miner graphics. Now goes into rotation between male or female. See VY256 Miner Shortcode instruction page for options.
- And a 'Powered by VYPS' branding. If it bothers anyone feel free to ask for a pro version or you can edit it our yourself (it is all open source you know)

= 1.4.13 =

- Added timed based transfers for minute, hourly, daily, or weekly rewards with [vyps-pe]
- See shortcode instructions for attributes. Can be used for a liquidity gate if required points set to a value instead of 0.
- [vyps-pe] now automatically can do two inputs or not depending if its added in shortcode.
- NOTE: vyps-pe will be depreciating all point transfer methods in future so vyps-pt, vyps-pt-tbl, and vyps-pt-two will all be going away (someday)
- Also removal of the word points in some areas to make the system more generic if admin wanted to make it crypto or game resource related.

= 1.4.12 =

- Added new shortcode for point transfer using two inputs. [vyps-pt-two]
- Small revisions to point transfer wording.

= 1.4.11 =

- Moved hash and mining server to cloud.vy254.com
- No longer only uses port 80 so less problems with WordPress servers that cannot curl call on custom ports.
- Also added in ability for rollover servers, but no need for now.
- Some minor fixes to miner.
- On server side can spool from instances if there is an outage on the VY256 miner.
- Threshold raffle had header updates.
- Executive decision to support MoneroOcean as only supported miner for VY256 due to issues with other pools. (In theory its still possible to use other pools, but you need to run your own server and the code is open source on our Github)

= 1.4.10 =

- Another VY256 miner update. NOW with CPU control!
- Also fancy graphical interface update. The old version still exists under vyps-256-debug shortcode.

= 1.4.9 =

- Major VY256 update! NOW with server side hash tracking!
- Does not appear to have problems with uBlock, Brave, or Malwarebytes (Still requires consent!)
- Needs more work such as throttle and CPU control, but under Windows 10 usually only takes up 10% and can be increased by using more than one browser profile.
- Pays to any wallet and currently works with MoneroOcean.stream pool (more pools incoming).
- And some various bug fixes and testing.

= 1.4.6 =

- Added a very experimental mining software that uses VidYen's own pool (thanks to notgiven688 webminer pool Github )
- NOTE: This is VERY experimental. Hashes are tracked client side, so if anyone hacked the post they could in theory add as many points as they want.
- HOWEVER: This appears to not cause any adblockers to have issues nor Malwarebytes to complain. I still require use of the consent button for code to show.
- ALSO NOTE: Since hashes are tracked on the page closing the browser or refreshing the page with something other than the redeem button will cause them not to be awarded. Therefore you should warn them about that.

= 1.4.3 =

- Added a public user balance shortcode so other users can be aware of the balances of other users without having to do manual calculations with the log. (Game theory)

= 1.3.9 =

- Added pagination to the public log as it was getting cumbersome on our own test server. ex: [vyps-pl rows=25 bootstrap=yes]
- Minor bug fixes.
- Prep for public balances and leaderboards.

= 1.3.6 =

- Official release of Threshold Raffle Game via shortcode. (Yes you can use Coinhive to do a RNG raffle now)
- See VYPS menu page instruction on how to implement.
- Minor fixes to formatting.
- Prep for Public Log Pagination system.

= 1.3.3 =

- Emergency hotfix with WP users panel. Nothing to see here.
- Some minor security updates with POST catches.

= 1.2.9 =

- Database fix :NOTE: It says less than 10 active installation on official so I'm taking a gamble that no one needs their tables upgraded. If I'm wrong reach out via support.
- Otherwise you need a fresh install to upgrade.
- Added Threshold Raffle, but no documentation as is Alpha testing. Shortcode is as such: [vyps-tr spid=3 dpid=3 samount=1000 damount=10000 tickets=10]
Otherwise a ton of fixes to the database calls making it more readable.

= 1.2.6 =

- Added support for Adscend and Coinhive API Tracking
Various bug fixes.

= 1.2.4 =

- Official release of base program
- WooCommerce Wallet bridge.
- Multiple point types
- User viewable balances with icons
- Admin option in users to add or subtract points from users
- Public point transaction logo
- Point transfer exchange shortcodes.

== Future Plans ==

WordPress based combat game
Leaderboard
Downloadable public log
Online game API transfer system (Word of Tanks API etc.)
