<?php
/**
 * da.php - formelly cyclotrope.net/agen/da.php
 *
 * @package     cyclotropeLib
 * @author      d-j-a-y <https://cyclotrope.net>
 * @copyright   Copyright (C) 2022 by jb aka d-j-a-y
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link
 */

/**
 * Parse cyclotrope iCalendars and display somehow
 *
 * Enter a calendar name on the URL ( cyclotrope.net/agen/da.php?calendar=name ),
 * or leave blank to parse all calendars.
 *
 */

/**
 * 
 * 
 * 
 * 
 */

require_once "./lib/zapcallib.php";
require_once "./lib/cyclodalib.php";

function cyclo_getGet()
{
    // URL parameters (and all form data submitted via the 'get' method)
    // Show a particular value.
    $id = $_GET["calendar"];

    if ($id) {
        echo "<p>calendar: ", $id, "<p/>";
        if ($id == 'bv')
            return true;
    } else {
        echo "<p>No calendar parameter.</p>";
    }
    return false;
    
//$onlybv = cyclo_getGet();
//if ($onlybv) die();

}

function cyclo_getAgendaBricoVelo()
{
    $icalfile = "https://framagenda.org/remote.php/dav/public-calendars/P7c5bbRpegLAmGFd?export"; //FIXME geturls from php call 
    return cyclo_getAgenda($icalfile);
}

function cyclo_getAgendaUnivCA()
{
    $icalfile = "https://framagenda.org/remote.php/dav/public-calendars/zwe6fDZSE6EceySH?export";
    return cyclo_getAgenda($icalfile);
}

/*****************************************************************************/


$icalobj = cyclo_getAgendaBricoVelo();

echo "<h2>Bric0Vél0</h2>";
echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";

$calendar_table = cyclo_getEvents($icalobj);
cyclo_formatCalendar($calendar_table);
//~ print_r($calendar_table);


$icalobj = cyclo_getAgendaUnivCA();

echo "</br></br></br><h2>Univ</h2>";
echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";

$calendar_table = cyclo_getEvents($icalobj);
cyclo_formatCalendar($calendar_table);

echo "</br>--------------------------------------------------------------------------</br>";
cyclo_dumpCalendar($icalobj);
