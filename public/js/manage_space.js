app.controller('manage_listing', ['$scope', '$http', '$rootScope', '$compile', '$filter', '$q', 'fileUploadService', function($scope, $http, $rootScope, $compile, $filter, $q, fileUploadService) {
    $(document).ready(function() {
        // Initialize values
        $scope.step_error           = '';
        $scope.is_loading           = false;
        $scope.main_loading         = false;
        $scope.location_found       = false;
        $scope.autocomplete_used    = false;
        $scope.back_button_clicked  = 0;
        $scope.base_url             = space_ajax_url.manage_space+'/';
        if($scope.space_id != '') {
            $scope.base_url += $scope.space_id+'/';
        }
        if($scope.space.space_address.latitude != '' && $scope.space.space_address.longitude != '') {
            $scope.location_found   = true;
            $scope.autocomplete_used= true;
        }

        if($scope.current_step_name == 'basics') {
            $scope.initAutocomplete();
            $scope.initMap();
        }
        if($scope.current_step_name == 'setup') {
            $scope.getPhotos();
        }
        if($scope.current_step_name == 'ready_to_host') {
            // Toggle plus minus icon on show hide of collapse element
            $(".collapse").on('shown.bs.collapse', function() {
                $(this).prev('.activity-header').find('.fa').toggleClass('fa-plus fa-minus');
            }).on('hide.bs.collapse', function() {
                $(this).prev('.activity-header').find('.fa').toggleClass('fa-plus fa-minus');
            });

             angular.element(document).ready(function () {
                calendar_data=$scope.month_calendar_data;
                $scope.monthclass=true;
                $scope.full_calendar();
                });
        }

        // browser back button click previous page
        if(window.history) {
            $(window).on('popstate', function() {
                $scope.back_button_clicked = 1;
                if($scope.space_id == '') {
                    var step_num = $scope.GetQueryStringParams('step_num');
                    step_num = (step_num > 0 && step_num <= 7) ? step_num : 1;
                    $scope.current_step = step_num;
                    $scope.applyScope();
                }
                else {
                    $scope.current_step_name = '';
                    window.location.reload();
                }
            });
        }

        $scope.updateSavedData();
        $scope.updateProgress($scope.current_step_name);
        $scope.updateBottomWidth();
    });

    $scope.GetQueryStringParams = function(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) 
        {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) 
            {
                return sParameterName[1];
            }
        }
    };

    // Update Progress Bar Based on step name
    $scope.updateProgress = function(step_name) {
        var total_steps = $scope.steps[step_name].total_steps;
        var progress = (($scope.current_step - 1) / total_steps) * 100;
        $scope.progress_style = {width:progress+ '%' };
        $scope.applyScope(); 
    };

    $scope.updateBottomWidth = function(step_name) {
        setTimeout( () => {
            var cls_mlistw = $('.cls_mlist').outerWidth() / $('.cls_mlist').parent().outerWidth() * 100;
            $scope.bottom_style = {width:cls_mlistw+ '%' };
            $scope.applyScope();
        },700);
    };

    // Common function to check and apply Scope value
    $scope.applyScope = function() {
        if(!$scope.$$phase) {
            $scope.$apply();
        }
    };

    // Update form_modified status to save step details
    $scope.updateFormStatus = function() {
        $scope.form_modified = true;
    };

    $(document).on("keypress",".price_input",function() {
        if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);
    });

    // Apply Saved data to original data
    $scope.updateSavedData = function() {
        $scope.original_space = angular.copy($scope.space);
        $scope.original_space_activities = angular.copy($scope.space_activities);
        $scope.original_availabilities = angular.copy($scope.availabilities);
    };

    // Get Checkbox Checked Values Based on given selector
    $scope.getSelectedData = function(selector) {
        var value = [];
        $(selector+':checked').each(function() {
            value.push($(this).val());
        });
        return value;
    };

    // Common function to perform post request
    $scope.http_post = function(url, data, callback) {
        data = (!data) ? {} : data;
        $scope.is_loading = true;
        $http.post(url,data).then(function(response) {
            if(response.status == 200) {
                if(callback) {
                    callback(response.data);
                }
                $scope.is_loading = false;
            }
        }, function(response) {
            if(response.status == '300') {
                window.location = APP_URL + '/login';
            }
            /*else if(response.status == '500') {
                window.location.reload();
            }*/
        });
    };

    // Convert time to momemnt object
    $scope.convertToTime = function(time) {
        return moment("2001-01-01 "+time,"YYYY-MM-DD HH:mm:ss");
    };

    // Change date to given format
    $scope.changeFormat = function(date,format = 'YYYY-MM-DD',type="Week") {
        date = $scope.convertToMoment(date);
        date1=date.format(format);
        if(type!="Month")
        return date1;
        else{
        var sub_date=moment(date).subtract(1, "days").format(format);
        return sub_date;
        }
    };

    // Convert normal date to moment object
    $scope.convertToMoment = function(date) {
        return moment(date);
    };

    // Watch space scope to check form is modified or not
    $scope.$watchCollection('space', function(new_value, old_value) {
        $scope.form_modified = false;
        // Convert String Value to Int
        new_value.space_type = parseInt(new_value.space_type) || 0;

        if($scope.original_space) {
            if(JSON.stringify(new_value) != JSON.stringify($scope.original_space)) {
                $scope.form_modified = true;
            }
        }
    });

    // Watch space scope to check form is modified or not
    $scope.$watchCollection('space.space_address', function(new_value, old_value) {
        $scope.validateStepData('basics', 'space_address');
    });

    // Watch activity_currency scope to update minimum price
    $scope.$watch('activity_currency', function(new_value, old_value) {
        var data_params = { prev_currency_code : old_value, currency_code : new_value };
        var url = space_ajax_url.get_min_amount;
        $scope.page_loading = 'loading';
        var callback_function = function(response_data) {
            $scope.currency_symbol  = response_data.currency_symbol;
            $scope.minimum_amount   = response_data.minimum_amount;
            $scope.form_modified    = true;
            $scope.applyScope();
            $scope.page_loading     = '';
        };

        $scope.http_post(url,data_params,callback_function);
    });

    // Watch activities scope to check form is modified or not
    $scope.watchActivities = function(new_value, original_value) {
        $scope.form_modified = false;

        if(original_value) {
            if(new_value != original_value) {
                $scope.form_modified = true;
            }
        }
    };

    // Watch activities scope to check form is modified or not
    $scope.watchAvailability = function(new_value, original_value) {
        $scope.form_modified = true;
    };

	// Check input is valid or not
    $scope.checkInValidInput = function(value) {
        return (value == undefined || value == 0 || value == '');
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

    // Basics Step validation rules
    $scope.validateBasicData = function(step) {
        var result      = false;
        var error_message = validation_messages.custom_errors.fill_required_fields;
        $scope.step_error = '';
        if(step == 'space_type') {
            result = $scope.checkInValidInput($scope.space.space_type);
        }
        else if(step == 'sq_ft') {
            result = $scope.checkInValidInput($scope.space.sq_ft) ||  $scope.checkInValidInput($scope.space.no_of_workstations) || $scope.checkInValidInput($scope.space.fully_furnished) || $scope.checkInValidInput($scope.space.shared_or_private) || $scope.checkInValidInput($scope.space.renting_space_firsttime);
        }
        else if(step == 'number_of_guests') {
            result = $scope.checkInValidInput($scope.space.number_of_guests);
            if($('.number_of_guests').val() > max_guest_limit) {
                result = true;
                error_message = validation_messages.custom_errors.max_guests_error+''+max_guest_limit;
            }
        }
        else if(step == 'guest_access') {
            result = $scope.checkInValidInput($scope.space.guest_access);
        }
        else if(step == 'space_address') {
            var check_1 = ($scope.checkInValidInput($scope.space.space_address.address_line) || $scope.checkInValidInput($scope.space.space_address.latitude) || $scope.checkInValidInput($scope.space.space_address.longitude) );
            var check_2 = !$scope.autocomplete_used;
            var check_3 = !$scope.location_found;
            result = (check_1 || check_2 || check_3);
            if(check_2) {
                error_message = validation_messages.custom_errors.choose_from_autocomplete;
            }
            else if(check_3) {
                error_message = validation_messages.custom_errors.exact_location_not_found;
            }
        }

        if(result) {
            $scope.step_error = error_message;
        }
        return result;
    };

    // Setup step validation rules
    $scope.validateSetupData = function(step) {
        var result      = false;
        var error_message = validation_messages.custom_errors.fill_required_fields;
        $scope.step_error = '';

        if(step == 'photos') {
            result = ($scope.photos_list.length == 0);
            error_message = validation_messages.custom_errors.please_upload_one_photo;
        }
        else if(step == 'description') {
            result = ($scope.checkInValidInput($scope.space.space_descriptions['en'].name) || $scope.checkInValidInput($scope.space.space_descriptions['en'].summary));
        }

        if(result) {
            $scope.step_error = error_message;
        }
        return result;
    };

    // Ready to Host Step validation rules
    $scope.validateReadyToHostData = function(step) {
        var result      = false;
        var error_message = validation_messages.custom_errors.fill_required_fields;
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
                if($scope.checkInValidInput(price.min_hours) || $scope.checkInValidInput(price.hourly)|| $scope.checkInValidInput(price.full_day) || $scope.checkInValidInput(price.weekly) || $scope.checkInValidInput(price.monthly)) {
                    tmp_result = false;
                    return;
                }
                if(price.min_hours < 1 || price.min_hours > 4) {
                    tmp_result = false;
                    return;
                }
                if(price.hourly < $scope.minimum_amount || price.full_day < $scope.minimum_amount || price.weekly < $scope.minimum_amount || price.monthly < $scope.minimum_amount) {
                    min_amt_result = false;
                    error_message  = validation_messages.custom_errors.enter_amount_greater_than_min;
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

    // Get Steps Data to update
    $scope.getStepData = function(step) {
        var data = {'space_id' : $scope.space_id,'user_id' : USER_ID};

        if(step == 'basics') {
            Object.assign(data,$scope.getBasicsData());
        }
        else if(step == 'setup') {
            Object.assign(data,$scope.getSetUpData());
        }
        else if(step == 'ready_to_host') {
            Object.assign(data,$scope.getReadyToHostData());
        }

        return data;
    };

    // Get data in Basics Step
    $scope.getBasicsData = function() {
        var data = {step : 'basics'};
        data.space_type             = $scope.space.space_type;

        data.fully_furnished        = $scope.space.fully_furnished;
        data.no_of_workstations    = $scope.space.no_of_workstations;
        data.shared_or_private      = $scope.space.shared_or_private;
        data.renting_space_firsttime= $scope.space.renting_space_firsttime;
        data.number_of_rooms        = $scope.space.number_of_rooms;
        data.number_of_restrooms    = $scope.space.number_of_restrooms;
        data.floor_number           = $scope.space.floor_number;
        data.sq_ft                  = $scope.space.sq_ft;
        data.size_type              = $scope.space.size_type;
        data.guest_access           = $scope.getSelectedData('.guest_access').toString();
        data.number_of_guests       = $scope.space.number_of_guests;
        data.amenities              = $scope.getSelectedData('.amenities').toString();
        data.services               = $scope.getSelectedData('.services').toString();
        data.services_extra         = $scope.space.services_extra;
        data.location_data          = $scope.getLocationData();

        return data;
    };

    // Get data in Setup Step
    $scope.getSetUpData = function() {
        var data = {step : 'setup'};
        data.space_style            = $scope.getSelectedData('.space_style').toString();
        data.special_feature        = $scope.getSelectedData('.special_feature').toString();
        data.space_rules            = $scope.getSelectedData('.space_rule').toString();
        data.name                   = $scope.space.space_descriptions['en'].name;
        data.summary                = $scope.space.space_descriptions['en'].summary;
        data.description_data       = $scope.getDescriptionData();

        return data;
    };

    // Get data in Ready to Host Step
    $scope.getReadyToHostData = function() {
        var data = {step : 'ready_to_host'};
        if($scope.current_step == 1) {
            data.space_activities  = $scope.getActivitiesData();
        }
        else if($scope.current_step == 2) {
            data.activity_price    = $scope.getActivitiesPriceData();
        }
        else if($scope.current_step == 3) {
            data.availability    = $scope.availabilities;
        }
        else {
            data.cancellation_policy = $scope.space.cancellation_policy;
        }
        data.activity_currency  = $scope.activity_currency;
        data.security_deposit   = $scope.space.space_price.security;
        data.booking_type       = $scope.space.booking_type;

        return data;
    };

    // Get both Description and Description translations data
    $scope.getDescriptionData = function() {
        var data = $scope.space.space_descriptions;
        return data;
    };

    // Get Location Data
    $scope.getLocationData = function() {
        var data = $scope.space.space_address;
        return data;
    };

    // Get Activities and sub activities Data based on activities class selector
    $scope.getActivitiesData = function() {
        var space_activities = {};
        $('.activities:checked').each(function() {
            var activity_type = $(this).data('activity_type');
            var activity_id = $(this).val();
            var sub_activity = '';
            if(space_activities[activity_type] == undefined) {
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

    // Get entered activities price
    $scope.getActivitiesPriceData = function() {
        var data = [];
        angular.forEach($scope.space.space_activities, function(value) {
            value.activity_price.currency_code = $scope.activity_currency;
            data.push(value.activity_price)
        });
        return data;
    };

    // Go to Given Step if form not saved then show warning popup & initialize functions related to current steps
    $scope.goToStep = function(step_name) {
        if($scope.current_step_name == step_name) {
            return false;
        }
        $("#ajax_container").addClass('loading');
        var data_params = {};

        var url = $scope.base_url+step_name;
        var callback_function = function(response_data) {
            $scope.current_step_name = step_name;
            $scope.current_step = 1;
            $('#save_warning-popup').modal('hide');
            $("#ajax_container").html($compile(response_data)($scope));
            $("#ajax_container").removeClass('loading');

            // Init map for location
            if(step_name == 'basics') {
                $scope.initMap();
            }
            // Get Photos List if step is photo
            if(step_name == 'setup') {
                $scope.getPhotos();
            }
            if(!$scope.back_button_clicked) {
                window.history.pushState({ path: url }, '', url);
            }

            $scope.back_button_clicked = 0
            $scope.updateProgress(step_name);
        };

        $scope.http_post(url,data_params,callback_function);
    };

    // Save Current Step data if step is last in that form,then move to next step
    $scope.saveStep = function(current_step = '') {
        var data_params = $scope.getStepData(current_step);
        var url = space_ajax_url.update_space;
        $scope.page_loading = 'loading';
        var callback_function = function(response_data) {
            if(response_data.redirect != '' && response_data.redirect != undefined) {
                window.location = response_data.redirect;
                return false;
            }

            $scope.space = response_data.space;
            $scope.updateSavedData();

            $scope.form_modified = false;
            $scope.applyScope();
            $scope.page_loading = '';
        };

        $scope.http_post(url,data_params,callback_function);
    };

    $scope.decrement=function(count){
        if(count<=0)
          $scope.space.number_of_guests=$scope.space.number_of_guests;
        else 
          $scope.space.number_of_guests=$scope.space.number_of_guests-1;
    };
    // Go to next step based on given step name if form modified then save current step
    $scope.nextStep = function(step_name) {
         var req_step = $scope.steps[step_name].mandatory_steps[$scope.current_step];
     
        if(req_step != undefined && req_step) {
            var result = $scope.validateStepData(step_name,req_step);
            if(result) return;
        }
        if($scope.form_modified && !$scope.new_space) {
            $scope.saveStep(step_name);
        }

        if($scope.current_step == 4 && step_name == 'ready_to_host') {
            $scope.calendar.destroy();
            setTimeout( () => {
                $scope.full_calendar();
                var current_date = moment().format('YYYY-MM-DD');
                $scope.calendar.gotoDate(current_date);
                $scope.updateCalendar(current_date,false);
            },1);
        }
        var total_steps = $scope.steps[step_name].total_steps;
        if($scope.current_step == total_steps) {
            $scope.saveStep(step_name);
            setTimeout(() => $scope.goToMainHome(),500);
            return false;
        }
        $scope.current_step++;
        $scope.updateUrl();
        $scope.updateProgress(step_name);
        $scope.updateBottomWidth();
    };

    $scope.revertSpaceDetails = function(step_name) {
        $scope.space = angular.copy($scope.original_space);
        $scope.space_activities = angular.copy($scope.original_space_activities);
        $scope.availabilities = angular.copy($scope.original_availabilities);
        $scope.step_error = '';
    };

    // Go to prev step based on given step name if form modified then save current step
    $scope.prevStep = function(step_name) {
        var req_step = $scope.steps[step_name].mandatory_steps[$scope.current_step];
        if(req_step != undefined && req_step && !$scope.new_space) {
            var result = $scope.validateStepData(step_name,req_step);
            if(result) return;
        }

        if($scope.form_modified && !$scope.new_space) {
            $scope.saveStep(step_name);
        }

        if($scope.current_step == 1) {
            $scope.goToMainHome();
            return false;
        }

        $scope.current_step--;
        $scope.updateUrl();
        $scope.updateProgress(step_name);
        $scope.updateBottomWidth();
    };

    // Save And Go to Main home page
    $scope.saveAndClose = function(step_name) {
        var req_step = $scope.steps[step_name].mandatory_steps[$scope.current_step];
        if(req_step != undefined && req_step) {
            var result = $scope.validateStepData(step_name,req_step);
            if(result) {
                return false;
            }
            $scope.saveStep($scope.current_step_name);
        }
        else if($scope.form_modified) {
            $scope.saveStep($scope.current_step_name);
        }
        $scope.goToMainHome();
    };

    $scope.updateUrl = function() {
        var url = $scope.base_url+$scope.current_step_name+'?step_num='+$scope.current_step;
        window.history.pushState({path: url}, '', url);
    };

    // Go To Listing Steps Home Page
    $scope.goToMainHome = function() {
        if($scope.space_id != '') {
            $scope.main_loading = true;
            window.location = $scope.listing_home_url;
        }
    };

    // Events to update space scope when user check/Uncheck values
    $(document).on('change', '.guest_access', function() {
        var guest_access = $scope.getSelectedData('.guest_access');
        $scope.space.guest_access = guest_access.toString();
        $scope.validateStepData('basics','guest_access');
        $scope.applyScope();
    });

    $(document).on('change', '.amenities', function() {
        var amenities = $scope.getSelectedData('.amenities');
        $scope.space.amenities = amenities.toString();
        $scope.applyScope();
    });

    $(document).on('change', '.services', function() {
        var services = $scope.getSelectedData('.services');
        $scope.space.services = services.toString();
        $scope.applyScope();
    });

    $(document).on('change', '.space_style', function() {
        var space_style = $scope.getSelectedData('.space_style');
        $scope.space.space_style = space_style.toString();
        $scope.applyScope();
    });

    $(document).on('change', '.special_feature', function() {
        var special_feature = $scope.getSelectedData('.special_feature');
        $scope.space.special_feature = special_feature.toString();
        $scope.applyScope();
    });

    $(document).on('change', '.space_rule', function() {
        var space_rule = $scope.getSelectedData('.space_rule');
        $scope.space.space_rules = space_rule.toString();
        $scope.applyScope();
    });

    // Initialize Location map
    $scope.initMap = function() {
        var space_location = $scope.space.space_address;
        var map_el = document.getElementById('location_map');
        if(!space_location.latitude || !space_location.longitude || !map_el) {
            return false;
        }

        $scope.map = new google.maps.Map(map_el, {
            center: { lat: parseFloat(space_location.latitude), lng: parseFloat(space_location.longitude) },
            zoom: 16,
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: true,
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL
            }
        });

        // Limit the zoom level
        google.maps.event.addListener($scope.map, 'zoom_changed', function () {
            if ($scope.map.getZoom() < 3) $scope.map.setZoom(3);
        });
        $scope.initMarker();
    };

    // Initialize map Pin and add dragend listener to update location based on pin
    $scope.initMarker = function() {
        var space_location = $scope.space.space_address;
        $scope.location_marker = new google.maps.Marker({
            map : $scope.map,
            draggable : true,
            animation : google.maps.Animation.DROP,
            position : new google.maps.LatLng(
                space_location.latitude, space_location.longitude
            ),
            icon :new google.maps.MarkerImage(
                APP_URL+'/images/map_pin.png',
                new google.maps.Size(34, 50),
                new google.maps.Point(0, 0),
                new google.maps.Point(17, 50)
            )
        });

        google.maps.event.addListener($scope.location_marker, 'dragend', function()  {
            marker_location = $scope.location_marker.getPosition();

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'latLng': { lat: marker_location.lat(), lng: marker_location.lng() }
            }, function(results, status) {
                if(status == google.maps.GeocoderStatus.OK) {
                    if(results[0]) {
                        $scope.location_found = true;
                        $scope.fetchMapAddress(results[0],true);
                    }
                }
            });

            $scope.space.space_address.latitude = marker_location.lat();
            $scope.space.space_address.longitude = marker_location.lng();
            $scope.applyScope();
        });
    };

    // Add Google Autocomplete to address input
    $scope.initAutocomplete = function() {
        address_line = document.getElementById('address_line');
        $scope.autocomplete = new google.maps.places.Autocomplete(address_line);
        $scope.autocomplete.addListener('place_changed', () => {
            if($scope.map == undefined) {
                setTimeout( () => $scope.initMap() ,1);
            }
            place = $scope.autocomplete.getPlace();
            $scope.autocomplete_used = true;
            $scope.applyScope();
            $scope.validateStepData('basics', 'space_address');
            $scope.fetchMapAddress(place);
        });
    };

    // Fetch Location details after choose address from autocomplete
    $scope.fetchMapAddress = function(data, from_map = false) {
        var space_location = $scope.space.space_address;
        var componentForm = {
            street_number : 'short_name',
            route : 'long_name',
            sublocality_level_1 : 'long_name',
            sublocality : 'long_name',
            locality : 'long_name',
            administrative_area_level_1 : 'long_name',
            country : 'short_name',
            postal_code : 'short_name'
        };

        var street_number = '';
        var place = data;
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                if(addressType == 'street_number') {
                    street_number = val;
                }
                if(addressType == 'route') {
                    space_location.address_line_1 = street_number + ' ' + val;
                }
                if(addressType == 'postal_code') {
                    space_location.postal_code = val;
                }
                if(addressType == 'locality') {
                    space_location.city = val;
                }
                if(addressType == 'administrative_area_level_1') {
                    space_location.state = val;
                }
                if(addressType == 'country') {
                    space_location.country = val;
                }
            }
        }
        space_location.latitude = place.geometry.location.lat();
        space_location.longitude = place.geometry.location.lng();

        $scope.moveMarker(space_location.latitude, space_location.longitude);

        $scope.space.space_address = space_location;
        if(from_map) {
            $scope.space.space_address.address_line = $scope.space.space_address.address_line_1 + ', ' + $scope.space.space_address.city + ', ' + $scope.space.space_address.state + ', ' + $scope.space.space_address.country;
        }
        else {
            var address = $scope.space.space_address.address_line_1 + ', ' + $scope.space.space_address.address_line_2 + ', ' + $scope.space.space_address.city + ', ' + $scope.space.space_address.state + ', ' + $scope.space.space_address.country + ', ' + $scope.space.space_address.postal_code;
            $scope.checkLocationGeoCode(address);
        }
        $scope.applyScope();
    };

    // Move Marker to new position
    $scope.moveMarker = function(lat, lng) {
        $scope.location_marker.setPosition( new google.maps.LatLng(lat, lng) );
        $scope.map.panTo( new google.maps.LatLng(lat, lng) );
    };

    // Check Exact location found or not
    $scope.checkLocationGeoCode = function(address) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'address': address
        }, function(results, status) {
            $scope.location_found = false;
            if(status == google.maps.GeocoderStatus.OK) {
                $scope.latitude = results[0].geometry.location.lat();
                $scope.longitude = results[0].geometry.location.lng();
                result = results[0];
                if(result['types'] == "street_address" || result['types'] == "premise") {
                    $scope.location_found = true;
                }
            }
            $scope.validateStepData('basics', 'space_address');
        });
    };

    // Reset Google Autocomplete when edit address line field
    $scope.resetAutoComplete = function() {
        $scope.space.space_address.latitude = '';
        $scope.space.space_address.longitude = '';
        $scope.autocomplete_used = false;
        $scope.location_found = false;
    };

    // Make Photos Draggable using Jquery sortable plugin
    $scope.initDraggablePhotos = function() {
        if($(window).width() < 767) {
            return false;
        }
        $('.photo-grid').sortable({
            axis: "x,y",
            revert: true,
            scroll: true,
            placeholder: 'sortable-placeholder',
            cursor: 'move',
            tolerance:'pointer',
            containment: $('.sortable_image_view'),
            start: function(){
                $('.photo-grid').addClass('sorting');
            },
            stop: function(){
                $('.photo-grid').removeClass('sorting');
                $scope.change_photo_order();
            }
        });
    };

    // Get the list of photos
    $scope.getPhotos = function() {
        $('#js-manage-listing-content').addClass('loading');
        var data_params = {};
        data_params['space_id'] = $scope.space_id;
        var callback_function = function(response_data) {
            $('#js-manage-listing-content').removeClass('loading');
            $scope.photos_list = response_data;
            $scope.initDraggablePhotos();
        };

        $scope.http_post(space_ajax_url.photos_list,data_params,callback_function);
    };

    // Upload photos to server
    $scope.uploadPhotos = function(element) {
        var photos = [];
        files = element.files;
        if(files) {
            photos = files;
            if(photos.length) {
                $('.image_step_view').addClass('loading');
                url = space_ajax_url.upload_photos;

                upload = fileUploadService.uploadFileToUrl(photos, url,{space_id : $scope.space_id});
                upload.then(function(response) {

                    if (response.error['error_title']) {
                        $scope.deletePopupContent(response.error['error_title'], response.error['error_description'], false);
                        $('#js-error').modal('show');
                    }

                    if(response.photos_list && response.photos_list != '') {
                        $scope.photos_list = response.photos_list;;
                        $scope.initDraggablePhotos();
                        document.getElementById('upload_photos').value='';
                    }

                    $scope.validateStepData('setup','photos');
                    $('.image_step_view').removeClass('loading');
                });
            }
        }
    };

    // Set data for popup and show photo delete warning popup
    $scope.delete_photo = function(item, delete_photo, delete_message) {
        id = item.id
        if($scope.photos_list.length == 1) {
            $scope.deletePopupContent($scope.delete_warning,$scope.delete_warning_desc, false);
            $('#js-error').modal('show');
            return false;
        }
        var index = $scope.photos_list.indexOf(item);
        $scope.deletePopupContent(delete_photo, delete_message,true);
        $('.js-delete-photo-confirm').attr('data-id', id);
        $('.js-delete-photo-confirm').attr('data-index', index);
        $('#js-error').modal('show');
    };

    // Setup modal content and title
    $scope.deletePopupContent = function(delete_photo, delete_message,show_delete) {
        $('#js-error .modal-header').text(delete_photo);
        $('#js-error .modal-body').text(delete_message);
        $('#js-error .js-delete-photo-confirm').addClass('d-none');
        if(show_delete) {
            $('#js-error .js-delete-photo-confirm').removeClass('d-none');
        }
    };

    // Delete and Update photos list
    $(document).on('click', '.js-delete-photo-confirm', function() {
        var index   = $(this).attr('data-index');
        var id      = $(this).attr('data-id');
        var url     = space_ajax_url.delete_photo;
        var data    = { space_id: $scope.space_id, photo_id: id };
        var callback_function = function(response_data) {
            $('#js-photo-grid').removeClass('loading');
            if(response_data.success == 'true') {
                $scope.photos_list = response_data.photos_list;
                $scope.applyScope();
            }
        };
        $('#js-photo-grid').addClass('loading');
        $('#js-error').modal('hide');
        $scope.http_post(url,data,callback_function);
    });

    // Update new order of the uploaded photos
    $scope.change_photo_order = function() {
        var image_order_list = $(".image_order_list").map(function() {
            return $(this).val();
        }).get();

        var url = space_ajax_url.change_photo_order;
        var data = { space_id: $scope.space_id, image_order: image_order_list };
        var callback_function = function(response_data) {
            // 
        };
        $scope.http_post(url,data,callback_function);
    };

    // Save photo description
    var canceller,isSending = false;
    $scope.keyup_highlights = function(id, value) {
        var url = space_ajax_url.photo_description;
        var data = { space_id: $scope.space_id, photo_id: id, data: value };

        if(isSending) {
            canceller.resolve()
        }
        isSending = true;
        canceller = $q.defer();

        $http({
            method: "post",
            url: url,
            data: data,
            timeout: canceller.promise
        }).success(function (response) {
            isSending = false;
        }).error(function(data, status) {
            isSending = false;
        });
    };

    // show language list for add new language
    $scope.addLanguage = function() {
        $scope.current_tab_code = '';
        $scope.disableLangAddBtn = true;
        $scope.activeDescTab = 'add_language';

        var data = { space_id : $scope.space_id };
        var callback_function = function(response_data) {
            $scope.all_language = response_data;
        };

        $scope.http_post(space_ajax_url.get_all_trans_desc,data,callback_function);
    };

    // Delete description language and it's related details
    $scope.deleteLanguage = function() {
        $('.manage-listing-content').addClass('loading');
        var url = space_ajax_url.delete_description;
        var data = { space_id :$scope.space_id, current_tab:$scope.current_tab_code };
        var callback_function = function(response_data) {
            $('.manage-listing-content').removeClass('loading');
            delete $scope.space.space_descriptions[$scope.current_tab_code];
            $scope.getdescription('en');
        };

        $scope.http_post(url,data,callback_function);
    };

    // Get Description of selected language
    $scope.getdescription = function(lang_code) {
        $scope.current_tab_code = lang_code;
        $scope.activeDescTab = 'edit_language';
    };

    // Add new language for description translation
    $scope.addlanguageRow = function() {
        var data_params = {};
        data_params['lang_code'] = $scope.current_language;
        data_params['space_id'] = $scope.space_id;
        $scope.current_tab_code = $scope.current_language;
        $scope.current_language = '';
        var url = space_ajax_url.add_description;
        var callback_function = function(response_data) {
            $scope.space.space_descriptions[$scope.current_tab_code] = response_data;
            $scope.disableLangAddBtn = true;
            $scope.activeDescTab = 'edit_language';
            $scope.applyScope();
        };
        $scope.http_post(url, data_params, callback_function);
    };

    // Update Maximum Number of Guests when click +/- Button
    $scope.updateMaxGuests = function(type, index) {
        $scope.space.number_of_guests = $('.number_of_guests').val();
        if(type == 'increase') {
            $scope.space.number_of_guests++;
        }
        else {
            $scope.space.number_of_guests--;
        }
        $scope.form_modified = true;
        setTimeout( () => {
            $scope.applyScope();
            $scope.validateStepData('basics','number_of_guests');
        },100);
    };

    // Update Activity min hours when click +/- Button
    $scope.updateActivityHours = function(type, index) {
        if(type == 'increase') {
            $scope.space.space_activities[index].activity_price.min_hours++;
        }
        else {
            $scope.space.space_activities[index].activity_price.min_hours--;
        }
        $scope.form_modified = true;
        $scope.validateStepData('ready_to_host', 'activity_price');
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
        $scope.applyScope();
    });

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
        $scope.applyScope();
    });

    /* Availablity related functions */
    // Show/ Hide Availability Time dropdowns based on selected Type
    $scope.availabilityTypeChanged = function(day, type) {
        $scope.step_error = '';
        
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
        $scope.availabilityChanged();
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

    // validate availability times
    $scope.validateAvailabilities = function() {
        var result = false;
        var error_message = validation_messages.custom_errors.select_valid_times;
        var result_data = [];

        $.each($scope.availabilities, function(key, availability) {
            if(availability.status == 'Open' && availability.available != 'all') {
                $.each(availability.availability_times, function(avail_key,availabile_times) {
                    // Check times are selected or not
                    if(!availabile_times.start_time || availabile_times.start_time == '' || availabile_times.end_time == '') {
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
                            error_message = validation_messages.custom_errors.select_valid_times;
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

    $scope.validateActivityPriceData = function() {
        setTimeout( () => $scope.validateStepData('ready_to_host', 'activity_price') ,1000);
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

    /* Calendar Step Functionality */
    $scope.date = moment().format('YYYY-MM-DD');
    $scope.current_month = moment().format('MM');
    $scope.current_day = moment().format('DD');



     var calendar_value="dayGridMonth";
    // Initialize Full Calendar
    $(document).on('click','.fc-timeGridWeek-button',function()
    {   $scope.monthclass=false;
        calendar_value="timeGridWeek";
        calendar_data=$scope.calendar_data;
        $scope.type_calendar="Week";
        $scope.calendar.destroy();
        setTimeout( () => $scope.full_calendar(),1);
    });
    $(document).on('click','.fc-dayGridMonth-button',function()
    {   
        $scope.monthclass=true;
        calendar_value="dayGridMonth";
        calendar_data=$scope.month_calendar_data;
        $scope.type_calendar="Month";
        $scope.calendar.destroy();
        setTimeout( () => $scope.full_calendar(),1);
    });
    var value_calender; 
  
    $scope.type_calendar=calendar_value=='dayGridMonth'?"Month":"Week";
    $scope.full_calendar = function() {
        $("#ajax_container").addClass('loading');
        var calendarEl = document.getElementById('calendar');

        $scope.calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['interaction','timeGrid','moment','dayGrid'],
            defaultView: calendar_value,
            selectable: true,
            selectMirror: true,
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
            locale: LANGUAGE_CODE,
            height:"parent",
            firstDay: 1, // Set Calendar Staring Day 0 -> Sunday, 1 -> Monday
            allDaySlot: false,
            slotDuration: '01:00:00',
            longPressDelay: 1000, // In Mobile Hold Click only works set long press time to 1 ms to work as normal select
            events:calendar_data,
            select: function(selectionInfo) {
                
                if(calendar_value=='dayGridMonth')
                $scope.updateFormData(selectionInfo.start,selectionInfo.end,'Month');
                else
                $scope.updateFormData(selectionInfo.start,selectionInfo.end);
            },
            unselect: function(event) {
                $scope.unSelectCalendar();
            },
            eventClick: function(info) {

                $scope.unSelectCalendar();
                if(info.el.classList.contains('status-r') || info.el.classList.contains('status-p') || info.el.classList.contains('status-res') || info.el.classList.contains('status-res')) {
                    return false;
                }
                var startDate  = $scope.convertToMoment(info.event.start);
                var endDate    = $scope.convertToMoment(info.event.end);
                
                if(!endDate.isValid()) {
                    return false
                }
                var notes = (info.event.extendedProps.notes != null) ? info.event.extendedProps.notes : '';
                setTimeout(() => {
                    if(calendar_value=='dayGridMonth')
                    $scope.updateCalendarFields(startDate, endDate, info.event.extendedProps.description, notes,'Month');
                    else
                    $scope.updateCalendarFields(startDate, endDate, info.event.extendedProps.description, notes);
                    $scope.applyScope();
                },1)
            },
            eventRender: function(info) {
                info.el.setAttribute("id", info.event.id);
                var notes = (info.event.extendedProps.notes != null) ? info.event.extendedProps.notes : '';
                $('<div class="fc-bgevent-data" data-notes="'+notes+'" data-status="'+info.event.extendedProps.description+'"> <span class="title">'+ '' +'</span> <span class="notes">'+ notes +'</span> </div>').appendTo(info.el);
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

    // Destroy Full Calendar
    $scope.destroyCalendar = function() {
        $scope.calendar.destroy();
    };

    // Check and update form dates, unselect if selected dates already has some other events
    $scope.updateFormData = function(startDate, endDate,type="Week") {

        var startDate  = $scope.convertToMoment(startDate);
        var endDate    = $scope.convertToMoment(endDate);
        $scope.showUpdateForm = false;
        if(startDate.isBefore(moment().subtract(1, 'h'))) {
            $scope.unSelectCalendar();
            return false;
        }
       
        cDateCheck = startDate.clone();
        while(cDateCheck < endDate) {
            if(type!="Month")
            var cur_el = document.getElementById(cDateCheck.format('YYYY-MM-DD_HH:mm:ss'));
            else
            var cur_el = document.getElementById(cDateCheck.format('YYYY-MM-DD')+'_month');
            var start_date=cDateCheck.format("YYYY-MM-DD");
            var end_date=endDate.format("YYYY-MM-DD");           
            
            if(!cur_el) {
                $scope.unSelectCalendar();
                return false;
            }

            if(cur_el.classList.contains('status-r') || cur_el.classList.contains('status-n') || cur_el.classList.contains('status-a') || cur_el.classList.contains('status-res') || cur_el.classList.contains('status-res-rem') || cur_el.classList.contains('status-not_available')) {
                $scope.unSelectCalendar();
                return false;
            }
                 
            if(type!="Month")
            cDateCheck.add(1, 'h');
            else
            cDateCheck.add(1, 'd');  
       
      
        }
       
      
        var selector = $scope.changeFormat(startDate)+'_'+$scope.changeFormat(startDate,'HH:mm:ss');
        // document.getElementsByTagName("H1")[0].getAttribute("class");
        var id=cur_el.getAttribute("id");
        var get_notes1=document.getElementById(id);
        var get_notes=$('#'+id).find('.fc-bgevent-data').attr('data-notes');
        var get_avail=$('#'+id).find('.fc-bgevent-data').attr('data-status');
       
        if(type !="Month"){
        var cur_el = document.getElementById(selector).getElementsByClassName("fc-bgevent-data")[0];
        $scope.updateCalendarFields(startDate,endDate,cur_el.dataset.status,cur_el.dataset.notes);
        }
        else
        $scope.updateCalendarFields(startDate,endDate,get_avail,get_notes,'Month');
        $scope.applyScope();
    };

    // Update Calendar edit form fields
    $scope.updateCalendarFields = function(startDate, endDate, status, notes,type="Week") {
        $scope.showUpdateForm = true;
       
        if(type!="Month"){
            $('#calendar-edit-end').val($scope.changeFormat(endDate,daterangepicker_format));
            $('#calendar-end').val($scope.changeFormat(endDate));
            $scope.calendar_start_time = startDate.format('HH:mm:ss');
            $scope.calendar_end_time = endDate.format('HH:mm:ss');
            $scope.calendar_edit_start_time = startDate.format('LT');        
            $scope.calendar_edit_end_time = endDate.format('LT');
            }
        else{
            $('#calendar-edit-end').val($scope.changeFormat(endDate,daterangepicker_format,"Month"));        
            $('#calendar-end').val($scope.changeFormat(endDate,"YYYY-MM-DD","Month"));
             $scope.calendar_start_time = '00:00:00';
              $scope.calendar_end_time = '23:59:59';
              $scope.calendar_edit_start_time ="False";        
            $scope.calendar_edit_end_time ="False";
            }          
              
        $('#calendar-edit-start').val($scope.changeFormat(startDate,daterangepicker_format));
        $('#calendar-start').val($scope.changeFormat(startDate));
        
        $scope.segment_status = status;
        $scope.notes = notes;
        $scope.isAddNote = (notes != '');
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
        data_params['month'] = month;
        data_params['year'] = year;
        var data = { data : JSON.stringify(data_params) };
        var url = document.URL;

        var callback_function = function(response_data) {
            $("#ajax_container").html($compile(response_data)($scope));
            $scope.date = year+'-'+ month +'-'+$scope.current_day;
            $scope.current_month = $scope.convertToMoment($scope.date).format('MM');

            $scope.calendar.destroy();
            if(calendar_value=="dayGridMonth")
            calendar_data=$scope.month_calendar_data;
            else
            calendar_data=calendar_data=$scope.calendar_data;
            setTimeout( () => $scope.full_calendar(),1);

            $('#calendar').removeClass('loading');
        };

        $scope.http_post(url,data,callback_function);
    };

    // Update Calendar Changes
    $scope.calendarEditSubmit = function() {
        var data_params = {};
        data_params['space_id'] = $scope.space_id;
        data_params['status'] = $scope.segment_status;
        data_params['start_date'] = $('#calendar-start').val();
        data_params['start_time'] = $scope.calendar_start_time;
        data_params['end_date'] = $('#calendar-end').val();
        data_params['end_time'] = $scope.calendar_end_time;
        data_params['notes'] = $scope.notes;
        data_params['type'] = $scope.type_calendar;
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
                $scope.date = sDate.format('YYYY-MM-DD');
                if(calendar_value=="dayGridMonth")
                calendar_data=$scope.month_calendar_data;
                else
                calendar_data=calendar_data=$scope.calendar_data;
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
        if(calendar_value!='dayGridMonth')
        $scope.unSelectCalendar();
    });

    $(window).resize(function(){
        $scope.updateBottomWidth();
    });

}]);

app.service('fileUploadService', function ($http, $q) {
    this.uploadFileToUrl = function (file, uploadUrl, data) {
        var fileFormData = new FormData();
        $.each(file, function( index, value ) {
            fileFormData.append('photos[]', value);
        });

        if(data) {
            $.each(data, function(i, v) {
                fileFormData.append(i, v);
            })
        }

        var deffered = $q.defer();
        $http.post(uploadUrl, fileFormData, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined},
            config:{
                uploadEventHandlers: {
                    progress: function(e) {
                        console.log('UploadProgress -> ' + e);
                    }
                }
            }
        })
        .success(function (response) {
            deffered.resolve(response);
        })
        .error(function (response) {
            deffered.reject(response);
        });

        var getProgressListener = function(deffered) {
            return function(event) {
                eventLoaded = event.loaded;
                eventTotal = event.total;
                percentageLoaded = ((eventLoaded/eventTotal)*100);
                deffered.notify(Math.round(percentageLoaded));
            };
        };
        return deffered.promise;
    }
});