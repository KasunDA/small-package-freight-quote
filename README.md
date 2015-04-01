#Small Package Freight Calculator

This is a simple package that wraps around the [Easypost](www.easypost.com) API.  It gets freight quotes for any carrier you have enabled on Easypost and displays it in a table.

The intended use of this package is to get quotes to give to customers before proceeding with submitting the shipment & scheduling a pickup via Easypost.

In other words: it shows you how much to bill a customer for shipping packages.

##Benefits Over Carrier's Websites:
* Much more user friendly.
* No need to type in account numbers or login information every time.
* Quotes from multiple carriers at one time.  Easier to compare costs.
* Default package sizes and weights.

#Requirements
* PHP.
* PHP-curl.
* An Easypost account.
* Any freight carrier account that Easypost supports (i.e.: FedEx, UPS).

#Getting Started
1. Create a new web directory.
	* `mkdir -p /var/www/apps/freightcalc`
2. Clone this repo into the just created directory.
	* If you use a different directory structure, make sure to change the "require_once" paths in index.php & get_quote.php.
3. Create and enable an Apache or Nginx site configuration.
4. Create a file for storing your Easypost API keys.
	* Directory: `/var/www/`
		* **Make sure this directory is not public!**
		* Otherwise, choose a different directory and change the "require_once" path in get_quote.php as appropriate.
	* Filename: `easypost_api_keys.php`
	* Contents:

	```
	<?php
		$EASYPOST_TEST_KEY = "your_easypost_test_key";
		$EASYPOST_LIVE_KEY = "your_easypost_live_key";
	?>
	```
5. Set defaults.
	* index.php
		* `$BRANDNAME`: Your company's name.
		* `$DEFAULT_FROM_ADDRESS`: Your shipping address.
	* js/script.js
		* `PERCENT_ADDITIONAL_CHARGE`: The percent margin on the freight charge you want to add to cover packaging & handling expenses.
		* `MINIMUM_CHARGE`: The lowest freight rate you want to charge a customer.
		* `DEFAULT_PACKAGES`: A list of the common packages you ship.  This is used to populate an HTML select with options of packages so users make less mistakes with dimensions.
6. Get correct transit maps.
	* Go to the correct website for each carrier and get the transit map from your location.
	* Save this file to the img/ directory.
	* Update index.php to reflect the names of the images for each carrier.
	* *Based on what carriers you use, you may need to adjust the values on the buttons that show the transit map modals.*
	* *If you are using additional carriers, simply copy & paste an existing modal's HTML and alter the modal's `id` and image's `src`.  You will also need to add another button to show this modal.*

#How it Works
* Users input a "From" (if not using the default) and "To" address.
* Next, choose a default package or type in a length, width, height, and weight.
	* *If you are shipping more than one package, choose the "Add" or "Cycle" (clone) button to add another row.*
	* *Choose the trash can to remove an accidentaly added row.*
* Click "Get Quote" to initate a call that gets the quotes.
* This process constucts objects from the "To", "From" and "Package" data and sends it off to Easypost, who in turn sends to to UPS, FedEx, etc.
* Once the quote is returned, it is ordered from lowest to highest price and displayed in the table.
* The rate returned *does not* take into account pickup fees or any other strange charges.


#Additional Info
* When viewing the page normally (ending in /freightcalc/), the rates shown in the Quotes table are adjusted to add the `PERCENT_ADDITIONAL_CHARGE` AND `MINIMUM_CHARGE`.
	* To view the exact rate you are charged by the carrier(s), add "?admin=poopstick" to the end of the URL.
	* The page will change to notify you that you are in "admin" mode.
* If you know that certain packages will set off an additional charge (oversized, not in cardboard box, etc.), you **must** manually adjust the js/script.js file to add the additional charge in.  Do so by taking the array index from `DEFAULT_PACKAGES` of the specific package and change the `if` statment as follows :
	* 

	```
	//CHECK IF ANY PACKAGES REQUIRE EXTRA HANDLING CHARGES
	//ups/fedex don't add this charge since they don't know if a package isn't packed in cardboard
	//show warning to user as a row in the table
	//match package IDs from above default packages
	//example: not in cardboard box
	var additionalCharge = 	0;
	packageIdsArray.forEach(function (i) {
		if (i === "1") {
			additionalCharge += 9;
			row = "<tr class='warning'><td colspan='4'><center>Warning! An additional charge of $" + additionalCharge + " was added to this shipment for package(s) not encased in cardboard.</center></td></tr>";
		}
	});
	```

#Misc.
* Bugs? Issues? File and issue.
* This project is in no way affiliated with or to Easypost.

#Packages used in this app:
* [Easypost](www.easypost.com)
* [jQuery](jquery.org)
* [Bootstrap](getbootstrap.com)