[![Code Climate](https://codeclimate.com/github/foae/users-searched-for/badges/gpa.svg)](https://codeclimate.com/github/foae/users-searched-for) [![Build Status](https://travis-ci.org/foae/users-searched-for.svg?branch=master)](https://travis-ci.org/foae/users-searched-for)

Users Searched For is a tool that records all searches made on your WordPress site. Detects if results were found.

**Description**

Users Searched For (USF) is a lightweight plugin that hides under the **Admin backed -> Tools -> Users Searched For** menu. It records all the terms that your users have searched for on your WordPress site and displayes them in a dedicated section, where you can apply different filters.

This plugin is very useful if want to have a quick insight of what people are searching for on your site. 

**Filters available (sorting options):**

* pagination
* username or Visitor (detects if the user is logged in and displays its name)
* landing page (if the search returns results, these will be recorded by title and direct link)
* IP address - stores the user's IP address for later statistics (country, city, ISP)
* Time and date - eg 2015-08-29 17:31:09 - have a clear timestamp when a user made the search

TODO - no plugin is ever in a final form. However, the following will be implemented in the future versions:

* Display countries, cities and ISPs instead of IPs
* Make automatic suggestions based on search records for building new pages or posts
* Compatibility with top 20 search-enhancing plugins
* Daily/Weekly/Monthly reports, downloadable or sent by e-mail
* Export as CSV, XLS, PDF with customizable time segments
* Trends

Please feel free to send improvement requests.

**Installation**

1. Upload the folder `users-searched-for` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. After a few searches, consult the results in your admin backend -> `Tools` -> `Users Searched For`
