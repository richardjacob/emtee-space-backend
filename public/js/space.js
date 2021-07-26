var daterangepicker_format = $('meta[name="daterangepicker_format"]').attr('content');
var datepicker_format = $('meta[name="datepicker_format"]').attr('content');
var datedisplay_format = $('meta[name="datedisplay_format"]').attr('content');

function initialize() {
    var mapCanvas = document.getElementById('map');
    if(!mapCanvas){
        return false;
    }
    var mapOptions = {
        center: new google.maps.LatLng($('#map').attr('data-lat'), $('#map').attr('data-lng')),
        zoom: 13,
        zoomControl: true,
        scrollwheel: false,
        mapTypeControl: false,
        streetViewControl: false,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL
        },
        panControl: false,
        scaleControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(mapCanvas, mapOptions);
    var geolocpoint = new google.maps.LatLng($('#map').attr('data-lat'), $('#map').attr('data-lng'));
    map.setCenter(geolocpoint );

    var citymap = {
        center: { lat: parseFloat($('#map').attr('data-lat')), lng: parseFloat($('#map').attr('data-lng')) }
    };

    // Add the circle for this city to the map.
    var cityCircle = new google.maps.Circle({
        strokeColor: '#11848E',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#7FDDC4',
        fillOpacity: 0.35,
        map: map,
        center: citymap['center'],
        radius: 1000
    });
}

google.maps.event.addDomListener(window, 'load', initialize);

