=== Michaels Space PDF Filler ===
Contributors: michaelsspace
Plugin Name: Michaels Space PDF Filler
Tags: pdf filler, pdf, form, form filler, pdf form filler
Requires at least: 5.0
Tested up to: 5.8.0
Requires PHP: 7.2.0
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create Forms that send filled pdfs!

== Description ==

Create Forms that send filled pdfs! Allows you to create forms without knowing a line of code! These forms create a pdf that gets filled in with the form inputs dynamically! The pdf will get sent to the email you set for each form and will download to the users device.

You just need to add a shortcode to the page you want to show the form on and the system does the rest!

The forms are send using the default WordPress wp_mail() function.

== Screenshots ==
1. PDF Forms Page List (Version 1.0.0)
2. PDF Form Edit Page (Version 1.0.0)
3. PDF Form Front End (Version 1.0.0)
4. PDF Form Recaptcha Settings (Version 1.1.1)


== Installation ==
1. Upload the entire plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the \'Plugins\' menu in WordPress

== Frequently Asked Questions ==

= The pdf's are not being sent to my selected email =

Some servers will need to setup the stmp settings for emails to be sent. The plugin uses the basic wordpress wp_mail function for sending.

= What shortcodes are there? =

As of version 1.0.0, there are two. [mmpdff id="{id of form}"] is used for showing a basic form on the site. This will convert any inputs/values into a pdf that gets saved on the server in the plugin /pdf folder and also emails it out.

The other one is [mmpdff_embed id="{id of form}" "height"="{height of embed}" width="width of embed" text="{download label}" link_text="{clickable download text}"]. This shows an embed pdf on the page that users can download/input values. 

Since pdf's don't embed nicely on all broswers, there will be link text that shows up instead if the users broswer can't embed the pdf.

= I already have a filled pdf. Can I use that one instead of the generated one? =

Of course! As of version 1.0.0, you will need to match the inputs of the plugin settings with the inputs that you created in the pdf then upload the file into the plugins \pdf\post-pdfs folder.

The file needs to be named the form id-custom.pdf. If you at anypoint hit the update button on the page, the system will override your uploaded pdf so be careful.

Always do your work in the plugin first and once that is set, upload the file

= The plugin isn't generating the pdfs! =

Make sure your folder permissions are set to 755. You may need to ask your host company to do so.

= The Front End Inputs On The Form Are All On One Line! =

As of version 1.1, This plugin just handles showing the form but it does not style it. You will need to update your site's CSS Theme.


== Upgrade Notice ==
= 1.2.0 =
* Reworked how the backend editor functions to be a drag and drop system 

= 1.1.3 =
* Changed required name for custom fillable pdfs. You will now need to upload {form id}-custom.pdf. Keep in mind, your server may not allow this feature to be used.

= 1.1.2 =
* Fixed Issue With The Custom PDF Not Being Used

= 1.1.1 =
* Fixed Fatal Error
* Changed Front End Form Inputs To Be Wrapped In The Label
* Updated Tested Up To Version

= 1.1 =
* Added Support For Google reCAPTCHA And hCaptcha
* Updated Tested Up To Version

= 1.0 =
* Initial Release

== Changelog ==
= 1.2.0 =
* Reworked how the backend editor functions to be a drag and drop system 

= 1.1.3 =
* Changed required name for custom fillable pdfs. You will now need to upload {form id}-custom.pdf. Keep in mind, your server may not allow this feature to be used.

= 1.1.2 =
* Fixed Issue With The Custom PDF Not Being Used

= 1.1.1 =
* Fixed Fatal Error
* Changed Front End Form Inputs To Be Wrapped In The Label
* Updated Tested Up To Version

= 1.1 =
* Added Support For Google reCAPTCHA And hCaptcha
* Updated Tested Up To Version

= 1.0 =
* Initial Release