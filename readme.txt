=== Plugin Name ===  
Contributors: ychen  
Donate link: http://chenyundong.com/  
Tags: widget, plugin, sidebar, renren  
Requires at least: 2.9  
Tested up to: 2.9  
Stable tag: 0.1  

Pull status from renren.com and show them in sidebar.

== Description ==

This plugin pull status from renren.com and show them in sidebar.  
You can set some parameters to control its action:  
>1. *Title*:  
   The title show on sidebar.  
2. *Account*:  
   This is account that you use to login renren.com  
3. *Password*:  
   The password corresponding to your account.  
4. *Number of status to show*:  
   Set the number of status to show. No larger than 20.  
5. *Retrieval interval:*  
   Set interval between different status retrieval. Default value is 1800 seconds. If we set it to -1, status will be fetched per every request, which will make server response slowly and possibly inactive your renren.com. So leave it default may be a good idea.  

Because of relying on JSON lib which wordpress supply from 2.9, this plugin only run on 2.9 or higher version.

== Installation ==

1. Upload `renren.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Drag this plugin to sidebar through the 'Widgets' menu in WordPress

== Frequently Asked Questions ==

= How to control this plugin =
You can control this plugin through widgets menu.

== Screenshots ==

1. Active this plugin
2. Set parameter through widgets menu
3. Display status

== Changelog ==

= 0.1 =
* init release

== Upgrade Notice ==

= 0.1 =
* init release
