//created global vairables
let date = new Date();
let year = date.getFullYear();
let month = date.getMonth();

let meetingsOfShepherd = {}; //store all shapred meetings

let defaultWorkingDaysOfShephred = {}; //store current shapred Default Working Days

let default_working_hours_start = 0; //store current shapred default working hours start
let default_working_min_start = 0;

let default_working_hours_end = 0; //store current shapred default working hours end
let default_working_min_end = 0;

let defaultShephredTimeZone = "";
let defaultUserTimeZone = "";

let default_user_start_time_current_day = "";
let defaultUserCurrentDatetime = "";
let startDefaultWorkingHourFull = "";
let endDefaultWorkingHourFull = "";

const day = document.querySelector(".calendar-dates");
const currdate = document.querySelector(".calendar-current-date");
const prenexIcons = document.querySelectorAll(".calendar-navigation span");
// Array of month names
const months = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

/*################################################################################################*/
/*Add zero before single integer number*/
/*################################################################################################*/
function addLeadingZeroIfSingleDigit(number) {
  const strNumber = String(number);
  // Check if the number is a single digit
  if (strNumber.length === 1) {
    // Add a leading zero for single-digit numbers
    return "0" + strNumber;
  } else {
    // If the number is already two digits or more, return it as is
    return strNumber;
  }
}

/*################################################################################################*/
// Function to format time as HH:mm AM/PM
/*################################################################################################*/
function ourFormatTime(datess) {
  var hours = datess.getHours();
  var minutes = datess.getMinutes();

  var ampm = hours >= 12 ? "PM" : "AM";
  hours = hours % 12;
  hours = hours ? hours : 12; // The hour '0' should be '12'

  return (
    (hours < 10 ? "0" : "") +
    hours +
    ":" +
    (minutes < 10 ? "0" : "") +
    minutes +
    " " +
    ampm
  );
}

/*################################################################################################*/
// Function to format Date (Y-m-d)
/*################################################################################################*/
function formattedDatee() {
  var currentDt = new Date();
  // Extract year, month, and day components
  var yearr = currentDt.getFullYear();
  var monthh = ("0" + (currentDt.getMonth() + 1)).slice(-2); // Months are zero-based
  var dayy = ("0" + currentDt.getDate()).slice(-2);

  // Form the 'YYYY-MM-DD' formatted string
  return yearr + "-" + monthh + "-" + dayy;
}

/*################################################################################################*/
// Function to do not display timeslot section
/*################################################################################################*/
function dontShowTimeSlots(shephredID) {
  var timeSlotsList = document.getElementById("time-slots");
  timeSlotsList.innerHTML = "Shepherd Not Available!";
}

/*################################################################################################*/
// Function to add meeting time duration
/*################################################################################################*/

function add30Minutes(timeString, time_duration) {
  // Parse the input time string
  var parsedTime = new Date("2024-01-01T" + timeString);

  var add30 = new Date("2024-01-01T" + timeString);
  // Add 30 minutes
  add30.setMinutes(add30.getMinutes() + 0);
  // Format the time in HH:mm AM/PM
  var str = add30.toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });

  // Add 30 minutes
  parsedTime.setMinutes(parsedTime.getMinutes() + time_duration);

  // Format the time in HH:mm AM/PM
  var formattedTimes = parsedTime.toLocaleTimeString([], {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });

  return `${str} - ${formattedTimes}`;
}

/*################################################################################################*/
// Function to convert time into 12hours format
/*################################################################################################*/
function convertTo12HourFormat(time24) {
  // Extract hours and minutes
  var [hours, minutes] = time24.split(":");

  // Convert to 12-hour format
  var period = hours >= 12 ? "PM" : "AM";
  hours = hours % 12 || 12; // Handle midnight (0) as 12

  // Pad single-digit hours and minutes with leading zeros
  hours = String(hours).padStart(2, "0");
  minutes = String(minutes).padStart(2, "0");

  // Combine and return the result
  return hours + ":" + minutes + " " + period;
}

