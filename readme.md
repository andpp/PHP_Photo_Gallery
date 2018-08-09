# Simple PHP Photo Gallery

Simple  PHP Photo Gallery - PHP, jQuery, Ajax Photo Gallery No Database 
  
Based on [FolioGallery](http://www.foliopages.com/php-jquery-ajax-photo-gallery-no-database). It is a simple and lightweight gallery that does not require a database to run. 

* Free for personal and commercial use.
* Display multiple albums and/or full gallery in one page.
* Responsive interface.
* Automatic thumbnail creation.
* Customizable appearance through CSS.

Difference from original version:

* Added support for subfolders
* On-fly resize images. You can use your original  photo archives without pre-resizing it for web view.
* Speed-up page loading for big image collections
* Moved thumbs to a separate folder. The gallery does not require write permissions to albums folders.
* Used [php-image-magician](https://github.com/Oberto/php-image-magician) library for thumbs generation and image on-fly resizing

## Requirements

* A server running PHP 5+.
* PHP GD library (for automatic thumbnail creation and on-fly image resizing).

## Configuration

In folio-gallery.php:

Update ==$mainFolder== with the folder for your photo albums.
Update ==$thumbFolde==r with the folder for thumbs. The folder should have write permissions for your webserver user (usually **www-data**).


	$mainFolder    = 'albums';   // folder where your albums are located - relative to root
	$thumbsFolder  = 'thumbs';   // folder where your thumbs are located - relative to root

