=== LH Login Page ===
Contributors: shawfactor
Donate link: http://lhero.org/plugins/lh-login-page/
Tags: login, frontend, popup, form, modal, widget
Requires at least: 3.0.
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Easily place a HTML5 login form on the front end of your website 

== Description ==

This plugin provides a shortcode to include a HTML5 login form on a page on your website and will natively link to this form throughout your site. It utilises email addresses rather than usernames to be more user friendly.

To include the login form on the front end of your site just paste the shortcode [lh_login_page_form] into a page on your site.

To change the login url to a pager where you have the front end form navigate to Wp Admin->Settings->LH Login Page and paste the page id into the field.

Check out [our documentation][docs] for more information. 

All tickets for the project are being tracked on [GitHub][].


[docs]: http://lhero.org/plugins/lh-login-page/
[GitHub]: https://github.com/shawfactor/lh-login-page

Features:

* Front end login form inserted via shortcode
* Login using an email address rather than username
* Multiple instances possible: Place login form shortcode multiple pages or in sidebars and widgets
* If configured will override the WordPress login url so that login links point to to your front end login page


== Installation ==

1. Upload the entire `lh-login-form` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Optionally navigate to Settings->LH Login Page if you add a page id with 


== Changelog ==

**1.0 July 13, 2015**  
Initial release.

== Changelog ==

**1.1 July 17, 2015**  
Settings link.