/*################################################################################################*/
/*Compare two Times Slot function*/
/*################################################################################################*/
function isTimeLessThan(time1, time2) {
  // Parse the times into hours and minutes
  const [hours1, minutes1] = time1.split(":").map(Number);
  const [hours2, minutes2] = time2.split(":").map(Number);

  // Convert the times to total minutes
  const totalMinutes1 = hours1 * 60 + minutes1;
  const totalMinutes2 = hours2 * 60 + minutes2;

  // Check if totalMinutes1 is less than totalMinutes2
  return totalMinutes1 <= totalMinutes2;
}
/*################################################################################################*/
/*Genrate Time Slot function*/
/*################################################################################################*/
function generateTimeSlots(selectedDate, time_duration, shephredID) {
  debugger;
  let bookedTimeSlots = [];
  if (meetingsOfShepherd[shephredID] != "No Bookings") {
    var meetingsForDate = getMeetingsByDate(meetingsOfShepherd, shephredID); //get all meetings
    if (meetingsForDate != "novalue") {
      var res = getAllBookedTimeslotOfShephredAccordingToDateIndex(
        selectedDate,
        meetingsForDate
      );
      if (res) {
        // Loop through the array using forEach
        res.forEach(function (cvalue) {
          bookedTimeSlots.push(add30Minutes(cvalue, time_duration));
        });
      }
    }
  }
  var timeSlots = [];
  // Set the start time to 9:00 AM
  var startTime = new Date();
  var formmtdate = formattedDatee();
  if (selectedDate === formmtdate) {
    //compare current datetime
    var currentTimeUserTimezone = new Date(defaultUserCurrentDatetime);
    // Get the current hours (0-23) and minutes (0-59)
    var currentHours = currentTimeUserTimezone.getHours();
    var currentMinutes = currentTimeUserTimezone.getMinutes();
    //var splitims = default_user_start_time_current_day.split('&');
    //var hours =  splitims[0];
    //var min =  splitims[1];
    //console.log('defaultUserCurrentDatetime',defaultUserCurrentDatetime,'startDefaultWorkingHourFull',startDefaultWorkingHourFull,'endDefaultWorkingHourFull',endDefaultWorkingHourFull);
    //console.log(startTime);
    // Get the hour from the startTime
    var shepherdDefaultTimezone = new Date();
    shepherdDefaultTimezone.setHours(
      default_working_hours_start,
      default_working_min_start,
      0,
      0
    );
    var shphrdHours = shepherdDefaultTimezone.getHours();
    var shphrdmins = shepherdDefaultTimezone.getMinutes();
    //var startSlotTime = shphrdHours;
    // Compare the hours
    if (currentTimeUserTimezone > shepherdDefaultTimezone) {
      console.log("startHour is earlier than otherHour");
      // Round current time to the next 30-minute interval
      shphrdHours = currentHours;
      shphrdmins = currentMinutes;
    }

    //console.log(startTime);
    (currentMinutesqq = Math.ceil(shphrdmins / time_duration) * time_duration),
      0;
    startTime.setHours(shphrdHours, currentMinutesqq, 0, 0);

    // Round current time to the next 30-minute interval
    //currentMinutes = Math.ceil(currentMinutes / time_duration) * time_duration, 0;
    //startTime.setHours(currentHours, currentMinutes, 0, 0);
  } else {
    startTime.setHours(
      default_working_hours_start,
      default_working_min_start,
      0,
      0
    );
  }

  // Set the end time to 6:00 PM
  var endTime = new Date();
  endTime.setHours(default_working_hours_end, default_working_min_end, 0, 0);

  // Increment the current time by 30 minutes until it exceeds the end time
  for (
    var currentTime = new Date(startTime);
    currentTime < endTime;
    currentTime.setMinutes(currentTime.getMinutes() + time_duration)
  ) {
    //added extra minutes

    // Format the current time and push it to the array
    var formattedTime = currentTime.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    });

    // Get the hours and minutes from the Date object
    let getHour = addLeadingZeroIfSingleDigit(currentTime.getHours());
    let getMinu = addLeadingZeroIfSingleDigit(currentTime.getMinutes());

    // Convert the time string to a Date object
    let update_dte = new Date(`${selectedDate}T${getHour}:${getMinu}:00`);

    // Add extra minutes
    update_dte.setMinutes(update_dte.getMinutes() + time_duration); //added extra minutes

    let getHours = addLeadingZeroIfSingleDigit(update_dte.getHours());
    let getMinus = addLeadingZeroIfSingleDigit(update_dte.getMinutes());

    // Format the result
    let endTimess = update_dte.toLocaleTimeString([], {
      hour: "2-digit",
      minute: "2-digit",
    });

    let time1 = getHours + ":" + getMinus;
    let time2 = default_working_hours_end + ":" + default_working_min_end;

    //do not exced end time limit
    if (isTimeLessThan(time1, time2)) {
      timeSlots.push(`${formattedTime} - ${endTimess}`);
    }
  }

  // Use filter to create a new array without the specified value
  const newArray = timeSlots.filter(
    (value) => !bookedTimeSlots.includes(value)
  );

  return newArray;
}

