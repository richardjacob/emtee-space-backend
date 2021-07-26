var daterangepicker_format = $('meta[name="daterangepicker_format"]').attr('content');
var datepicker_format = $('meta[name="datepicker_format"]').attr('content');
var datedisplay_format = $('meta[name="datedisplay_format"]').attr('content');

$('#input_dob').datepicker({ 'dateFormat': 'dd-mm-yy'});
var night_value    = $('#night').val();
var cleaning_value = $('#cleaning').val();
var additional     = $('#additional_guest').val();
var guests         = $('#guests').val();
var security_fee   = $('#security').val();
var weekend_price  = $('#weekend').val();
var week           = $('#week').val();
var month          = $('#month').val();
var currency_code  = $("#currency_code").find( "option:selected" ).prop("value");
         
function step(step)
{
  $(".frm").hide();
  $("#sf"+step).show();
  $('.tab_btn').removeAttr('disabled');
  $('.tab_btn#tab_btn_'+step).attr('disabled', 'disabled')
}

app.controller('space_admin', ['$scope', '$http', '$rootScope', '$compile', '$filter', function($scope, $http,$rootScope, $compile, $filter) {
  // Common Function to Handle All post requests
  $scope.http_post = function(url, data, callback) {
        
        data = (!data) ? {} : data;

        $http.post(url,data).then(function(response) {
            if(response.status == 200) {
                if(callback) {
                    callback(response.data);
                }
            }
        }, function(response) {
            if(response.status == '300') {
                window.location = APP_URL + '/login';
            }
            else if(response.status == '500'){
                window.location.reload();
            }
        });
    };

  $scope.date = new Date();

  function strip(html)
  {
     var tmp = document.createElement("DIV");
     tmp.innerHTML = html;
     return tmp.textContent || tmp.innerText || "";
  }

  function getMonthFromString(mon)
  {
    return moment().month(mon).format("MM");
  }

  $(document).ready(function(){
    if(typeof $('#space_id').val() !== 'undefined'){
      angular.element(document).ready(function () {
        calendar_data=$scope.month_calendar_data;
        $scope.monthclass=true;
        $scope.full_calendar();
        });
      
    }
  })

  /* Calendar Step Functionality */
    $scope.date = moment().format('YYYY-MM-DD');
    $scope.current_month = moment().format('MM');
    $scope.current_day = moment().format('DD');

     var calendar_value="dayGridMonth";
    // Initialize Full Calendar
    $(document).on('click','.fc-timeGridWeek-button',function()
    {   
      // $scope.monthclass=false;
        calendar_value="timeGridWeek";
        calendar_data=$scope.calendar_data;
        $scope.calendar.destroy();
        setTimeout( () => $scope.full_calendar(),1);
    });
    $(document).on('click','.fc-dayGridMonth-button',function()
    {   
        // $scope.monthclass=true;
        calendar_value="dayGridMonth";
        calendar_data=$scope.month_calendar_data;

        $scope.calendar.destroy();
        setTimeout( () => $scope.full_calendar(),1);
    });
    var value_calender; 
  


    // Initialize Full Calendar
    $scope.full_calendar = function() {
        $("#ajax_container").addClass('loading');

        var calendarEl = document.getElementById('calendar');

        $scope.calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['interaction','timeGrid','moment','dayGrid'],
            defaultView: calendar_value,
            selectable: false,
            unselectAuto: false,
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            header: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek' // To Set Weekly and Daily set view as month,agendaWeek,agendaDay
            },
            customButtons: {
                today: {
                    text: 'Today',
                    click: function() {
                        // Move calendar to today
                        var current_date = moment().format('YYYY-MM-DD');
                        $scope.calendar.gotoDate(current_date);
                        $scope.updateCalendar(current_date);
                    }
                },
            },
            defaultDate: $scope.date,
            firstDay: 1, // Set Calendar Staring Day 0 -> Sunday, 1 -> Monday
            allDaySlot: false,
            slotDuration: '01:00:00',
            longPressDelay: 500, // In Mobile Hold Click only works set long press time to 1 ms to work as normal select
            events: calendar_data,
            select: function(selectionInfo) {
                $scope.updateFormData(selectionInfo.start,selectionInfo.end);
            },
            unselect: function(event) {
                $scope.unSelectCalendar();
            },
            eventClick: function(info) {
                $scope.unSelectCalendar();
                if(info.el.classList.contains('status-r')) {
                    return false;
                }
                var startDate  = $scope.convertToMoment(info.event.start);
                var endDate    = $scope.convertToMoment(info.event.end);
                var notes = (info.event.extendedProps.notes != null) ? info.event.extendedProps.notes : '';
                setTimeout(() => {
                    $scope.updateCalendarFields(startDate, endDate, info.event.extendedProps.description, notes);
                    $scope.applyScope();
                },1)
            },
            eventRender: function(info) {
                info.el.setAttribute("id", info.event.id);
                var notes = (info.event.extendedProps.notes != null) ? info.event.extendedProps.notes : '';
                $('<div class="fc-bgevent-data" data-notes="'+notes+'" data-status="'+info.event.extendedProps.description+'"> <span class="title">'+ info.event.title +'</span> <span class="notes">'+ notes +'</span> </div>').appendTo(info.el);
            }
        });
        $scope.calendar.render();
        
        $scope.unSelectCalendar();
        $("#ajax_container").removeClass('loading');
    };

    // Unselect full Calendar and hide calendar edit form
    $scope.unSelectCalendar = function() {
        $scope.calendar.unselect();
        $scope.showUpdateForm = false;
        $scope.applyScope();
    };

    // Check and update form dates, unselect if selected dates already has some other events
    $scope.updateFormData = function(startDate, endDate) {
        var startDate  = $scope.convertToMoment(startDate);
        var endDate    = $scope.convertToMoment(endDate);

        $scope.showUpdateForm = false;
        if(startDate.isBefore(moment().subtract(1, 'h'))) {
            $scope.unSelectCalendar();
            return false;
        }

        cDateCheck = startDate.clone();
        while(cDateCheck < endDate) {
            var cur_el = document.getElementById(cDateCheck.format('YYYY-MM-DD_HH:mm:ss'));
            if(!cur_el) {
                $scope.unSelectCalendar();
                return false;
            }
            if(cur_el.classList.contains('status-r') || cur_el.classList.contains('status-n')) {
                $scope.unSelectCalendar();
                return false;
            }
            cDateCheck.add(1, 'h');
        }
        var selector = $scope.changeFormat(startDate)+'_'+$scope.changeFormat(startDate,'HH:mm:ss');
        var cur_el = document.getElementById(selector).getElementsByClassName("fc-bgevent-data")[0];
        
        $scope.updateCalendarFields(startDate,endDate,cur_el.dataset.status,cur_el.dataset.notes);
        $scope.applyScope();
    };

    // Update Calendar edit form fields
    $scope.updateCalendarFields = function(startDate, endDate, status, notes) {
        $scope.showUpdateForm = true;
        var startTime  = startDate.format('HH:mm:ss');
        var endTime    = endDate.format('HH:mm:ss');

        $('#calendar-edit-start').val($scope.changeFormat(startDate,daterangepicker_format));
        $('#calendar-edit-end').val($scope.changeFormat(endDate,daterangepicker_format));
        $('#calendar-start').val($scope.changeFormat(startDate));
        $('#calendar-end').val($scope.changeFormat(endDate));
        $scope.calendar_edit_start_time = startTime;
        $scope.calendar_edit_end_time = endTime;
        $scope.segment_status = status;
        $scope.notes = notes;
        $scope.isAddNote = (notes != '');
    };

    // Change date to given format
    $scope.changeFormat = function(date,format = 'YYYY-MM-DD') {
        date = $scope.convertToMoment(date);
        return date.format(format);
    };

    // Navigate next and previous months
    $scope.updateCalendar = function(current_date) {
        current_date     = $scope.convertToMoment(current_date);
        $scope.current_day = current_date.format('DD');
        var month   = current_date.format('MM');
        var year    = current_date.format('YYYY');

        if($scope.current_month == month) {
            $scope.date = current_date.format('YYYY-MM-DD');
            return false;
        }
        $('#calendar').addClass('loading');

        var data_params = { month: month, year: year};
        var url= APP_URL+'/'+ADMIN_URL+'/space_calendar/' + $('#space_id').val();

        var callback_function = function(response_data) {
            $scope.calendar_data = response_data;
            $scope.date = year+'-'+ month +'-'+$scope.current_day;
            $scope.current_month = $scope.convertToMoment($scope.date).format('MM');
            
             $scope.calendar_data = response_data.calendar_data;
            $scope.month_calendar_data = response_data.month_calendar_data;
            if(calendar_value=="dayGridMonth")
            calendar_data=$scope.month_calendar_data;
            else
            calendar_data=$scope.calendar_data;
            $scope.calendar.destroy();
           $scope.full_calendar();

            $('#calendar').removeClass('loading');
        };

        $scope.http_post(url,data_params,callback_function);
    };

    // Update Calendar Changes
    $scope.calendarEditSubmit = function() {
        var data_params = {};
        data_params['space_id'] = $scope.space_id;
        data_params['status'] = $scope.segment_status;
        data_params['start_date'] = $('#calendar-start').val();
        data_params['start_time'] = $scope.calendar_edit_start_time;
        data_params['end_date'] = $('#calendar-end').val();
        data_params['end_time'] = $scope.calendar_edit_end_time;
        data_params['notes'] = $scope.notes;
        var data = JSON.stringify(data_params);
        var url= space_ajax_url.calendar_edit;

        $('.calendar-side-option').addClass('loading');
        var callback_function = function(response_data) {
            $scope.showUpdateForm = false;
            $scope.notes = '';
            var date = $scope.calendar.getDate();
            var sDate = $scope.convertToMoment(date);
            var month = sDate.format('MM');
            var year = sDate.format('YYYY');

            var data_params = {};
            data_params['month'] = month;
            data_params['year'] = year;

            var data = { data : JSON.stringify(data_params) };
            $('.calendar-side-option').removeClass('loading');
            $('#calendar').addClass('loading');
            var url= document.URL;
            var callback_function = function(response_data) {
                $("#ajax_container").html($compile(response_data)($scope));
                $scope.date = year+'-'+ month +'-10';
                $scope.full_calendar();

                $('#calendar').removeClass('loading');
            };
            $scope.http_post(url,data,callback_function);
        };

        $scope.http_post(url,data,callback_function);
    };

    // Navigate next / prev weeks
    $(document).on('click','.fc-prev-button,.fc-next-button',function() {
        var current_date = $scope.calendar.getDate();
        $scope.updateCalendar(current_date);
    });

    // Unselect if click other events in full calendar
    $(document).on('click', '.fc-day-top, .fc-bgevent-data, .fc-bgevent-skeleton > table > tbody > tr > td', function() {
        $scope.unSelectCalendar();
    });

    // Convert normal date to moment object
    $scope.convertToMoment = function(date) {
        return moment(date);
    };

    $scope.submit_disable = 0
    $scope.currency_change = function(curency){
      $scope.submit_disable = 1
      currency = $scope.activity_currency
      $http.post(APP_URL+'/get_min_amount', { currency_code:currency }).then(function(response) {
        responce_data = response.data
        $scope.minimum_amount=responce_data.minimum_amount;
        $scope.min_price_symbol=responce_data.currency_symbol
        $scope.submit_disable = 0
      });
    }

  var v = $("#space_form").validate({
      ignore: ':hidden:not(.do-not-ignore)',
      rules: {
        calendar: { required: true },
        space_type: { required: true },
        number_of_rooms: { min: 0 },
        number_of_restrooms: { min: 0 },
        fully_furnished: { required: true },
        shared_or_private: { required: true },
        renting_space_firsttime: { required: true },
        no_of_workstations: { required: true, min: 0 },
        floor_number: { min: 0 },
        sq_ft: { 
          required: true,
          min: 0,
        },
        'guest_accesses[]': {
          required: true,
        },
        number_of_guests: {
          required: true,
          min: 1,
          max: max_guest_limit,
        },
        country: { required: true },
        address_line_1: { required: true },
        city: { required: true },
        state: { required: true },
        latitude : {
          required:{ 
            depends: function(element){
              address_line_1 = $("#address_line_1").val();
              if($scope.step_id == '8' && address_line_1){
                return true;
              }
              else{
                return false;
              }
            }
          }
        },
        'photos[]': { required: { depends: function(element){
          if($('#js-photo-grid li').length == 0){
            return true;
          }
          else{
            return false;
          }
        } } ,extension:"png|jpg|jpeg|gif"},
        "name[]": { required: true },
        "summary[]": { required: true },        
        "language[]": { required: true },  
        "activity[]": { activity_length: true },  
        "avail_hours[start_time]": { validateAvailabilities: true },  
        hourly_rate: { required: true,min:$scope.minimum_amount },
        currency: { required: true },
        booking_type: { required: true },
        cancellation_policy: { required: true },
        user_id: { required: true },
      },
      messages: {
        night : {
          min : jQuery.validator.format("Please enter a value greater than 0")
        },
        latitude : {
            required : "Please choose the address from the google results.",
        },
      },
      errorElement: "span",
      errorClass: "text-danger",
      errorPlacement: function( label, element ) {
        if(element.attr( "data-error-placement" ) === "container" ){
          container = element.attr('data-error-container');
          $(container).append(label);
        } 
        else if(label.attr('id')=="renting_space_firsttime-error" || label.attr('id')=="fully_furnished-error" || label.attr('id')=="shared_or_private-error")
        {  

            place=element.parent();
            place.append(label); 
          $('#renting_space_firsttime-error').addClass('col-sm-12 row');
          $('#fully_furnished-error').addClass('col-sm-12 row');
          $('#shared_or_private-error').addClass('col-sm-12 row');
          
        }else {
          label.insertAfter( element ); 
        }
      },
      extension:"Only png file is allowed!"
    });

   $.validator.addMethod("extension", function(value, element, param) {
  param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
  return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
  }, $.validator.format("Please upload the images like JPG,JPEG,PNG,GIF File Only."));

  $.validator.addMethod("activity_length", function(value, element, param) {
    if ($scope.step_id == '14' ) {
      var activities = $scope.getSelectedData('.activities');
      if(activities.length <= 0){
        return false
      }
    }
    return true
  }, $.validator.format("This filed is required"));

  $.validator.addMethod("validateAvailabilities", function(value, element, param) {
    validateAvailabilities = $scope.validateAvailabilities()
    return (!validateAvailabilities['status'])
  }, $.validator.format("Please Select Valid Times"));

   $('.frm').hide();
   $('.frm#sf1').show();


   function next(step)
   {
    if(v.form())
    {
      if(step != 18)
      {
        $(".frm").hide();
        $("#sf"+(step+1)).show();
      }
      else
      {
        document.getElementById("space_form").submit();
      }
    }
   }

   function back(step)
   {
    $(".frm").hide();
    $("#sf"+(step-1)).show();
   }



  $scope.steps = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18'];
  $scope.add_steps = ['2', '3','5', '6', '7','8','4','9', '10', '11', '12', '13', '14', '15', '16', '17', '18'];
  $scope.step_name = ""; 
  $scope.step = 0;
  $scope.go_to_step = function(step)
  {
    step_id = $scope.steps[step];
    $scope.step_id = step_id; 
    $(".frm").hide();
    $("#sf"+step_id).show();
    $scope.step_name = $("#sf"+step_id).attr('data-step-name');
    $scope.step = step;
    $('#input_current_step_id').val(step_id);
    $('#input_current_step').val(step);
  }
    $scope.go_to_edit_step = function(step)
   {
      $scope.submit_step = $('.tab_btn#tab_btn_'+step).attr('step')
      $(".frm").hide();
      $("#sf"+step).show();
      $scope.step_id = step;
      $('.tab_btn').removeAttr('disabled');
      $('.tab_btn#tab_btn_'+step).attr('disabled', 'disabled')
   }
  $scope.go_to_step($scope.step);
  $scope.add_space_steps = function()
  {
    $scope.steps = $scope.add_steps;
    $scope.go_to_step($scope.step);
  }
  $scope.next_step =function(step)
  {
    current_step = $scope.steps[step];
    if(v.form())
    {
      if(current_step != '18')
      {
        $('html, body').animate({
          scrollTop: ($('.content-header').offset().top)
      },500);
        $scope.step = next_step = (step+1);
        $scope.go_to_step(next_step);
      }
      else
      {
        $('#space_form').submit();
        $scope.submit_disable = 1;
      }
    }
  }
  $scope.back_step = function(step)
  {
      $scope.step = next_step = (step-1); 
      $scope.go_to_step(next_step);
  }
  $scope.get_step_name = function(step)
  {
    step_id = $scope.steps[step]; 
    step_name = $("#sf"+step_id).attr('data-step-name');
    return step_name;
  }

