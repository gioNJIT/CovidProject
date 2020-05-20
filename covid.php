<!DOCTYPE html>
<html>
<body>
<h1>COVID-19 CASES BY STATE</h1>
<h4>To search for particular state(s), please seperate by commas *Must click submit instead of pressing enter*</h4>

<! –– ##################### CREATING BUTTONS AT THE TOP OF PAGE ––>
<form method="post"> 

		
		<input type="submit" name="states"
                class="button" value="States" />
                
        <input type="submit" name="cases"
                class="button" value="Cases" />
                
        <input type="submit" name="newcases"
                class="button" value="New Cases" />
         
        <input type="submit" name="deaths"
                class="button" value="Deaths" /> 
                
        <input type="submit" name="newdeaths"
                class="button" value="New Deaths" />
                
        <input type="text" name="value">
		<input type="submit">
    </form> 



<?php

//########################## MYSQL RETRIEVING DATA
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "newDB";

// Creating connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Checking connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


//$casecounter=0;
//$deathcounter=0;

$ip= $_SERVER['REMOTE_ADDR'];
$longip = ip2long($ip);
$ipsql="SELECT * FROM ipaddr";
$ipresult=$conn->query($ipsql);
$ipcheck=0;

while($row = $ipresult->fetch_assoc()) {

	if($row["ipaddy"] == $longip){
		//$s_num=$row["statenum"];
		//$c_num=$row["casenum"];
		//$d_num=$row["deathnum"];
		//$nc_num=$row["newcasecounter"];
		//$nd_num=$row["newdeathcounter"];
		$ipcheck=1;
	}   	
}    	
if($ipcheck==0){
	$conn->query("INSERT INTO ipaddr (ipaddy) VALUES ($longip)");
} 


$result = $conn->query("SELECT * FROM coronastat");
$deathresults = $conn->query("SELECT * FROM coronastat ORDER BY deaths ASC");
$caseresults = $conn->query("SELECT * FROM coronastat ORDER BY cases ASC");
$stateresult = $conn->query("SELECT * FROM coronastat ORDER BY state ASC");
$stateresultdesc = $conn->query("SELECT * FROM coronastat ORDER BY state DESC");
$deathresultsdesc = $conn->query("SELECT * FROM coronastat ORDER BY deaths DESC");
$caseresultsdesc = $conn->query("SELECT * FROM coronastat ORDER BY cases DESC");

//displaying initial stats
ob_start();

session_start();
if(isset($_SESSION['arr_count'])){
    //get it
    $casecounter = $_SESSION['arr_count'][0];//+$c_num;
    $deathcounter = $_SESSION['arr_count'][1];//+$d_num;
    $statecounter = $_SESSION['arr_count'][2];//+$s_num;
} else {
    //set a default value if not isset
    $casecounter = 0;
    $deathcounter=0;
    $statecounter=0;
    $arr_count=array($casecounter,$deathcounter,$statecounter);

}

display_stats($result);

//########################## CREATING BUTTON FUNCTIONS
function cases($result,$caseresults,&$casecounter) 
{ 			
	ob_end_clean();	
	if (($casecounter % 2) == 0){
		$casecounter++;
		display_stats($caseresults);
	}
	else {
		$casecounter++;
		display_stats($result);	
	}          
} 

function deaths($results,$resultsdesc,&$deathcounter) 
{ 
	ob_end_clean();
	if (($deathcounter % 2) == 0){	
		$deathcounter++;
		display_stats($results);
	}
	else {
		$deathcounter++;
		display_stats($resultsdesc);	
	}	
}

function states($results,$resultsdesc,&$statecounter) 
{ 
	ob_end_clean();
	if (($statecounter % 2) == 0){	
		$statecounter++;
		display_stats($results);
	}
	else {
		$statecounter++;
		display_stats($resultsdesc);	
	}	
}


//########################## CREATING BUTTON CALLS

if(array_key_exists('cases', $_POST)) { 
	cases($caseresultsdesc,$caseresults,$casecounter); 
} 
else if(array_key_exists('deaths', $_POST)) { 
	deaths($deathresults,$deathresultsdesc,$deathcounter); 
} 
else if(array_key_exists('states', $_POST)) { 
	states($stateresult,$stateresultdesc,$statecounter); 
} 
else if(array_key_exists('newcases', $_POST)) { 
	cases($caseresultsdesc,$caseresults,$casecounter);
}
else if(array_key_exists('newdeaths', $_POST)) { 
	deaths($deathresults,$deathresultsdesc,$deathcounter);
}
else if(array_key_exists('value', $_POST)) {
	$states=convert_str($_POST['value']);
	$spec_state = $conn->query($states);
	display_stats($spec_state);
}  

function convert_str($str){
	$sel_state="SELECT * FROM coronastat WHERE state = '";
	strlen($str);
	
	for ($x = 0; $x <= strlen($str); $x++) {

		if(strlen($str)==($x)){
			$sel_state.="'";
			ob_end_clean();
			
			return $sel_state;
		}
    	if(substr($str, $x,1)==','){
    		if(substr($str, $x+1,1)==' '){
    			$x++;
    		}
    		$sel_state.="' OR state = '";
    		continue;
    	}
    	$sel_state.=substr($str, $x,1);
	}
	
}

     
//########################## DISPLAY FUNCTION
function display_stats($result) { 

	if ($result->num_rows > 0) {		
		 //output data of each row
		while($row = $result->fetch_assoc()) {
			$NEWDEATHS = ($row["deaths"])-($row["yest_DEATH"]);
			$newcases = $row["cases"] - $row["yest_case"];
	    	echo "state: " . $row["state"]. " - Cases: " . $row["cases"]. " - New Cases: " . $newcases . " - Deaths: " . $row["deaths"]." - New Deaths: " . $NEWDEATHS . "<br>";
		}
	} else {
		echo "0 results";
	}          
}

        
$arr_count[0]=$casecounter;
$arr_count[1]=$deathcounter;
$arr_count[2]=$statecounter;
$_SESSION['arr_count'] = $arr_count;


#$conn->close();
?>


</body>
</html>
