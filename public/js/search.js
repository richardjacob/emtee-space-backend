var daterangepicker_format = $('meta[name="daterangepicker_format"]').attr('content');
var datepicker_format = $('meta[name="datepicker_format"]').attr('content');
var php_date_format = $('meta[name="php_date_format"]').attr('content');

//These variables are used to during cancel process
var prop_app_fil = '',
amen_app_fil = '',
search_on_map=''
map_search_first='';

$('.customBox').hover(function() {
    $(mark).addClass('hover');
});

app.directive('postsPagination', function() {
    return {
        restrict: 'E',
        template: '<ul class="pagination" ng-cloak>' +
        '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="search_result(1)">&laquo;</a></li>' +
        '<li ng-show="currentPage != 1"><a href="javascript:void(0)" ng-click="search_result(currentPage-1)">&lsaquo; ' + $('#pagin_prev').val() + '</a></li>' +
        '<li ng-repeat="i in range" ng-class="{active : currentPage == i}">' +
        '<a href="javascript:void(0)" ng-disabled="currentPage == i" ng-click="search_result(i)">{{i}}</a>' +
        '</li>' +
        '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="search_result(currentPage+1)">' + $('#pagin_next').val() + ' &rsaquo;</a></li>' +
        '<li ng-show="currentPage != totalPages"><a href="javascript:void(0)" ng-click="search_result(totalPages)">&raquo;</a></li>' +
        '</ul>'
    };
}).controller('search-page', ['$scope', '$http', '$compile', '$filter', '$q', function($scope, $http, $compile, $filter, $q) {
    $scope.first_search = 'Yes';
    $scope.current_date = new Date();

    $scope.totalPages = 0;
    $scope.currentPage = 1;
    $scope.range = [];
    $scope.marker_click = 0;
    $scope.map_lat_long = '';

    $scope.map_fit_bounds = '';
    var canceller,isSending = false;

    $(window).ready(function() {
        if ($(window).width() < 1025) {
            $scope.isMobile = true;
            $scope.show_map = true;
        }
        else {
            $scope.show_map = false;
        }
    });

    $(document).ready(function() {

        function map_position() {
            var header_height = $('header').outerHeight();
            var search_filter_height = $('.search_filter').outerHeight();
            $("#map_canvas").css({"top": header_height  +  search_filter_height + "px"});
        }

        function search_wrap() {
            var header_height = $('header').outerHeight();
            var search_filter_height = $('.search_filter').outerHeight();
            var search_top = $('.cls_searchtop').outerHeight();
            var map_margin = $('.cls_searchmap').outerWidth();

            $(".cls_searchwarp").css({"margin-top": header_height  + "px"});
            $(".search_filter").css({"top": header_height   + "px"});
            $(".search-wrap").css({"margin-top": header_height  + search_filter_height + "px"});
        }

        function more_filter() {
            $(".more-filter-btn").click(function() {
                var header_height = $("header").outerHeight();
                var search_filter_height = $(".search_filter").outerHeight();
                var more_filter_height = $(".more-filter .filter-btn").outerHeight();
                var more_filter_width = $(".more-filter").outerWidth();
                var ssscroll = -10;
                var window_height = $(window).outerHeight();
                $(".more-filter").css({"height": (window_height - (header_height + search_filter_height + more_filter_height)) + "px"});
                $(".more-filter").css({"margin-top": header_height  + search_filter_height + "px"});
                $(".search-page").toggleClass('no-scrol')
                $('.footheader').toggleClass('hide');
                $(".cls_moreftl_btn").css({"width": more_filter_width + "px"});
            });     
        }

        function mobile_mode_functions() {
            map_position();
            search_wrap();
            more_filter();
        }

        $(window).scroll(function () {
            mobile_mode_functions();
        });

        $(window).resize(function () {
            mobile_mode_functions();
        });

        $('.filter-btn a').click(function() {
            $(this).closest(".dropdown-menu").removeClass("show");
            $(this).closest(".dropdown").removeClass("show");
            $(this).closest(".dropdown-toggle").removeClass("active");
            $(this).closest(".dropdown-toggle").attr("[aria-expanded='true']");
        });

        $('.more-filter-btn').click(function() {
            $scope.changeMoreFilter();
        });

        $('.reset-more_filter').click(function() {
            $(".search-page").removeClass('no-scrol');
            $scope.changeMoreFilter();
        });

        $('.show-map').click(function() {
            $(this).hide();
            $('.show-result').show();
            $('.map-canvas').addClass('active');
            $('.cls_searchmap').css('display', 'block');
            $('.footheader').addClass('hide');
            $scope.search_result();
        });
        $('.show-result').click(function() {
            $(this).hide();
            $('.show-map').show();
            $('.map-canvas').removeClass('active');
            $('.cls_searchmap').css('display', 'none');
            $('.footheader').removeClass('hide');
        }); 

        $('.show-all-toggle').click(function() {
            var target = $(this).data('target-filter');
            $('.'+target).toggleClass('d-none');
             $(this).parent('.all-list').toggleClass('active');
        });

        $scope.setParams();
        setTimeout( () => mobile_mode_functions(),500);
    });

    $(document).ready(function() {
        var checkin = moment($scope.checkin,daterangepicker_format).toDate(); new Date($('#checkin').val()); 
        var checkout = moment($scope.checkout,daterangepicker_format).toDate(); new Date($('#checkout').val());
        var today = new Date();
        start = moment();

        $('.dbdate').daterangepicker({
            autoApply: true,
            applyButton: false,
            cancelClass: 'd-none',
            parentEl: '.search-date',
            resetButton: false,
            autoUpdateInput: false,
            linkedCalendars: false,
            locale: {
                format: daterangepicker_format,
                resetLabel: CLEAR_LANG,
                applyLabel: APPLY_LANG
            },
            minDate: today,
            alwaysShowCalendars: true,
            inline:true,
        });

        $('.dbdate').on('apply.daterangepicker', function(ev, picker) {
            $scope.checkin = picker.startDate.format(daterangepicker_format);
            $scope.checkout = picker.endDate.format(daterangepicker_format);
            $scope.startTimeChanged();
            $scope.applyScope();
        });

        if(checkin != 'Invalid Date' && checkout != 'Invalid Date') {
            var picker = $('.dbdate').data('daterangepicker');
            picker.setStartDate(checkin);
            picker.setEndDate(checkout);
        }
    });

    $scope.applyScope = function() {
        if(!$scope.$$phase) {
            $scope.$apply();
        }
    }

    $scope.getSearchTitle = function() {
        var search_title = '';
        if($scope.checkin != '') {
            if($scope.checkin == $scope.checkout) {
                search_title += $scope.format_date($scope.checkin, 'DD MMM');
            }
            else {
                search_title += $scope.format_date($scope.checkin, 'DD MMM') +' - '+ $scope.format_date($scope.checkout, 'DD MMM');
            }
        }
        if($scope.start_time != '' && $scope.end_time != '' && $scope.checkin == $scope.checkout) {
            search_title += ' (' + $scope.formatTime($scope.start_time, 'hh:mm A') +' - '+ $scope.formatTime($scope.end_time, 'hh:mm A') + ')';
        }
        return search_title
    };

    $scope.apply_date_filter = function(picker) {
        startDate = picker.startDate;

        if (($scope.checkin == startDate.format(daterangepicker_format) && !$scope.reload_on_close)) {
            if($scope.checkin == '') {
                $('.dbdate').removeClass('active');
                $('.gut5').removeClass('active');
            }
            return true;
        }

        $scope.checkin = startDate.format(daterangepicker_format);

        if($scope.checkin != '') {
            $('.dbdate').addClass('active');
            $('.gut5').addClass('active');
        }
        else {
            $('.dbdate').removeClass('active');
            $('.gut5').removeClass('active');
        }

        $scope.search_result();
    };

    // Change End time value based on on strat time
    $scope.startTimeChanged = function() {
        if($scope.checkin == $scope.checkout) {
            if($scope.start_time >= $scope.end_time) {
                $scope.end_time = '';
            }
        }
    };

    $scope.reload_on_close = false
    $('.dbdate').on('reset.daterangepicker', function(ev, picker) {
        $scope.reload_on_close = false
        if ($scope.checkin != '') {
            $scope.reload_on_close = true
        }

        $scope.checkin = '';
        $('.dbdate').removeClass('active');
        $('.gut5').removeClass('active');
        var picker = $('#checkin,.dbdate, .gut5').data('daterangepicker');
        $scope.is_filter_active('date')
    });

    $scope.saveWishlist = function(space_detail) {
        if (typeof USER_ID == 'object') {
            $http.get(APP_URL + "/wishlist_list", {}).then(function(response) {
                window.location.href = APP_URL + '/login';
            });
            return false;
        }
        $('.add-wishlist').addClass('loading');

        $('.background-listing-img').css('background-image', 'url(' + space_detail.photo_name + ')');
        $('.host-profile-img').attr('src', space_detail.users.profile_picture.src);
        $('.wl-modal-listing-name').text(space_detail.name);
        $('.wl-modal-listing-address').text(space_detail.space_address.city);
        $('.wl-modal-footer-input').val(space_detail.space_address.city);

        $scope.space_id = space_detail.id;

        $http.get(APP_URL + "/wishlist_list?id=" + $scope.space_id, {}).then(function(response) {
            $scope.wishlist_list = response.data;
            $('.wl-modal-form').addClass('d-none');
            $('.add-wishlist').removeClass('loading');
            $('.create-wl').removeClass('d-none');
        });
    };

    $scope.wishlist_row_select = function(index) {

        $http.post(APP_URL + "/save_wishlist", {
            space_id: $scope.space_id,
            wishlist_id: $scope.wishlist_list[index].id,
            saved_id: $scope.wishlist_list[index].saved_id
        }).then(function(response) {
            if (response.data == 'null')
                $scope.wishlist_list[index].saved_id = null;
            else
                $scope.wishlist_list[index].saved_id = response.data;
        });

        if ($('#wishlist_row_' + index).hasClass('text-dark-gray'))
            $scope.wishlist_list[index].saved_id = null;
        else
            $scope.wishlist_list[index].saved_id = 1;
    };

    $(document).on('submit', '.wl-modal-form', function(event) {
        event.preventDefault();
        $('.add-wishlist').addClass('loading');
        $http.post(APP_URL + "/wishlist_create", {
            data: $('#wish_list_text').val(),
            id: $scope.space_id
        }).then(function(response) {
            $('.wl-modal-form').addClass('d-none');
            $('.add-wishlist').removeClass('loading');
            $('.create-wl').removeClass('d-none');
            $('#wish_list_text').val('');
            $scope.wishlist_list = response.data;
            event.preventDefault();
        });
        event.preventDefault();
    });

    $('.wl-modal-close').click(function() {
        var null_count = $filter('filter')($scope.wishlist_list, {
            saved_id: null
        });

        if (null_count.length == $scope.wishlist_list.length)
            $('#wishlist-widget-' + $scope.space_id).prop('checked', false);
        else
            $('#wishlist-widget-' + $scope.space_id).prop('checked', true);
    });

    $(document).ready(function() {
        $scope.map_lat_long = '';

        var amenities = [];
        $('.amenities:checked').each(function(i) {
            amenities[i] = $(this).val();
        });

        var location_val = $("#location").val();
        $("#header-search-form").val(location_val);
        $("#modal-locations").val(location_val);

        createSlider(document.getElementById('slider'));
        createSlider(document.getElementById('mob_slider'));

        $('.show-more').click(function() {
            $(this).children('span').toggleClass('d-none');
            $(this).parent().parent().children('div').children().toggleClass('filters-more');
        });

        $("#more_filters").click(function() {
            $(".toggle-group").css("display", "block");
            $(".toggle-hide").css("display", "none");
            $(".sidebar").css("height", "87%");
        });
    });


    $('#wishlist-modal').on('hidden.bs.modal', function () {
        var null_count = $filter('filter')($scope.wishlist_list, {
            saved_id: null
        });
        var checked = (null_count.length == $scope.wishlist_list.length) ? false : true;
        $('#wishlist-widget-' + $scope.space_id).prop('checked', checked);
    });



    function createSlider(selector)
    {
        var direct = 'ltr';
        if ($('html').attr('lang') == "ar") {
            direct = 'rtl';
        }

        noUiSlider.create(selector, {
            start: [min_slider_price_value, max_slider_price_value],
            connect: true,
            step: 1,
            margin: 2,
            direction: direct,
            range: {
                'min': min_slider_price,
                'max': max_slider_price
            }
        });

        selector.noUiSlider.on('update', function(values, handle) {
            if (handle) {
                $scope.max_value = parseInt(values[handle]);
            }
            else {
                $scope.min_value = parseInt(values[handle]);
            }
            $scope.applyScope();
        });

        selector.noUiSlider.on('change', function(values, handle) {
            $scope.min_value = parseInt(values[0]);
            $scope.max_value = parseInt(values[1]);
            $scope.update_filter_status();
        });
    }

    function map_loading() {
        if ($('.search-wrap').css('display') == 'none') {
            $('.map').addClass('loading');
        }
    }

    function map_loading_remove() {
        if ($scope.first_search == 'Yes') {
            $scope.first_search = 'No';            
            $('.map.hide-sm-view').hide();
            $('.search-wrap').show();
            $('.filter-div').hide();
        }
        $('.map').removeClass('loading');
    }

    var location1 = getParameterByName('location');

    var current_url = (window.location.href).replace('/s', '/searchResult');

    pageNumber = 1;

    if (pageNumber === undefined) {
        pageNumber = '1';
    }

    $('.search-wrap').addClass('loading');
    map_loading();

    $scope.on_mouse = function(index) {
        if (markers[index] != undefined) {
            mark = markers[index].div_;
            $(mark).addClass('hover');
        }
    };
    $scope.out_mouse = function(index) {
        if (markers[index] != undefined) {
            mark = markers[index].div_;
            $(mark).removeClass('hover');
        }
    };

    $scope.setParams = function() {
        setGetParameter('min_price', $scope.min_value);
        setGetParameter('max_price', $scope.max_value);
    }

    // Get Checkbox Checked Values Based on given selector
    $scope.getSelectedData = function(selector) {
        var value = [];
        $(selector+':checked').each(function() {
            value.push($(this).val());
        });
        return value;
    };

    // Check input is valid or not
    $scope.checkInValidInput = function(value) {
        return (value == undefined || value == 0 || value == '');
    };

    $scope.search_result = function(pageNumber = '1') {

        if(isSending) {
            canceller.resolve()
        }
        isSending = true;
        canceller = $q.defer();

        if($scope.checkInValidInput($scope.currentPage) && $scope.currentPage == pageNumber) {
            return false
        }
        var min_price = $scope.min_value;
        var max_price = $scope.max_value;
        var space_type      = $scope.checkInValidInput($scope.space_type) ? [] : $scope.space_type;
        var activity_type   = $scope.activity_type;
        var amenities       = $scope.getSelectedData('.amenities');
        var services        = $scope.getSelectedData('.services');
        var space_rules     = $scope.getSelectedData('.space_rules');
        var special_feature = $scope.getSelectedData('.special_feature');
        var space_style     = $scope.getSelectedData('.space_style');
        var checkin         = $scope.checkin;
        var checkout        = $scope.checkout;
        var start_time      = $scope.start_time;
        var end_time        = $scope.end_time;
        var instant_book    = $scope.instant_book;
        if($scope.checkInValidInput($scope.search_guest)) {
            $scope.search_guest = 1;
        }
        var guest_select    = $scope.search_guest;

        var map_details = "";
        if ($.trim($scope.map_lat_long) != '' && search_on_map != '') {
            var map_details = $scope.map_lat_long;
        }

        setGetParameter('space_type', space_type);
        setGetParameter('activity_type', activity_type);
        setGetParameter('checkin', checkin);
        setGetParameter('checkout', checkout);
        setGetParameter('start_time', start_time);
        setGetParameter('end_time', end_time);
        setGetParameter('guests', guest_select);
        setGetParameter('min_price', min_price);
        setGetParameter('max_price', max_price);
        setGetParameter('page', pageNumber);
        setGetParameter('instant_book', instant_book);
        setGetParameter('php_date_format', php_date_format);
        setGetParameter('amenities', amenities);
        setGetParameter('services', services);
        setGetParameter('space_rules', space_rules);
        setGetParameter('special_feature', special_feature);
        setGetParameter('space_style', space_style);

        var location1 = getParameterByName('location');

        $('.search-wrap').addClass('loading');
        $scope.search_loading = true;
        map_loading();
        $http.post('searchResult?page=' + pageNumber, {
            location: location1,
            checkin: checkin,
            checkout: checkout,
            start_time: start_time,
            end_time: end_time,
            min_price: min_price,
            max_price: max_price,
            activity_type: activity_type,
            space_type: space_type,
            amenities: amenities,
            services: services,
            space_rules: space_rules,
            special_feature: special_feature,
            space_style: space_style,
            guest: guest_select,
            map_details: map_details,
            instant_book: instant_book
        },
        { timeout : canceller.promise}
        ).then(function(response) {
            $scope.space_result = response.data;
            $scope.checkin = checkin;
            $scope.checkout = checkout;
            $scope.totalPages = response.data.last_page;
            $scope.currentPage = response.data.current_page;
            // Pagination Range
            var pages = [];
            for (var i = 1; i <= response.data.last_page; i++) {
                pages.push(i);
            }
            $scope.range = pages;

            var bounds = new google.maps.LatLngBounds();
            angular.forEach(response.data.data, function(value,key) {
                var lat = value["space_address"]["latitude"];
                var lng = value["space_address"]["longitude"]; 
                bounds.extend(new google.maps.LatLng(lat,lng));
            });

            $scope.map_fit_bounds = 'Yes';
            if($(window).width() > 760 || $scope.first_search != 'Yes') {
                if(response.data.total>0 && search_on_map=='') {
                    $scope.viewport = bounds;
                    $scope.cLat=response.data.data[0]["space_address"]["latitude"];
                    $scope.cLong=response.data.data[0]["space_address"]["longitude"];
                    map_search_first='Yes';

                    initialize(bounds);
                }
                else if(search_on_map=='') {
                    $scope.viewport = $scope.locationViewport;
                    $scope.cLat=$scope.locationLat;
                    $scope.cLong=$scope.locationLong;

                    initialize();
                }
            }

            setTimeout(() => {
                $('.search-img-slide').owlCarousel({
                  loop: false,
                  autoplay: true,
                  rtl:rtl,
                  nav: false,
                  dots: true,
                  items: 1,
                  responsiveClass: true,
                  navText:['<i class="icon icon-chevron-right custom-rotate"></i>','<i class="icon icon-chevron-right"></i>']
              });
            },1);

            $('.search-img-slide').owlCarousel('refresh');

            $('.search-wrap').removeClass('loading');
            $('.selectpicker').selectpicker();
            map_loading_remove();
            $scope.search_loading = false;
            marker(response.data);
            isSending = false;
        });
    };

    $scope.changeMoreFilter = function() {
        $('.more-filter').toggleClass('active');
        $('.search-wrap').toggleClass('d-md-flex');
        $('.search-wrap').toggleClass('d-none');
        $('.cls_searchwarp').toggleClass('active');
    };

    $scope.apply_filter = function() {
        if ($(window).width() < 760) {
            $('.search-wrap').show();
        }
        else {
            $scope.search_result();
        }
    };
    $scope.remove_filter = function(parameter) {
        $('.' + parameter).removeAttr('checked');
        var paramName = parameter.replace('-', '_');
        var paramValue = '';
        setGetParameter(paramName, paramValue)
        $('.' + parameter + '_tag').addClass('d-none');

        $scope.search_result();
    };

    $scope.format_date =function(date, format) {
        if($scope.checkInValidInput(date)) {
            return '';
        }
        return moment(date,daterangepicker_format).format(daterangepicker_format);
    }

    $scope.formatTime =function(time, format) {
        if($scope.checkInValidInput(time)) {
            return '';
        }
        return moment(time,'hh:mm:ss').format(format);
    }

    $scope.filter_status = [];
    $scope.filter_text = [];
    $scope.update_filter_status= function()
    {
        room_types_length = $('[id^="room_type_"]:checked').length;
        min_price = $scope.min_value;
        max_price = $scope.max_value;

        more_filters_count = 0;
        $scope.search_bedrooms > 0 ? (more_filters_count++) : '';
        $scope.search_beds > 0 ? (more_filters_count++) : '';
        $scope.search_bath > 0 ? (more_filters_count++) : '';
        more_filters_amenities_length = $('[id^="amenities_"]:checked').length;
        more_filters_services_length = $('[id^="services_"]:checked').length;
        more_filters_space_rules_length = $('[id^="space_rules_"]:checked').length;
        more_filters_special_features_length = $('[id^="special_feature_"]:checked').length;
        more_filters_space_styles_length = $('[id^="space_style_"]:checked').length;
        more_filters_count  += [more_filters_amenities_length, more_filters_services_length, more_filters_space_rules_length, more_filters_special_features_length,more_filters_space_styles_length].reduce((partial_sum, a) => partial_sum + a,0);

        filters_count = 0;
        filters_count += ($scope.instant_book != '0') ? 1 : 0;
        filters_count += (min_price > min_slider_price || max_price < max_slider_price) ? 1 : 0;

        if($scope.isMobile) {
            more_filters_count += filters_count;
        }

        var date_filter = ($scope.checkin && $scope.checkout)? true: false;
        var time_filter = ($scope.start_time != '' && $scope.end_time != '') ? true: false;
        $scope.filter_status['guests'] = $scope.search_guest > 1 ? true: false;
        $scope.filter_status['room_types'] = (room_types_length > 0) ? true: false;
        $scope.filter_status['prices'] = (min_price > min_slider_price || max_price < max_slider_price) ? true: false;
        $scope.filter_status['instant_book'] = ($scope.instant_book != '0') ? true: false;
        $scope.filter_status['more_filters'] = (more_filters_count > 0) ? true: false;

        $scope.filter_status['date_time'] = (date_filter || time_filter) ? true: false;
        $scope.filter_status['date'] = date_filter;
        $scope.filter_status['time'] = time_filter;

        price_text = '';
        if(min_price > min_slider_price && max_price < max_slider_price)
        {
            price_text = $scope.currency_symbol+min_price+' - '+$scope.currency_symbol+max_price;
        }
        else if(min_price > min_slider_price)
        {
            price_text = $scope.currency_symbol+min_price+'+ ';   
        }
        else if(max_price < max_slider_price)
        {
            price_text = 'Up to '+$scope.currency_symbol+max_price;
        }

        $scope.filter_text['room_types'] =  ' · '+room_types_length;
        $scope.filter_text['prices'] = price_text;
        $scope.filter_text['more_filters'] =  ' · '+more_filters_count;
        $scope.filter_text['filters'] =  ' · '+filters_count;
        $scope.filter_text['filters_count'] =  filters_count;
        $scope.applyScope();

        $scope.guests = $scope.search_guest;
    }
    $(document).ready(function(){
        $scope.update_filter_status();
    });
    $('.guestbut').click(function(){
        $scope.update_filter_status();
    });
    $('.amenities, .services, .space_rules, .special_feature, .space_style').click(function(){
        $scope.update_filter_status();
    });
    $scope.filter_btn_text = function(filter)
    {
        $scope.update_filter_status();
        btn_text = $scope.filter_text[filter];
        return btn_text;
    }
    $scope.is_filter_active = function(filter)
    {
        $scope.update_filter_status();
        result = false;
        result = $scope.filter_status[filter];
        return result;
    }
    $scope.filter_active = function(filter)
    {
        is_active = ($scope.is_filter_active(filter) || $scope.opened_filter == filter);
        class_name = (is_active) ? 'active' : '';
        return class_name;
    }
    $scope.update_opened_filter = function(filter)
    {
        // Close Previous Opened Filter Dropdown
        $(".show:not(.inner)").removeClass('show');
        if(filter != 'more_filters' && $('.more-filter').hasClass('active')) {
            $scope.changeMoreFilter();
            $scope.reset_filters('more_filters')
        }

        if($scope.opened_filter == filter) {
            setTimeout( () => {
                $(".show:not(.inner)").removeClass('show');
            },1);
            $scope.reset_filters(filter);
        }
        else {
            $scope.opened_filter = filter;
        }
    };

    $scope.apply_filters = function(filter) {

        if(filter == 'date_time') {
            picker = $(".dbdate").data('daterangepicker');
            startDate = picker.startDate;
            endDate = picker.endDate;
            
            $scope.checkin = startDate.format(daterangepicker_format);
            $('#checkin').val($scope.checkin);

            if (endDate == null) {
                start_date = moment(startDate.format("YYYY-MM-DD"));
                start_date = start_date.add(1,'days');
                $scope.checkout = start_date.format(daterangepicker_format);
                $('#checkout').val($scope.checkout);
                $(".dbdate").data('daterangepicker').setEndDate($scope.checkout);
            }
            else if(!endDate.isValid()) {
                $scope.checkin = '';
                $scope.checkout = '';
            }
            else {
                $scope.checkout = endDate.format(daterangepicker_format);
                $('#checkout').val($scope.checkout);
            }

            $(".cls_date_filter").removeClass('show');
        }

        if(filter == 'location_refinement') {
            var location = $('#header-search-form-mob').val();
            var locations="";
            if(location) {
                locations = location.replace(" ", "+");
            }
            setGetParameter('location', locations);
            $('#search-modal--sm').addClass('d-none');
            $('#search-modal--sm').attr('aria-hidden', 'true');
        }

        $scope.search_result();
        $scope.opened_filter = '';
        $scope.update_filter_status();
    };

    $scope.resetDateTimefilter = function() {
        var checkin = getParameterByName('checkin');

        picker = $(".dbdate").data('daterangepicker');
        picker.setStartDate(moment().startOf('day'));
        picker.setEndDate(moment().startOf('day'));

        $scope.checkin = $scope.checkout = $scope.start_time = $scope.end_time = '';
        if(checkin != '') {
            $scope.search_result();
            $(".cls_date_filter").removeClass('show');
        }
        $scope.opened_filter = '';
        $scope.update_filter_status();
    };

    $scope.reset_filters = function(filter) {
        if(filter == 'date_time') {
            picker = $(".dbdate").data('daterangepicker');
            $scope.checkin = start_date = getParameterByName('checkin');
            $scope.checkout = end_date = getParameterByName('checkout');
            if(start_date != '' && end_date != '') {
                picker.setStartDate(start_date);
                picker.setEndDate(end_date);
            }
            else {
                picker.setStartDate(moment().startOf('day'));
                picker.setEndDate(moment().startOf('day'));
            }

            start_time = getParameterByName('start_time');
            end_time = getParameterByName('end_time');
            $scope.start_time = start_time;
            $scope.end_time = end_time;

            $(".cls_date_filter").removeClass('show');
        }
        if(filter == 'guests') {
            guests = getParameterByName('guests');
            if(!guests)
            {
                guests = 1;
            }
            guests = guests-0;
            $scope.search_guest = guests;
        }
        if(filter == 'prices') {
            $scope.price_reset();
        }
        if(filter == 'instant_book') {
            instant_book = getParameterByName('instant_book');
            $scope.instant_book  =instant_book;
        }
        if(filter == 'more_filters') {
            $scope.resetMoreFilter();
        }
        if(filter == 'filters') {
            $scope.price_reset();
            $scope.resetMoreFilter();
        }
        $scope.opened_filter = '';
        $scope.update_filter_status();
        $scope.applyScope();
    };

    $scope.price_reset = function() {
        var min_price_check = getParameterByName('min_price');
        var max_price_check = getParameterByName('max_price');

        var slider_check = document.getElementById('slider');
        slider_check.noUiSlider.set([min_price_check-0, max_price_check-0]);
    };
    
    $scope.resetMoreFilter = function() {
        $scope.resetCheckBoxTypes('amenities');
        $scope.resetCheckBoxTypes('services');
        $scope.resetCheckBoxTypes('space_rules');
        $scope.resetCheckBoxTypes('special_feature');
        $scope.resetCheckBoxTypes('space_style');
    };

    $scope.resetCheckBoxTypes = function(selector) {
        filter = getParameterByName(selector);
        filter_array = filter.split(',');
        $('.'+selector).prop('checked', false);
        $.each(filter_array, function(i, v){
            $('#'+selector+'_'+v).prop('checked', true);
        });
    };

    function setGetParameter(paramName, paramValue) {
        var url = window.location.href;

        if (url.indexOf(paramName + "=") >= 0) {
            var prefix = url.substring(0, url.indexOf(paramName));
            var suffix = url.substring(url.indexOf(paramName));
            suffix = suffix.substring(suffix.indexOf("=") + 1);
            suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
            url = prefix + paramName + "=" + paramValue + suffix;
        } else {
            if (url.indexOf("?") < 0)
                url += "?" + paramName + "=" + paramValue;
            else
                url += "&" + paramName + "=" + paramValue;
        }
        history.pushState(null, null, url);
    }

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    var viewport = $scope.locationViewport = JSON.parse($('#viewport').val());
    var lat0 = '';
    var long0 = '';
    var lat1 = '';
    var long1 = '';
    var infoBubble = new InfoBubble({
        maxWidth: 3000
    });
    var bounds;

    angular.forEach(viewport, function(key, value) {
        lat0 = viewport['southwest']['lat'];
        long0 = viewport['southwest']['lng'];
        lat1 = viewport['northeast']['lat'];
        long1 = viewport['northeast']['lng'];
    });

    var bounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(lat0, long0),
        new google.maps.LatLng(lat1, long1)
        );

    $scope.viewport = $scope.locationViewport = bounds;

    setTimeout(function() {
        initializeMap();
        $scope.map_lat_long = '';
    }, 1000);


    function initializeMap() {

        autocomplete = new google.maps.places.Autocomplete(document.getElementById('header-search-form'), {
            types: ['geocode']
        });
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var location = $('#header-search-form').val();
            var locations = location.replace(" ", "+");
            setGetParameter('location', locations);
            var place = autocomplete.getPlace();
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            if (place && place.geometry && place.geometry.viewport)
                $scope.viewport  = $scope.locationViewport = place.geometry.viewport;

            $scope.cLat = $scope.locationLat = latitude;
            $scope.cLong = $scope.locationLong = longitude;

            $scope.map_lat_long = '';
            search_on_map = '';
            $(".search_new_header").show();
            $scope.search_result();
            initialize();
        });

        sm_autocomplete1 = new google.maps.places.Autocomplete(document.getElementById('header-search-form-mob'), {
            types: ['geocode']
        });
        google.maps.event.addListener(sm_autocomplete1, 'place_changed', function() {
            $("#header-search-form").val($("#header-search-form-mob").val());
            var location = $('#header-search-form-mob').val();
            var locations = location.replace(" ", "+");
            var place = sm_autocomplete1.getPlace();
            if(!place.geometry) {
                return false;
            }
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            if (place && place.geometry && place.geometry.viewport)
                $scope.viewport  = $scope.locationViewport= place.geometry.viewport;

            $scope.cLat = $scope.locationLat = latitude;
            $scope.cLong = $scope.locationLong = longitude;

            $scope.map_lat_long = '';
            search_on_map = '';
            setGetParameter('location', locations);
            $scope.apply_filters('location_refinement');
        });
    }

    $scope.zoom = '';
    $scope.cLat = '';
    $scope.cLong = '';
    $scope.locationLat = '';
    $scope.locationLong = '';
    var html = '';
    var markers = [];
    var map;
    var infowindow = new google.maps.InfoWindow({
        content: html
    });

    $(document).ready(function(){
        $scope.search_result();
    });

    function initialize(value = '') {

        if ($scope.zoom == '') {
            var zoom_set = 10;
        } else {
            var zoom_set = $scope.zoom;
        }
        if ($("#lat").val() == 0) {
            var zoom_set = 1;
        }
        if ($scope.cLat == '' && $scope.cLong == '') {
            var latitude = $("#lat").val();
            var longitude = $("#long").val();
        } else {
            var latitude = $scope.cLat;
            var longitude = $scope.cLong;
        }

        var myCenter = new google.maps.LatLng(latitude, longitude);

        var mapProp = {
            scrollwheel: false,
            center: myCenter,
            zoom: zoom_set,
            minZoom: 2,
            maxZoom: 18,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP,
                style: google.maps.ZoomControlStyle.SMALL
            },
            mapTypeControl: false,
            streetViewControl: false,
            navigationControl: false,
            backgroundColor: '#a4ddf5',
            gestureHandling: 'cooperative',
            styles: [

            {
                featureType: 'water',
                elementType: 'geometry',
                stylers: [{
                    color: '#a4ddf5'
                }]
            }
            ],
        }

        map = new google.maps.Map(document.getElementById("map_canvas"), mapProp);
        if (latitude != 0 && longitude != 0) {
            map.fitBounds($scope.viewport);
        }
        google.maps.event.addListener(map, 'idle', function() {
            $scope.map_fit_bounds = '';
            $scope.zoom = map.getZoom();

            var zoom = map.getZoom();
            var bounds = map.getBounds();
            var minLat = bounds.getSouthWest().lat();
            var minLong = bounds.getSouthWest().lng();
            var maxLat = bounds.getNorthEast().lat();
            var maxLong = bounds.getNorthEast().lng();
            var cLat = bounds.getCenter().lat();
            var cLong = bounds.getCenter().lng();

            $scope.cLat = bounds.getCenter().lat();
            $scope.cLong = bounds.getCenter().lng();

            map_display = $(".map").css('display');
            if (map_display != 'none') {
                $scope.map_lat_long = zoom + '~' + bounds + '~' + minLat + '~' + minLong + '~' + maxLat + '~' + maxLong + '~' + cLat + '~' + cLong;
            } else {
                $scope.map_lat_long = '';
            }
            var redo_search = '';
            $('.map-auto-refresh-checkbox:checked').each(function(i) {
                redo_search = $(this).val();
            });
            //alert(redo_search);
            if (redo_search == 'true') {

            } else {
                $(".map-auto-refresh").addClass('d-none');
                $(".map-manual-refresh").removeClass('d-none');
            }
        });

        var homeControlDiv = document.createElement('div');
        var homeControl = new HomeControl(homeControlDiv, map);

        map.controls[google.maps.ControlPosition.LEFT_TOP].push(homeControlDiv);

        google.maps.event.addListener(map, 'dragend', function() {
            search_on_map='Yes';
            if (infoBubble.isOpen()) {
                infoBubble.close();
                infoBubble = new InfoBubble({
                    maxWidth: 3000
                });
            }
            $scope.zoom = map.getZoom();

            var zoom = map.getZoom();
            var bounds = map.getBounds();
            var minLat = bounds.getSouthWest().lat();
            var minLong = bounds.getSouthWest().lng();
            var maxLat = bounds.getNorthEast().lat();
            var maxLong = bounds.getNorthEast().lng();
            var cLat = bounds.getCenter().lat();
            var cLong = bounds.getCenter().lng();

            $scope.cLat = bounds.getCenter().lat();
            $scope.cLong = bounds.getCenter().lng();

            var map_lat_long = zoom + '~' + bounds + '~' + minLat + '~' + minLong + '~' + maxLat + '~' + maxLong + '~' + cLat + '~' + cLong;

            old_map_lat_long = $scope.map_lat_long
            $scope.map_lat_long = map_lat_long;
            var redo_search = '';
            $('.map-auto-refresh-checkbox:checked').each(function(i) {
                redo_search = $(this).val();
            });

            if (redo_search == 'true') {
                if(old_map_lat_long != $scope.map_lat_long){
                    $(".map-auto-refresh").removeClass('d-none');
                    $(".map-manual-refresh").addClass('d-none');
                    $scope.search_result();
                }
            } else {
                $(".map-auto-refresh").addClass('d-none');
                $(".map-manual-refresh").removeClass('d-none');
            }
        });
        $scope.infowindow = '';
        function fixInfoWindow() {
            //Here we redefine set() method.
            //If it is called for map option, we hide InfoWindow, if "noSupress" option isnt true.
            //As Google doesn't know about this option, its InfoWindows will not be opened.
            var set = google.maps.InfoWindow.prototype.set;
            google.maps.InfoWindow.prototype.set = function (key, val) {
                if (key === 'map') {
                    if (!this.get('noSupress')) {
                        // console.log('This InfoWindow is supressed. To enable it, set "noSupress" option to true');
                        $scope.infowindow = this;
                        // return;
                    }
                }
                set.apply(this, arguments);
            }
        }
        fixInfoWindow();

        google.maps.event.addListener(map, 'click', function() {

            if ($scope.marker_click > 0) {
                $scope.marker_click = 0;
            }

            if (infoBubble.isOpen()) {
                infoBubble.close();
                infoBubble = new InfoBubble({
                    maxWidth: 3000
                });
            }
            if($scope.infowindow != '')
            {
                $scope.infowindow.close();   
            }
        });
        google.maps.event.addListenerOnce(map, 'mousemove', function(){
            google.maps.event.addListener(map, 'zoom_changed', function(e) {
                search_on_map='Yes';
                if (infoBubble.isOpen()) {
                    infoBubble.close();
                    infoBubble = new InfoBubble({
                        maxWidth: 3000
                    });
                }
                $scope.zoom = map.getZoom();

                var zoom = map.getZoom();
                var bounds = map.getBounds();
                var minLat = bounds.getSouthWest().lat();
                var minLong = bounds.getSouthWest().lng();
                var maxLat = bounds.getNorthEast().lat();
                var maxLong = bounds.getNorthEast().lng();
                var cLat = bounds.getCenter().lat();
                var cLong = bounds.getCenter().lng();
                $scope.cLat = bounds.getCenter().lat();
                $scope.cLong = bounds.getCenter().lng();
                var map_lat_long = zoom + '~' + bounds + '~' + minLat + '~' + minLong + '~' + maxLat + '~' + maxLong + '~' + cLat + '~' + cLong;

                old_map_lat_long = $scope.map_lat_long
                $scope.map_lat_long = map_lat_long;

                var redo_search = '';
                $('.map-auto-refresh-checkbox:checked').each(function(i) {
                    redo_search = $(this).val();
                });

                if (redo_search == 'true') {
                    if(old_map_lat_long != $scope.map_lat_long){
                        $(".map-auto-refresh").removeClass('d-none');
                        $(".map-manual-refresh").addClass('d-none');
                        $scope.search_result();
                    }
                } else {
                    $(".map-auto-refresh").addClass('d-none');
                    $(".map-manual-refresh").removeClass('d-none');
                }
            });
        });
    }

    function HomeControl(controlDiv, map) {
        var controlText = document.createElement('div');
        controlText.style.position = 'relative';
        controlText.style.padding = '5px';
        controlText.style.margin = '-65px 0px 0px 50px';
        controlText.style.fontSize = '14px';
        controlText.innerHTML = '<div class="map-refresh-controls google"><a class="map-manual-refresh btn btn-primary d-none">' + $('#redo_search_value').val() + ' <i class="icon icon-refresh icon-space-left"></i></a><div class="panel map-auto-refresh"><label class="checkbox"><input type="checkbox" checked="checked" name="redo_search" value="true" class="map-auto-refresh-checkbox"><small>' + $('#current_language').val() + '</small></label></div></div>'
        controlDiv.appendChild(controlText);

        // Setup click-event listener: simply set the map to London
        google.maps.event.addDomListener(controlText, 'click', function() {});
    }
    /*Overlay Script*/
    function TxtOverlay(pos, txt, cls, map) {

        // Now initialize all properties.
        this.pos = pos;
        this.txt_ = txt;
        this.cls_ = cls;
        this.map_ = map;

        // We define a property to hold the image's
        // div. We'll actually create this div
        // upon receipt of the add() method so we'll
        // leave it null for now.
        this.div_ = null;

        // Explicitly call setMap() on this overlay
        this.setMap(map);
    }

    TxtOverlay.prototype = new google.maps.OverlayView();

    TxtOverlay.prototype.onAdd = function() {

        // Note: an overlay's receipt of onAdd() indicates that
        // the map's panes are now available for attaching
        // the overlay to the map via the DOM.

        // Create the DIV and set some basic attributes.
        var div = document.createElement('DIV');
        div.className = this.cls_;

        div.innerHTML = this.txt_;

        // Set the overlay's div_ property to this DIV
        this.div_ = div;
        var overlayProjection = this.getProjection();
        var position = overlayProjection.fromLatLngToDivPixel(this.pos);
        div.style.left = position.x - 25 + 'px';
        div.style.top = position.y - 25 + 'px';
        // We add an overlay to a map via one of the map's panes.

        var panes = this.getPanes();
        panes.overlayMouseTarget.appendChild(div);

        var me = this;
        google.maps.event.addDomListener(div, 'click', function(event) {
            google.maps.event.trigger(me, 'click');
            event.stopPropagation();
        });
        google.maps.event.addDomListener(div, 'touchstart', function(event) {
            google.maps.event.trigger(me, 'click');
            event.stopPropagation();
        });
        google.maps.event.addDomListener(div, 'dblclick', function(event) {
            event.stopPropagation();
        });

    }
    TxtOverlay.prototype.draw = function() {


        var overlayProjection = this.getProjection();

        // Retrieve the southwest and northeast coordinates of this overlay
        // in latlngs and convert them to pixels coordinates.
        // We'll use these coordinates to resize the DIV.
        var position = overlayProjection.fromLatLngToDivPixel(this.pos);

        var div = this.div_;
        div.style.left = position.x - 25 + 'px';
        div.style.top = position.y - 25 + 'px';
        div.style.position = 'absolute';
        div.style.cursor = 'pointer';


    }
    //Optional: helper methods for removing and toggling the text overlay.  
    TxtOverlay.prototype.onRemove = function() {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
    }
    TxtOverlay.prototype.hide = function() {
        if (this.div_) {
            this.div_.style.visibility = "hidden";
        }
    }

    TxtOverlay.prototype.show = function() {
        if (this.div_) {
            this.div_.style.visibility = "visible";
        }
    }

    TxtOverlay.prototype.toggle = function() {
        if (this.div_) {
            if (this.div_.style.visibility == "hidden") {
                this.show();
            } else {
                this.hide();
            }
        }
    }

    TxtOverlay.prototype.toggleDOM = function() {
        if (this.getMap()) {
            this.setMap(null);
        } else {
            this.setMap(this.map_);
        }
    }

    /*Overlay Script*/
    function marker(response) {
        var checkin = $scope.checkin;
        var start_time = $scope.start_time;
        var end_time = $scope.end_time;
        var guests = $scope.guests;     

        setAllMap(null);
        markers = [];

        angular.forEach(response.data, function(obj) {
            var map_slider = '';

            angular.forEach(obj["space_photos"], function(obj1) {
                map_slider +=  '<img id="marker_image_' + obj["id"] + '" space_image = "" alt="' + obj1["name"] + '" class="img-fluid w-100" data-current="0" src="' + obj1["name"] + '">';
            });
            var space_link = obj["link"] + "?checkin=" + checkin + "&start_time=" + start_time + "&end_time=" + end_time + "&guests=" + guests;

            var html = '<div id="info_window_' + obj["id"] + '" class="listing listing-map-popover"><div class="panel-image listing-img">';
            html += '<a class="media-photo media-cover" target="listing_' + obj["id"] + '" href="' + space_link + '"><div class="search-map-slider owl-carousel">' + map_slider + '</div></a>';

            if (obj["space_photos"].length>1) {
                html += '<div class="target-prev target-control block-link marker_slider" ng-click="marker_slider($event,\'prev\')"  data-space_id="' + obj["id"] + '"><i class="icon icon-chevron-left icon-size-2 icon-white"></i></div><a class="link-reset panel-overlay-bottom-left panel-overlay-label panel-overlay-listing-label" target="listing_' + obj["id"] + '" href="' + space_link + '"><div>';
            }
            var instant_book = '';

            var price_text = '';
            var currency_value = '';
            var currency_symbol = '';
            if(obj["activity_price"] != null) {
                var currency_symbol = obj["activity_price"]["currency"]["symbol"];
                var currency_value = obj["activity_price"]["hourly"];
            }

            if(obj["booking_type"] == 'instant_book') {
                instant_book = '<span aria-label="Book Instantly" data-behavior="tooltip"><i class="icon icon-instant-book icon-flush-sides"></i></span>';
            }

            if (obj["space_photos"].length>1) {
                html += '</div></a><div class="target-next target-control marker_slider block-link" ng-click="marker_slider($event,\'next\')" data-space_id="' + obj["id"] + '"><i class="icon icon-chevron-right icon-size-2 icon-white"></i></div></div>';
            }
            html += '<div class="search-info"><h4 class="text-truncate"><span>'+ obj["space_type_name"] +'</span><span>·</span><span>'+ obj["sq_ft_text"] +'</span></h4><a class="text-truncate" itemprop="name" title="' + obj["name"] + '">' + obj["name"] + '</a>';
            html += '<p class="search-price">'+ currency_symbol + currency_value + ' ' +$scope.per_hour;

            if(obj.booking_type == 'instant_book') {
                html += '<span> <i class="icon icon-instant-book"></i></span>';
            }

            html += '</p>';
            var star_rating = '';

            if(obj['overall_star_rating'] != '') {
                star_rating = '' + obj['overall_star_rating'];
            }

            var reviews_count = '';
            var review_seperator = '';
            var review_text = (obj['reviews_count'] > 1) ? $scope.reviews_text : $scope.review_text;

            if (obj['reviews_count'] != 0){
                reviews_count = ' ' + obj['reviews_count'] + ' ' + review_text;
                review_seperator = '.';
            }

            html += '<div class="listing-location text-truncate" itemprop="description">';
            html +='<span>' + star_rating + '</span><span>' + reviews_count + '</span></div></div></a></div></div>';
            var lat = obj["space_address"]["latitude"];
            var lng = obj["space_address"]["longitude"];
            var point = new google.maps.LatLng(lat, lng);
            var name = obj["name"];

            var marker = new google.maps.Marker({
                position: point,
                map: map,
                icon: getMarkerImage('normal'),
                title: name,
                zIndex: 1
            });
            customTxt = currency_symbol + currency_value;

            if(obj["booking_type"] == 'instant_book') {
                customTxt += instant_book;
            }

            txt = new TxtOverlay(point, customTxt, "customBox", map);

            markers.push(txt);

            google.maps.event.addListener(marker, "mouseover", function() {
                marker.setIcon(getMarkerImage('hover'));
            });

            google.maps.event.addListener(marker, "mouseout", function() {
                marker.setIcon(getMarkerImage('normal'));
            });
            createInfoWindow(txt, html);

        });

        angular.forEach($scope.place_result, function(obj) {
            var lat = obj["latitude"];
            var lng = obj["longitude"];
            var point = new google.maps.LatLng(lat, lng);

            var marker = new Marker({
                map: map,
                position: point,
                icon: ' ',
                map_icon_label: getMarkerLabel(obj['type'])
            });

            markers.push(marker);

            place_info = '<p>' + obj['name'] + '</p><p>' + obj['address_line_1'] + ' ' + obj['address_line_2'] + ', ' + obj['city'] + '</p><p>' + obj['state'] + ', ' + obj['country'] + '</p>';
            $scope.places_info.push(place_info);

            html = '<div style="font-size:16px"><h3 style="margin:0px; color:#000;">' + obj['name'] + '</h3><div class="popup-review">' + obj['reviews_star_rating_div'] + '</div><div class="address-align">' + obj['address_line_1'] + '</div><div class="address-align">' + obj['address_line_2'] + '</div><div class="address-align">' + obj['city'] + '</div><div class="address-align">' + obj['state'] + '</div><div class="address-align">' + obj['country_name'] + '</div><div class="address-align">' + obj['postal_code'] + '</div>';
            html += '<br><br><a class="review-btn-pop" href="' + APP_URL + '/add_place_reviews/place/' + obj['id'] + '" target="_blank" >Review</a>';
            html += '<div class="review-search-popup"><div onclick="reviews_popup(event, this)" class="close" >close</div>';

            angular.forEach(obj['reviews'], function(review) {
                html += '<div class="review-content flt-left">';
                html += '<div class="left-blk review-content-blk" ><img width="40" height="40" src="' + review.users_from.profile_picture.src + '" class="flt-left img-rnd" ></div>';
                html += '<div class="right-blk review-content-blk" ><div class="place_comments">' + review.place_comments + '</div><div class="place_stars" >' + review.place_review_stars_div + '</div></div>';
                html += '</div>';
            });

            html += '</div></div></div></div>';
            createPlaceInfoWindow(marker, html);
        });
    }