initAutocomplete(); // Call Google Autocomplete Initialize Function

$scope.rows = [];
//$(document).on('click', '#check', function()
  $(document).ready(function(){
  var value=$('#space_id').val();
  $http.post(APP_URL+'/get_lang_details/'+value, { }).then(function(response) {
    $scope.rows = response.data;
  $http.post(APP_URL+'/get_language_list', { }).then(function(response) {
    $scope.lang_list = response.data;
  });
});
});


// Google Place Autocomplete Code
$scope.location_found = false;
$scope.autocomplete_used = false;
var autocomplete;

function initAutocomplete()
{
  autocomplete = new google.maps.places.Autocomplete(document.getElementById('address_line_1'),{types: ['geocode']});
    autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() 
{
    $scope.autocomplete_used = true;
    fetchMapAddress(autocomplete.getPlace());
}

$scope.addNewRow = function() {
    var newItemNo = $scope.rows.length+1;
    $scope.rows.push({'id':'rows'+newItemNo});
  };

  $scope.removeRow = function(name) {       
    var index = name;   
    var comArr = eval( $scope.rows );
    for( var i = 0; i < comArr.length; i++ ) {
      if( comArr[i].name === name ) {
        index = i;
        break;
      }
    }
      $scope.rows.splice( index, 1 );   
  };

function fetchMapAddress(data)
{ //console.log(data);
  if(data['types'] == 'street_address')
    $scope.location_found = true;
  var componentForm = {
    street_number: 'short_name',
      route: 'long_name',
      sublocality_level_1: 'long_name',
      sublocality: 'long_name',
      locality: 'long_name',
      administrative_area_level_1: 'long_name',
      country: 'short_name',
      postal_code: 'short_name'
  };

    $('#city').val('');
    $('#state').val('');
    $('#country').val('');
    $('#address_line_1').val('');
    $('#address_line_2').val('');
    $('#postal_code').val('');

    var place = data;
    $scope.street_number = '';
    for (var i = 0; i < place.address_components.length; i++) 
    {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) 
      {
        var val = place.address_components[i][componentForm[addressType]];
      
      if(addressType       == 'street_number')
        $scope.street_number = val;
      if(addressType       == 'route')
        var street_address = $scope.street_number+' '+val;
        $('#address_line_1').val($.trim(street_address));
        //$('#address_line_1').val(val);
      if(addressType       == 'postal_code')
        $('#postal_code').val(val);
      if(addressType       == 'locality')
        $('#city').val(val);
      if(addressType       == 'administrative_area_level_1')
        $('#state').val(val);
      if(addressType       == 'country')
        $('#country').val(val);
      }
    }

  var address   = $('#address_line_1').val();

  var latitude  = place.geometry.location.lat();
  var longitude = place.geometry.location.lng();

    if($('#address_line_1').val() == '')
      $('#address_line_1').val($('#city').val());

    if($('#city').val() == '')
      $('#city').val('');
    if($('#state').val() == '')
      $('#state').val('');
    if($('#postal_code').val() == '')
      $('#postal_code').val('');

  $('#latitude').val(latitude);
  $('#longitude').val(longitude);
}   

