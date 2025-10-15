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
<?php
//query
$query = "SELECT agent, data1  FROM $DBTable WHERE time >= '$start' AND time <= '$end' AND event = 'CONFIRM' AND queuename IN ($queue) AND agent IN ($agent)";

//$cid = '%';
//$agent = '%';
//$status = '%';
if (isset($_POST['callerid'])) {
	$cid = $_POST['callerid'];
	$agnt = $_POST['agent'];
	$status = $_POST['status'];
	$camp = $_POST['camp'];
	$sql = "SELECT time, agent, data1, data2, data3, data4, data5, callid  FROM $DBTable WHERE time >= '$start' AND time <= '$end' AND event = 'CONFIRM' AND queuename IN ($queue) AND agent IN ($agent) AND data2 like '%$cid%' AND data1 like '%$status' AND agent like '%$agnt' AND data3 like '%$camp%';";
	$resconfirm = $connection->query($sql);
	$_POST = array();
}

function array_mesh() {
	$numargs = func_num_args();
	$arg_list = func_get_args();
	$out = array();
	for ($i = 0; $i < $numargs; $i++) {
		$in = $arg_list[$i];
		foreach ($in as $key => $value) {
			if (array_key_exists($key, $out)) {
				$sum = $in[$key] + $out[$key];
				$out[$key] = $sum;
			} else {
				$out[$key] = $in[$key];
			}
		}
	}
	return $out;
}

function array_min() {
	$numargs = func_num_args();
	$arg_list = func_get_args();
	$out = array();
	for ($i = 0; $i < $numargs; $i++) {
		$in = $arg_list[$i];
		foreach ($in as $key => $value) {
			if (array_key_exists($key, $out)) {
				$sum = $in[$key] - $out[$key];
				$out[$key] = $sum;
			} else {
				$out[$key] = $in[$key];
			}
		}
	}
	return $out;
}

function RandomString($length = 16) {
	$randstr;
	srand((double) microtime(TRUE) * 1000000);
	//our array add all letters and numbers if you wish
	$chars = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'p',
		'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5',
		'6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
		'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

	for ($rand = 0; $rand <= $length; $rand++) {
		$random = rand(0, count($chars) - 1);
		$randstr .= $chars[$random];
	}
	return $randstr;
}

function arr_cnt($res) {
	$out = array();
	$i = 0;
	foreach ($res as $key => $row) {
		$i = $i + 1;
		$count[$i] = $row['data1'];
	}
	$out = array_count_values($count);
	return $out;
}

$confirm = $connection->query($query);

$confirm_all = array();
$confirm_all['YES'] = 0;
$confirm_all['NOT'] = 0;
$confirm_all['LAT'] = 0;

$confirm_all = arr_cnt($confirm);

//print_r(json_encode($confirm_all));
//echo "<br/>";

foreach ($confirm as $k => $r) {
	if ($r['data1'] == 'YES') {
		$agent_yes = $r['agent'];
		$yes["$agent_yes"] += count($r['data1']);
		ksort($yes, SORT_STRING);
	} else if ($r['data1'] == 'NOT') {
		$agent_not = $r['agent'];
		$not["$agent_not"] += count($r['data1']);
		ksort($not, SORT_STRING);

	}
	//   else if ($r['data1'] == 'LAT') {
	// 	$agent_not = $r['agent'];
	// 	$lat["$agent_lat"] += count($r['data1']);
	// 	ksort($lat, SORT_STRING);

	// }

}
$agents_name = array_keys($not);
foreach ($agents_name as $k => $v) {
	$agentname[] = "'" . $v . "'";
}
sort($agentname, SORT_STRING);

$dummy = array_min($not, $not);
//$yes = $yes + $dummy;
$yes = array_mesh($yes, $dummy);
ksort($yes, SORT_STRING);

$alls = array_map(null, $agentname, $yes, $not);

$cnt = count($alls);
// print_r(json_encode($yes2));
// echo "<br/>";
// print_r(json_encode($not));
// echo "<br/>";
// print_r(json_encode($dummy));
$start_parts = explode(" ,:", $start);
$end_parts = explode(" ,:", $end);
$confirm->free();
$connection->close();
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Asterisk FreeStats</title>
      <style type="text/css" media="screen">@import "css/basic.css";</style>
      <style type="text/css" media="screen">@import "css/tab.css";</style>
      <style type="text/css" media="screen">@import "css/table.css";</style>
      <style type="text/css" media="screen">@import "css/fixed-all.css";</style>
    <script type="text/javascript" src="js/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/sorttable.js"></script>
</head>

<body>
<?php include "menu.php";?>
<div id="main">
    <div id="contents">
    <TABLE width='99%' cellpadding=3 cellspacing=3 border=0>
    <THEAD>
    <TR>
      <TD valign=top width='50%'>
        <TABLE width='100%' border=0 cellpadding=0 cellspacing=0>
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

      </TD>
      <TD valign=top width='50%'>

        <TABLE width='100%' border=0 cellpadding=0 cellspacing=0>
        <CAPTION>Согласие на услугу</CAPTION>
        <TBODY>
            <TR>
                  <TD>Согласились:</TD>
              <TD><?php echo $confirm_all['YES'] ?></TD>
              </TR>
                <TR>
                  <TD>Не согласились:</TD>
                  <TD><?php echo $confirm_all['NOT'] ?></TD>
                </TR>
            <TR>
                  <TD>Эффективность:</TD>
              <TD><?php echo round($confirm_all['YES'] * 100 / ($confirm_all['YES'] + $confirm_all['NOT']), 2) ?>%</TD>
              </TR>
                <TR>
                  <TD>Всего:</TD>
                  <TD><?php echo $confirm_all['YES'] + $confirm_all['NOT'] ?></TD>
                </TR>
        </TBODY>
            </TABLE>

      </TD>
    </TR>
    </THEAD>
    </TABLE>
    <br/>

           <h3> Распределение по агентам</h3>
