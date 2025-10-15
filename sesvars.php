<?php
/*
   Copyright 2020 cyber-cottage.co.uk

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

if(isset($_POST['List_Queue'])) {
	$queue="";
	foreach($_POST['List_Queue'] as $valor) {
		$queue.=stripslashes($valor).",";
	}
	$queue=substr($queue,0,-1);
    $_SESSION['freestats']['queue']=$queue;
} else {
	$queue="'NONE'";
}

if(isset($_POST['List_Agent'])) {
    $agent="";
	foreach($_POST['List_Agent'] as $valor) {
		$agent.=stripslashes($valor).",";
	}
	$agent=substr($agent,0,-1);
    $_SESSION['freestats']['agent']=$agent;
} else {
	$agent="''";
}

/*
if(isset($_POST['queue'])) {
   $queue = stripslashes($_POST['queue']);
   $_SESSION['freestats']['queue']=$queue;
} else {
   $queue="'NONE'";
}
*/


if(isset($_POST['start'])) {
   $start = $_POST['start'];
   $_SESSION['freestats']['start']=$start;
} else {
   $start = date('Y-m-d 00:00:00');
}

if(isset($_POST['end'])) {
   $end = $_POST['end'];
   $_SESSION['freestats']['end']=$end;
} else {
   $end = date('Y-m-d 23:59:59');
}

if(isset($_SESSION['freestats']['start'])) {
   $start = $_SESSION['freestats']['start'];
}

if(isset($_SESSION['freestats']['end'])) {
   $end = $_SESSION['freestats']['end'];
}

if(isset($_SESSION['freestats']['queue'])) {
   $queue = $_SESSION['freestats']['queue'];
}

if(isset($_SESSION['freestats']['agent'])) {
   $agent = $_SESSION['freestats']['agent'];
}

$fstart_year  = substr($start,0,4);
$fstart_month = substr($start,5,2);
$fstart_day = substr($start,8,2);

$fend_year  = substr($end,0,4);
$fend_month = substr($end,5,2);
$fend_day = substr($end,8,2);

$timestamp_start = return_timestamp($start);
$timestamp_end   = return_timestamp($end);
$elapsed_seconds = $timestamp_end - $timestamp_start;
$period          = floor(($elapsed_seconds / 60) / 60 / 24) + 1; 

if(!isset($_SESSION['freestats']['start'])) {
	if(basename($self)<>"index.php") {
		Header("Location: ./index.php");
	}
}


?>
