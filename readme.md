# Rotonde web client
Very basic, php-based web client to view an ordered timeline based on a base Rotonde feed. It's awfully slow and needs some caching to be more usable.

There is a running instance at http://rotonde.electricgecko.de/feed/

## Setup
- Copy all files to your server. 
- Point a **rotonde.** subdomain to the folder you copied the script to.
- Create a *feed.json* in the main folder and edit it according to the [Rotonde Spec](https://github.com/Rotonde/Specs).

## Reading
Visit **/feed** to display a timeline of all feeds listed in the *Portal* section of your base Rotonde feed.

## Posting
Visit **/post** to add a new post to your base Rotonde feed.
