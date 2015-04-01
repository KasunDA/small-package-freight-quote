<?php

	//REQUIRE
	set_include_path("/var/www/apps/freightcalc/common/");
	require_once "access_control.php";

	//CHECK FOR ADMIN MODE
	//displays the real rates charged/billed instead of the adjusted rates with the extra margin and rounding
	//user would need to manually type in "?admin=poopstick" in the URL after the path to this file i.e www.example.com/freightcalc/?admin=poopstick
	if (isset($_GET['admin']) && $_GET['admin'] == "poopstick") {
		$admin = "true";
	}
	else {
		$admin = "";
	}

	//PAGE CONSTANTS
	$BRANDNAME = 				"Example Corp.";
	$DEFAULT_FROM_ADDRESS = 	array(
		"city" =>					"New York",
		"state" => 					"NY",
		"zip" => 					"10005",
		"country" => 				"US"
	);
?>

<html>
	<head>
		<title><?php echo $BRANDNAME; ?> - Small Package Freight Quote</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link href="//fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet" type="text/css">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
	</head>

	<body>
		<!-- HIDDEN INPUT IS USED TO RELAY INFO TO JAVASCRIPT ABOUT WHICH RATES SHOULD BE SHOWN -->
		<!-- admins can use this to verify quotes to what they are billed from UPS/FedEx/etc. -->
		<!-- this value is set to "true" if the user is in admin mode -->
		<input id="admin" type="hidden" value="<?php echo $admin; ?>">
		
		<div class="container">
			
			<!-- HEADER -->
			<header>
				<h1><?php echo $BRANDNAME; ?> - Small Package Freight Quote</h1>
				<hr>
			</header>

			<!-- MAIN BODY -->
			<div id="main">
				<div class="row">

					<!-- DATALIST HOLDING COUNTRY CODES -->
					<datalist id="country-codes">
						<option value="US">
						<option value="CA">
						<option value="MX">
					</datalist>

					<!-- FROM ADDRESS, DEFAULT IS SET IN CODE -->
					<div class="col-xs-12 col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">From Address</h3>
							</div>
							<div class="panel-body">
								<form class="form-horizontal">

									<!-- SETS THE DEFAULT "FROM" ADDRESS -->
									<!-- toggle is done with js.  default values are set in data-default attributes for each input so default values don't have to be hardcoded in js -->
									<div class="form-group">
										<label class="control-label col-xs-4">Default:</label>
										<div class="col-xs-8">
											<div class="btn-group" data-toggle="buttons">
												 <label class="btn btn-primary active" id="from-default-yes">
													<input type="radio" autocomplete="off" checked> Yes
												</label>
												<label class="btn btn-primary" id="from-default-no">
													<input type="radio" autocomplete="off"> No
												</label>
											</div>
										</div>
									</div>

									<!-- FIELDS ARE DISABLED UNTIL DEFAULT IS TOGGLED TO "NO" -->
									<!-- default values are filled in by php injection -->
									<!-- default values are also stored in data-default attribute for when a user toggles back and forth between default "yes" and default "no"-->
									<fieldset id="from-fieldset-inputs" disabled>
										<div class="form-group">
											<label class="control-label col-xs-4">City:</label>
											<div class="col-xs-8">
												<input class="form-control" id="from-city" type="text" required value="<?php echo $DEFAULT_FROM_ADDRESS['city']; ?>" data-default="<?php echo $DEFAULT_FROM_ADDRESS['city']; ?>" autocomplete="off">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-xs-4">State:</label>
											<div class="col-xs-8">
												<input class="form-control" id="from-state" type="text" required value="<?php echo $DEFAULT_FROM_ADDRESS['state']; ?>" data-default="<?php echo $DEFAULT_FROM_ADDRESS['state']; ?>" autocomplete="off">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-xs-4">Zip:</label>
											<div class="col-xs-8">
												<input class="form-control" id="from-zip" type="text" required value="<?php echo $DEFAULT_FROM_ADDRESS['zip']; ?>" data-default="<?php echo $DEFAULT_FROM_ADDRESS['zip']; ?>" autocomplete="off">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-xs-4">Country:</label>
											<div class="col-xs-8">
												<input class="form-control" id="from-country" type="list" list="country-codes" required value="<?php echo $DEFAULT_FROM_ADDRESS['country']; ?>" data-default="<?php echo $DEFAULT_FROM_ADDRESS['country']; ?>" autocomplete="off">
											</div>
										</div>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
					<!-- end from address -->

					<!-- TO ADDRESS -->
					<div class="col-xs-12 col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">To Address</h3>
							</div>
							<div class="panel-body">
								<form class="form-horizontal">

									<!-- TRANSIT MAPS MODAL BUTTONS -->
									<!-- these just launch modals that show a image of the transit map per freight carrier -->
									<!-- edit the maps in the modals below -->
									<!-- you will need to edit these buttons (add or remove) based on what carriers you use -->
									<div class="form-group">
										<label class="control-label col-xs-4">Transit Maps:</label>
										<div class="col-xs-8">
											<div class="btn-group" id="transit-maps-btns">
												 <a class="btn btn-primary" data-toggle="modal" data-target="#modal-transit-map-ups" href="#modal-transit-map-ups">UPS</a>
												 <a class="btn btn-primary" data-toggle="modal" data-target="#modal-transit-map-fedex" href="#modal-transit-map-fedex">FedEx</a>
											</div>
										</div>
									</div>

									<fieldset id="to-fieldset-inputs">
										<div class="form-group">
											<label class="control-label col-xs-4">City:</label>
											<div class="col-xs-8">
												<input class="form-control" id="to-city" type="text" required autocomplete="off">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-xs-4">State:</label>
											<div class="col-xs-8">
												<input class="form-control" id="to-state" type="text" required autocomplete="off">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-xs-4">Zip:</label>
											<div class="col-xs-8">
												<input class="form-control" id="to-zip" type="text" required autocomplete="off">
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-xs-4">Country:</label>
											<div class="col-xs-8">
												<input class="form-control" id="to-country" type="list" list="country-codes" required value="US">
											</div>
										</div>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
					<!-- end too address -->

				</div>
				<!-- end address panels -->

				<!-- PACKAGE DETAILS -->
				<div class="row">
					<div class="col-xs-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Package Details</h3>
							</div>
						
							<div class="panel-body">

								<!-- JS SHOWS THE FIRST ROW BY CLONING THIS DIV AND REMOVING THE ID AND DISPLAY:NONE -->
								<!-- done this way to the default select options don't have to hardcoded here and in js -->
								<!-- makes it so only one place has to have the correct list of default packages -->
								<div class="package-row" id="package-row-template" style="display: none;">
									<center>
										<div class="form-inline">
											<div class="form-group package-inputs">
												<div class="input-group">
													<span class="input-group-addon">Defaults:</span>
													<select class="form-control package-default-options">
														<option value="-1">Custom</option>
													</select>
												</div>
											</div>
											<div class="form-group package-size package-inputs">
												<div class="input-group">
													<span class="input-group-addon" >L:</span>
													<input class="form-control package-length" type="text" placeholder="inches">
												</div>
											</div>
											<div class="form-group package-size package-inputs">
												<div class="input-group">
													<span class="input-group-addon" >W:</span>
													<input class="form-control package-width" type="text" placeholder="inches">
												</div>
											</div>
											<div class="form-group package-size package-inputs">
												<div class="input-group">
													<span class="input-group-addon" >H:</span>
													<input class="form-control package-height" type="text" placeholder="inches">
												</div>
											</div>
											<div class="form-group package-size package-inputs">
												<div class="input-group">
													<span class="input-group-addon" >Lbs.</span>
													<input class="form-control package-weight" type="text" placeholder="lbs.">
												</div>
											</div>
											<!-- HIDDEN INPUT FOR DOUBLE CHECKING EXTRA CHARGES -->
											<input class="package-id" type="hidden" value="-1">
											<div class="form-group package-inputs">
												<div class="btn-group package-add-remove-clone-btns">
													<button class="btn btn-default package-add"><span class="glyphicon glyphicon-plus-sign"></span></button>
													<button class="btn btn-default package-remove"><span class="glyphicon glyphicon-trash"></span></button>
													<button class="btn btn-default package-clone"><span class="glyphicon glyphicon-repeat"></span></button>
												</div>
											</div>
										</div>
									</center>
									<br>
								</div>
								<!-- end of template row-->

								<!-- THIS IS WHERE THE LIST OF ACTIVE AND USED PACKAGES ARE STORED -->
								<!-- the template div#package-row-template is cloned into this upon page load -->
								<!-- every time a new package is added or an existing package is cloned, it is added to this div -->
								<div id="list-of-packages"></div>

							</div>
						</div>
					</div>
				</div>
				<!-- end packages -->

				<!-- GET QUOTE BTN -->
				<!-- disabled on click, re-enabled when data is displayed -->
				<div class="row">
					<div class="col-xs-12">
						<center>
							<button class="btn btn-primary" id="get-quote-btn">Get Quote</button>
						</center>
					</div>
				</div>
				<br>

				<!-- ERROR MESSAGES -->
				<!-- test is replaced and display:none is removed by js -->
				<div class="row" id="quote-error" style="display:none;">
					<div class="col-xs-12 col-sm-8 col-sm-offset-2">
						<div class="alert alert-danger">
							<p><b>Error! </b><span id="quote-error-text">This is the error text.</span></p>
						</div>
					</div>
				</div>

				<!-- QUOTES  -->
				<!-- results from the "get_quote" page and output by js -->
				<!-- column "<th>" items are needed for each row of data used and output by js -->
				<!-- js outputs rows into tbody#quotes-results -->
				<div class="row">
					<div class="col-xs-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Quotes</h3>
							</div>
						
							<div class="panel-body">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Carrier</th>
											<th>Service</th>
											<th>Rate</th>
										</tr>
									</thead>
									<tbody id="quotes-results"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			</div>

			<!-- FOOTER -->
			<!-- always shows the accurate "year" -->
			<footer>
				<hr>
				<p>&copy; <?php echo $BRANDNAME; ?> <?php echo date("Y"); ?> </p>
			</footer>

		</div>
		<!-- end .container -->

		<!-- TRANSIT MAP MODALS -->
		<!-- to get your transit map, go to the respective website below and type in your zip code.  Screenshot the map (windows = snipping tool, OSX = grab), and save it to the img directory.  Then update the file names below as needed -->
		<!-- if you are using a new/different carrier on Easypost, you will need to add a new modal (copy & paste one from below) and alter the data to relate to a new carrier. -->
		<!-- you must also add a new button to the transit maps button above id=transit-maps-btns -->
		
		<!-- UPS -->
		<!-- http://www.ups.com/maps -->
		<div class="modal fade" id="modal-transit-map-ups">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-body">
						<img class="img-responsive img-full-width" src="img/ups_transit_map.gif">
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary" type="button" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<!-- FEDEX -->
		<!-- http://www.fedex.com/grd/maps/ShowMapEntry.do?CMP=PAC-fxg_g2m_082 -->
		<div class="modal fade" id="modal-transit-map-fedex">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-body">
						<img class="img-responsive img-full-width" src="img/fedex_transit_map.PNG">
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary" type="button" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>


		<!-- SCRIPTS -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		<script src="js/script.js"></script>
	</body>
</html>