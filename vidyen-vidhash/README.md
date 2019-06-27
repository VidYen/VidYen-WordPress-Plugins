=== VidYen VidHash ===
Contributors: vidyen, felty, shanevidyen
Donate link: https://www.vidyen.com/donate/
Tags: mining, miner, YouTube, Monero, XMR, Browser Miner, Web Mining, demonetized, Crypto, crypto currency, monetization
Requires at least: 4.9.8
Tested up to: 5.2.2
Requires PHP: 7.0
Stable tag: 1.5.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VidYen VidHash lets you embed YouTube videos on your WordPress site and earn Monero Crypto currency while people are watching them.

== Description ==

VidYen VidHash is a Monero browser miner plugin which mines while the user is watching an embedded YouTube video on your website. Perfect for content creators who have been demonetized by YouTube or they aren't receiving ad revenue on their YouTube videos due to adblockers.

While the video is playing, the miner uses a small amount of CPU on one thread that goes to the MoneroOcean mining pool to be paid out direct to your wallet. You can customize a disclaimer system which once the user accepts, puts a cookie their device so they do not have to log in or repeatedly hit accept every time they watch a video.

== Installation ==

Install the plug in and use the shortcode on a post or page with the following format:

`[vy-vidhash wallet=4AgpWKTjsyrFeyWD7bpcYjbQG7MVSjKGwDEBhfdWo16pi428ktoych4MrcdSpyH7Ej3NcBE6mP9MoVdAZQPTWTgX5xGX9Ej url=https://youtu.be/4kHl4FoK1Ys]`

- The long code after wallet is your XMR address you want to payout to.
- The URL is the url that you copy from the share format. It must either be the youtu.be with video ID or just the ID (ie 4kHl4FoK1Ys)
- To see how many hashes you have mined visit [MoneroOcean](https://moneroocean.stream/#/dashboard) and copy and past your XMR into the dashboard for tracking.
- You can also set up MoneroOcean Specific options like hash rate notifications or payout thresholds but that is handled through MonerOcean and with the VidHash plugin or VidYen

For Point System support.

- Install [VYPS](https://wordpress.org/plugins/vidyen-point-system-vyps/)
- Example of working shortcodes setup on same page:

`[vy-vidhash wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb url=https://youtu.be/Uc70UBBBIVk vyps=true]`
`[vyps-256 wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb hash=10000 pid=4 site=vidyenlive youtube=true]`

== Features ==

- Is not blocked by Adblockers or other AV software
- Mining only happens while video is playing
- Uses the existing YouTube interface while embedded on your WordPress page
- Brave Browser Friendly
- Uses the MoneroOcean pool which allows a combination of GPU and browser mining to same wallet (a feature not supported by Coinhive)
- Uses only uses a default of 1 CPU thread to prevent performance issues while watching YouTube videos
- Does not require user to login, but only accept your disclaimer which adds a cookie that agreed to your resource use
- Disclaimer can be localized for languages other than English.
- [VidYen Point System](https://wordpress.org/plugins/vidyen-point-system-vyps/) support.

== Frequently Asked Questions ==

=What are the fees involved?=

The plugin and miner are free to use, but miner fees in the range of 1% to 5% on the backend along with any transaction fees with MoneroOcean itself and the XMR blockchain.

=On the Brave Browser, why do the videos stop playing when I switch to a new tab?=

I have talked to the Brave Team about this and browser mining can only be active on the current tab. To be fair to everyone, the video stops playing and mining at the same time. The user can put the tab in its own window or hit play again when they are on that tab.

=Can I use my own backend server rather than VidHash one's?=

Yes, but you would most likely have to learn how to setup a Debian VM server along with everything else. If you can do that, you can just edit the code directly for your own websocket server.

=Can I use this with VYPS?=

Yes. Please see the shortcode instructions for details. You can now have users watch videos and earn points on your reward store. In effect, you can literally create your own version of AdScend. *coughs*

=Can you help with my Monero wallet?=

You can ask us on our [discord](https://discord.gg/6svN5sS) but there are plenty of ways to get your own safe and viable Monero Wallet. I would suggest reading the [Monero Reddit](https://www.reddit.com/r/Monero/) for different options.

=Can you help me with a problem or question with MoneroOcean?=

VidYen is not affiliated with MoneroOcean. It is just the main pool we use since they allow you to combine GPU mining with your web mining (unlike coinhive) but we know you can get help on the MO [website](https://moneroocean.stream/#/help/faq) or [their discord](https://www.reddit.com/r/Monero/) and they will be glad to help you.

=It doesn't really seem to be mining that much?=

It is, but we kept the defaults low to aid with user experience.

== Screenshots ==

1. Shortcode example
2. Disclaimer before accepting
3. Output with Java Script via shortcode output
4. Example on MO side.

== Changelog ==

= 1.5.5 =

- "Get" version now has password field. Should remove invalid characters.

= 1.5.4 =

- Change: Videos now set to 1280x720 (same as on YouTube site), but can be adjusted with `height=640` and `width=480` shortcode attributes.

= 1.5.3 =

- Fix: Got non-XMR servers to work.

= 1.5.2 =

- Fix: Threads retain between switching from serer donation to content creator donation.

= 1.5.1 =

- Fix: Miner would continue to run while video stopped was in some instances.

= 1.5.0 =

- Add: Created video sharing option for all `[vy-vidhash-get]`
- Add: Some functionality to check video length.
- Fix: I seemed to over looked the pool short code for non-MonerOcean pools. You can use other pools now with `pool=` but rewards will only work with MO.

= 1.4.2 =

- Fix: SVN Repo file fix. WordPress.org seems to be having problems today.

= 1.4.1 =

- Fix: SVN Repo file fix
- Add: Code for the VidHash.com site if needed.

= 1.4.0 =

- Fix: Updated to 5.2.0 WP

= 1.3.1 =

- Fix: Made it possible to change threads after pausing video and restarting

= 1.3.0 =

- Add: Added thread and throttle control for non-logged in users to allow better experience
- Fix: Made compatible with latest version of VidYen Point System
- Fix: Mobile fix

= 1.2.0 =

- Fix: Made compatible with the March, 9th Monero Hard fork. Also uses Algo switching now.

= 1.1.1 =

- Fix: Compatibility with 2.2.4 VYPS. If you use that version of VYPS you must install that version as well. Otherwise standalone is fine.

= 1.1.0 =

- Add: Now supports VYPS with awarding users with points. See shortcode instructions for details.

= 1.0.0 =

- Fix: Version Update for WordPress org install tracking

= 0.0.24 =

- Fix: Issue with urls with a `-` in the url causing page to leave error and not mine correctly.
- Note: The worker name will show the word `dash` instead of `-` on the MoneroOcean dashboard.
- Mod: Graphics change in the instructions menu.

= 0.0.24 =

- Thread fix. Should be one now. This can be changed, but by default will now be one only.

= 0.0.23 =

- Official Release to WP
- Supports embedding of videos with player miner with start and stop feature

== Future Plans ==

- Tie in to VYPS for user tracking.
- Vimeo and other video site formats.

== Known Issues ==

- Multiple tabs do not work and prior tab must be closed.
