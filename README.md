# LeetSubdomain

LeetSubdomain is a plugin for Wordpress that allows you to setup your pages work as subdomains.

# == Description ==

LeetSubdomain is a plugin for Wordpress that allows you to setup your pages work as subdomains.

# == Installation ==

This section describes how to install the plugin.

1. Upload and Unarchive
2. Copy the 'Leet-Subdomain' folder to the '/wp-content/plugins/' directory
3. Activate the plugin in Wordpress

# = Upgrading = 

If Upgrading manually follow the above instrutions, but first deactivate the existing plugin.
This is so when you activate the new plugin it'll run any required updates.

# = Configuration =

See the other notes.


# == Subdomain Setup ==

The plugin uses the  Page slug  as the subdomain name.

You'll need to configure your webserver for each subdomain you want to use. It uses the same wordpress install as your main blog.
If you run your own server you should know how todo this already (Apache users can just add ServerAlias to their existing vhost)
If you use managed hosting then add a subdomain and set it's document path to that of your main blog.

Be sure to add a DNS entry to point your subdomains to your server.

Note, some hosting services allow a forward all rule that will forward all subdomains to your server.



# == Plugin Configuration ==

# = Main Domain =
To use the subdomain blog feature (e.g. main page at http://blog.mydomain.com) you’ll need to create blog page.

# = Disable Plugin =
Allows you to disable the plugin functionality whilst still being able to configure it


# == Notes ==

* If using Subdomains you'll probably want your cookie to span the subdomains and not just your own domain. In order to achieve this you need to add an option to your wp-config.php:

  define('COOKIE_DOMAIN', '.mydomain.com');

  Where mydomain.com is your domain name. Remember to add the preceeding dot (.) as this is what makes it work.

