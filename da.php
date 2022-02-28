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
 */

/**
 * TODO
 * da.css
 * multiple event sources /https://stackoverflow.com/questions/20071119/fullcalendar-and-multiple-event-sources
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

    #calendar {
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

    .fc-daygrid-day-number{
        font-size: large;
    }

    .fc-day-sun {
        background: repeating-linear-gradient(
            45deg,
            #606dbc,
            #606dbc 10px,
            #4aa2c8 10px,
            #4aa2c8 20px);
    }

    .fc-col-header-cell{
        background: #aa6dbc;
    }

    .fc-day-past{
        background: repeating-linear-gradient(
          -45deg,
          #aa6dbc,
          #aa6dbc 0.5em,
          #12c2c8 0.5em,
          #46c2c8 4em);
    }

    .fc-day-today {
        background: unset;
    }

    #main-menu:hover,nav.main-menu.expanded {
        left:0px;
        overflow:visible;
    }

    #main-menu {
        background:#aa6dbc;
        box-shadow: 0px 0px 10px #000;
        position:absolute;
        top:0;
        bottom:0;
        height:100%;
        left:-230px;
        width:250px;
        overflow:hidden;
        -webkit-transition:left .05s linear;
        transition:left .05s linear;
        -webkit-transform:translateZ(0) scale(1,1);
        z-index:1000;
    }

    .menu-entry{
        text-align:right;
    }
    </style>

    <script  type='text/javascript'>
        window.calendar = null;
        const patapouf = 51; //DEBUG

        function cycloaddevent(evt, cal, col){
            eventsKeys = Object.keys(evt);
//~ console.log(events[eventsKeys[1]].RRULE); //DEBUG
            for (var i = 0; i < eventsKeys.length; i++)
                if(events[eventsKeys[i]].RRULE)
                    cal.addEvent({ title: evt[eventsKeys[i]].SUMMARY,
                                   rrule: evt[eventsKeys[i]].RRULE,
                                   color:col });
                else
                    cal.addEvent({ title: evt[eventsKeys[i]].SUMMARY,
                                   start: evt[eventsKeys[i]].DTSTART,
                                   end: evt[eventsKeys[i]].DTEND,
                                   color:col });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            window.calendar = new FullCalendar.Calendar(calendarEl, {
                firstDay: 1,    //Monday
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

            if (false){ //TEST
                //~ window.calendar.addEvent({ title: 'recurring test', rrule: 'DTSTART:20220425T140000Z\nRRULE:FREQ=WEEKLY;COUNT=11;BYDAY=MO\nEXDATE:20220502T140000Z' , color:'red' }); 
                //~ window.calendar.addEvent({ title: 'recurring test', rrule: 'DTSTART:20220425T140000Z\nFREQ=WEEKLY;COUNT=11;BYDAY=MO' , color:'red' }); 
            }

            var mainmenuEl = document.getElementById('main-menu');

            /* UNIV NCA */
            <?php
                $icalobj = cyclo_getAgendaUnivCA();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "red");
                mainmenuEl.innerHTML = mainmenuEl.innerHTML + "<div class=menu-entry>UnivNCA <span style=\"color: red ; font-size : xx-large\">&#xEFFA</span></div>"
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
                mainmenuEl.innerHTML = mainmenuEl.innerHTML + "<div class=menu-entry>BV Moulins<span style=\"color: blue ; font-size : xx-large\">&#xEFFA</span></div>"

            }

            /* DV Monjoye */
            <?php
                $icalobj = cyclo_getAgendaMontjoye();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "green");
                mainmenuEl.innerHTML = mainmenuEl.innerHTML + "<div class=menu-entry>DV Monjoye <span style=\"color: green ; font-size : xx-large\">&#xEFFA</span></div>"

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
                mainmenuEl.innerHTML = mainmenuEl.innerHTML + "<div class=menu-entry>NissaBicy22 <span style=\"color: purple ; font-size : xx-large\">&#xEFFA</span></div>"

            }

            /* Autres 22 */
            <?php
                $icalobj = cyclo_getAgendaAutres();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
//~ $calendar_data = cyclo_dumpCalendar($icalobj); //DEBUG
            ?>
            {
//~ datadump = <?php echo json_encode($calendar_data); ?>; //DEBUG
//~ console.log(datadump); //DEBUG
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "orange");
                mainmenuEl.innerHTML = mainmenuEl.innerHTML + "<div class=menu-entry>Autres22 <span style=\"color: orange ; font-size : xx-large\">&#xEFFA</span></div>"
            }

            window.calendar.render();

            //~ document.getElementById('next').addEventListener('click', function() {
                //~ calendar.next(); // call method
            //~ });
        });

    </script>
  </head>
  <body>
      <nav id="main-menu"></nav>
    <div id='calendar-container'>
        <div id='calendar'></div>
    </div>
    <script type='text/javascript'> 
        console.log(patapouf);
    </script>
  </body>
</html>
