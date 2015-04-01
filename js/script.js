$(document).ready(function() {

	//CONSTANTS
	var PERCENT_ADDITIONAL_CHARGE = 1.15;	//percentage difference between what we are charged versus what we charge customers (extra margin for packaging & handling costs)
	var MINIMUM_CHARGE = 			10;		//lowest price you will charge for a shipment

	//DEFAULT PACKAGES
	//name (string), length (inches), width (inches), height (inches), weight (pounds)
	//ids (comment column) are just incremented from "0" and are used for checking if a package needs additional charges: for example, if a package is oversized/overweight or not packaged in cardboard
	//this list is automatically populated into the default packages "select" dropdown menu on page load (see buildDefaultPackageOptions function below)
	var DEFAULT_PACKAGES = [
		//name 						len,  wid,  hgt,  lbs
		["Package 1", 				"12", "12", "12", "47"],	//0
		["Package w/0 box", 		"36", "36", "12", "60"],	//1
		["Package 2", 				"20", "20", "9",  "40"],	//2
		["Box", 					"16", "16", "20", "7"]
	];

	//PUT LIST OF DEFAULT PACKAGES INTO DOM
	//done this wasy so you don't have to put a new item in js (above) and in html and have to worry about lists being the exact same
	//puts the list of packages above into the "select" dropdown menu
	function buildDefaultPackageOptions() {
		var templateSelect = $('#package-row-template').find(".package-default-options");

		for (var i = 0; i < DEFAULT_PACKAGES.length; i++) {
			templateSelect.append("<option value='" + i + "'>" + DEFAULT_PACKAGES[i][0] + "</option>");
		}
	}
	//call the funtion
	buildDefaultPackageOptions();

	//TOGGLE DEFAULT FROM ADDRESS
	//NOT FROM DEFAULT
	$('body').on('click', '#from-default-no', function() {
		//enable all inputs
		$('#from-fieldset-inputs').attr("disabled", false);

		//clear default input values
		$('#from-city').val("");
		$('#from-state').val("");
		$('#from-zip').val("");
		$('#from-country').val("US");
	});

	//SET DEFAULT FROM ADDRESS
	$('body').on('click', '#from-default-yes', function() {
		//disable all inputs
		$('#from-fieldset-inputs').attr("disabled", true);

		//set default values
		var cityElem = 			$('#from-city');
		var stateElem = 		$('#from-state');
		var zipElem = 			$('#from-zip');
		var countryElem = 		$('#from-country');

		var cityDefault = 		cityElem.data("default");
		var stateDefault = 		stateElem.data("default");
		var zipDefault = 		zipElem.data("default");
		var countryDefault = 	countryElem.data("default");

		cityElem.val(cityDefault);
		stateElem.val(stateDefault);
		zipElem.val(zipDefault);
		countryElem.val(countryDefault);

	});

	//DEFAULT PACKAGE CHOICES
	//fill out inputs for length, width, height, weight based on package chosen from default select dropdown
	//takes the "value" (which is the array id) from the selected option and gets the array of values (l, w, d, lbs) associated with this value from the default packages
	$('body').on('change', '.package-default-options', function() {
		//elements for which we will set values
		var packageDetails = 	$(this).parents('.form-inline');
		var length = 			packageDetails.find('.package-length');
		var width = 			packageDetails.find('.package-width');
		var height = 			packageDetails.find('.package-height');
		var weight = 			packageDetails.find('.package-weight');
		var id = 				packageDetails.find('.package-id');

		//get selected option value
		var optionVal = 		$(this).find(":selected").val();

		//catch if user chose the custom select option
		//so a user can input their own values for the dimensions of the package
		if (parseInt(optionVal) === -1) {
			length.val("");
			width.val("");
			height.val("");
			weight.val("");
			id.val("-1");
			return;
		}

		//show correct data based on chosen option
		var packageOption = 	DEFAULT_PACKAGES[optionVal];
		length.val(packageOption[1]);
		width.val(packageOption[2]);
		height.val(packageOption[3]);
		weight.val(packageOption[4]);
		id.val(optionVal);
	});


	//****************************************************************************************************
	//VALIDATION
	//really, really basic validation of inputs

	//STREET, CITY, STATE, COUNTRY
	function validateTextInput (text) {
		text = text.trim();

		if (text.length < 1) {
			return false;
		}
		else {
			return true;
		}
	}

	//ZIPCODES
	//5 or 6 chars, text or int since Canada uses letters
	function validateZip (zip) {
		zip = zip.trim();

		if (zip.length < 1) {
			console.log("len")
			return false;
		}
		else if (zip.length == 5 || zip.length == 6) {
			return true;
		}
		else {
			return false;
		}
	}

	//DIMENSIONS
	//dimensions are in an array b/c of multiple packages
	function validateDimension (input) {
		for (var i = 0; i < input.length; i ++) {
			var testValue = input[i].trim();

			if (testValue === "") {
				return false;
			}
			else if (testValue.length < 1) {
				return false;
			}
			else if (parseInt(testValue) < 1 || parseInt(testValue) > 100) {
				return false;
			}
			//no else, so for loop doesn't break out on first "true" value
		}

		//all dimensions for all packages ok
		return true;
	}

	//****************************************************************************************************
	//GET QUOTE
	$('body').on('click', '#get-quote-btn', function() {
		//get all inputs (trim whitespace)
		//from
		var fromCity = 		$('#from-city').val().trim();
		var fromState = 	$('#from-state').val().trim();
		var fromZip = 		$('#from-zip').val().trim();
		var fromCountry = 	$('#from-country').val().trim();

		//to
		var toCity = 		$('#to-city').val().trim();
		var toState = 		$('#to-state').val().trim();
		var toZip = 		$('#to-zip').val().trim();
		var toCountry = 	$('#to-country').val().trim();

		//dimensions
		var lengths = 		$('#list-of-packages > .package-row').find('.package-length');
		var widths = 		$('#list-of-packages > .package-row').find('.package-width');
		var heights = 		$('#list-of-packages > .package-row').find('.package-height');
		var weights = 		$('#list-of-packages > .package-row').find('.package-weight');
		var packageIDs = 	$('#list-of-packages > .package-row').find('.package-id');

		//put dimensions into arrays
		var lengthsArray = [];
		lengths.each(function (i) {
			lengthsArray.push($(this).val().trim());
		});
		var widthsArray = [];
		widths.each(function (i) {
			widthsArray.push($(this).val().trim());
		});
		var heightsArray = [];
		heights.each(function (i) {
			heightsArray.push($(this).val().trim());
		});
		var weightsArray = [];
		weights.each(function (i) {
			weightsArray.push($(this).val().trim());
		});
		var packageIdsArray = [];
		packageIDs.each(function (i) {
			packageIdsArray.push($(this).val().trim());
		});

		//show errors
		//just a wrapper to clean up the code below
		function showError (from, field) {
			$('#quote-error').css({"display": "block"});
			$('#quote-error-text').text("The " + from + " " + field + " is not valid.");
		}
		
		//validattion
		//"else ifs" stop when one of them encounters an error
		//so we step through each value and find errors one by one

		//from
		if (!validateTextInput(fromCity)) {
			showError("From", "City");
		}
		else if (!validateTextInput(fromState)) {
			showError("From", "State");
		}
		else if (!validateZip(fromZip)) {
			showError("From", "Zip");
		}
		else if (!validateTextInput(fromCountry)) {
			showError("From", "Country");
		}

		//to
		else if (!validateTextInput(toCity)) {
			showError("To", "City");
		}
		else if (!validateTextInput(toState)) {
			showError("To", "State");
		}
		else if (!validateZip(toZip)) {
			showError("To", "Zip");
		}
		else if (!validateTextInput(toCountry)) {
			showError("To", "Country");
		}

		//dimensions
		else if (!validateDimension(lengthsArray)) {
			showError("Package", "Length");
		}
		else if (!validateDimension(widthsArray)) {
			showError("Package", "Width");
		}
		else if (!validateDimension(heightsArray)) {
			showError("Package", "Height");
		}
		else if (!validateDimension(weightsArray)) {
			showError("Package", "Weight");
		}

		//validation success
		else {
			//hide error message if it is showing
			$('#quote-error').css({"display": "none"});

			//package count
			var packageCount = $('#list-of-packages > .package-row').length;
			
			//fire off ajax request to get data from easypost api
			$.ajax({
				type: 	"GET",
				url: 	"get_quote.php",
				data: {
					fromCity: 			fromCity,
					fromState: 			fromState,
					fromZip: 			fromZip,
					fromCountry: 		fromCountry,
					
					toCity: 			toCity,
					toState: 			toState,
					toZip: 				toZip,
					toCountry: 			toCountry,

					lengths: 			lengthsArray,
					widths: 			widthsArray,
					heights: 			heightsArray,
					weights: 			weightsArray,
					packageCount: 		packageCount
				},
				beforeSend: function() {
					//disable "get quote" button so user cannot click it multiple times
					$('#get-quote-btn').attr("disabled", true).text("Getting Quote...");
					
					//clear the table's existing results
					$('#quotes-results').html("")
				},
				success: function (res) {
					//freight quotes returned
					//parse data
					//count the number of quotes returned to build table rows
					var data = 				JSON.parse(res)['rates'];
					var numResults = 		data.length;
					
					//placeholers
					var row = 				"";
					var rawRates = 			[];
					var rawCarriers = 		[];
					var rawServices = 		[];
					var rawTransitDays = 	[];
					var ratesOutput = 		[];
					var carriersOutput = 	[];
					var servicesOutput = 	[];

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

					//ORGANIZE RATES FROM CHEAPEST TO MOST EXPENSIVE
					//put rates, carriers, services, into their own arrays
					for (var i = 0; i < numResults; i++) {
						var rateData = data[i];
						rawRates.push(parseFloat(rateData['rate'].replace(/,/g,'')));	//need to use regex to remove commas so we get full number, parseFloat acts weird otherwise
						rawCarriers.push(rateData['carrier']);
						rawServices.push(rateData['service']);
					}

					//ITERATE THROUGH ARRAY OF RATES TO SORT THEM
					//find the min value in the array, then find the index of the min value
					//put the lowest value into a new ouput array
					//splice (remove) the lowest value from the raw array
					//this organizes the outputted rates by price
					for (var i = 0; i < numResults; i++) {
						var minValue = 	Math.min.apply(Math, rawRates);
						var index = 	rawRates.indexOf(minValue);

						ratesOutput.push(rawRates[index]);
						carriersOutput.push(rawCarriers[index]);
						servicesOutput.push(rawServices[index]);

						rawRates.splice(index, 1);
						rawCarriers.splice(index, 1);
						rawServices.splice(index, 1);
					}

					//BUILD ROWS OF RATES
					for (var i = 0; i < numResults; i++) {
						var rate = 			ratesOutput[i];
						var carrier = 		carriersOutput[i];
						var service = 		servicesOutput[i];
						var totalRate = 	"";

						//add additional margin to rates if the user is not id admin mode
						if ($('#admin').val() !== "true") {
							//add extra amount to cost just in case
							rate *= 	PERCENT_ADDITIONAL_CHARGE;

							//make sure rate is a minimum of MINIMUM_CHARGE
							rate = 		(rate < MINIMUM_CHARGE) ? MINIMUM_CHARGE : rate;

							//add in additional handling charge (if any)
							rate += 	additionalCharge;

							//round up to next dollar and display as 2 decimal places
							rate = 		parseInt(rate) + 1;
							rate = 		rate.toFixed(2);
						}

						//build table rows
						row += "<tr>" + 
							"<td>" 		+ carrier		+ "</td>" +
							"<td>" 		+ service		+ "</td>" +
							"<td>$ " 	+ rate 			+ "</td>" +
						"</tr>";
					}

					//DISPLAY NEW ROWS OF RATES
					var table = $('#quotes-results');
					table.append(row);

					//RE-ENABLE THE QUOTE BTN
					$('#get-quote-btn').attr("disabled", false).text("Get Quote");
				}
			});
		}
	});

	//****************************************************************************************************
	//PACKAGE DETAILS

	//SHOW ERRORS
	//autohide the error message afer a few seconds
	function showPackageError (text) {
		$('#quote-error').css({"display": "block"});
		$('#quote-error-text').text(text);

		setTimeout(function() {
			$('#quote-error').fadeOut();
		}, 2000);
	}

	//SHOW FIRST ROW
	//functionized b/c this is used again to add more packages
	//template for package rows is hidden, so this shows the first row instead of just having it in the html as shown
	//hidden and template for purposes of styling since the template is outside of the #list-of-packages div.  also will not be taken into account in quoting
	function addNewPackage() {
		$('#package-row-template').clone().appendTo('#list-of-packages').removeAttr("id").css({"display":"block"});
	}
	//done on page load to show the first package row
	addNewPackage();
	
	//ADD ADDITIONAL PACKAGE
	//clone the template row
	$('body').on('click', '.package-add', function() {
		addNewPackage();
	});

	//REMOVE PACKAGE
	//remove the row that was clicked
	//user cannot remove last row (row count === 2 b/c of existing row and template row)
	$('body').on('click', '.package-remove', function() {
		if ($('.package-row').length !== 2) {
			$(this).parents('.package-row').remove();
		}
		else {
			showPackageError("You cannot remove the last row.");
		}
	});

	//CLONE AN EXISTING ROW
	$('body').on('click', '.package-clone', function() {
		//get parent row of clone button
		var row = 		$(this).parents('.package-row');

		//get select value
		var selectVal = row.find(".package-default-options").val();

		//clone row right after clicked row
		row.clone().insertAfter(row);

		//set select value in cloned row
		row.next().find(".package-default-options option[value=" + selectVal + "]").attr("selected", true);
	});

	//CHECK ADMIN MODE
	//add styling to page to notify user
	//stops the quote calculation from adding in any extra margin aka rows of quotes are exactly what freight carrier will charge
	if ($('#admin').val() === "true") {
		$('header > h1').text("Small Package Freight Quote | ADMIN");
		$('body').css({"background-color": "#02FFC2"});
	}
});