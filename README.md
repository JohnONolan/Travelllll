# Travelllll

This is the WordPress theme that powered Travelllll.com for just under 2 years. Following the close of the site, I've decided to Open Source the code and make it available to anyone who wants to use it.

## Usage

This is a very complicated theme. It **will not "just work" out of the box**. It requires certain categories, certain tags, and those certain categories and certain tags to have certain ID's. This theme makes a veritable shit-ton of queries, so your server will pretty much die if you don't use a caching plugin.

In short, you can use this theme as a starting point for developing your own theme - but if you simply install it on any WordPress site - it won't really work. Below I've tried to provide some  rough guidance on what's in the theme and what it does.

### Basic Structure

**index.php** - This is the home page. It calls a partial for trending stories which only appears on page 1 of the site.

**everything prefixed with "loop"** - These are the different styles of output for different content types. Eg. a normal post has a title/description/link - a video post has just a title and embed.

**everything prefixed with "template"** - These custom page templates used for 1-off page designs.

### Advanced Features

**functions.php** - Most of the magic is in here, and it imports the two next things:

**library/functions** - This is an old copy of the WooFramework, quite heavily modified, to power custom fields, shortcodes, and various handy things for the admin ui.

**departures** - This is a custom post type which we built to show off blogtrip departures. It never worked exactly right, and caused some bugs through the rest of the theme. We outsourced most of this code and then I rewrote about half of it. I'd strip this out - it's probably the weakest part of the theme.

### The Rest

You'll have to figure out for yourself. 

PRs are welcome to make this a more stable and publicly usable theme.

## License

The Travelllll theme is Copyright ©2013 John O'Nolan, and is released under the GNU GPLv2 License, contained within this repository.