$( "#username" ).autocomplete({
  source: APP_URL+'/'+ADMIN_URL+'/rooms/users_list',
  select: function(event, ui)
  {
    $('#user_id').val(ui.item.id);
  }
});

$(".slide").each(function(i) {
  var item = $(this);
  var item_clone = item.clone();
  item.data("clone", item_clone);
  var position = item.position();
  item_clone
  .css({
    left: position.left,
    top: position.top,
    visibility: "hidden"
  })
    .attr("data-pos", i+1);
  
  $("#cloned-slides").append(item_clone);
});

$(".all-slides").sortable({
  
  axis: "x,y",
  revert: true,
  scroll: false,
  // placeholder: "sortable-placeholder1",
  cursor: "move",

  start: function(e, ui) {
    ui.helper.addClass("exclude-me");
    // $(".all-slides .slide:not(.exclude-me)")
    //   .css("visibility", "hidden");
    ui.helper.data("clone").hide();
    $(".cloned-slides .slide").css("visibility", "visible");
  },

  stop: function(e, ui) {
    $(".all-slides .slide.exclude-me").each(function() {
      var item = $(this);
      var clone = item.data("clone");

      var position = item.position();

      clone.css("left", position.left);
      clone.css("right", position.right);
      clone.css("top", position.top);
      clone.css("bottom", position.bottom);
      clone.show();

      item.removeClass("exclude-me");
    });
    
    $(".all-slides .slide").each(function() {
      var item = $(this);
      var clone = item.data("clone");
      
      clone.attr("data-pos", item.index());
    });
   
    $(".all-slides .slide").css("visibility", "visible");
    $(".cloned-slides .slide").css("visibility", "hidden");
  },

  change: function(e, ui) {
    $(".all-slides .slide:not(.exclude-me)").each(function() {
      var item = $(this);

      var clone = item.data("clone");
     // alert(clone);
      clone.stop(true, false);
      var position = item.position();
      clone.animate({
        left: position.left,
        right: position.right,
        top:position.top,
        bottom:position.bottom
       
      }, 0);
    });
  }
  
});
$(document).on('click', '.delete-photo-btn', function()
{
  var id = $(this).attr('data-photo-id');
  var space_id = $('#space_id').val();
  
  if($('[id^="photo_li_"]').size() > 1)
  {
  $http.post(APP_URL+'/'+ADMIN_URL+'/delete_photo', { photo_id : id,space_id : space_id }).then(function(response) 
  {
    if(response.data.success == 'true')
    {
      $('#photo_li_'+id).remove();
    }
  });
  }
  else
  {
    alert('You cannnot delete last photo. Please upload alternate photos and delete this photo.');
  }
});