/*################################################################################################*/
// Function to show time slots for the selected date
/*################################################################################################*/
function showTimeSlots(selectedDate, shephredID, currentobjed) {
debugger;
  console.log(selectedDate, shephredID, currentobjed);
  var selectedDateElement = document.getElementById("selected-date");
  var timeSlotsContainer = document.getElementById("time-slot-container");
  var timeSlotsList = document.getElementById("time-slots");
  let time_durations = document.getElementById("time_duration").innerHTML;

  if (currentobjed) {
    jQuery(".calendar-dates li.active").removeClass("active");
    jQuery(currentobjed).addClass("active"); // adding active class
  }
  let time_duration = parseInt(time_durations, 10);


  // Creating a date object
  var myDate = new Date(selectedDate+'T00:00:00'); // yyyy-mm-dd

  // Options for formatting the date
  var options = { weekday: "long", month: "long", day: "numeric" };

  // Convert the date to a string with the desired format
  var formattedDate = myDate.toLocaleDateString("en-US", options);

// Get user-selected timezone from dropdown
  // var userTimezone = document.getElementById("user_timezone_option").value;
  // // Parse selectedDate (yyyy-mm-dd) into parts
  // var parts = selectedDate.split("-");
  // var myDate = new Date(parts[0], parts[1] - 1, parts[2]); // Local midnight
  // // Format date according to user-selected timezone
  // var options = { 
  //   weekday: "long", 
  //   month: "long", 
  //   day: "numeric", 
  //   timeZone: userTimezone 
  // };
  // var formattedDate = new Intl.DateTimeFormat("en-US", options).format(myDate);


  // Set the selected date in the header
  selectedDateElement.textContent = formattedDate;
  // Generate example time slots
  var timeSlots = generateTimeSlots(selectedDate, time_duration, shephredID); //yyyy-mm-dd

  // Clear previous time slots
  timeSlotsList.innerHTML = "";

  // Populate time slots
  timeSlots.forEach(function (slot) {
    var li = document.createElement("li");
    li.textContent = slot;
    // Set the class name for the new list item
    li.className = "cus_class";
    // Set the data attribute for the selected date
    li.setAttribute("selectedDate", selectedDate);
    // Set the data attribute for the selected time
    li.setAttribute("selectedTime", slot);
    // Set the data attribute for the new list item
    li.setAttribute("data-bs-toggle", "modal");
    // Add the handleClick function to the click event
    li.addEventListener("click", function () {
      // Call your function with parameters
      activateLiItem(selectedDate, slot, shephredID, this);
    });
    // Add the handleClick function to the click event
    li.setAttribute("data-bs-target", "#myCustomModal");
    // Add the handleClick function to the click event
    li.setAttribute("shephredID", shephredID);

    timeSlotsList.appendChild(li);
  });

  // Show the time slots container
  timeSlotsContainer.style.display = "block";
}
/*################################################################################################*/
// Function to generate the calendar on page load
/*################################################################################################*/
const renderCalenderfun = (shephredID) => {
  // Get the first day of the month
  let dayone = new Date(year, month, 1).getDay();

  // Get the last date of the month
  let lastdate = new Date(year, month + 1, 0).getDate();

  // Get the day of the last date of the month
  let dayend = new Date(year, month, lastdate).getDay();

  // Get the last date of the previous month
  let monthlastdate = new Date(year, month, 0).getDate();

  // Variable to store the generated calendar HTML
  let lit = "";

  const curntdate = new Date();

  // Loop to add the last dates of the previous month
  for (let i = dayone; i > 0; i--) {
    lit += `<li class="inactive" >${monthlastdate - i + 1}</li>`;
  }

  // Loop to add the dates of the current month
  for (let i = 1; i <= lastdate; i++) {
    let todate = new Date(year, month, i);

    // Check if the current date is today
    let isToday = "";

    let lmon = month + 1; //get month from 1/january

    let dat = new Date(year + "-" + lmon + "-" + i);

    //let dat = new Date(year, month, i); // ✅ Local date, no timezone bug

    if (
      i === date.getDate() &&
      month === new Date().getMonth() &&
      year === new Date().getFullYear()
    ) {
      let isvalyrexist = false;
      for (const keys in defaultWorkingDaysOfShephred) {
        if (defaultWorkingDaysOfShephred.hasOwnProperty(keys)) {
          const values = parseInt(keys);
          if (dat.getDay() === values) {
            isvalyrexist = true;
          }
        }
      }
      if (isvalyrexist) {
        isToday = "currentDayActive active";
      } else {
        isToday = "isTodayButNotShephredOff";
      }
    } else if (todate < curntdate) {
      isToday = "inactive";
    } else {
      let isValueFound = false;
      for (const keys in defaultWorkingDaysOfShephred) {
        if (defaultWorkingDaysOfShephred.hasOwnProperty(keys)) {
          const values = parseInt(keys);
          if (dat.getDay() === values) {
            isValueFound = true;
            break; // Exit the inner loop if the value is found
          }
        }
      }
      if (!isValueFound) {
        isToday = "inactive";
      } else {
        isToday = "availableDay";
      }
    }

    let newfuns = "";
    if (isToday !== "inactive" && isToday !== "isTodayButNotShephredOff") {
      // newfuns = `onClick="showTimeSlots('${
      //   year +
      //   "-" +
      //   addLeadingZeroIfSingleDigit(lmon) +
      //   "-" +
      //   addLeadingZeroIfSingleDigit(i)
      // }',${shephredID},this)"`;
      newfuns = `onClick="showTimeSlots('${year}-${addLeadingZeroIfSingleDigit(lmon)}-${addLeadingZeroIfSingleDigit(i)}',${shephredID},this)"`;






    } else {
      newfuns = `onClick="dontShowTimeSlots(${shephredID})"`;
    }

    lit += `<li class="${isToday}" ${newfuns}>${i}</li>`;
  }

  // Loop to add the first dates of the next month
  for (let i = dayend; i < 6; i++) {
    lit += `<li class="inactive">${i - dayend + 1}</li>`;
  }

  // Update the text of the current date element
  // with the formatted current month and year
  currdate.innerText = `${months[month]} ${year}`;

  // update the HTML of the dates element
  // with the generated calendar
  day.innerHTML = lit;
};

