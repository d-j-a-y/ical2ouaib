<?php
/**
 * cyclodalib.php
 *
 * @package     cyclotropeLib
 * @author      d-j-a-y <https://cyclotrope.net>
 * @copyright   Copyright (C) 2022 by jb aka d-j-a-y
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link
 */

/**
 * Parse cyclotrope iCalendars and return data somehow
 *
 */

/**
 * 
 * 
 * 
 * 
 */


Class Cyclo {
    const SUMM      = "SUMMARY";
    const START     = "DTSTART";
    const END       = "DTEND";
    const STATUS    = "STATUS";
    const RULES     = "RRULE";
}

Class CycloICAL {
    const SUMM      = "SUMMARY";
    const START     = "DTSTART";
    const END       = "DTEND";
    const STATUS    = "STATUS";
    const RULES     = "RRULE";
    const EXDAT     = "EXDATE";
    const LOC       = "LOCATION";
    const DESC      = "DESCRIPTION";
}

function cyclo_formatEvent($event)
{
    echo "Action n°X: (" . $event[Cyclo::STATUS] . ") \"" . $event[Cyclo::SUMM] . "\" du " . $event[Cyclo::START] . " au " . $event[Cyclo::END] . "</br>";
}


function cyclo_formatCalendar($calendar_table)
{
    ksort($calendar_table, SORT_NUMERIC);
    foreach ($calendar_table as $key => $event) {
        cyclo_formatEvent($event);
    }
}


function cyclo_formatTimezone($key, $val)
{
    $local_timezone = "Europe/Paris";
    $time_format = "Y-m-d H:i:s";

    if ($key === "DTSTART" || $key === "DTEND") {

        if (8 == strlen($val)) {
            //no start time, assume whole day
            $time_format = "Y-m-d";
        }
        // Convert Date/Time from UTC to Local Time
        date_default_timezone_set("UTC");

        $datetime = new DateTime($val);
        if ($datetime === false || $datetime->getLastErrors()["warning_count"] != 0) {
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
/* WIP eindex not used*/
function cyclo_getEvent($icalobj, $eindex)
{
    //~ $eindex = "parcours chateau";
    //~ return $eindex;

     $events_table = array();

    // read back icalendar, extract event
    if (isset($icalobj->tree->child)) {
        $node = $icalobj->tree->child[1];//fixme
        if ($node->getName() == "VEVENT") {
            $event_rrule = "";
            $event_end = "";
            $event_start = "";
            $event_summary = "";
            $event_status = 'TODO';
            $ecount++;
            foreach ($node->data as $key => $value) {
                $event_value = cyclo_getValue($value);
                switch ($key) {
                    case CycloICAL::SUMM:
                        $event_summary = $event_value;

                        break;
                    case CycloICAL::RULES:
                        $event_rrule = $event_value;

                        break;
                    case CycloICAL::START:
                        $key_primary = ZDateHelper::fromiCaltoUnixDateTime($event_value);
                        $event_start = cyclo_formatTimezone($key, $event_value);

                        break;
                    case CycloICAL::END:
                        if (ZDateHelper::isPast(ZDateHelper::fromiCaltoUnixDateTime($event_value), "Europe/Paris")) {
                            $event_status = 'DONE';
                        }
                        $event_end = cyclo_formatTimezone($key, $event_value);

                        break;
                    case CycloICAL::LOC:
                        break;
                    case CycloICAL::DESC:
                        break;
                }
            }

            $events_table = array (Cyclo::SUMM => $event_summary,
                                   Cyclo::START => $event_start,
                                   Cyclo::END => $event_end,
                                   Cyclo::RULES => $event_rrule,
                                   Cyclo::STATUS => $event_status);
            //~ echo "Event $ecount: ($event_status) $event_summary - $event_start  -- $event_end</br>";
        }
    }
    
    return $events_table;
}

function cyclo_getEvents($icalobj)
{
    $ecount = 0;
    $events_table = array();

    // read back icalendar, extract event
    if (isset($icalobj->tree->child)) {
        foreach ($icalobj->tree->child as $node) {
            if ($node->getName() == "VEVENT") {
                $event_rrule = "";
                $event_exdate = "";
                $event_end = "";
                $event_start = "";
                $event_rawstart= "";
                $event_summary = "";
                $event_status = 'TODO';
                $ecount++;
                foreach ($node->data as $key => $value) {
                    $event_value = cyclo_getValue($value);
                    switch ($key) {
                        case CycloICAL::SUMM:
                            $event_summary = $event_value;

                            break;
                        case CycloICAL::RULES:
                            $event_rrule = $event_value;

                            break;
                        case CycloICAL::START:
                            $key_primary = ZDateHelper::fromiCaltoUnixDateTime($event_value);
                            $event_start = cyclo_formatTimezone($key, $event_value);
                            $event_rawstart = $event_value;

                            break;
                            /* FIXME - event status outdated? */
                        case CycloICAL::END:
                            if (ZDateHelper::isPast(ZDateHelper::fromiCaltoUnixDateTime($event_value), "Europe/Paris")) {
                                $event_status = 'DONE';
                            }
                            $event_end = cyclo_formatTimezone($key, $event_value);

                            break;
                        case CycloICAL::EXDAT:
                            $event_exdate = $event_value;
                            break;
                        case CycloICAL::LOC:
                            break;
                        case CycloICAL::DESC:
                            break;
                    }
                }
                if ($event_rrule != "") {
                  $event_rrule = CycloICAL::START . ":" . $event_rawstart . "Z\n". $event_rrule;
                  if ($event_exdate != "")
                      $event_rrule = $event_rrule . "\n" . CycloICAL::EXDAT . ":" . $event_exdate . "Z";
                }
                $events_table[$key_primary] = array (Cyclo::SUMM => $event_summary,
                                                     Cyclo::START => $event_start,
                                                     Cyclo::END => $event_end,
                                                     Cyclo::RULES => $event_rrule,
                                                     Cyclo::STATUS => $event_status);
                //~ echo "Event $ecount: ($event_status) $event_summary - $event_start  -- $event_end</br>";
            }
        }
    }
    return $events_table;
}

function cyclo_getAgenda($icalfile)
{
    $icalfeed = file_get_contents($icalfile);
    // create the ical object
    $icalobj = new ZCiCal($icalfeed);
    return $icalobj;
}

function cyclo_dumpCalendar($icalobj)
{
    $datadump = "";
    // read back icalendar data that was just parsed
    if (isset($icalobj->tree->child)) {
        foreach ($icalobj->tree->child as $node) {
            if ($node->getName() == "VEVENT") {
                $ecount++;
                //~ echo "Event $ecount:\n";
                foreach ($node->data as $key => $value) {
                    if (is_array($value)) {
                        for ($i = 0; $i < count($value); $i++) {
                            $p = $value[$i]->getParameters();
                            //~ echo "  $key: " . $value[$i]->getValues() . "</br>";
                            $datadump = $datadump . "  $key: " . $value[$i]->getValues() . "</br>";
                        }
                    } else {
                        //~ echo "  $key: " . $value->getValues() . "</br>";
                        $datadump = $datadump . "  $key: " . $value->getValues() . "</br>";
                    }
                }
            }
        }
    }
    return $datadump;
}
