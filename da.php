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

Class Cyclo {
    const SUMM      = "SUMMARY";
    const START     = "DTSTART";
    const END       = "DTEND";
    const STATUS    = "STATUS";
    
}

function cyclo_timeIspast($date)
{
    //check datehelper funct
    $maintenant = new DateTime();
}

function cyclo_Formatevent($event)
{
    echo "Action n°X: (" . $event[Cyclo::STATUS] . ") \"" . $event[Cyclo::SUMM] . "\" du " . $event[Cyclo::START] . " au " . $event[Cyclo::END] . "</br>";
}


function cyclo_Formatcalendar($calendar_table)
{
    foreach ($calendar_table as $key => $event) {
        cyclo_Formatevent($event);
    }
}


function cyclo_formatTimezone($key, $val)
{
    //~ global $local_timezone;
    $local_timezone = "Europe/Paris";
    $time_format = "Y-m-d H:i:s";

    if ($key === "DTSTART" || $key === "DTEND") {

        if (8 == strlen($val)) {
            //assume whole day
            $time_format = "Y-m-d";
        }
        // Convert Date/Time from UTC to Local Time
        date_default_timezone_set("UTC");

        $datetime = new DateTime($val);
        if (
            $datetime === false ||
            $datetime->getLastErrors()["warning_count"] != 0
        ) {
            return "ERR:" . $key . $val;
        }
        $local_time = new DateTimeZone($local_timezone);
        $datetime->setTimezone($local_time);
        $val = $datetime->format($time_format);

    } else {
        return "ERR:" . $key . $val;
    }
    //~ return [ $date, $time ]; TODO
    return $val;
}

function cyclo_getValue($value)
{
    if (is_array($value)) {
        for ($i = 0; $i < count($value); $i++) { //FIXME loop is not needed!?
            $p = $value[$i]->getParameters();
            return $value[$i]->getValues();
        }
    } else {
        return $value->getValues();
    }
}

function cyclo_parse_calendar($icalobj)
{
    $ecount = 0;
    $events_table = array();

    // read back icalendar data that was just parsed
    if (isset($icalobj->tree->child)) {
        foreach ($icalobj->tree->child as $node) {
            if ($node->getName() == "VEVENT") {
                $event_summary = "";
                $event_status = 'TODO';
                $ecount++;
                foreach ($node->data as $key => $value) {
                    //                                        echo " ---";
                    $event_value = cyclo_getValue($value);
                    switch ($key) {
                        case "SUMMARY":
                            $event_summary = $event_value;

                            break;
                        case "DTSTART":
                            $key_primary = ZDateHelper::fromiCaltoUnixDateTime($event_value);
                            $event_start = cyclo_formatTimezone($key, $event_value);

                            break;
                        case "DTEND":
                            if (ZDateHelper::isPast(ZDateHelper::fromiCaltoUnixDateTime($event_value), "Europe/Paris")) {
                                $event_status = 'DONE';
                            }
                            $event_end = cyclo_formatTimezone($key, $event_value);

                            break;
                    }
                }
                $events_table[$key_primary] = array (Cyclo::SUMM => $event_summary,
                                                     Cyclo::START => $event_start,
                                                     Cyclo::END => $event_end,
                                                     Cyclo::STATUS => $event_status);
                //~ echo "Event $ecount: ($event_status) $event_summary - $event_start  -- $event_end</br>";
            }
        }
    }
    return $events_table;
}

function cyclo_calendarDump($icalobj)
{
    // read back icalendar data that was just parsed
    if (isset($icalobj->tree->child)) {
        foreach ($icalobj->tree->child as $node) {
            if ($node->getName() == "VEVENT") {
                $ecount++;
                echo "Event $ecount:\n";
                foreach ($node->data as $key => $value) {
                    if (is_array($value)) {
                        for ($i = 0; $i < count($value); $i++) {
                            $p = $value[$i]->getParameters();
                            echo "  $key: " . $value[$i]->getValues() . "</br>";
                        }
                    } else {
                        echo "  $key: " . $value->getValues() . "</br>";
                    }
                }
            }
        }
    }
}

function cyclo_getGet()
{
    // Show all URL parameters (and
    // all form data submitted via the
    // 'get' method)
    foreach ($_GET as $key => $value) {
        echo $key, " => ", $value, "<br/>";
    }

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
}

$onlybv = cyclo_getGet();

$icalfile = "https://framagenda.org/remote.php/dav/public-calendars/P7c5bbRpegLAmGFd?export"; //FIXME geturls from php call 
$icalfeed = file_get_contents($icalfile);

// create the ical object
$icalobj = new ZCiCal($icalfeed);

echo "<h2>BricoVélo</h2>";
echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";

$calendar_table = cyclo_parse_calendar($icalobj);
cyclo_Formatcalendar($calendar_table);
//~ print_r($calendar_table);

if ($onlybv) die();

echo "</br></br></br><h2>Univ</h2>";

$icalfile = "https://framagenda.org/remote.php/dav/public-calendars/zwe6fDZSE6EceySH?export";
$icalfeed = file_get_contents($icalfile);

// create the ical object
$icalobj = new ZCiCal($icalfeed);

echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";

$calendar_table = cyclo_parse_calendar($icalobj);
cyclo_Formatcalendar($calendar_table);

echo "</br>--------------------------------------------------------------------------</br>";
cyclo_calendarDump($icalobj);
