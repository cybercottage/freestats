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

require_once("config.php");
if(isset($_REQUEST['sesvar'])) {
  $variable=$_REQUEST['sesvar'];
  $value=$_REQUEST['value'];
  $_SESSION['freestats'][$variable]=$value;
}

?>

