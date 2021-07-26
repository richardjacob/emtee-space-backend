var daterangepicker_format = $('meta[name="daterangepicker_format"]').attr('content');
var datepicker_format = $('meta[name="datepicker_format"]').attr('content');


app.directive('postsPagination', function() {
    return {
        restrict: 'E',
        template: '<ul class="pagination">' +
            '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="messages_result(1)">&laquo;</a></li>' +
            '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="messages_result(currentPage-1)">&lsaquo; ' + $('#pagin_prev').val() + ' </a></li>' +
            '<li ng-repeat="i in range" ng-class="{active : currentPage == i}">' +
            '<a href="javascript:void(0)" ng-click="messages_result(i)">{{i}}</a>' +
            '</li>' +
            '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="messages_result(currentPage+1)"> ' + $('#pagin_next').val() + ' &rsaquo;</a></li>' +
            '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="messages_result(totalPages)">&raquo;</a></li>' +
            '</ul>'
    };
}).controller('inbox', ['$scope', '$http','$rootScope', function($scope, $http,$rootScope) {
    $scope.today = new Date();

    // Common function to perform post request
    $scope.http_post = function(url, data = {}, callback,options = {}) {
        $('.inbox-wrap').addClass('loading');
        $http.post(url,data,options).then(function(response) {
            if(response.status == 200) {
                if(callback) {
                    callback(response.data);
                }
                $('.inbox-wrap').removeClass('loading');
            }
        }, function(response) {
            if(response.status == '300') {
                window.location = common_ajax_url.login;
            }
            /*if(response.status == '500') {
                window.location.reload();
            }*/
        });
    };

    $scope.getDataParams = function() {
        var type = $('#inbox_filter_select').val();
        var data = $scope.user_id;
        return { data: data, type: type };
    };

    $scope.messages_result = function(pageNumber = 1) {
        var url = 'inbox/message_by_type?page=' + pageNumber;
        var data_params = $scope.getDataParams();
        var callback_function = function(response_data) {
            $scope.message_result   = response_data;
            $scope.totalPages       = response_data.last_page;
            $scope.currentPage      = response_data.current_page;
            // Pagination Range
            var pages = [];

            for (var i = 1; i <= response_data.last_page; i++) {
                pages.push(i);
            }

            $scope.range = pages;
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $scope.messages_count = function() {
        var url = 'inbox/message_count';
        var data_params = $scope.getDataParams();
        var callback_function = function(response_data) {
            $scope.message_count = response_data;
            $rootScope.inbox_count = response_data.unread_count;
            $scope.messages_result($scope.currentPage);
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $scope.archive = function(index, id, msg_id, type) {
        var url = 'inbox/archive';
        var data_params =  {id: id, msg_id: msg_id, type: type };
        var callback_function = function(response_data) {
            if (type == "Archive") {
                $scope.message_result.data[index].archive = 1;
            }

            if (type == "Unarchive") {
                $scope.message_result.data[index].archive = 0;
            }

            $scope.messages_count();
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $scope.star = function(index, id, msg_id, type) {
        var url = 'inbox/star';
        var data_params =  {id: id, msg_id: msg_id, type: type };
        var callback_function = function(response_data) {
            if (type == "Star") {
                $scope.message_result.data[index].star = 1;
            }

            if (type == "Unstar") {
                $scope.message_result.data[index].star = 0;
            }

            $scope.messages_count();
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $("#inbox_filter_select").change(function() {
        $scope.messages_result();
    });

    setTimeout(() => {
        $scope.totalPages = 0;
        $scope.currentPage = 1;
        $scope.range = [];
        pageNumber = 1;
        $scope.messages_count(pageNumber);
    }, 10);

}]);

app.controller('conversation', ['$scope', '$http','$q', function($scope, $http,$q) {
    $scope.calculation_status = '';
    var canceller,isSending = false;

     // Common function to perform post request
    $scope.http_post = function(url, data, callback,options = {}) {
        data = (!data) ? {} : data;
        $http.post(url,data,options).then(function(response) {
            if(response.status == 200) {
                if(callback) {
                    callback(response.data);
                }
            }
        }, function(response) {
            if(response.status == '300') {
                window.location = common_ajax_url.login;
            }
            if(response.status == '500') {
                window.location.reload();
            }
        });
    };

    $scope.user_location_times = function(date,time,time_zone='') {
        if (typeof date === 'undefined' || date.length === 0 || !date.trim()) {
            date = moment().format('DD-MM-YYYY')
        }

        if (time_zone != '') {
            current_time = moment().tz(time_zone);
        }else{
            current_time = moment();
        }
        
        given_time = moment(date+' '+time,'DD-MM-YYYY HH:mm:ss');
        if(given_time.isBefore(current_time)) {
            return true;
        }

        return false;
    }

    $scope.range = function(min, max) {
        var input = [];
        for (var i = min; i <= max; i++) input.push(i);
        return input;
    };

    // Common function to check and apply Scope value
    $scope.applyScope = function() {
        if(!$scope.$$phase) {
            $scope.$apply();
        }
    };

    // Check input is valid or not
    $scope.checkValidInput = function(value) {
        return (value == undefined || value == 0 || value == '');
    };

    $scope.reply_message = function(value) {

        var message = $('[data-key="' + value + '"] textarea[name="message"]').val();
        $('.message_error_box').addClass('d-none')
        if (!message) {
            $('[data-key="' + value + '"] textarea[name="message"]').siblings('.message_error_box').removeClass('d-none')
            return false;
        }
        $('#post_message_box').addClass('loading');
        $('[data-key="' + value + '"] textarea[name="message"]').val('');
        var template = $('[data-key="' + value + '"] input[name="template"]').val();
        var template_message = $('[data-key="' + value + '"] input[name="template"]').data('message');
        if (template == 2) {
            $scope.availability_error = false;
            if($('#pricing_start_date').val() == '' || $('#pricing_end_date').val() == '') {
                $scope.availability_error = true;
                return '';
            }
            if($('#pricing_price').val() == '') {
                $scope.availability_error = true;
                return '';
            }
        }
        if(template == 9 ){
            $("li[data-tracking-section='decline']").addClass('d-none');
        }
        var url = space_ajax_url.reply_message;
        var data_params = $scope.getBasicDataParams();
        if(value == 'special_offer') {
            data_params = $scope.getSpecialOfferData();
        }
        data_params['message'] = message;
        data_params['template'] = template;
        data_params['template_message'] = template_message;

        var callback_function = function(response_data) {
            if (response_data.success != 'false') {
                if(value == 'guest_conversation') {
                    $('#thread-list').prepend(response_data);
                }
                else {
                    $(response_data).insertAfter('#post_message_box');
                }
                $('[data-key="' + value + '"] textarea[name="message"]').val('');
                $('.inquiry-form-fields').addClass('d-none');
                $('[data-tracking-section="accept"] ul').addClass('d-none');
                $('[data-tracking-section="decline"] ul').addClass('d-none');
                $('[data-tracking-section="discussion"] ul').addClass('d-none');
            } else {
                $('[data-error="price"]').html(response_data.msg);
            }
            $('#post_message_box').removeClass('loading');
        }
        $scope.http_post(url,data_params,callback_function);
    };

    $(document).on('click', '.attach-offer', function() {
        $('.inquiry-form-fields').removeClass('d-none');
        $('[data-tracking-section="accept"] ul').removeClass('d-none');
        $('[data-tracking-section="accept"] input[name="template"][value=2]').prop('checked', true);
        $('[data-key="special_offer"] .drawer').removeClass('d-none');
        var key = $('[data-tracking-section="accept"] input[name="template"]:checked').closest().data('key');
        $('[data-key="' + key + '"] .drawer').removeClass('d-none');
    });

    $(document).on('click', '.pre-approve', function() {
        $('.inquiry-form-fields').removeClass('d-none');
        $('[data-tracking-section="accept"] ul').removeClass('d-none');
        $('[data-tracking-section="accept"] input[name="template"][value=1]').prop('checked', true);
        var key = $('[data-tracking-section="accept"] input[name="template"]:checked').closest().data('key');
        $('[data-key="' + key + '"] .drawer').removeClass('d-none');
    });

    $(document).on('click', '.option-list a', function() {
        var track = $(this).parent().data('tracking-section');
        $('[data-tracking-section] ul').addClass('d-none');
        $('[data-tracking-section="' + track + '"] ul').removeClass('d-none');
        var key = $('[data-tracking-section="' + track + '"] input[name="template"]:checked').closest().data('key');
        $('[data-key="' + key + '"] .drawer').removeClass('d-none');
    });

    $(document).on('click', 'input[name="template"]', function() {
        $('[data-key] .drawer').addClass('d-none');
        $(this).parent().parent().addClass('active');
        var key = $(this).parent().parent().data('key');
        $('[data-key="' + key + '"] .drawer').removeClass('d-none');
    });

    $(document).ready(function() {
        if(first_segment == 'messaging') {
            $scope.getCalendarDates();
            $scope.getSpaceActivities();
        }
    });
    
    $scope.date = moment().format('YYYY-MM-DD');
    $scope.current_month = moment().format('MM');
    $scope.current_day = moment().format('DD');

    // Convert normal date to moment object
    $scope.convertToMoment = function(date) {
        return moment(date);
    };

    $scope.full_calendar = function() {
        $('#calendar').addClass('loading');

        var calendarEl = document.getElementById('calendar');

        $scope.calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['interaction','timeGrid','moment'],
            defaultView: 'timeGridWeek',
            selectable: false,
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            header: {
              left: 'prev,next today',
              center: 'title',
              right: '' // To Set Weekly and Daily set view as month,agendaWeek,agendaDay
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
            locale: LANGUAGE_CODE,
            height:"parent",
            firstDay: 1, // Set Calendar Staring Day 0 -> Sunday, 1 -> Monday
            allDaySlot: false,
            slotDuration: '01:00:00',
            events: $scope.calendar_data,
            eventRender: function(info) {
                info.el.setAttribute("id", info.event.id);
                var notes = (info.event.extendedProps.notes != null) ? info.event.extendedProps.notes : '';
                $('<div class="fc-bgevent-data" data-notes="'+notes+'" data-status="'+info.event.extendedProps.description+'"> <span class="title">'+ info.event.title +'</span> <span class="notes">'+ notes +'</span> </div>').appendTo(info.el);
            }
        });
        $scope.calendar.render();

        $('#calendar').removeClass('loading');
    };

    // Destroy Full Calendar
    $scope.destroyCalendar = function() {
        $scope.calendar.destroy();
    };

    // Navigate next and previous months
    $scope.updateCalendar = function(current_date, check_month = true) {
        current_date     = $scope.convertToMoment(current_date);
        $scope.current_day = current_date.format('DD');
        var month   = current_date.format('MM');
        var year    = current_date.format('YYYY');

        if($scope.current_month == month && check_month) {
            $scope.date = current_date.format('YYYY-MM-DD');
            return false;
        }
        $('#calendar').addClass('loading');

        var data_params = {};
        data_params['space_id'] = $scope.space_id;
        data_params['month'] = month;
        data_params['year'] = year;
        var url = common_ajax_url.host_calendar;

        var callback_function = function(response_data) {
            $scope.calendar_data = response_data.calendar_data;
            $scope.date = year+'-'+ month +'-'+$scope.current_day;
            $scope.current_month = $scope.convertToMoment($scope.date).format('MM');

            $scope.calendar.destroy();
            setTimeout( () => $scope.full_calendar(),1);

            $('#calendar').removeClass('loading');
        };

        $scope.http_post(url,data_params,callback_function);
    };

    // Navigate next / prev weeks
    $(document).on('click','.fc-prev-button,.fc-next-button',function() {
        var current_date = $scope.calendar.getDate();
        $scope.updateCalendar(current_date);
    });

    // Update start date and end date DatePickers
    $scope.updateCalendar = function(selector) {

        $(selector).datepicker({
            minDate: 0,
            showAnim:'slideDown',
            dateFormat: datepicker_format,
            beforeShowDay: function(date) {
                var date_str = $.datepicker.formatDate('yy-mm-dd', date);
                var daynum = date.getDay();
                var now = new Date();
                now.setDate(now.getDate()-1);

                var has_not_available = $scope.not_available_days.indexOf(daynum);

                return [ has_not_available == -1];
            },
            onSelect: function(date,obj) {
                var selected_day = moment(date,datedisplay_format.toUpperCase());

                if(selector == '#pricing_start_date') {
                    $scope.booking_date_times.start_date     = date;
                    $scope.booking_date_times.formatted_start_date = selected_day.format('YYYY-MM-DD');
                    $scope.booking_date_times.start_week_day       = selected_day.weekday();

                    var checkout = $('#pricing_start_date').datepicker('getDate');
                    var checkout_obj = moment(checkout,datedisplay_format.toUpperCase());
                    var setEndDate = true;

                    if($scope.checkValidInput($scope.booking_date_times.end_date)) {
                        setEndDate = true;
                    }
                    else {
                        var selected_checkout = checkout_obj.format('YYYY-MM-DD');
                        if(selected_checkout > $scope.booking_date_times.formatted_end_date) {
                            setEndDate = true;
                        }
                    }
                    if(setEndDate) {
                        $('#pricing_end_date').val(date);
                        $('#pricing_end_date').datepicker('setDate', checkout);
                        $scope.setEndDate(date);
                    }
                    $('#pricing_end_date').datepicker('option', 'minDate',checkout);
                    if($scope.booking_period == 'Multiple') {
                        setTimeout( () => $("#pricing_end_date").datepicker("show") ,1);
                    }
                    else {
                        $scope.setEndDate(date);
                    }
                }
                if(selector == '#pricing_end_date') {
                    $scope.setEndDate(date);
                }
                $scope.applyScope();
                $scope.calculateSpecialOffer();
            }
        });
    };

    $scope.setEndDate = function(date) {
        var selected_day = moment(date,datedisplay_format.toUpperCase());
        $scope.booking_date_times.end_date     = date;
        $scope.booking_date_times.formatted_end_date = selected_day.format('YYYY-MM-DD');
        $scope.booking_date_times.end_week_day       = selected_day.weekday();
        $scope.applyScope();
    };

    $scope.getCalendarDates = function() {
        var url = space_ajax_url.space_calendar;
        var data_params = { space_id : $scope.space_id };
        var callback_function = function(response_data) {
            $scope.not_available_days  = response_data.not_available_days;
            $scope.not_available_times = response_data.not_available_times;
            $scope.blocked_times       = response_data.blocked_times;
            $scope.updateCalendar('#pricing_start_date');
            $scope.updateCalendar('#pricing_end_date');
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $scope.getSpaceActivities = function() {
        var url = space_ajax_url.space_activities;
        var data_params = { space_id : $scope.space_id };
        var callback_function = function(response_data) {
            $scope.activities  = response_data.space_activities;
            $scope.activity_type = "";
            $scope.event_type = '';
            $scope.hidden_event_type = JSON.stringify($scope.event_type);
            $('.special-offer-date-fields').removeClass('loading');
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $scope.switchDayType = function($event) {
        $event.stopPropagation();
        $scope.booking_period = ($scope.booking_period == 'Single') ? 'Multiple' : 'Single';
        if($scope.booking_period) {
            var date = $scope.booking_date_times.start_date;
            $scope.setEndDate(date);
        }
    };

    $scope.validateDetails = function(reply_message) {
        var result = true;
        $scope.hidden_date_times = angular.toJson($scope.booking_date_times);
        if($scope.checkValidInput($scope.booking_date_times.start_date) || $scope.checkValidInput($scope.booking_date_times.formatted_start_date) || $scope.checkValidInput($scope.booking_date_times.start_time) || $scope.checkValidInput($scope.booking_date_times.end_time)) {
            result = false
        }
        if($scope.checkValidInput($scope.number_of_guests) || $scope.checkValidInput($scope.event_type)) {
            result = false;
        }
        if(reply_message && ($scope.checkValidInput($scope.price) || $scope.checkValidInput($scope.message))) {
            result = false;
        }

        if($scope.price < $scope.minimum_amount) {
            result = false;
        }

        $scope.applyScope();
        return result;
    };

    $scope.calculateSpecialOffer = function(reply_message = false) {
        var result = $scope.validateDetails(reply_message);
        $scope.contact_error = (result != true);
        if(result) {
            $scope.calculation(reply_message);
        }
    };

    //change Space details
    $scope.changeSpace = function() {
        $('.special-offer-date-fields').addClass('loading');
        $scope.getCalendarDates();
        $scope.getSpaceActivities();
    };

    $scope.getBasicDataParams = function() {
        return { space_id : $scope.space_id, id: $scope.reservation_id };
    };

    $scope.getSpecialOfferData = function() {
        var data_params = $scope.getBasicDataParams();
        data_params['event_type'] = $scope.event_type;
        data_params['booking_date_times'] = $scope.booking_date_times;
        data_params['number_of_guests'] = $scope.number_of_guests;
        data_params['price']            = $scope.price;
        data_params['booking_period']   = $scope.booking_period;
        return data_params;
    };

    $scope.calculation = function(reply_message) {
        $('.special-offer-date-fields').addClass('loading');

        if(isSending) {
            canceller.resolve()
        }
        isSending = true;
        canceller = $q.defer();
        var options = { timeout : canceller.promise };

        var url = space_ajax_url.price_calculation;
        var data_params = $scope.getSpecialOfferData();

        var callback_function = function(response_data) {
            $('.special-offer-date-fields').removeClass('loading');
            $scope.availability_error = false;
            if (response_data.status == 'Not available') {
                $scope.availability_error = true;
                $scope.not_available = response_data.not_available;
                $scope.availability_error_message = response_data.status_message;
            }
            else if(reply_message) {
                $scope.reply_message('special_offer');
                $scope.message = '';
            }
            else {
                $scope.price = response_data.subtotal;
            }
            isSending = false;
        };

        if (typeof $scope.user_time_zone !== 'undefined' && $scope.user_time_zone.trim() != '') {
            data_params['booking_date_times']['user_time_zone'] = $scope.user_time_zone;
        }else{
            data_params['booking_date_times']['user_time_zone'] = moment.tz.guess();
        }
        
        $scope.http_post(url,data_params,callback_function,options);
    };

    $(document).on('change','.event_type', function (event) {
        var activity_type = $('option:selected', this).attr("data-activity_type");
        var activity = $('option:selected', this).attr("data-activity");
        var sub_activity = this.value;
        $scope.event_type = { activity_type : activity_type, activity : activity, sub_activity : sub_activity };
        $scope.hidden_event_type = JSON.stringify($scope.event_type);
        event.stopPropagation();
    });

}]);

$(document).on('contextmenu', 'a[data-method="post"]', function() {
    return false;
});

$(document).on('click', 'a[data-method="post"]', function() {
    $('a[data-method="post"]').attr('disabled', 'disabled');
});