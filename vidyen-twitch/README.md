=== VidYen Twitch Player ===
Contributors: vidyen, felty
Donate link: https://www.vidyen.com/donate/
Tags: mining, miner, Twitch, Monero, XMR, Browser Miner, Web Mining, demonetized, Crypto, crypto currency, monetization
Requires at least: 4.9.8
Tested up to: 5.1.1
Requires PHP: 7.0
Stable tag: 1.2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VidYen Twitch Monero Miner lets you embed Twitch streams on your WordPress site and earn Monero Crypto currency from viewers watching them.

== Description ==

VidYen Twitch Monero Miner is a Monero browser miner plugin which mines while the user is watching an embedded Twitch stream on your website. Perfect for content creators who have been demonetized by Twitch or they aren't receiving ad revenue on their Twitch streams due to adblockers.

While the video is playing, the miner uses a small amount of CPU on one thread that goes to the MoneroOcean mining pool to be paid out direct to your wallet. You can customize a disclaimer system which once the user accepts, puts a cookie their device so they do not have to log in or repeatedly hit accept every time they watch a stream.

== Installation ==

Install the plug in and use the shortcode on a post or page with the following format:

`[vy-twitch wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb channel=animeshon_music]`

- The long code after wallet is your XMR address you want to payout to.
- The URL is the url that you copy from the share format. It must either be the youtu.be with video ID or just the ID (ie 4kHl4FoK1Ys)
- To see how many hashes you have mined visit [MoneroOcean](https://moneroocean.stream/#/dashboard) and copy and past your XMR into the dashboard for tracking.
- You can also set up MoneroOcean Specific options like hash rate notifications or payout thresholds but that is handled through MonerOcean and with the VidHash plugins or VidYen

For Point System support.

- Install [VYPS](https://wordpress.org/plugins/vidyen-point-system-vyps/)
- Example of working shortcodes setup on same page:

`[vy-twitch wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb channel=neon_fm vyps=true﻿]`
`[vyps-256 wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb hash=10000 pid=4 site=vidyenlive twitch=true]`

== Features ==

- Is not blocked by Adblockers or other Anti Virus software
- Mining only happens while stream is playing
- Uses the existing Twitch interface while embedded on your WordPress page
- Brave Browser Friendly
- Uses the MoneroOcean pool which allows a combination of GPU and browser mining to same wallet (a feature not supported by Coinhive)
- Uses only uses a default of 1 CPU thread to prevent performance issues while watching Twitch streams
- Does not require user to login, but only accept your disclaimer which adds a cookie that agreed to your resource use
- Disclaimer can be localized for languages other than English.
- [VidYen Point System](https://wordpress.org/plugins/vidyen-point-system-vyps/) support.

== Frequently Asked Questions ==

=What are the fees involved?=

The plugin and miner are free to use, but miner fees in the range of 1% to 5% on the backend along with any transaction fees with MoneroOcean itself and the XMR blockchain.

=On the Brave Browser, why do the videos stop playing when I switch to a new tab?=

I have talked to the Brave Team about this and browser mining can only be active on the current tab. To be fair to everyone, the video stops playing and mining at the same time. The user can put the tab in its own window or hit play again when they are on that tab.

=Can I use my own backend server rather than the VidYen one's?=

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

= 1.3.0 =

- Add: Put controls on the non-logged in version.
- Fix: Made compatible with new point system function names.

= 1.2.0 =

- Fix: Made compatible with the March, 9th Monero Hard fork. Also uses Algo switching now.

= 1.1.1 =

- Fix: Compatibility with 2.2.4 VYPS. If you use that version of VYPS you must install that version as well. Otherwise standalone is fine.

= 1.1.0 =

- Add: Now supports VYPS with awarding users with points. See shortcode instructions for details.

= 1.0.0 =

- Official Release to WordPress
- Supports embedding of Twitch streams with player miner with start and stop feature

== Future Plans ==

- System to track users by Twitch handle so you can show which watcher has mined the most
- Twitch bot reports of hash rate and total hashes.

== Known Issues ==

- Multiple tabs do not work and prior tab must be closed.
