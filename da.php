<?php
/**
 * da.php - physically cyclotrope.net/agen/da.php
 *
 * @package     ical2ouaib
 * @author      d-j-a-y <https://cyclotrope.net>
 * @copyright   Copyright (C) 2022 by jb aka d-j-a-y
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/d-j-a-y/ical2ouaib
 */

/**
 * Parse cyclotrope iCalendars and display somehow
 *
 */

/**
 * TODO
 * dot utf
 * multiple event sources /https://stackoverflow.com/questions/20071119/fullcalendar-and-multiple-event-sources
 * Use fullcalendar keywords -title.start...- directly in cyclodalib???
 * Remplace getAgendaXXX fnct by table [name/url/color]
 * Remove some locale from fullcalen???
 * dependencies version
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

function cyclo_getAgendaVieDeLasso()
{
    $icalfile = "https://framagenda.org/remote.php/dav/public-calendars/a9X2SY7coQ3LsS28?export";
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
    <link href='./css/da.css' rel='stylesheet' />

    <script src='./js/fullcalendar/main.js'></script>
    <script src='./js/rrule/rrule-tz.min.js'></script>
    <script src='./js/fullcalendar/rruleconnector/main.global.min.js'></script>

    <style>
    </style>

    <script  type='text/javascript'>
        window.calendar = null;
        const patapouf = 51; //DEBUG

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
                },
                eventClick: function(info) { //https://fullcalendar.io/docs/eventClick
                    cyclopopupeventinfo(info.event.title,
                                        info.event.start.getHours()+"h"+String(info.event.start.getMinutes()).padStart(2, '0') ,
                                        info.event.end.getHours()+"h"+String(info.event.end.getMinutes()).padStart(2, '0') ,
                                        info.event.extendedProps.description);
//~ alert(info.event.title + '\n\n' + info.event.extendedProps.description); //DEBUG

//~ alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
//~ alert('View: ' + info.view.type);

                    // change the border color just for fun
                    //~ info.el.style.borderColor = 'red';
                }
            });

            if (false){ //TEST
                //~ window.calendar.addEvent({ title: 'recurring test', rrule: 'DTSTART:20220425T140000Z\nRRULE:FREQ=WEEKLY;COUNT=11;BYDAY=MO\nEXDATE:20220502T140000Z' , color:'red' }); 
                //~ window.calendar.addEvent({ title: 'recurring test', rrule: 'DTSTART:20220425T140000Z\nFREQ=WEEKLY;COUNT=11;BYDAY=MO' , color:'red' }); 
            }

            var mainmenuEl = document.getElementById('main-menu');

            /* VIE DE LASSO */
            <?php
                $icalobj = cyclo_getAgendaVieDeLasso();
                //~ echo "<p>Nombre d'évènements trouvé : " . $icalobj->countEvents() . "</p>";
                $calendar_table = cyclo_getEvents($icalobj);
            ?>
            {
                events = <?php echo json_encode($calendar_table); ?>;
                cycloaddevent(events, window.calendar, "Khaki");
                mainmenuEl.innerHTML = mainmenuEl.innerHTML + "<div class=menu-entry>VieDeLasso <span style=\"color: Khaki ; font-size : xx-large\">&#xEFFA</span></div>"
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
    <!-- Event popup info, hidden -->
    <div id="evtPopup" class="modal">
      <div class="modal-content">
        <span class="closemodal">&times;</span>
        <h3 id="myModalTitle">Some text in the Modal..</h3>
        <p id="myModalText">Some text in the Modal..</p>
      </div>
    </div>
    <!-- Agenda side bar global info, leftfold -->
    <nav id="main-menu"></nav>
    <!-- Main stuff feed by cycloaddevent -->
    <div id='calendar-container'>
        <div id='calendar'></div>
    </div>

    <script type='text/javascript'> 
        console.log(patapouf); //DEBUG
    </script>

    <script src='./js/da.js'></script>
  </body>
</html>
