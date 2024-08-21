=== Subscribenow ===
Contributors: Subscirbe and keep track of your visitors
Tags: database, wordpress, newsletter
Requires at least: 6.4.4
Tested up to: 6.6.1
Stable tag: 1.0.0
Author URI: https://agrafiotis.info
Donate link: https://www.paypal.com/donate/?hosted_button_id=LJKW8ZMVFSM9S&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWxvYmplY3RzLmNvbS9kb25hdGUvc2RrL2RvbmF0ZS1zZGsuanMiLCJhdHRycyI6eyJkYXRhLXVpZCI6IjI1YzU0MGNjZjNfbWRrNm5kZzZuZGcifX0&targetMeta=eyJ6b2lkVmVyc2lvbiI6IjlfMF81OCIsInRhcmdldCI6IkRPTkFURSIsInNka1ZlcnNpb24iOiIwLjguMCJ9
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Link to download: https://github.com/Athaagra/subscribenow_plugin/raw/main/Subscribenow.zip
Copyright (C) 2024  Athanasios Agrafiotis

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
== Description ==

 Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `readme.txt` file says that the stable tag is `1.0.0`, then it is `/tags/1.0.0/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.


All the web design style that is used for the is plugin is in the main folder in the document "subscribers-s.css".
Not additional libraries are used for javascript and css that are linked with other softwares.

For all the js libraries, the npm node js have been used:
To make nodejs is installed navigating to the folder and run init that create the package.json file.
The package.json modified with start in:
		"start": "wp-scripts start src/subscribenow.js"
and the modules that have been used for the implementation are:
		"@glidejs/glide": "^3.4.1",
		"@wordpress/scripts": "*",
		"axios": "^0.21.1",
		"normalize.css": "^8.0.1",
		"package.json": "^2.0.1"

A plugin that creates a subscription menu you can host it in all your pages and posts moreover store the subsrciber and support mail messages.

Example:
Subscribe Now
display with a shortcorde
cells:
smtp.gmail.com
usersubscribenow
example@mail.com
ksdifdsifdifjfdf
587
tls

[Save settings]

Save the initial message and use it for all users!


SubscribeNow:

* Keep track of subscribers
* Newsletter manually 
* Configuration of your own smtp server
* Is comprehensive and easy to use
