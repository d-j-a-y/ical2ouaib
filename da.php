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
 * FIXME
 * les evenements repetif ne sont pas répété!
 * 
 * TODO
 * FullCalendar and multiple event sources /https://stackoverflow.com/questions/20071119/fullcalendar-and-multiple-event-sources
 * Use fullcalendar keywords -title.start...- directly in cyclodalib???
 * Remplace getAgendaXXX fnct by table [name/url/color]
 * Remove some locale from fullcalen???
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

function cyclo_getAgendaMontjoye()
{
    $icalfile = "https://framagenda.org/remote.php/dav/public-calendars/SLBHLRPTdzLXoJRZ?export";
    return cyclo_getAgenda($icalfile);
}

function cyclo_getAgendaNissaBici22()
{
    $icalfile = "https://framagenda.org/remote.php/dav/public-calendars/2rCjNqKjCEpNcYAG?export";
    return cyclo_getAgenda($icalfile);
}

function cyclo_getAgendaAutres()
{
    $icalfile = "https://framagenda.org/remote.php/dav/public-calendars/pTos79jcpPyjHogB?export";
    return cyclo_getAgenda($icalfile);
}

/*****************************************************************************/

?>

<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8' />
    <link href='./js/fullcalendar/main.css' rel='stylesheet' />

    <script src='./js/fullcalendar/main.js'></script>
    <script src='./js/rrule/rrule-tz.min.js'></script>
    <script src='./js/fullcalendar/rruleconnector/main.global.min.js'></script>

<style>

  html, body {
    font-size: 14px;
    background: #e2e2e2;
  }

  #calendar{
    width: 80%;
    margin-left: 100px;
    box-shadow: 0px 0px 10px #000;
    padding:15px; 
    background: #fff;
  }

  #calendar-container {
    position: fixed;
    top: 0%;
    text-align: center;
    left: 10%;
    right: 10%;
    bottom: 20%;
  }

</style>


    <script  type='text/javascript'>
        window.calendar = null;
        const patapouf = 51;

        function cycloaddevent(evt, cal, col){
            eventsKeys = Object.keys(evt);

            //~ console.log(events[eventsKeys[1]].RRULE);
            for (var i = 0; i < eventsKeys.length; i++)
                if(events[eventsKeys[i]].RRULE)
                    cal.addEvent({ title: evt[eventsKeys[i]].SUMMARY, rrule: evt[eventsKeys[i]].RRULE , color:col });
                else
                    cal.addEvent({ title: evt[eventsKeys[i]].SUMMARY, start: evt[eventsKeys[i]].DTSTART , color:col });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            window.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                buttonText: {
                    today:    'Aujourd\'hui',
                    month:    'Mois',
                    week:     'Semaine',
                    day:      'Jour',
                    list:     'Liste',
                    //~ next:     'Mois suivant',
                    //~ prev: 'Mois précédent',
                }
            });


            if (false){
                //~ window.calendar.addEvent({ title: 'my recurring event', rrule: 'DTSTART:20220201T103000Z\nRRULE:FREQ=WEEKLY;INTERVAL=5;UNTIL=20220601;BYDAY=MO,FR' , color:'red' }); 
                window.calendar.addEvent({ title: 'my recurring event', rrule: 'DTSTART:20220425T140000Z\nFREQ=WEEKLY;COUNT=11;BYDAY=MO' , color:'red' }); 
                //~ window.calendar.addEvent({ title: 'my recurring event',  
                                            //~ start: '20220425T140000' , 
                                            //~ rrule: 'RRULE:FREQ=WEEKLY;INTERVAL=5;UNTIL=20220601;BYDAY=MO,FR' , 
                                            //~ color:'red' }); 
            }

            /* UNIV NCA */
            <?php
                $icalobj = cyclo_getAgendaUnivCA();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "red");
            }

            /* BV Moulins */
            <?php
                $icalobj = cyclo_getAgendaBricoVelo();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "blue");
            }

            /* DV Monjoye */
            <?php
                $icalobj = cyclo_getAgendaMontjoye();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
                //~ $calendar_data = cyclo_dumpCalendar($icalobj);
            ?>
            {
                //~ datadump = <?php echo json_encode($calendar_data); ?>;
                //~ console.log(datadump);

                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "green");
            }

            /* NissaBici 22 */
            <?php
                $icalobj = cyclo_getAgendaNissaBici22();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "purple");
            }

            /* Autres 22 */
            <?php
                $icalobj = cyclo_getAgendaAutres();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "orange");
            }


            window.calendar.render();

            //~ document.getElementById('next').addEventListener('click', function() {
                //~ calendar.next(); // call method
            //~ });


        });

    </script>

  </head>
  <body>

    <div id='calendar-container'>

        <div id='calendar'></div>
    </div>
    <script type='text/javascript'> 
        console.log(patapouf);
        //~ if (typeof window.calendar != "undefined") {
            //~ console.log(typeof window.calendar);
            //~ console.log("!=");
            //~ console.log(window.calendar.entries());
        //~ }else{
            //~ console.log("undefined");
            //~ console.log("==");
            //~ }
    </script>
        

  </body>
</html>