/*################################################################################################*/
// Attach a click event listener to each icon
/*################################################################################################*/
prenexIcons.forEach((icon) => {
  // When an icon is clicked
  icon.addEventListener("click", () => {
    // Check if the icon is "calendar-prev"
    // or "calendar-next"
    month = icon.id === "calendar-prev" ? month - 1 : month + 1;
    // Check if the month is out of range
    if (month < 0 || month > 11) {
      // Set the date to the first day of the
      // month with the new year
      date = new Date(year, month, new Date().getDate());

      // Set the year to the new year
      year = date.getFullYear();

      // Set the month to the new month
      month = date.getMonth();
    } else {
      // Set the date to the current date
      date = new Date();
    }
    // Call the renderCalenderfun function to
    // update the calendar display
    let selectedItemshpred = document.querySelector(".selected-shephred-item");
    let shphredcID = selectedItemshpred.getAttribute("datashephredid");
    renderCalenderfun(shphredcID);
  });
});

/*################################################################################################*/
// Create a new div element and add to just after body tag
/*################################################################################################*/

const newDiv = document.createElement("div");
// Set some attributes and content for the new div (optional)
newDiv.id = "ajax_loading_imgs";
// Insert the new div after the body tag
document.body.insertAdjacentElement("afterbegin", newDiv);

/*################################################################################################*/
// function will get called on page load, shepherd change - for current date only
/*################################################################################################*/
//jQuery( window ).on( "load", function() {
jQuery(document).ready(function (jQuery) {
  let selectedItemshpred = document.querySelector(".selected-shephred-item");
  let shphredcID = selectedItemshpred.getAttribute("datashephredid");
  loadCalendarAndTimeSlotsByShepherd(shphredcID);
});

/*################################################################################################*/
// function for toggle shepherd dropdown show or hide
/*################################################################################################*/
function toggleDropdown() {
  var dropdownContent = document.querySelector(".shephrddropdown-content");
  dropdownContent.style.display =
    dropdownContent.style.display === "block" ? "none" : "block";
}
/*################################################################################################*/
// function for onchange select shepherd
/*################################################################################################*/
function selectShephredOption(option, imageUrl, ids) {
  var selectedItem = document.querySelector(".selected-shephred-item");
  //var selectedImage = selectedItem.querySelector('img');
  var selectedText = selectedItem.querySelector("span");
  //selectedImage.src = imageUrl;
  selectedText.textContent = option;
  var datatoggle = selectedItem.getAttribute("datashephredid");
  //selectedItem.setAttribute('modal');

  selectedItem.setAttribute("datashephredid", ids);
  //renderCalenderfun(ids);
  toggleDropdown();
  loadCalendarAndTimeSlotsByShepherd(ids);
}

