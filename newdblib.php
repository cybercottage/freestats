<?php
/*

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

if (!isset($DB_MUERE)) { $DB_MUERE = false; }
if (!isset($DB_DEBUG)) { $DB_DEBUG = false; }

function conecta_db($dbhost, $dbname, $dbuser, $dbpass) {
    /* Connect to the database $dbname on host $dbhost using $dbuser and $dbpass */

    global $DB_MUERE, $DB_DEBUG;

    // Use persistent connection with mysqli
    $dbh = new mysqli('p:' . $dbhost, $dbuser, $dbpass, $dbname);

    // Check for a connection error
    if ($dbh->connect_error) {
        if ($DB_DEBUG) {
            echo "<h2>Could not connect to $dbhost as $dbuser</h2>";
            echo "<p><b>MySQLi Error</b>: " . $dbh->connect_error . "</p>";
        } else {
            echo "<h2>Database Error</h2>";
        }

        if ($DB_MUERE) {
            echo "<p>This script cannot continue. Aborting...";
            die();
        }
    }

    return $dbh;
}

function desconecta_db($dbh) {
    /* Disconnect from the database, though PHP does this automatically */
    $dbh->close();
}

function consulta_db($dbh, $query, $debug=false, $die_on_debug=true, $silent=false) {
    /* Execute the query $query in the current database. 
       If $debug is true, display the query. 
       If $die_on_debug is true and $debug is true, stop the script after printing the error. 
       If $silent is true, suppress all error messages. */

    global $DB_MUERE, $DB_DEBUG;

    if ($debug) {
        echo "<pre>" . htmlspecialchars($query) . "</pre>";

        if ($die_on_debug) die;
    }

    $qid = $dbh->query($query);

    if (!$qid && !$silent) {
        if ($DB_DEBUG) {
            echo "<h2>Can not execute query</h2>";
            echo "<pre>" . htmlspecialchars($query) . "</pre>";
            echo "<p><b>MySQLi Error</b>: " . $dbh->error . "</p>";
        } else {
            echo "<h2>Database Error</h2>";
        }

        if ($DB_MUERE) {
            echo "<p>This script cannot continue. Aborting...";
            die();
        }
    }

    return $qid;
}

function db_fetch_array($qid) {
    /* Return an associative array from the query result $qid. */
    return $qid->fetch_array(MYSQLI_ASSOC);
}

function db_fetch_row($qid) {
    /* Fetch the next row from the result set */
    return $qid->fetch_row();
}

function db_fetch_object($qid) {
    /* Fetch the next row from the result set as an object */
    return $qid->fetch_object();
}

function db_num_rows($qid) {
    /* Return the number of rows in the result set */
    return $qid->num_rows;
}

function db_affected_rows($dbh) {
    /* Return the number of rows affected by the last INSERT, UPDATE, or DELETE query */
    return $dbh->affected_rows;
}

function db_insert_id($dbh) {
    /* Return the ID of the last inserted row */
    return $dbh->insert_id;
}

function db_free_result($qid) {
    /* Free the result set */
    $qid->free();
}

function db_num_fields($qid) {
    /* Return the number of fields in the result set */
    return $qid->field_count;
}

function db_field_name($qid, $fieldno) {
    /* Return the name of the field with index $fieldno in the result set */
    $field_info = $qid->fetch_field_direct($fieldno);
    return $field_info->name;
}

function db_data_seek($qid, $row) {
    /* Move the result pointer to the specified row */
    return $qid->data_seek($row);
}

?>

