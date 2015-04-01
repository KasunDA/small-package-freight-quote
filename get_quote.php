<?php

	//GET QUOTE FROM EASYPOST

	//REQUIRE
	//"/var/www/" is non-public directory for storing API keys
	//"/var/www/apps/freightcalc/common/" is directory for storing common files and libraries
	set_include_path("/var/www/:/var/www/apps/freightcalc/common/");
	require_once "easypost_api_keys.php";
	require_once "access_control.php";
	require_once "easypost-php-master/lib/easypost.php";
	
	//SET EASYPOST API KEY
	//live key is needed to get accurate rates to what you are billed from UPS/FedEx (i.e.: takes into account negotiated rates and any discounts)
	//you are not billed by Easypost for just using the live key to get quotes
	\EasyPost\EasyPost::setApiKey($EASYPOST_LIVE_KEY);

	//TRIM WHITEPACE FROM VARS
	foreach ($_GET as $key => $value) {
		if (!is_array($value)) {
			$value = trim($value);
		}
	}

	//VARS FROM FORM
	$fromCity = 		$_GET['fromCity'];
	$fromState = 		$_GET['fromState'];
	$fromZip = 			$_GET['fromZip'];
	$fromCountry = 		$_GET['fromCountry'];

	$toCity = 			$_GET['toCity'];
	$toState = 			$_GET['toState'];
	$toZip = 			$_GET['toZip'];
	$toCountry = 		$_GET['toCountry'];

	$lengths = 			$_GET['lengths'];		//array
	$widths = 			$_GET['widths'];		//array
	$heights = 			$_GET['heights'];		//array
	$weights = 			$_GET['weights'];		//array
	$packageCount = 	$_GET['packageCount'];


	//FROM ADDRESS
	//name, street, phone aren't needed to get quotes
	//state and country code MUST be upper case
	//assume residential is false
	$fromAddress = \EasyPost\Address::create(array(
		"company" => 		"Example Corp",
		"street1" =>		"1 Example Street",
		"street2" => 		"",
		"city" =>			$fromCity,
		"state" => 			strtoupper($fromState),
		"zip" =>			$fromZip,
		"country" =>		strtoupper($fromCountry),
		"phone" => 			"555-555-5555",
		"residential" => 	false
	));

	//TO ADDRESS
	//name, street, phone aren't needed to get quotes
	//state and country code MUST be upper case
	//assume residential is false
	$toAddress = \EasyPost\Address::create(array(
		"company" => 		"Example Corp",
		"street1" =>		"1 Example Street",
		"street2" => 		"",
		"city" =>			$toCity,
		"state" => 			strtoupper($toState),
		"zip" =>			$toZip,
		"country" =>		strtoupper($toCountry),
		"phone" => 			"555-555-5555",
		"residential" => 	false
	));

	//SHIPMENT
	//handle multiple packages in one shipment
	$shipments = array();
	for ($i = 0; $i < $packageCount; $i++) {
		$item = array(
			"parcel" => array(
				"length" => 	$lengths[$i],
				"width" => 		$widths[$i],
				"height" => 	$heights[$i],
				"weight" => 	$weights[$i] * 16	//ounces
			)
		);

		$shipments[] = $item;
	}

	//GET SHIPMENT QUOTE
	$order = \EasyPost\Order::create(array(
		"from_address" =>	$fromAddress,
		"to_address" =>		$toAddress,
		"shipments" => 		$shipments
	));

	//SEND BACK DATA TO JS
	//this files was called via ajax
	//ajax will receive result, parse it, and show the list of quotes in a table
	echo $order;
?>