/*################################################################################################*/
// compare user selected timezone to shephreed timezone and render it in available shepherd dropdown
/*################################################################################################*/
function user_timezone_option() {
  let selected_user_timezone = jQuery("#user_timezone_option").val();
  // AJAX request
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: custom_ajax_object.ajax_url,
    data: {
      action: "getUserTimeZoneShephredAjaxAction", // AJAX action name
      selected_user_timezone: selected_user_timezone,
    },
    success: function (response) {
      // Handle the AJAX response
      jQuery(".shephrddropdown-content").empty();
      let newItem = "";
      let firstID = null;
      let firstindexvalue = true;
      jQuery.each(response, function (index, item) {
        // Output each item
        if (firstindexvalue) {
          jQuery("#shephared-image-block").html(
            `<img class="shephared-img-src" src="${item.thumbnail_url}" />`
          );
          jQuery("#selected-shephred-namd-div").attr("datashephredid", item.id);
          jQuery("#sheparedNameSpan").text(item.title);

          defaultUserTimeZone = jQuery("#user_timezone_option").val();
          firstID = item.id;
        }
        newItem = jQuery(
          `<li onclick="selectShephredOption('${item.title}','${item.thumbnail_url}',${item.id})"><img src="${item.thumbnail_url}" /> ${item.title} </li>`
        ); // Create a new li element with text
        jQuery(".shephrddropdown-content").append(newItem);
        firstindexvalue = false;
      });
      //display selcted shepherd on shepherd id
      commonFunctionOnLoadAndOnChangeShephred(firstID);
    },
    error: function (error) {
      console.log(error);
    },
  });
}

/*################################################################################################*/
// hide popup on cros button cliecked
/*################################################################################################*/
jQuery(document).ready(function () {
  // Attach a click event to the button with id "hideButton"
  jQuery("#click_btn_action_welcome_sec").on("click", function () {
    console.log("ffffffffffffffffff");
    // Hide the section with id "mySection" on click
    jQuery("#ajax_loading_imgs").show();
    let hide_welcome_section = "hide_welcome_section";
    $.ajax({
      url: custom_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "hide_welcome_section_update_user_meta_ajax",
        hide_welcome_section: hide_welcome_section,
      },
      success: function (response) {
        console.log(response);
        jQuery(".cutom-container-width").hide();
        jQuery("#ajax_loading_imgs").hide();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("AJAX error:", textStatus, errorThrown);
      },
    });
  });
});

/*################################################################################################*/
// load calender and time slot according to select shephred ID
/*################################################################################################*/
function getAllBookedTimeslotOfShephredAccordingToDateIndex(
  setdate,
  meetingsForDate
) {
  // Object to store dates and associated times
  var dateTimesObject = {};

  // Loop through each date-time string
  meetingsForDate.forEach(function (dateTimeString) {
    // Split the date and time
    var [dat, tim] = dateTimeString.split("T");

    // Check if the date is already in the object
    if (!dateTimesObject[dat]) {
      // If not, create an array for the date
      dateTimesObject[dat] = [];
    }
    // Add the time to the array associated with the date
    dateTimesObject[dat].push(tim);
  });

  for (var key in dateTimesObject) {
    if (dateTimesObject.hasOwnProperty(key)) {
      if (key == setdate) {
        return dateTimesObject[key];
      }
    }
  }
}
/*################################################################################################*/
//get current shephred idBookedtime slot from all time slot
/*################################################################################################*/
function getMeetingsByDate(meetingsOfShepherd, shepherdId) {
  let shepherdOjbject = {};
  // Loop through the outer object
  let isvalue = false;
  for (var key in meetingsOfShepherd) {
    if (meetingsOfShepherd.hasOwnProperty(key)) {
      if (key == shepherdId) {
        shepherdOjbject = meetingsOfShepherd[key];
        isvalue = true;
      }
    }
  }
  if (isvalue) {
    return shepherdOjbject;
  } else {
    return (shepherdOjbject = ["novalue"]);
  }
}

/*################################################################################################*/
// function for convert day string to respective number
/*################################################################################################*/
function getAllDaysNumberObjec(dayString) {
  // Split the string into an array using ";" as the separator
  const dayArray = dayString.split(";");

  // Create an object to store key-value pairs
  const dayObject = {};

  // Loop through the array and set each day as both key and value in the object
  for (let i = 0; i < dayArray.length; i++) {
    const day = dayArray[i];

    if (dayArray[i] === "Monday") {
      dayObject[1] = day;
    } else if (dayArray[i] === "Tuesday") {
      dayObject[2] = day;
    } else if (dayArray[i] === "Wednesday") {
      dayObject[3] = day;
    } else if (dayArray[i] === "Thursday") {
      dayObject[4] = day;
    } else if (dayArray[i] === "Friday") {
      dayObject[5] = day;
    } else if (dayArray[i] === "Saturday") {
      dayObject[6] = day;
    } else {
      dayObject[0] = day;
    }
  }
  return dayObject;
}