<br />
       <TABLE width='90%' cellpadding=0 cellspacing=0  border=0>
        <THEAD>
        <TR>
      <TH>Агент</TH>
      <TH>Согл.</TH>
      <TH>Эффект.</TH>
      <TH>Не согл.</TH>
       <TH><b>Всего</b></TH>
       </TR>
     </THEAD>
     <TBODY>
<?php
$header_pdf = array("Агент", "Согл.", "Эффект.%", "Не согл.", "Всего");
$width_pdf = array(25, 23, 23, 23, 23);
$title_pdf = "Согласие на услугу";
$data_pdf = array();
for ($i = 0; $i < $cnt; $i++) {
	$sum99 = $alls["$i"][2] + $alls["$i"][1];
	$effect = $alls["$i"][1] * 100 / $sum99;
	$effect = round($effect, 2, PHP_ROUND_HALF_UP);
	echo '<TR>
            <TD>' . $alls["$i"][0] . '</TD>
            <TD>' . $alls["$i"][1] . '</TD>
            <TD>' . $effect . '%</TD>
            <TD>' . $alls["$i"][2] . '</TD>
            <TD><b>' . $sum99 . '</b></TD>
        <tr/>';

	$linea_pdf = array($alls["$i"][0], $alls["$i"][1], $effect, $alls["$i"][2], $sum99);
	$data_pdf[] = $linea_pdf;

}

//print_exports($header_pdf, $data_pdf, $width_pdf, $title_pdf, $cover_pdf);

?>
  </TBODY>
 </TABLE>
<?php
print_exports($header_pdf, $data_pdf, $width_pdf, $title_pdf, $cover_pdf);
?>
 <br />
  <h3> Детализация </h3>
</br>
<div id="search" align="left">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="frm1" name="frm1" method="post">
&nbsp;&nbsp;&nbsp;<b>CallerID:</b>&nbsp;<input type="text" id="callerid" name="callerid" />
&nbsp;&nbsp;&nbsp;<b>Агент:</b>&nbsp;<input type="text" id="agent" name="agent"/>
&nbsp;&nbsp;&nbsp;<b>База:</b>&nbsp;<input type="text" id="camp" name="camp"/>
&nbsp;&nbsp;&nbsp;<select  id="status" name="status">Статус:
  <option selected value="YES">Согл.</option>
  <option value="NOT">Не согл.</option>
   <option  value="%">Любой</option>

</select>
&nbsp;&nbsp;&nbsp;<button type="submit" name="submit">Найти</button>
</form>
</div>
<br />
 <a name='20'></a>
   <table width='90%' cellpadding=1 cellspacing=1 border=0  id='20' >
    <thead>
        <TR>
      <TH>Дата</TH>
      <TH>Агент</TH>
      <TH>Статус</TH>
      <TH>Номер</TH>
      <TH>База</TH>
      <TH>ФИО</TH>
      <TH>Рожд.</TH>
      <TH>Запись</TH>
       </TR>
      </thead>
      <tbody>
<?php
$header_pdf = array("Номер", "ФИО", "Емайл", "Имя", "Отчество", "Фамилия", "Рожд.");
$width_pdf = array(25, 23, 23, 23, 23, 25, 25);
$title_pdf = "Согласие на услугу";
$data_pdf = array();
foreach ($resconfirm as $k => $r) {
	if ($r['data1'] == 'NOT') {
		$status = '<b style="color:firebrick">Не согл.</b>';
	} else if ($r['data1'] == 'YES') {
		$status = '<b style="color:green">Согласен</b>';

	}
	$time = strtotime($r['time']);
	$tmpError = $r['callid'];
	$tmpRec = '<audio controls preload="none">
             <source src="dl.php?f=[_file]">
         </audio>
         <a href="dl.php?f=[_file]"></a>';

	$rec['filename'] = $r['callid'] . '.mp3';
	$rec['path'] = '/var/spool/asterisk/monitor/mp3/' . date('Y/m/d/', $time) . $rec['filename'];

	if (file_exists($rec['path']) && preg_match('/(.*)\.mp3$/i', $rec['filename'])) {
		$tmpRes = str_replace('[_file]', base64_encode($rec['path']), $tmpRec);
	} else {
		$tmpRes = $tmpError;
	}

	$fio = explode(" ", $r['data4']);
	$email = randomString() . '@live.ru';
	echo '<TR>
              <TD>' . date('Y-m-d H:i:s', $time) . '</TD>
              <TD>' . $r['agent'] . '</TD>
              <TD>' . $status . '</TD>
              <TD>' . $r['data2'] . '</TD>
              <TD>' . $r['data3'] . '</TD>
              <TD>' . $r['data4'] . '</TD>
              <TD>' . $r['data5'] . '</TD>
              <TD>' . $tmpRes . '</TD>
            <tr/>';

	$linea_pdf = array(substr($r['data2'], -10), $r['data4'], $email, $fio[0], $fio[1], $fio[2], str_replace("/", ".", $r['data5']));
	$data_pdf[] = $linea_pdf;

}
$resconfirm->free();
//print_exports($header_pdf, $data_pdf, $width_pdf, $title_pdf, $cover_pdf);

?>
  </tbody>
 </table>
<?php
print_exports($header_pdf, $data_pdf, $width_pdf, $title_pdf, $cover_pdf);
?>



    </div>
</div>

<div id='footer'><a href='https://www.cyber-cottage.co.uk'>cyber-cottage.co.uk</a> 2020</div>
</body>
</html>

