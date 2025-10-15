<?php
/*
Copyright 2020, https://www.cyber-cottage.co.uk

This file is part of Asterisk FreeStats.
Asterisk FreeStats is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Asterisk FreeStats is distributed in the hope that it will be
useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Asterisk FreeStats.  If not, see
<http://www.gnu.org/licenses/>.
 */
require_once "config.php";
include "sesvars.php";
//ini_set('display_errors',1);
//error_reporting(E_WARNING);
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Asterisk FreeStats</title>
    <style type="text/css" media="screen">@import "css/basic.css";</style>
    <style type="text/css" media="screen">@import "css/tab.css";</style>
    <style type="text/css" media="screen">@import "css/table.css";</style>
    <style type="text/css" media="screen">@import "css/fixed-all.css";</style>
	<script type="text/javascript" src="js/sorttable.js"></script>
</head>
<?php
if (isset($_POST['pagerows'])) {
	$page_rows = $_POST['pagerows'];
	$_SESSION['freestats']['pagerows'] = $page_rows;
} else {
	$_SESSION['freestats']['pagerows'] = 100;
}

if ((isset($_REQUEST['callerid_search'])) && (strlen($_REQUEST['callerid_search']) > 0)) {
	$callerid_search = $_REQUEST['callerid_search'];
	$sql = "select distinct(callid) from $DBTable where time >= '$start' AND time <= '$end' and data2 like '%$callerid_search%'";
	$restemp = mysqli_query($connection, $sql);
	foreach ($restemp as $temp) {
		$callid_search .= ",'" . $temp['callid'] . "'";
	}
	$callid_search = substr($callid_search, 1);
	$sql = "select time, callid, queuename, agent, event, data1, data2, data3 from $DBTable where time >= '$start' AND time <= '$end'
            and event in ('COMPLETECALLER','COMPLETEAGENT','ENTERQUEUE', 'REC') and callid in ($callid_search) order by callid";
	$rescomplete = mysqli_query($connection, $sql);
} elseif ((isset($_REQUEST['outagent'])) && (strlen($_REQUEST['outagent']) > 0)) {
	$outagent = $_REQUEST['outagent'];
	$sql = "select time, callid, queuename, agent, event, data1, data2, data3 from $DBTable where time >= '$start' AND time <= '$end'
            and event in ('COMPLETECALLER','COMPLETEAGENT','ENTERQUEUE', 'REC')  and agent in ('$outagent','NONE') order by callid";
	$rescomplete = mysqli_query($connection, $sql);
} else {
	$sql = "select time, callid, queuename, agent, event, data1, data2, data3 from $DBTable
           where time >= '$start' AND time <= '$end' AND agent IN ($agent , 'NONE') and queuename in ($queue, 'rec', 'recordcheck')
           and event in ('COMPLETECALLER','COMPLETEAGENT','ENTERQUEUE', 'REC') order by callid, time limit $page_rows";
	$rescomplete = mysqli_query($connection, $sql);
}




mysqli_close($connection);
$start_parts = explode(" ,:", $start);
$end_parts = explode(" ,:", $end);

//$conn_fpbx = new mysqli("localhost", "freepbxuser", "4094e7b341f3b8353246db18840290c6", "asteriskcdrdb");

//    $sql2 = $conn_fpbx->query("SELECT uniqueid, recordingfile from cdr where calldate >= '$start' AND calldate <= '$end'");
//      $datacdr = mysqli_query($conn_fpbx, $sql2);
//    $cdrd = array();
//while ($row = $datacdr->fetch_array(MYSQLI_ASSOC)) {
//    $cdrd[] = $row;
//}
//mysqli_close($conn_fpbx);

?>

<body>
<?php include "menu.php";?>
<div id="main">
    <div id="contents">
		<TABLE width='90%' border=0 cellpadding=0 cellspacing=0>
                <CAPTION><?php echo $lang["$language"]['report_info'] ?></CAPTION>
                <TBODY>
                <TR>
                    <TD><?php echo $lang["$language"]['queue'] ?>:</TD>
                    <TD><?php echo $queue ?></TD>
                </TR>
                </TR>
                    <TD><?php echo $lang["$language"]['start'] ?>:</TD>
                    <TD><?php echo $start_parts[0] ?></TD>
                </TR>
                </TR>
                <TR>
                    <TD><?php echo $lang["$language"]['end'] ?>:</TD>
                    <TD><?php echo $end_parts[0] ?></TD>
                </TR>
                <TR>
                    <TD><?php echo $lang["$language"]['period'] ?>:</TD>
                    <TD><?php echo $period ?> <?php echo $lang["$language"]['days'] ?></TD>
                </TR>
                </TBODY>
                </TABLE>
<br />
<div id="search" align="left">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="frm1" name="frm1" method="post">
&nbsp;&nbsp;&nbsp;<b>CallerID:</b>&nbsp;<input type="text" id="callerid_search" name="callerid_search" />
&nbsp;&nbsp;&nbsp;<b>Agent:</b>&nbsp;<input type="text" id="outagent" name="outagent"/>
&nbsp;&nbsp;&nbsp;<button type="submit" name="submit">Submit</button>
</form>
</div>
<div id="rows" align="right">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="frm2" name="frm2" method="post">
	&nbsp;&nbsp;&nbsp;<b><?php echo $lang["$language"]['page_rows']; ?></b>&nbsp;