/*################################################################################################*/
// function for on page load and onchange shepherd
/*################################################################################################*/
function commonFunctionOnLoadAndOnChangeShephred(shepherdId) {
  defaultUserTimeZone = jQuery("#user_timezone_option").val();

  // console.log(
  //   "defaultUserTimeZone commonFunctionOnLoadAndOnChangeShephred fun",
  //   defaultUserTimeZone
  // );

  jQuery("#ajax_loading_imgs").show();

  jQuery.ajax({
    type: "post",
    dataType: "json",
    url: custom_ajax_object.ajax_url,
    data: {
      action: "getShephredInfoFunction",
      shepherdId: shepherdId,
      defaultUserTimeZone: defaultUserTimeZone,
    },
    success: function (response) {
      console.log("getShephredInfoFunction----------------------", response);
      meetingsOfShepherd = response.allBookedShephredMeeting;

      defaultWorkingDaysOfShephred = getAllDaysNumberObjec(
        response.default_working_days
      );
      let default_working_hours_starts =
        response.default_working_hours_start.split(":");
      let default_working_hours_ends =
        response.default_working_hours_end.split(":");

      default_working_hours_start = default_working_hours_starts[0];
      default_working_min_start = default_working_hours_starts[1];

      default_working_hours_end = default_working_hours_ends[0];
      default_working_min_end = default_working_hours_ends[1];

      default_user_start_time_current_day =
        response.default_user_start_time_current_day;
      defaultUserCurrentDatetime = response.defaultUserCurrentDatetime;
      startDefaultWorkingHourFull = response.default_working_hours_start;
      endDefaultWorkingHourFull = response.default_working_hours_end;

      console.log("getShephredInfoFunction ajax", response);
      console.log("default_working_hours_start", default_working_hours_start);
      console.log("default_working_hours_end", default_working_hours_end);
      console.log(
        "default_user_start_time_current_day",
        default_user_start_time_current_day
      );
      console.log("defaultUserCurrentDatetime", defaultUserCurrentDatetime);

      jQuery("#shephared-image-block").html(
        `<img class="shephared-img-src" src="${response.featured_image_url}" />`
      );

      jQuery("#shephreddetailsBlock").html(
        `<h3 class="meeting-with-div">Meeting with ${response.title} at Broker4Less</h3>
				<div class="durartion"><i class="fa-solid fa-clock i-pding" aria-hidden="true"></i> <span id="time_duration">${response.duration_of_meeting} min</span></div>
				<div style="display: flex;">
        <i class="fa-solid fa-video i-pding" aria-hidden="true" style="margin-top: 5px;"></i>
        <span id="time_duration">${response.sub_title && response.sub_title.trim() !== '' ? response.sub_title : 'Web conferencing details provided upon confirmation'}</span>
        </div>`
      );

      defaultShephredTimeZone = response.admin_shephred_timezone;
      defaultUserTimeZone = jQuery("#user_timezone_option").val();

      console.log("defaultShephredTimeZone", defaultShephredTimeZone);
      console.log("defaultUserTimeZone", defaultUserTimeZone);
      console.log("response", response);

      //render calender function
      renderCalenderfun(shepherdId); //render calender

      const curntDate = response.todayDateTime.split("T");

      let isValueFound = false;
      for (const keys in defaultWorkingDaysOfShephred) {
        if (defaultWorkingDaysOfShephred.hasOwnProperty(keys)) {
          const currentDatee = new Date();
          const currentDayIndex = currentDatee.getDay();
          const values = parseInt(keys);
          if (currentDayIndex === values) {
            isValueFound = true;
            break; // Exit the inner loop if the value is found
          }
        }
      }
      if (isValueFound) {
        showTimeSlots(curntDate[0], shepherdId);
      } else {
        jQuery("#time-slot-container").show();
        var timeSlotsList = document.getElementById("time-slots");
        timeSlotsList.innerHTML = "Shepherd Not Available!";
      }
      jQuery("#ajax_loading_imgs").hide();
    },
    error: function (error) {
      jQuery("#ajax_loading_imgs").hide();
      console.log(error);
    },
  });
}

/*################################################################################################*/
// Load first shephred data on page load on selected dropdown
/*################################################################################################*/
function loadCalendarAndTimeSlotsByShepherd(shepherdId) {
  if (shepherdId) {
    commonFunctionOnLoadAndOnChangeShephred(shepherdId);
  }
}

/*################################################################################################*/
// convert time in 24hourse
/*################################################################################################*/
function convertTo24HourFormat(time12h) {
  // Parse the time string using Date object
  var timeSplit = time12h.split(":");
  var hours = parseInt(timeSplit[0], 10);
  var minutes = parseInt(timeSplit[1], 10);

  // Extract the AM/PM part if it exists
  var ampm = time12h.slice(-2).toUpperCase();

  // Adjust hours based on AM/PM
  if (ampm === "PM" && hours < 12) {
    hours += 12;
  } else if (ampm === "AM" && hours === 12) {
    hours = 0;
  }

  // Format the hours and minutes in 24-hour format
  var formattedHours = hours < 10 ? "0" + hours : hours;
  var formattedMinutes = minutes < 10 ? "0" + minutes : minutes;

  // Return the time in 24-hour format
  return formattedHours + ":" + formattedMinutes;
}

