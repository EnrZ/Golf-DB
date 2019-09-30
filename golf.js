function enterText( box ) {
  var patt = / .*$/;
  var name = box;
  name = box.replace( patt, "" );
  if ( document.getElementById( name + "Search" ).value == box ) {
    document.getElementById( name + "Search" ).value = '';
  }
}

function leaveBox( box ) {
  var patt = / .*$/;
  var temp = box;
  name = box.replace( patt, "" );
  if ( document.getElementById( name + "Search" ).value == '' ) {
    document.getElementById( name + "Search" ).value = box;   
  }
}

function roundUpdateButton() {
  var elem = document.getElementsByClassName( "roundUpdate" );
  for ( var i=0; i<elem.length; i++ ) {
    elem[i].style.visibility = 'visible';
  }

  document.getElementById( "updateButton" ).innerHTML = "<td><button type=\"button\" onclick=\"cancelButton();\" >Cancel Update</button></td>";
}

function cancelButton() {
  var elem = document.getElementsByClassName( "roundUpdate" );
  for ( var i=0; i<elem.length; i++ ) {
    elem[i].style.visibility = 'hidden';
  }

  document.getElementById( "updateButton" ).innerHTML = "<td><button type=\"button\" onclick=\"roundUpdateButton();\" >Update Round</button></td>";
}

function courseSize(holes) {
  var elem = document.getElementsByClassName( "backNine" );
  if ( holes == 9 ) {
    for ( var i=0; i<elem.length; i++ ) {
      elem[i].style.visibility = 'hidden';
    }
  }
  else {
    for ( var i=0; i<elem.length; i++ ) {
      elem[i].style.visibility = 'visible';
    } 
  }
}

function validateLocation() {
   var valid = true;

   var locElm = document.getElementById("course_location");
   var locVal = locElm.value;
   
   if (locVal == "") {
      locElm.style.background = "#FFFFDB";
      displayError("Please enter a location in the following format: City, State", "loc");
      document.getElementById("submit").disabled = true;
      locElm.focus();
      valid = false;
   } else 
      if (!locVal.match(/^[A-Za-z .]*[,]\s(([A-Z]{2})|(([A-Za-z .]*)([,]\s[A-Za-z .]*)?))$/)) {
         locElm.style.background = "#FFFFDB";
         displayError("Location invalid! Please use the following format: City, State", "loc");
         document.getElementById("submit").disabled = true;
         valid = false;
      }
   else {
      displayError("", "loc");
      locElm.style.background = "#BAEE86";
      document.getElementById("submit").disabled = false;
   }
   return valid   
}

function validatePar(name) {
   var valid = true;
   var parElm = document.getElementById(name);
   var parVal = parElm.value;
   
   if (!parVal.match(/^[3-5]$/)) {
      parElm.style.background = "#FFFFDB";
      displayError("Invalid Par!", "par");
      document.getElementById("submit").disabled = true;
      valid = false;
   } else {
      displayError("", "par");
      parElm.style.background = "#BAEE86"
      document.getElementById("submit").disabled = false;
   }
   return valid;
}

function validatePlayerName() {
   var valid = true;

   var nameElm = document.getElementById("player_name");
   var nameVal = nameElm.value;

   if (!nameVal.match(/^[A-Z][a-z]*\s[A-Z][a-z]*$/) || nameVal == 'Player Name') {
      nameElm.style.background = "#FFFFDB";
      displayError("Player name in invalid format! Please use following format: First Last.", "name");
      document.getElementById("submit").disabled = true;
      valid = false;
   } else {
      displayError("", "name");
      nameElm.style.background = "#BAEE86";
      document.getElementById("submit").disabled = false;
   }
   return valid   
}

function validateDate() {
   var valid = true;
   var dateElm = document.getElementById("round_date");
   var dateVal = dateElm.value;

   if (!dateVal.match(/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/)) {  //found online
      document.getElementById("submit").disabled = true;
      dateElm.style.background = "#FFFFDB";
      displayError("Date Invalid!", "date");
      valid = false;
   } else {
      document.getElementById("submit").disabled = false;
      dateElm.style.background = "#BAEE86";
      displayError("", "date");
   }
   return valid;
}
     
function validateScores(id) {
   var valid = true;
   var scoreElm = document.getElementById(id);
   var scoreVal = scoreElm.value;
   
   if (scoreVal <= 0) {
      scoreElm.style.background = "#FFFFDB";
      displayError("Invalid Score! Score must be a positive number!", "score");
      document.getElementById("submit").disabled = true;
      valid = false;
   } else {
      displayError("", "score");
      scoreElm.style.background = "#BAEE86";
      document.getElementById("submit").disabled = false;
   }
   return valid;
}

function displayError(msg, err) {
   var errorElm = document.getElementById(err + "Error");
 
   errorElm.innerText = msg;
} 