<select onchange="this.form.submit()" id="pagerows" name="pagerows">
  <option selected="<?php echo $_SESSION['freestats']['pagerows']; ?>"><?php echo $_SESSION['freestats']['pagerows'] / 2; ?></option>
  <option value="200">100</option>
  <option value="1000">500</option>
  <option value="2000">1000</option>
  <option value="20000">10000</option>
</select>
</form>
</div>
<br />
<a name='5'></a>
           <h3> <?php echo $lang["$language"]['answered_calls'] ?></h3>
<br />
		   <table width='90%' cellpadding=1 cellspacing=1 border=0 class='sortable' id='5' >
        <TR>
 	   	<TH><?php echo $lang["$language"]['time'] ?></TH>
		<TH><?php echo $lang["$language"]['callerid'] ?></TH>
		<TH><?php echo $lang["$language"]['queue'] ?></TH>
		<TH><?php echo $lang["$language"]['agent'] ?></TH>
		<TH><?php echo $lang["$language"]['event'] ?></TH>
		<TH><?php echo $lang["$language"]['holdtime'] ?></TH>
		<TH><?php echo $lang["$language"]['calltime'] ?></TH>
		<TH><?php echo $lang["$language"]['recordfile'] ?></TH>
       </TR>
<?php
$header_pdf = array($lang["$language"]['time'], $lang["$language"]['callerid'], $lang["$language"]['queue'], $lang["$language"]['agent'], $lang["$language"]['event'], $lang["$language"]['holdtime'], $lang["$language"]['calltime']);
$width_pdf = array(25, 23, 23, 23, 23, 25, 25, 20);
$title_pdf = $lang["$language"]['answered_calls'];
$data_pdf = array();

foreach ($rescomplete as $row) {


	switch ($row['event']) {
	case "REC":
	       $recfile = $row['data1'];
	       $break;
	case "ENTERQUEUE":
		$callerid = $row['data2'];
		$break;
	case "COMPLETEAGENT":
		$holdtime = seconds2minutes($row['data1']);
		$calltime = seconds2minutes($row['data2']);
		$time = strtotime($row['time']) - ($row['data1'] + $row['data2']);
		$break;
	case "COMPLETECALLER":
		$holdtime = seconds2minutes($row['data1']);
		$calltime = seconds2minutes($row['data2']);
		$time = strtotime($row['time']) - ($row['data1'] + $row['data2']);
		$break;
		if ($row['event'] == "COMPLETEAGENT") {
			$cause_hangup = $lang["$language"]['agent_hungup'];
		} elseif ($row['event'] == "COMPLETECALLER") {
			$cause_hangup = $lang["$language"]['caller_hungup'];
		}
		if (($row['event'] == "COMPLETEAGENT") || ($row['event'] == "COMPLETECALLER")) {
			$page_rows2 += count($row['event']);
			$tmpError = $row['callid'];
			$tmpRec = '<audio controls preload="none">
	           <source src="dl.php?f=[_file]">
			   </audio>';
			// <a href="dl.php?f=[_file]">' . $row['callid'] . '</a>';
			
			//$rec['filename'] = 'q-' . $row['queuename'] . '-' . $callerid . '-' . date('Ymd', $time) . '-000000-' . $row['callid'] . '.wav';
			$rec['filename'] = $recfile . '.wav';
			$rec['path'] = '/var/spool/asterisk/monitor/' . date('Y/m/d/', $time) . $rec['filename'];

			if (file_exists($rec['path']) && preg_match('/(.*)\.wav$/i', $rec['filename'])) {
				$tmpRes = str_replace('[_file]', base64_encode($rec['path']), $tmpRec);
			} else {
				$tmpRes = $tmpError;
			}
			echo "<TR><TD>" . date('Y-m-d H:i:s', $time) . "</TD>
	      <TD><a href='" . htmlspecialchars($_SERVER_['PHP_SELF']) . "?callerid_search=" . $callerid . "'>" . $callerid . "</TD>
	      <TD>" . $row['queuename'] . "</TD>
	      <TD><a href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?outagent=" . $row['agent'] . "'>" . $row['agent'] . "</a></TD>
	      <TD>" . $cause_hangup . "</TD>
	      <TD>" . $holdtime . "</TD>
	      <TD>" . $calltime . "</TD>
	      <TD>" . $tmpRes . "</TD>
	      </TR>\n";
			$linea_pdf = array($time, $callerid, $row['queuename'], $row['agent'], $cause_hangup, $holdtime, $calltime);
			$data_pdf[] = $linea_pdf;
		}
	}
}
mysqli_free_result($rescomplete);

print_exports($header_pdf, $data_pdf, $width_pdf, $title_pdf, $cover_pdf);
?>
 </table>
<?php
print_exports($header_pdf, $data_pdf, $width_pdf, $title_pdf, $cover_pdf);
?>
	  <br/>
     </div>
    </div>
   <div id="footer"><a href='https://www.cyber-cottage.co.uk'>cyber-cottage.co.uk</a> 2020</div>
  </body>
 </html>