/*################################################################################################*/
// enable and desable confirm meeting button on meeting reason input field
/*################################################################################################*/
jQuery("#myCustomModal").on("keyup", "#meeting_reason", function () {
  let submitButton = jQuery("button#confirm_mycustom_meting"); // Get the submit button
  let inputValue = jQuery(this).val().trim(); // Get the trimmed value of the input field
  if (inputValue !== "") {
    submitButton.removeClass("disabledbutton").prop("disabled", false);
  } else {
    submitButton.addClass("disabledbutton").prop("disabled", true);
  }
});




/*################################################################################################*/
// ajax function for check booked meeting
// add class on active time slot li item
/*################################################################################################*/
function activateLiItem(  selected_date,  selected_time,  selected_shaphred_id,  elements) {

    console.log(selected_date, selected_time,  selected_shaphred_id);

    let splitTimeIn24Hourses = selected_time.split("-");
    const meetingstart_24time = convertTo24HourFormat(
    splitTimeIn24Hourses[0].trim()
    );
    const meetingend_24time = convertTo24HourFormat(
    splitTimeIn24Hourses[1].trim()
    );
    const slot_start = `${selected_date}T${meetingstart_24time}:00`;
    const slot_end = `${selected_date}T${meetingend_24time}:00`;

    $.ajax({
          url: custom_ajax_object.ajax_url,
          type: "POST",
          data: {
              action: "check_meeting_slot",
              slot_start: slot_start,
              slot_end: slot_end,
              shepherd_id: selected_shaphred_id
          },
          beforeSend: function() {
             // $("#slotResult").text("Checking...");
              jQuery("#ajax_loading_imgs").show();
          },
          success: function(response) {
             jQuery("#ajax_loading_imgs").hide();
              if (response.success) {                
                  jQuery("#time-slots li.activeLi").removeClass("activeLi");
                  jQuery(elements).addClass("activeLi"); // adding active class

                if (response.data.isBooked) {
                      jQuery("#selected-meeting-time-render").html("This time slot is already booked! Please choose another one.");
                      jQuery("#confirm_mycustom_meting").hide();
                      
                } else {   

                  jQuery("#confirm_mycustom_meting").show();
                  let mtime = selected_time.split("-");
                  const s_time = mtime[0];
                  const e_time = mtime[1];

                  ///get shpred name from ul li slite created
                  var selectedItemshpred = document.querySelector(".selected-shephred-item");
                  let selected_shaphred_name =
                  selectedItemshpred.querySelector("span").textContent;
                  let appendData =
                  '<div class="row"><div class="col-md-4"><div class="meeting-title-css">Date</div><div class="meeting-time-css">' +
                  selected_date +
                  '</div></div><div class="col-md-4"><div class="meeting-title-css">Start Time</div><div class="meeting-time-css">' +
                  s_time +
                  '</div></div><div class="col-md-4"><div class="meeting-title-css">End Time</div><div class="meeting-time-css">' +
                  e_time +
                  '</div></div></div><div class="provideDiv row"><div class="col-md-12"><div class="meeting-title-css">Provider Name</div><div class="meeting-time-css">' +
                  selected_shaphred_name +
                  "</div></div></div>";

                  //get post count value of add and remove input field
                  let userpostscount = document.getElementById("time-slots").getAttribute("data-userpostscount");

                  if (userpostscount === "1") {
                  appendData +=
                      '<div class="row"><div class="col-md-12"><div class="meeting-title-css">Meeting Title<span style="color:red;">*</span></div><input name="meeting_reason" id="meeting_reason" value="" class="form-control form-control-lg valid" type="text" placeholder="Enter Meeting Title" aria-label=".form-control-lg example" required="" aria-required="true" aria-invalid="false"><span class="error-msgb"></span></div></div>';
                  jQuery("button#confirm_mycustom_meting").addClass("disabledbutton");
                  jQuery("button#confirm_mycustom_meting").prop("disabled", true);
                  } else {
                  jQuery("button#confirm_mycustom_meting").removeClass("disabledbutton");
                  jQuery("button#confirm_mycustom_meting").prop("disabled", false);
                  appendData +=
                      '<input type="hidden" name="meeting_reason" id="meeting_reason" value="B4L Registration Meeting" />';
                  }

                  appendData +=
                  '<div class="confirmDiv"><div class="meeting-time-css">Do you want to confirm this meeting?</div></div>';

                  jQuery("#selected-meeting-time-render").html(appendData);

                  jQuery("#meeting_selected_date").val(selected_date);
                  jQuery("#meeting_selected_time").val(selected_time);
                  jQuery("#shaphredId").val(selected_shaphred_id);

                  let splitTimeIn24Hourse = selected_time.split("-");

                  const meeting_start_24time = convertTo24HourFormat(splitTimeIn24Hourse[0].trim());             

                  const finalDatetime = `${selected_date}T${meeting_start_24time}:00`;  
                  jQuery("#currentISTTime").val(finalDatetime); //2024-01-16T14:00:00

                }                 
              } 
          },
          error: function() {              
               jQuery("#selected-meeting-time-render").html('Error checking slot.');
          }
    });


   



}

