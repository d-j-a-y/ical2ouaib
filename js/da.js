// Get the modal
var modal = document.getElementById("evtPopup");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("closemodal")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

/* display modal event popup info : H3 + P */
function cyclopopupeventinfo(title, start, end, info){
  modal.style.display = "block";
  element = document.getElementById("myModalTitle");
  element.innerText = title;
  element = document.getElementById("myModalText");
  element.innerText = "De " + start + " à " + end + "\n\n" + info;
}

/* add new event to the calendar object */
function cycloaddevent(evt, cal, col){
    eventsKeys = Object.keys(evt);
//~ console.log(events[eventsKeys[1]].RRULE); //DEBUG
    for (var i = 0; i < eventsKeys.length; i++)
        if(events[eventsKeys[i]].RRULE)
            cal.addEvent({ title: evt[eventsKeys[i]].SUMMARY,
                           rrule: evt[eventsKeys[i]].RRULE,
                           description: 'Quoi:' + evt[eventsKeys[i]].DESCRIPTION + '\nOu:' + evt[eventsKeys[i]].LOCATION,
                           color:col });
        else
            cal.addEvent({ title: evt[eventsKeys[i]].SUMMARY,
                           start: evt[eventsKeys[i]].DTSTART,
                           end: evt[eventsKeys[i]].DTEND,
                           description: 'Quoi:' + evt[eventsKeys[i]].DESCRIPTION + '\nOu:' + evt[eventsKeys[i]].LOCATION,
                           color:col });
}