app.controller('space_detail', ['$scope', '$http', '$filter', '$q', function($scope, $http, $filter, $q) {

    var datetime_format = 'DD-MM-YYYY HH:mm:ss';
    $scope.user_location_times = function(date,time,time_zone='') {
        if (typeof date === 'undefined' || date.length === 0 || !date.trim()) {
            date = moment().format('DD-MM-YYYY')
        }
        var date_time = date+' '+time;

        if (time_zone != '') {
            current_time = moment().tz(time_zone).format(datetime_format);
        }
        else {
            current_time = moment().format(datetime_format);
        }

        current_time    = moment(current_time,datetime_format);
        given_time      = moment(date_time,datetime_format);

        var booking_type = $('#booking_type').val();
        if(booking_type != 'instant_book') {
            current_time = current_time.add('1','day');
        }

        if(given_time.isBefore(current_time)) {
            return true;
        }

        return false;
    }

    var canceller,isSending = false;

    //restrict tab key when image popup shown
    $(document).on('keydown', function(e) {
        var target = e.target;
        var shiftPressed = e.shiftKey;
        if (e.keyCode == 9) {
            if ($(target).parents('.lg-on').length) {
                return false;
            }
        }
        return true;
    });

    // Common function to perform post request
    $scope.http_post = function(url, data = {}, callback,options = {}) {
        $http.post(url,data,options).then(function(response) {
            if(response.status == 200) {
                if(callback) {
                    callback(response.data);
                }
            }
        }, function(response) {
            /*if(response.status == '500') {
                window.location.reload();
            }*/
        });
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

    // Space Slider
    $scope.detail_slider = function() {
        $('#detail-gallery').lightSlider({
            gallery: false,
            item:1,
            loop: true,
            pager: false,
            thumbItem:9,
            slideMargin:0,
            enableDrag: false,
            enableTouch:false,
            currentPagerPosition:'left',
            onSliderLoad: function(el) {
                el.lightGallery({
                    selector: '#detail-gallery .lslide',
                    subHtmlSelectorRelative:true,
                    mode: 'lg-fade',
                    closable:true,
                    autoWidth:true,
                    mousewheel:false,
                    enableDrag:true,
                    enableSwipe:true,
                    loop: true,
                    hideControlOnEnd:true,
                    slideEndAnimatoin:false,
                    thumbItem: 5,
                    thumbnail:true,
                    animateThumb: true,
                });
            }
        });
    };

    $(document).ready(function() {
        $('.bx-prev').addClass('icon icon-chevron-left icon-gray icon-size-2 ');
        $('.bx-prev').text('');
        $('.bx-next').addClass('icon icon-chevron-right icon-gray icon-size-2 ');
        $('.bx-next').text('');
        $scope.detail_slider();
    });

    // Similar listing Slider
    $(document).ready(function() {
        length = $('#similar-slider').attr('item-length');
        can_loop = (length > 3) ? true : false;

        $('#similar-slider').owlCarousel({
            loop: can_loop,
            autoplay: true,
            margin: 20,
            rtl:rtl,
            nav: false,
            items: 3,
            responsiveClass: true,
            navText:['<i class="icon icon-chevron-right custom-rotate"></i>','<i class="icon icon-chevron-right"></i>'],  
            responsive:{
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {           
                    items: 3  
                }
            }
        });
    });

    $('.open-gallery').click(function() {
        $('#detail-gallery .lslide').trigger('click');
        $scope.detail_slider();
    });

    // Wishlist Space
    $(document).on('click', '.rich-toggle-unchecked,.rich-toggle-checked', function() {
        if(typeof USER_ID == 'object') {
            window.location.href = APP_URL+'/login';
            return false;
        }
        $('.add-wishlist').addClass('loading');
        $http.get(APP_URL+"/wishlist_list?id="+ $scope.space_id, {}).then(function(response) {
            $('.add-wishlist').removeClass('loading');
            $('.wl-modal__col:nth-child(2)').removeClass('d-none');
            $scope.wishlist_list = response.data;
        });
    });

     $scope.wishlist_row_select = function(index) {
        $('.add-wishlist').addClass('loading');
        var url = common_ajax_url.save_wishlist;
        var data_params = { space_id: $scope.space_id, wishlist_id: $scope.wishlist_list[index].id, saved_id: $scope.wishlist_list[index].saved_id };
        var callback_function = function(response_data) {
            $scope.wishlist_list[index].saved_id = (response_data == 'null') ? null : response_data;
            $('.add-wishlist').removeClass('loading');
        };
        $scope.http_post(url,data_params,callback_function);

        var saved_id = ($('#wishlist_row_' + index).hasClass('text-dark-gray')) ? null : 1;
        $scope.wishlist_list[index].saved_id = saved_id;
    };

    $(document).on('submit', '.wl-modal-form', function(event) {
        event.preventDefault();
        $('.add-wishlist').addClass('loading');
        var url = common_ajax_url.wishlist_create;
        var data_params = { data: $('.wl-modal-input').val(), id: $scope.space_id };

        var callback_function = function(response_data) {
            $('.wl-modal-form').addClass('d-none');
            $('.create-wl').removeClass('d-none');
            $('.add-wishlist').removeClass('loading');
            $scope.wishlist_list = response_data;
        };
        $scope.http_post(url,data_params,callback_function);
    });

    $('#wishlist-modal').on('hidden.bs.modal', function () {
        var null_count = $filter('filter')($scope.wishlist_list, {saved_id : null});
        var checked = (null_count.length == $scope.wishlist_list.length) ? false : true;
        $('#wishlist-button').prop('checked', checked);
    });

    //  calendar triggered
    $("#view-calendar").click(function(event) {
        $("#list_start_date").datepicker("show");
    });

    //  calendar triggered
    $(".review_link").click(function(event) {
        header_height = $('header').height();
        detail_sticky = $('.detail-sticky').height();
        $(window).scrollTop($('#review-info').offset().top - (header_height+detail_sticky));
    });

    $(document).on('click','.detail-sticky li a',function(e) {
        e.preventDefault();
        var target = $(this).attr("href");
        var top = $(target).offset().top - $('header').outerHeight() - $('.detail-sticky').outerHeight();

        $('html, body').stop().animate({
            scrollTop: top
        }, 500);
    });

    $(window).scroll(function () {
        var scrollDistance = $(window).scrollTop();
        var header_height = $('header').outerHeight();
        var detail_sticky = $('.detail-sticky').outerHeight();
        $('.scroll-section').each(function (i) {
            // Calculate extra height because Map placed outer div
            var extra_height = ($(this).attr('id') == 'detail-map') ? -(header_height + detail_sticky) : 300;
            if ($(this).position().top <= scrollDistance - extra_height) {
                $('.detail-sticky li a.active').removeClass('active');
                $('.detail-sticky li a').eq(i).addClass('active');
                
            } else {
                $('.detail-sticky li a').eq(i).removeClass('active');
                // $('.booking-form').css('z-index', '9');
            }
        });
    }).scroll();

    /* Booking Functionality Start */

    $(document).ready(function() {
        $scope.refreshPrice();
        $scope.getCalendarDates();
        $('#list_start_time').val($scope.booking_date_times.start_time);
        $('#list_end_time').val($scope.booking_date_times.end_time);
        $scope.applyScope();
    });

    $scope.getUserTimeZone = function() {
        if (typeof $scope.user_time_zone !== 'undefined' && $scope.user_time_zone.trim() != '') {
            return $scope.user_time_zone;
        }
        return moment.tz.guess();
    };

    $scope.getMomentTimeWithTz = function(time_zone,datetime_format = 'DD-MM-YYYY HH:mm:ss') {
        if (time_zone != '') {
            current_date = moment().tz(time_zone).format(datetime_format);
        }
        else {
            current_date = moment().format(datetime_format);
        }
        return moment(current_date,datetime_format);
    }

    $scope.initDatePickers = function(selector,type) {
        var booking_type = $('#booking_type').val();
        var timezone = $scope.getUserTimeZone();
        var current_date = $scope.getMomentTimeWithTz(timezone);
        if(booking_type != 'instant_book') {
            current_date = current_date.add(1, 'day');
        }
        current_date = new Date(current_date);

        $(selector).datepicker({
            minDate: current_date,
            showAnim:'slideDown',
            dateFormat: datepicker_format,
            beforeShow: function(input, inst) {
                setTimeout(() => {
                    inst.dpDiv.find('a.ui-state-highlight').removeClass('ui-state-highlight');
                    $('.ui-state-disabled').removeAttr('title');
                    $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
                }, 100);
            },
            beforeShowDay: function(date) {
                var date_str = $.datepicker.formatDate('yy-mm-dd', date);
                var daynum = date.getDay();
                var now = new Date();
                now.setDate(now.getDate()-1);

                var has_not_available = $scope.not_available_days.indexOf(daynum);
                var has_date_not_available = $scope.not_available_dates.indexOf(date_str);

                return [ (has_not_available == -1 && has_date_not_available == -1) , 'highlight', $scope.currency_symbol+$scope.default_price];
            },
            onSelect: function (date,obj) {
                var selected_day = moment(date,datedisplay_format.toUpperCase());
                if(type == 'contact_host') {
                    if(selector == '#msg_start_date') {
                        $scope.msg_booking_date_times.start_date     = date;
                        $scope.msg_booking_date_times.formatted_start_date = selected_day.format('YYYY-MM-DD');
                        $scope.msg_booking_date_times.start_week_day       = selected_day.weekday();

                        var checkout = $('#msg_start_date').datepicker('getDate');
                        var checkout_obj = moment(checkout,datedisplay_format.toUpperCase());
                        var setEndDate = true;

                        if($scope.checkValidInput($scope.msg_booking_date_times.end_date)) {
                            setEndDate = true;
                        }
                        else {
                            var selected_checkout = checkout_obj.format('YYYY-MM-DD');
                            if(selected_checkout > $scope.msg_booking_date_times.formatted_end_date) {
                                setEndDate = true;
                            }
                        }
                        if(setEndDate) {
                            $('#msg_end_date').val(date);
                            $('#msg_end_date').datepicker('setDate', checkout);
                            $scope.setEndDate(date,type);
                        }
                        $('#msg_end_date').datepicker('option', 'minDate',checkout);
                        if(selector == '#msg_end_date' || $scope.booking_period == 'Single') {
                            $scope.setEndDate(date,type);
                        }
                        if($scope.booking_period == 'Multiple') {
                            setTimeout( () => $("#msg_end_date").datepicker("show") ,1);
                        }
                        else {
                            $scope.setEndDate(date,type);
                        }
                    }
                    if(selector == '#msg_end_date') {
                        $scope.setEndDate(date,type);
                    }
                }
                else {
                    if(selector == '#list_start_date') {
                        $scope.booking_date_times.start_date     = date;
                        $scope.booking_date_times.formatted_start_date = selected_day.format('YYYY-MM-DD');
                        $scope.booking_date_times.start_week_day       = selected_day.weekday();

                        var checkout = $('#list_start_date').datepicker('getDate');
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
                            $('#list_end_date').val(date);
                            $('#list_end_date').datepicker('setDate', checkout);
                            $scope.setEndDate(date,type);
                        }
                        $('#list_end_date').datepicker('option', 'minDate',checkout);
                        if($scope.booking_period == 'Multiple') {
                            setTimeout( () => $("#list_end_date").datepicker("show") ,1);
                        }
                        else {
                            $scope.setEndDate(date,type);
                        }
                    }
                    if(selector == '#list_end_date') {
                        $scope.setEndDate(date,type);
                    }
                }

                $('.tooltip').hide();
                if(date != new Date()) {
                    $('.ui-datepicker-today').removeClass('ui-datepicker-today');
                }

                $scope.applyScope();
                $scope.validateDetails();
            },
            onChangeMonthYear: function() {
                setTimeout(() => {
                    $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
                },100);
            }
        });

        $('html body').on('mouseenter', '.ui-datepicker-calendar a.ui-state-hover, .ui-datepicker-calendar a.ui-state-default', function(e) {
            $('.highlight').not('.ui-state-disabled').tooltip({container:'body'});
        });
    };

    $scope.setEndDate = function(date,type = 'booking') {
        var selected_day = moment(date,datedisplay_format.toUpperCase());
        if(type == 'contact_host') {
            $scope.msg_booking_date_times.end_date     = date;
            $scope.msg_booking_date_times.formatted_end_date = selected_day.format('YYYY-MM-DD');
            $scope.msg_booking_date_times.end_week_day       = selected_day.weekday();
        }
        else {
            $scope.booking_date_times.end_date     = date;
            $scope.booking_date_times.formatted_end_date = selected_day.format('YYYY-MM-DD');
            $scope.booking_date_times.end_week_day       = selected_day.weekday();
        }
        $scope.applyScope();
    };

    $('.event_type').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var activity_type = $('option:selected', this).attr("data-activity_type");
        var activity = $('option:selected', this).attr("data-activity");
        var sub_activity = this.value;
        $scope.event_type = { activity_type : activity_type, activity : activity, sub_activity : sub_activity };
        $scope.hidden_event_type = JSON.stringify($scope.event_type);
        setTimeout(() => $scope.validateDetails(), 1);
    });

    $('.msg_event_type').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var activity_type = $('option:selected', this).attr("data-activity_type");
        var activity = $('option:selected', this).attr("data-activity");
        var sub_activity = this.value;
        $scope.msg_event_type = { activity_type : activity_type, activity : activity, sub_activity : sub_activity };
        $scope.msg_hidden_event_type = JSON.stringify($scope.msg_event_type);

        var index = $scope.space_activities.findIndex(obj => obj.activity_type_id == activity_type);
        $scope.default_price = $scope.space_activities[index].activity_price.hourly;
    });

    $(document).ready(function () {
        if( /iPhone|iPad|iPod/i.test(navigator.userAgent) ) {
            $(document).on('change','.msg_event_type', function(event) {
                var activity_type = $('option:selected', this).attr("data-activity_type");
                var activity = $('option:selected', this).attr("data-activity");
                var sub_activity = this.value;
                $scope.msg_event_type = { activity_type : activity_type, activity : activity, sub_activity : sub_activity };
                $scope.msg_hidden_event_type = JSON.stringify($scope.msg_event_type);
                var index = $scope.space_activities.findIndex(obj => obj.activity_type_id == activity_type);
                $scope.default_price = $scope.space_activities[index].activity_price.hourly;
            });
            $(document).on('change','.event_type', function(event) {
                var activity_type = $('option:selected', this).attr("data-activity_type");
                var activity = $('option:selected', this).attr("data-activity");
                var sub_activity = this.value;
                $scope.event_type = { activity_type : activity_type, activity : activity, sub_activity : sub_activity };
                $scope.hidden_event_type = JSON.stringify($scope.event_type);
                setTimeout(() => $scope.validateDetails(), 1);
            });
        }
    });

    $scope.getCalendarDates = function() {

        var url = space_ajax_url.space_calendar;

        var data_params = { space_id : $scope.space_id };

        var callback_function = function(response_data) {
            $scope.not_available_days  = response_data.not_available_days;
            $scope.not_available_dates = response_data.not_available_dates;
            $scope.not_available_times = response_data.not_available_times;
            $scope.blocked_times       = response_data.blocked_times;
            $scope.initDatePickers('#list_start_date','list');
            $scope.initDatePickers('#list_end_date','list');
            $scope.initDatePickers('#msg_start_date','contact_host');
            $scope.initDatePickers('#msg_end_date','contact_host');
            $scope.is_calculate = '';
            $('.book-it-panel').removeClass('loading');
        };
        $scope.http_post(url,data_params,callback_function);
    };

    $scope.switchDayType = function() {
        $scope.booking_period = ($scope.booking_period == 'Single') ? 'Multiple' : 'Single';
        setTimeout(() => $scope.validateDetails(), 1);
    };

    $scope.refreshPrice = function() {
        $scope.base_hour_price = $scope.default_price;
        $scope.total_hours = 0;
        $scope.total_hour_price = 0;
        $scope.service_fee = 0;
        $scope.total_price = 0;
        $scope.security_fee = 0;
    };

    $scope.validateDetails = function() {
        var result = true;
        $scope.hidden_date_times = angular.toJson($scope.booking_date_times);

        if($scope.checkValidInput($scope.booking_date_times.start_date) || $scope.checkValidInput($scope.booking_date_times.formatted_start_date) || $scope.checkValidInput($scope.booking_date_times.start_time) || $scope.checkValidInput($scope.booking_date_times.end_time)) {
            result = false
        }

        if($scope.checkValidInput($scope.number_of_guests) || $scope.checkValidInput($scope.event_type)) {
            result = false;
        }

        if(result) {
            setTimeout(() => $scope.calculation('booking'),1);
        }
        else {
            if(isSending) {
                $scope.is_calculate = '';
                canceller.resolve();
            }
            $scope.not_available_reason = ' ';
            if(!$scope.checkValidInput($scope.event_type)) {
                var activity_type = $scope.event_type.activity_type;
                var index = $scope.space_activities.findIndex(obj => obj.activity_type_id == activity_type);
                $scope.base_hour_price = $scope.space_activities[index].activity_price.hourly;
                $scope.default_price = $scope.base_hour_price;
                $scope.applyScope();
            }
        }
    };

    $scope.getBasicDataParams = function() {
        return { space_id : $scope.space_id };
    };

    $scope.getDataParams = function(type) {
        var data_params = {};
        if(type == 'booking') {
            data_params = $scope.getBookingFormData();
        }
        else if(type == 'contact_host') {
            data_params = $scope.getContactHostFormData();
        }

        return data_params;
    };

    $scope.getBookingFormData = function() {
        var data_params = $scope.getBasicDataParams();
        data_params['event_type'] = $scope.event_type;
        data_params['booking_period'] = $scope.booking_period;
        data_params['booking_date_times'] = $scope.booking_date_times;
        data_params['number_of_guests'] = $scope.number_of_guests;
        return data_params;
    };

    $scope.getContactHostFormData = function() {
        var data_params = $scope.getBasicDataParams();
        data_params['event_type'] = $scope.msg_event_type;
        data_params['booking_period'] = $scope.booking_period;
        data_params['booking_date_times'] = $scope.msg_booking_date_times;
        data_params['number_of_guests'] = $scope.msg_number_of_guests;
        return data_params;
    };

    $scope.setPriceToBookingForm = function(price_details) {
        $scope.available_status = price_details.status;
        $scope.not_available_reason = '';
        if(price_details.status == 'Not available') {
            $scope.not_available_reason = price_details.status_message;
        }
       
        $scope.base_hour_price  = price_details.base_hour_price;
        $scope.total_hours      = price_details.total_hours;
        $scope.count_total_hour = price_details.count_total_hour;
        $scope.count_total_days = price_details.count_total_days;
        $scope.count_total_week = price_details.count_total_week;
        $scope.count_total_month= price_details.count_total_month;
        $scope.hour_amount= price_details.hour_amount;
        $scope.full_day_amount= price_details.full_day_amount;
        $scope.weekly_amount= price_details.weekly_amount;
        $scope.monthly_amount= price_details.monthly_amount;
        $scope.total_hour_price1= price_details.count_total_hour*price_details.hour_amount;
        $scope.total_full_day_price= price_details.count_total_days*price_details.full_day_amount;
        $scope.total_week_price= price_details.count_total_week*price_details.weekly_amount;
        $scope.total_month_price= price_details.count_total_month*price_details.monthly_amount;
   
       
        $scope.total_hour_price = price_details.total_hour_price;
        $scope.service_fee      = price_details.service_fee;
        $scope.security_fee     = price_details.security_fee;
        $scope.total_price      = price_details.total_price;
    };

    $scope.setPriceToContactForm = function(price_details) {
        setTimeout( () => $('#message_form').submit());
    };

    $scope.startTimeChanged = function(price_details) {
        if($scope.msg_booking_date_times.start_time > $scope.msg_booking_date_times.end_time && $scope.booking_period == 'Single') {
            var end_time = moment($scope.msg_booking_date_times.start_time,'HH:mm:ss');
            $scope.msg_booking_date_times.end_time = end_time.add(1,'hour').format("HH:mm:ss");
        }
    };

    $scope.setPriceToForm = function(type,price_details) {
        if(type == 'booking') {
            $scope.setPriceToBookingForm(price_details);
        }
        else if(type == 'contact_host') {
            $scope.setPriceToContactForm(price_details);
        }
        $scope.is_calculate = '';
    };

    $scope.calculation = function(type) {
        $scope.is_calculate = 'loading';
        var data_params = $scope.getDataParams(type);
        $scope.calculatePrice(data_params,type);
    };

    $scope.calculatePrice = function(data,type) {
        if(isSending) {
            canceller.resolve()
        }
        isSending = true;
        canceller = $q.defer();
        var options = { timeout : canceller.promise };

        var url = space_ajax_url.price_calculation;
        var callback_function = function(response_data) {
            $scope.price_calculated = true;
            $scope.setPriceToForm(type,response_data);
            isSending = false;
        };

        data['booking_date_times']['user_time_zone'] = $scope.getUserTimeZone();
        $scope.http_post(url,data,callback_function,options);
    };

    $scope.validateContactDetails = function() {
        var result = true;

        $scope.msg_booking_date_times.user_time_zone = $scope.getUserTimeZone();
        $scope.msg_hidden_date_times = angular.toJson($scope.msg_booking_date_times);
        if($scope.checkValidInput($scope.msg_booking_date_times.start_date) || $scope.checkValidInput($scope.msg_booking_date_times.formatted_start_date) || $scope.checkValidInput($scope.msg_booking_date_times.start_time) || $scope.checkValidInput($scope.msg_booking_date_times.end_time)) {
            result = false
        }
        if($scope.checkValidInput($scope.msg_number_of_guests) || $scope.checkValidInput($scope.msg_event_type) || $scope.checkValidInput($scope.question)) {
            result = false;
        }
        if($scope.booking_period == 'Single' || $scope.checkValidInput($scope.msg_booking_date_times.end_date) || $scope.checkValidInput($scope.msg_booking_date_times.formatted_end_date))

        $scope.applyScope();
        return result;
    };

    $scope.sendContactRequest = function($event) {
        $scope.contact_loading = 1;
        $event.preventDefault();
        $event.stopPropagation();
        var result = $scope.validateContactDetails();
        $scope.contact_error = (result != true);
        if(result) {
            $scope.calculation('contact_host');
        }
        else {
            $scope.contact_loading = 0;
        }
    };

    $("#book_it_form").bind("keypress", function (e) {
        if (e.keyCode == 13) {
            return false;
        }
    });

    /* Booking Functionality End */
}]);