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
?>
<div id="sidebar">&nbsp;</div>
<div id="content">
<a name='0'></a>
<div id='header'>
<ul id='primary'>
<?php
$menu[] = $lang["$language"]['menu_home'];
$menu[] = $lang["$language"]['menu_answered'];
$menu[] = $lang["$language"]['ans_cdr'];
$menu[] = $lang["$language"]['menu_unanswered'];
$menu[] = $lang["$language"]['unans_cdr'];
$menu[] = $lang["$language"]['compare'];
$menu[] = $lang["$language"]['menu_distribution'];
$menu[] = $lang["$language"]['realtime'];
$menu[] = "Search Log";

$link[] = "index.php";
$link[] = "answered.php";
$link[] = "answered_cdr.php";
$link[] = "unanswered.php";
$link[] = "unanswered_cdr.php";
$link[] = "compare.php";
$link[] = "distribution.php";
$link[] = "realtime.php";
$link[] = "search.php";

$anchor = Array();

for ($a = 0; $a < count($menu); $a++) {
	if (basename($self) == $link[$a]) {
		echo "<li><span>" . $menu[$a] . "</span></li>\n";
		if (count($anchor) > 0 && $a = $b) {
			echo "<ul id='secondary'>\n";
			$contador = 1;
			foreach ($anchor as $item) {
				echo "<li><a href='#$contador'>$item</a></li>\n";
				$contador++;
			}
			echo "</ul>\n";
		}

	} else {
		if (isset($_SESSION['freestats']['start'])) {
			echo "<li><a href='" . $link["$a"] . "'>" . $menu["$a"] . "</a></li>\n";
		}
	}
}
?>
</ul>

</div>