function createInfoWindow(marker, popupContent) {
    infoBubble = new InfoBubble({
        maxWidth: 3000
    });

    var contentString = $compile(popupContent)($scope);
    google.maps.event.addListener(marker, 'click', function() {

        var useragent = navigator.userAgent;
            if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('iPad') != -1 || useragent.indexOf('Android') != -1) {
                $scope.marker_click = 1;
            }
            if (infoBubble.isOpen()) {
                infoBubble.close();
                infoBubble = new InfoBubble({
                    maxWidth: 3000
                });
            }

            infoBubble.addTab('', contentString[0]);

            var borderRadius = 0;
            infoBubble.setBorderRadius(borderRadius);
            var maxWidth = 300;
            infoBubble.setMaxWidth(maxWidth);

            var maxHeight = 300;
            infoBubble.setMaxHeight(maxHeight);
            var minWidth = 282;
            infoBubble.setMinWidth(minWidth);

            var minHeight = 245;
            infoBubble.setMinHeight(minHeight);
            infoBubble.setPosition(marker.pos);
            infoBubble.open(map);
        });
}

function createPlaceInfoWindow(marker, popupContent) {
    infoBubble = new InfoBubble({
        maxWidth: 1500
    });

    var contentString = popupContent;
    google.maps.event.addListener(marker, 'click', function() {

        if (infoBubble.isOpen()) {
            infoBubble.close();
            infoBubble = new InfoBubble({
                maxWidth: 1500
            });
        }

        infoBubble.addTab('', contentString);

        var borderRadius = 0;
        infoBubble.setBorderRadius(borderRadius);
        var maxWidth = 300;
        infoBubble.setMaxWidth(maxWidth);

        var maxHeight = 250;
        infoBubble.setMaxHeight(maxHeight);
        var minWidth = 300;
        infoBubble.setMinWidth(minWidth);

        var minHeight = 250;
        infoBubble.setMinHeight(minHeight);

        infoBubble.open(map, marker);
    });
}