/*################################################################################################*/
// function for call Confirm Meeting function
/*################################################################################################*/
jQuery("#confirm_mycustom_meting").on("click", function (e) {
  jQuery("#ajax_loading_imgs").show();
  e.preventDefault();

  var meeting_selected_date = jQuery("#meeting_selected_date").val();
  var meeting_selected_time = jQuery("#meeting_selected_time").val();

 // var meeting_reason = jQuery("#meeting_reason").val();

  var meeting_reason = jQuery("#meeting_reason").val().trim(); // Trim to remove spaces
  
  if (meeting_reason === "") {
      // alert("Please enter a reason for the meeting.");
      // jQuery("#meeting_reason").focus(); // Set focus back to the field
      // //error-msg
      // return false; // Stop further action like form submission
  }
 

  let currentISTTime = jQuery("#currentISTTime").val();

  var shaphredId = jQuery("#shaphredId").val();
  var zoom_security = jQuery("#zoom_security").val(); // Add a hidden field with nonce value

  let splitTimeIn24Hourse = meeting_selected_time.split("-");
  const meeting_start_time = convertTo24HourFormat(
    splitTimeIn24Hourse[0].trim()
  );
  const meeting_end_time = convertTo24HourFormat(splitTimeIn24Hourse[1].trim());

  if (shaphredId) {
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: custom_ajax_object.ajax_url,
      data: {
        action: "create_meeting_post_ajax_handler",
        meeting_selected_date: meeting_selected_date,
        meeting_selected_time: meeting_selected_time,
        shaphredId: shaphredId,
        meeting_start_time: meeting_start_time,
        meeting_end_time: meeting_end_time,
        currentISTTime: currentISTTime,
        zoom_security: zoom_security,
        meeting_reason: meeting_reason,
        defaultShephredTimeZone,
        defaultUserTimeZone,
      },
      success: function (response) {
        if (response.post_id) {
          var myCustomModal = document.getElementById("myCustomModal");
          // Check if the modal element exists
          if (myCustomModal) {
            myCustomModal.style.display = "none";
          }

          //jQuery("#myCustomModal").modal('hide');
          // Parse the JSON string
          var responseObj = JSON.parse(response.zoomResponce);
          var resu = responseObj.message
            ? responseObj.message
            : " Zoom Meeting has been Created";
          jQuery("#ajax_loading_imgs").hide();
          jQuery("#meetingSuccessModal").modal("show");
        } else {
          alert("Failed to create post. " + response.message);
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
});

/*################################################################################################*/
// function for redirect on dashboard page after cliecked on close and cross buttons one success popup
/*################################################################################################*/
jQuery("#closeSuccesspopu , #cros-success-Btn").on("click", function (e) {
  jQuery("#ajax_loading_imgs").show();
  e.preventDefault();
  // Create a URLSearchParams object to parse the query string
  const urlParams = new URLSearchParams(window.location.search);
  // Check if the 'businessId' parameter exists
  if (urlParams.has("businessId")) {
    const businessId = urlParams.get("businessId");
    console.log(`businessId exists: ${businessId}`);
    // Redirect to the desired page
    window.location.href = `/business-details/?businessID=${businessId}`;
  } else {
    console.log("businessId parameter does not exist");
    // Redirect to the home page
    window.location.href = "/dashboard/";
  }
});


/*################################################################################################*/
// function for select user timezone in dropdon list
/*################################################################################################*/
 
document.addEventListener("DOMContentLoaded", function () {
    // Try to get the user's local timezone
    var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    var timezoneSelect = document.getElementById("user_timezone_option");
    //console.log('userTimeZone',userTimeZone,'timezoneSelect',timezoneSelect);

    // Check if the detected timezone exists in the dropdown
    var optionFound = false;
    for (var i = 0; i < timezoneSelect.options.length; i++) {
        if (timezoneSelect.options[i].value === userTimeZone) {
            timezoneSelect.selectedIndex = i;
            optionFound = true;
            break;
        }
    }

    // If not found, set the first option as default
    if (!optionFound) {
        timezoneSelect.selectedIndex = 0;
    }
});
 