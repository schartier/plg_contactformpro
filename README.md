plg_contactformpro
==================

Contact form plugin for Joomla!

{jatabs type="content" position="top" animType="animMoveHor"} [tab title="Highlights"]
ContactFormPro is a Joomla! Plugin to add Contact Forms in your Joomla! articles, K2 items, Custom HTML modules or any other content parsed by the Content Plugins (pretty much everywhere you use a HTML editor to enter data)

NEW EDITOR BUTTON !!!

Inserting contact forms in your Joomla! content has never been so easy! Insert ContactFormPro in a few clicks with our latest addition, the EditorButton (Free with ContactFormPro)!

ContactFormPro also provides Captcha support!

Forms can be displayed directly in content or as a link that opens the form in a popup.

CAPTCHA PLUGIN

A captcha plugin is included in the download. Although it is not indispensable for ContactFormPro to function, this addition can protect your system from being exploited by automated processes.

MathGuard

MathGuard Captcha System for Joomla! Contact Form Pro extension



Screenshots

 Umega-green theme for ContactFormPro Umega-orange theme for ContactFormPro  Theme Alfa Theme Icones
 Theme LegerteTheme Light Theme Smooth Theme Minimal
[/tab] [tab title="Features list"]
Features

 Mailto field now accepts a list of recipients separated by ;NEW
 Use MathGuard Captcha System (included in the extension) or the system's default captcha system.NEW
 Name + email inserted automatically for logged in users.NEW
 Visitors can receive a copy of the message they sendNEW
 You can enter one or more "admin" email addresses to send a copy of every message sent with ContactForm over the siteNEW
 Forms can be displayed directly in content AND/OR as a link that opens a "lightbox" like popup (based on Mediabox)
 Messages are sent using AJAX
 Nice feedback is displayed to the visitors while the email is being sent
 Many options are available
 Default options can be set in the plugins manager
 Options can be overridden in the plugin tags (using the Editor Button or Manually)
 You can insert multiple instances with different displays on the same page without problem
 Easily customizable with css
[/tab] [tab title="Instructions"]
Installation

Register or login to download the extension
Install using Joomla! installer
Configure plugin in the plugins manager
Use the Editor Button to insert ContactFormPro in your Joomla! content.
Plugin configuration

You can set some options in the plugins manager. Most of these options can be overridden in the editor button when you create the forms.

Basic options

ContactFormPro plugin basic options

Display: Choose 'popup' to display the form inside a 'lightbox' like popup (based on mediabox)

Style: Choose a default style for the form.

Copyright removal: This is your key, it is inserted automatically during installation.



ContactFormPro plugin advanced options

Form title: Default title for the contact forms

Success message: Message displayed after a message has been sent successfully

Error Message: Default message displayed when the message could not be sent. More details may also be added by the plugin about the problem.

Width: The width of the form, if empty the dimensions will be calculated automatically

Link text: The text for the link that will open the Contact Form

Validate session: When session validation is enabled, users trying to send the form after their session is over will get an error message. This option is configurable in the main site options, by default it's 15 minutes.

Captcha: You can use MathGuard captcha system (included in the plugin) or your website default Captcha system. This option is also configurable in the main site options.

Admin address: The addresses inserted in this field will receive all emails that are sent using the ContactFormPro plugin. Emails must be separated by semicolon (;).

Validate on blur: Required fields are validated as soon as visitor leaves the field.

The editor button



Using the Editor Button is easy as 1, 2, 3!

Click the "ContactFormPro" button at the bottom of your favorite editor.
Enter the destination email and, optionally, modify the default options.
Click "Insert" to insert the form tag
Options available in the editor plugin

Almost all plugin options are also available in the editor button but in addition you get the following fields:

Subject: Initial value of the subject field in the contact form

Message: Initial value of the message field in the contact form

[/tab] [tab title="FAQ"]
QI try using Recaptcha captcha system but I get a message that I need to register for a key but I already have a key?

This happens only when you have more than one form on the same page. This is not an error with my extension neither the Recaptcha plugin, it is the Recaptcha system that does not allow this.

I am half way resolving this issue but I am not shure that it is even possible to do this because of all the security implemented in this javascript library.

"multiple recaptcha on same page" on Google

QWhere are my messages? I get a message that the message was sent successfully but cannot find the message in my mailbox.

First Verify your spam folder... If you are using the php mail function to send your emails, your message are most likely to be marked as spam. That is because one can pretend to be anybody by entering any email. Changing your mailer in site/global configuration/server to SMTP should prevent this (valid username/password usually required).

If you are using XAMPP, WAMPP, etc. mail functions are not available. Unfortunately, the JMail class does not return any error message.

You can test your mail configuration is valid by creating a contact page (com_contact).

QNothing is displayed where the contact form should be with the popup display.

Make sure there is a value for label at least one place: the plugin configuration or the tag.

QThe form will not open

There is a javascript problem on your website and it will probably cause you other issues on your website.

If you are using jQuery, the script must be loaded as described here.

QInstead of seeing the form I see the tag

Inside an article

If you edit the tag manually, you must use # instead of @ in you email address (only in the tag). Also, make sure there is no formatting directly inside the tag. To do so, look into html code and remove any html tag that is not encoded.

example (html view):





{contactformpro mailto="sebastien.chartier#gmail.com" error_message="<p>There was an error</p>" display="popup" style="light" width="400" /}
In the previous example, you should remove the <pre> tag.

Inside a module

To display the form inside a module, the option "prepare content" must be set to yes.

Custom Html module configuration

Inside other components

Many components have an option to "enable/disable" content plugins (SobiPro for example). Make sure that content plugins are enabled in your component ("prepare content" can be interpreted as "enable content plugins")

QThe response message opens in the same page instead of the popup.

Another problem caused when there is a javascript conflict. Most of the time cause when using jQuery unproperly (read this).
Q"Message is currently being sent" hangs

Another problem caused when there is a javascript conflict. Most of the time cause when using jQuery unproperly (read this).

View forum

[/tab] {/jatabs}
