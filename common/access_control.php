<?php
	//CHECK USER ACCESS RIGHTS

	//REALLY BASIC ACCESS CONTROL
	//just checks if the user's ip is on a specific subnet
	//this should be made MUCH stronger if you intend on using this on a non-private network (i.e. you are using outside your office)
	function accessControl() {
		$userIP = 		$_SERVER['REMOTE_ADDR'];

		//local ip range high and low
		$ipLow = 		ip2long("172.10.10.1");
		$ipHigh = 		ip2long("172.10.10.254");
		$userIPLong = 	ip2long($userIP);
		if ($userIPLong >= $ipLow && $userIPLong <= $ipHigh) {
			$inRange = True;
		}
		else {
			$inRange = False;
		}

		//check if the user's ip is an external IP or within the lan range
		if ($inRange) {
			return true;
		}
		else {
			echo "You are not allowed acces.<br>";
			echo "Your IP: " . $userIP;
			die;
		}
	}


	//CALL THIS FUNCTION EVERY TIME THIS PAGE IS INCLUDED
	accessControl();

?>