function getMarkerImage(type) {
    var image = '';

    if (type == 'hover')
        image = '';

    var gicons = new google.maps.MarkerImage("images/" + image,
        new google.maps.Size(50, 50),
        new google.maps.Point(0, 0),
        new google.maps.Point(9, 20));

    return gicons;

}

function setAllMap(map) {
    if (infoBubble != undefined) {
        if (infoBubble.isOpen()) {
            infoBubble.close();
            infoBubble = new InfoBubble({
                maxWidth: 3000
            });
        }
    }
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

    $(document).on('click', '.map-manual-refresh', function() {
        $(".map-manual-refresh").addClass('d-none');
        $(".map-auto-refresh").removeClass('d-none');
        $scope.search_result();
    });
    $(document).on('click', '.rooms-slider', function() {
        var space_id = $(this).attr("data-space_id");
        var dataurl = $("#space_image_" + space_id).attr("space_image");
        var img_url = $("#space_image_" + space_id).attr("src");
        if ($.trim(dataurl) == '') {
            $(this).parent().addClass("loading");
            $http.post('space_photos', {
                space_id: space_id
            })
            .then(function(response) {
                angular.forEach(response.data, function(obj) {
                    if ($.trim(dataurl) == '') {
                        dataurl = obj['name'];
                    } else
                    dataurl = dataurl + '^>' + obj['name'];
                });

                $("#space_image_" + space_id).attr("space_image", dataurl);
                var all_image = dataurl.split('^>');
                var space_img_count = all_image.length;
                var i = 0;
                var set_img_no = '';
                angular.forEach(all_image, function(img) {
                    if ($.trim(img) == $.trim(img_url)) {
                        set_img_no = i;
                    }
                    i++;
                });
                if ($(this).is(".target-prev") == true) {
                    var cur_img = set_img_no - 1;
                    var count = space_img_count - 1;
                } else {
                    var cur_img = set_img_no + 1;
                    var count = 0;
                }

                if (typeof(all_image[cur_img]) != 'undefined' && $.trim(all_image[cur_img]) != "null") {
                    var img = all_image[cur_img];
                } else {

                    var img = all_image[count];
                }

                var set_img_url = img;

                $(".panel-image").removeClass("loading");
                $("#space_image_" + space_id).attr("src", set_img_url);
            });
        } else {
            $(this).parent().addClass("loading");
            var all_image = dataurl.split('^>');
            var space_img_count = all_image.length;
            var i = 0;
            var set_img_no = '';
            angular.forEach(all_image, function(img) {
                if ($.trim(img) == $.trim(img_url)) {
                    set_img_no = i;
                }
                i++;
            });
            if ($(this).is(".target-prev") == true) {
                var cur_img = set_img_no - 1;
                var count = space_img_count - 1;
            } else {
                var cur_img = set_img_no + 1;
                var count = 0;
            }

            if (typeof(all_image[cur_img]) != 'undefined' && $.trim(all_image[cur_img]) != "null") {
                var img = all_image[cur_img];
            } else {
                var img = all_image[count];
            }
            var set_img_url =img;

            $(".panel-image").removeClass("loading");
            $("#space_image_" + space_id).attr("src", set_img_url);
        }
    });

    $scope.marker_slider = function($event,type) {
        var map_owl = $('.search-map-slider').owlCarousel({
            loop: true,
            nav: true,
            autoplay: true,
            rtl:rtl,
            responsiveClass: true,
            items: 1,
        });

        map_owl.on('changed.owl.carousel', function(e) {
            map_owl.trigger('stop.owl.autoplay');
            map_owl.trigger('play.owl.autoplay');
        });

        $('.search-map-slider.owl-loaded').trigger(type+'.owl.carousel');

        $event.stopPropagation();
    };

}]);

$(document).ready(function() {
    $(".search_header_form").submit(function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
});