$(document).on('keyup', '.highlights', function()
{
  var value = $(this).val();
  var id = $(this).attr('data-photo-id');
  $('#saved_message').fadeIn();
  $http.post(APP_URL+'/'+ADMIN_URL+'/photo_highlights', { photo_id : id, data : value }).then(function(response)
  {
    $('#saved_message').fadeOut();
  });
});

$(document).on('change', '#additional_guest', function() {
  disableAdditionalGuestCharge();
});
disableAdditionalGuestCharge();
function disableAdditionalGuestCharge() {
  if ($('#additional_guest').val() == "0")
    $('#guests').prop('disabled', true);
  else
    $('#guests').prop('disabled', false);
}

  $.validator.addMethod("youtube", function(value, element) {
    if (value != undefined && value.length > 0) {
      var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
      var match = value.match(regExp);
      if (match && match[2].length == 11) {
        return true
      }
      else {
          return false;
      }
    }
    return true;
  }, 'Please select a valid youtube url.');
  $.validator.addMethod("maximum_stay_value", function(value, element, param) {
    min_elem = $(element).attr('data-minimum_stay');
    min_value = $(min_elem).val();
    if((min_value-0) > (value-0) && min_value != '' && value != '')
    {
      return false;
    }
    else
    {
      return true;
    }
  }, $.validator.format("Maximum stay must be greater than Minimum stay"));
  $.validator.addClassRules({
    discount: {
      digits : true,
      required : true,
      min: 1,
      max: 99,
    },
    early_bird_period: {
      digits: true,
      required: true,
      min: 30,
      max : 1080,
    },
    last_min_period: {
      digits: true,
      required: true,
      min: 1,
      max : 28,
    },
    minimum_stay: {
      digits: true,
      min: 1,
    },
    maximum_stay: {
      digits: true,
      min: 1,
      maximum_stay_value:true,
    },
    availability_minimum_stay: {
      digits: true,
      min: 1,
    },
    availability_maximum_stay: {
      required: {
        depends: function(element){
          min_elem = $(element).attr('data-minimum_stay');
          min_value = $(min_elem).val();
          return min_value == '';
        }
      },
      digits: true,
      min: 1,
      maximum_stay_value:true,
    }
  });
  $scope.add_price_rule = function(type) {
    if(type == 'length_of_stay')
    {
      new_period = $scope.length_of_stay_period_select;
      $scope.length_of_stay_items.push({'period' : new_period-0});
      $scope.length_of_stay_period_select = '';
    }
    else if(type== 'early_bird') 
    {
      $scope.early_bird_items.push({'period' : ''});
    }
    else if(type== 'last_min') 
    {
      $scope.last_min_items.push({'period' : ''});
    }
  }
  $scope.remove_price_rule = function(type, index) {
    if(type == 'length_of_stay') {
      item =$scope.length_of_stay_items[index];
      $scope.length_of_stay_items.splice(index, 1);
    }
    else if(type == 'early_bird') {
      item =$scope.early_bird_items[index];
      $scope.early_bird_items.splice(index, 1);
    }
    else if(type == 'last_min') {
      item =$scope.last_min_items[index];
      $scope.last_min_items.splice(index, 1);
    }
    if(item.id != '' && item.id) {
      $('.'+type+'_wrapper').addClass('loading');
      $('button[type="submit"]').attr('disabled', true);
      $http.post(APP_URL+'/'+ADMIN_URL+'/rooms/delete_price_rule/'+item.id, {}).then(function(response){
        $('.'+type+'_wrapper').removeClass('loading');
        $('button[type="submit"]').removeAttr('disabled');
      })
    }
  }
  $scope.length_of_stay_option_avaialble = function(option) {
    var found = $filter('filter')($scope.length_of_stay_items, {'period': option}, true);
    var found_text = $filter('filter')($scope.length_of_stay_items, {'period': ''+option}, true);
    return !found.length && !found_text.length;
  }
  $scope.add_availability_rule = function() {
    $scope.availability_rules.push({'type' : ''});
    setTimeout(function(){
      $scope.availability_datepickers();
    }, 20);
  }
  $scope.remove_availability_rule = function(index) {
    item = $scope.availability_rules[index];
    type = 'availability_rules';
    if(item.id != '' && item.id) {
      $('.'+type+'_wrapper').addClass('loading');
      $('button[type="submit"]').attr('disabled', true);
      $http.post(APP_URL+'/'+ADMIN_URL+'/rooms/delete_availability_rule/'+item.id, {}).then(function(response){
        $('.'+type+'_wrapper').removeClass('loading');
        $('button[type="submit"]').removeAttr('disabled');
      })
    }
    $scope.availability_rules.splice(index, 1); 
  }
  $scope.availability_rules_type_change = function(index) {
    rule = $scope.availability_rules[index];
    if(rule.type != 'custom')
    {
      this_elem = $("#availability_rules_"+index+"_type option:selected");
      start_date = this_elem.attr('data-start_date');
      end_date = this_elem.attr('data-end_date');
      $scope.availability_rules[index].start_date = start_date;
      $scope.availability_rules[index].end_date = end_date;
    }
  }
  $scope.availability_datepickers = function() {
    if(!$scope.availability_rules)
    {
      return;
    }
    $.each($scope.availability_rules, function(i, data){
      var start_date_element = $("#availability_rules_"+i+"_start_date");
      var end_date_element = $("#availability_rules_"+i+"_end_date");
      start_date_element.datepicker({
        'minDate':0,
        'dateFormat': datepicker_format,
        onSelect: function(date, obj){
          var start_date = start_date_element.datepicker('getDate'); 
          start_date.setDate(start_date.getDate() + 1); 
          end_date_element.datepicker('option', 'minDate',start_date );
          // end_date_element.trigger('focus');
        }
      })
      end_date_element.datepicker({
        'minDate':1,
        'dateFormat': datepicker_format
      })
    });
  }
  $(document).ready(function(){
    $scope.availability_datepickers();
  });

  /* Availablity related functions */
    // Show/ Hide Availability Time dropdowns based on selected Type
    $scope.availabilityTypeChanged = function(day, type) {
      if($scope.availabilities[day].availability_times == undefined) {
        $scope.availabilities[day].availability_times = [];
      }

      if(type == 'set_hours') {
        $scope.availabilities[day].availability_times.push({'id':'','start_time':'','end_time':''});
      }
      else {
        $scope.availabilities[day].availability_times = [];
      }
      $scope.watchAvailability($scope.availabilities,$scope.original_availabilities);
    };

    // Add new availability Hour Dropdown
    $scope.addAvailabilityHours = function(day) {
      $scope.availabilities[day].availability_times.push({'id':''});
      $scope.watchAvailability($scope.availabilities,$scope.original_availabilities);
    };

    // Remove new availability Hour Dropdown
    $scope.removeAvailabilityHours = function(day,index) {
      var removed_hours = $scope.availabilities[day].availability_times[index].id;
      $scope.availabilities[day].removed_availability.push(removed_hours);
      $scope.availabilities[day].availability_times.splice(index, 1);
      $scope.watchAvailability($scope.availabilities,$scope.original_availabilities);
    };

    // Get formatted Availability status based on database
    $scope.getAvailabilityStatus = function(status) {
      return (status == 'Closed') ? 'Closed' : 'Open';
    };

    // Get formatted Available status based on database
    $scope.getAvailabeStatus = function(status) {
      return (status == 'Open') ? 'set_hours' : 'all';
    };

    // Update step error and check form modified when change availability
    $scope.availabilityChanged = function() {
      $scope.step_error = '';
      $scope.watchAvailability($scope.availabilities,$scope.original_availabilities);
    };

    // Watch activities scope to check form is modified or not
    $scope.watchAvailability = function(new_value, original_value) {
        $scope.form_modified = true;
    };

    // Validate all steps data
    $scope.validateStepData = function(step_name,step) {
        var result = false;

        if(step_name == 'basics') {
            result = $scope.validateBasicData(step);
        }
        else if(step_name == 'setup') {
            result = $scope.validateSetupData(step);
        }
        else if(step_name == 'ready_to_host') {
            result = $scope.validateReadyToHostData(step);
        }
        $scope.applyScope();
        return result;
    };

    // Ready to Host Step validation rules
    $scope.validateReadyToHostData = function(step) {
        var result      = false;
        var error_message = 'Please fill Required Fields';
        $scope.step_error = '';
        if(step == 'activities') {
            var activities = $scope.getSelectedData('.activities');
            result = (activities.length <= 0);
        }
        else if(step == 'activity_price') {
            var tmp_result = true;
            var min_amt_result = true;
            var activity_price = $scope.getActivitiesPriceData();
            angular.forEach(activity_price, function(price) {
                if($scope.checkValidInput(price.min_hours) || $scope.checkValidInput(price.hourly)) {
                    tmp_result = false;
                    return;
                }
                if(price.hourly < $scope.minimum_amount) {
                    min_amt_result = false;
                    error_message  = 'Please Enter amount greater than minimum amount';
                    return;
                }
            });
            result = (tmp_result == false || min_amt_result == false);
        }
        else if(step == 'availability') {
            var avail_result = $scope.validateAvailabilities();
            result = avail_result['status'];
            error_message = avail_result['error_message'];
        }

        if(result) {
            $scope.step_error = error_message;
        }
        return result;
    };

    // Convert time to momemnt object
    $scope.convertToTime = function(time) {
        return moment("2001-01-01 "+time,"YYYY-MM-DD HH:mm:ss");
    };

    // validate availability times
    $scope.validateAvailabilities = function() {
        var result = false;
        var error_message = 'Please Select Valid Times';
        var result_data = [];

        $.each($scope.availabilities, function(key, availability) {
            if(availability.status == 'Open' && availability.available != 'all') {
                $.each(availability.availability_times, function(avail_key,availabile_times) {
                    // Check times are selected or not
                    if(typeof availabile_times.start_time ==='undefined' || typeof availabile_times.end_time ==='undefined' || 
                      availabile_times.start_time == '' || availabile_times.end_time == '') {
                        result = true;
                        return false;
                    }
                    else {
                        between_time = false;
                        selected_start = $scope.convertToTime(availabile_times.start_time);
                        selected_end = $scope.convertToTime(availabile_times.end_time);
                        var current_range  = moment.range(selected_start, selected_end);

                        $.each(availability.availability_times, function(check_key,check_times) {

                            if(avail_key != check_key && (availabile_times.start_time != '' && availabile_times.end_time != '')) {
                                var start = $scope.convertToTime(check_times.start_time);
                                var end = $scope.convertToTime(check_times.end_time);

                                var check_range  = moment.range(start, end);
                                between_time = current_range.overlaps(check_range);

                                if(between_time) {
                                    return false;
                                }
                            }
                        });

                        if(between_time) {
                            error_message = 'Please choose valid times';
                            result = true;
                            return false;
                        }
                    }

                });
            }

            if(result) return false;
        });

        result_data['status']           = result;
        result_data['error_message']    = error_message;
        return result_data;
    };

    // Change end time based on start itme
    $scope.startTimeChanged = function(day,index) {
      var start_time = $scope.availabilities[day].availability_times[index].start_time;
      var end_time = $scope.availabilities[day].availability_times[index].end_time;

      var minimum_end_time = moment.utc(start_time,'HH:mm:ss').add(1,'hour').format('HH:mm:ss');
      if(end_time < minimum_end_time || end_time == undefined) {
          $scope.availabilities[day].availability_times[index].end_time = minimum_end_time;
      }
    };

    
    // Events to update space scope when user check/Uncheck activities and sub activities
    $(document).on('change', '.activities', function() {
      var activities = $scope.getSelectedData('.activities');
      $scope.space_activities.activity_id = activities.toString();

      // Check Sub activities based on Actvity
      var activity_id = $(this).val();
      if($(this).prop('checked')) {
        $('[data-activity_id="'+activity_id+'"]').prop('checked',true);
      }
      else {
        $('[data-activity_id="'+activity_id+'"]').prop('checked',false);
      }
      $scope.watchActivities($scope.space_activities,$scope.original_space_activities);
      $scope.validateStepData('ready_to_host','activities');
      $scope.selected_activity_type_filter()
      $scope.applyScope();
    });

    $scope.selected_activity_type = []
    $scope.selected_activity_type_filter = function(){
      $scope.selected_activity_type = []
      for(var key in $scope.all_activity_types) {
        if ($scope.selected_activity_type_ids.includes($scope.all_activity_types[key].id)) {
          $scope.all_activity_types[key].activity_price = {
                            activity_id : $scope.all_activity_types[key].id,
                            hourly : 0,
                            min_hours : 0,
                            full_day : 0,
                          }
          $scope.selected_activity_type.push($scope.all_activity_types[key]);
        }
      }
    };

    $(document).on('change', '.sub_activities', function() {
      var sub_activities = $scope.getSelectedData('.sub_activities');
      var activity_id    = $(this).data('activity_id').toString();

      // Check Activity type based on sub activity
      var cur_sub_activities = $scope.getSelectedData('[data-activity_id="'+activity_id+'"]');
      if(cur_sub_activities.length > 0) {
        $('#activity_'+activity_id).prop('checked',true);
      }
      else {
        $('#activity_'+activity_id).prop('checked',false);
      }

      $scope.space_activities.sub_activity_id = sub_activities.toString();
      $scope.watchActivities($scope.space_activities,$scope.original_space_activities);
      $scope.validateStepData('ready_to_host','activities');
      $scope.selected_activity_type_filter();
      $scope.applyScope();
    });

    // Update Activity min hours when click +/- Button
    $scope.updateActivityHours = function(type, index) {
        if($scope.checkValidInput($scope.space.space_activities[index].activity_price.min_hours)) {
          $scope.space.space_activities[index].activity_price.min_hour = 0;
        }
        if(type == 'increase') {
            $scope.space.space_activities[index].activity_price.min_hours++;
        }
        else {
            $scope.space.space_activities[index].activity_price.min_hours--;
        }
        $scope.form_modified = true;
        $scope.applyScope();
        $scope.validateStepData('ready_to_host', 'activity_price');
    };
    // Get entered activities price
    $scope.getActivitiesPriceData = function() {
        var data = [];
        angular.forEach($scope.space.space_activities, function(value) {
            value.activity_price.currency_code = $scope.activity_currency;
            data.push(value.activity_price)
        });
        return data;
    };
    // Check input is valid or not
    $scope.checkValidInput = function(value) {
        return (value == undefined || value == 0 || value == '');
    };

    // Watch activities scope to check form is modified or not
    $scope.watchActivities = function(new_value, original_value) {
        $scope.form_modified = false;

        if(original_value) {
            if(new_value != original_value) {
                $scope.form_modified = true;
            }
        }
    };

    // Get Checkbox Checked Values Based on given selector
    $scope.getSelectedData = function(selector) {
        var value = [];
        $(selector+':checked').each(function() {
            value.push($(this).val());
        });
        return value;
    };

    // Common function to check and apply Scope value
    $scope.applyScope = function() {
        if(!$scope.$$phase) {
            $scope.$apply();
        }
    };

    // Get Activities and sub activities Data based on activities class selector
    $scope.selected_activity_type_ids = [];
    $scope.getActivitiesData = function() {
        $scope.selected_activity_type_ids = [];
        var space_activities = {};
        $('.activities:checked').each(function() {
            var activity_type = $(this).data('activity_type');
            var activity_id = $(this).val();
            var sub_activity = '';
            if(space_activities[activity_type] == undefined) {
                $scope.selected_activity_type_ids.push(activity_type);
                space_activities[activity_type] = [];
            }

            $('[data-activity_id="'+activity_id+'"]').each(function() {
                if($(this).prop('checked')) {
                    sub_activity += $(this).val()+',';
                }
            });
            sub_activity = sub_activity.replace(/(.+),$/, '$1');

            space_activities[activity_type].push({ activity_id : activity_id, sub_activities : sub_activity });
        });

        return space_activities;
    };
}]);