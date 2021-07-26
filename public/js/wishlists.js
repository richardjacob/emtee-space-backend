app.controller('wishlists', ['$scope', '$http', '$filter', function($scope, $http, $filter) {

    $scope.wishlists_space  = [];
    $scope.common_loading   = 0;
    $scope.wishlist_count   = 0;
    $scope.infowindow = new google.maps.InfoWindow();
    $(document).ready(function() {
        $scope.get_wishlists_space();
    });
    
    $scope.get_wishlists_space = function() {
        $scope.wishlists_space=[];
        $scope.common_loading=1;
        setTimeout(function(){
            $http.post(APP_URL + '/get_wishlists_space', {id: $("#wl_id").val()}).then(function(response) {
                if(response.data[0] == undefined) {
                    return false;
                }
                if(response.data[0].all_space_count) {
                    $scope.wishlists_space = response.data[0].saved_wishlists;
                    $scope.wishlist_count = response.data[0].saved_wishlists.length;
                    $scope.common_loading = 0;
                }
                setTimeout( () => $scope.update_slider('.listing-slideshow'),100);
            });
        }, 1000);
    };

    $scope.delete_wishlist_space=function(index,item) {
        var space_id = $scope.wishlists_space[index].space_id;
        var s="#noteloader_"+index;
        $(s).show();
        $http.post(APP_URL + '/remove_saved_wishlist/' + $("#wl_id").val(), {
            space_id: space_id,type:'Space'
        }).then(function(response) {
            if(response.data.length) {
                $scope.wishlist_count=response.data[0].space_count;
                item.splice(index, 1);   
            }
            else {
                $scope.wishlist_count = 0;
                $scope.wishlists_space=[];
            }
        });
    };

    $scope.add_space_note=function(space_id,index) {
        var selector = "#noteloader_"+index;
        $(selector).show();
        $http.post(APP_URL + '/add_note_wishlist/' + $("#wl_id").val(), {
            space_id: space_id,
            note: $('#note_' + space_id).val()
        }).then(function(response) {
            $(selector).hide();
        });
    };

    $scope.get_map_space = function() {
        $("#results_map").addClass("loading");
        $http.post(APP_URL + '/get_wishlists_space', {id: $("#wl_id").val()}).then(function(response) {
            initialize(response.data[0].saved_wishlists);
            $("#results_map").removeClass("loading");
        });
    };

    $scope.update_slider = function(selector) {
        $(selector).owlCarousel({
            loop: false,
            autoplay: true,
            rtl:rtl,
            nav: true,
            dots: true,
            items: 1,
            responsiveClass: true,
            navText:['<i class="icon icon-chevron-right custom-rotate"></i>','<i class="icon icon-chevron-right"></i>']
        });
    };

    $('.create').click(function() {
        $('.modal-transitions').removeClass('d-none');
    });

    $('.cancel').click(function(event) {
        event.preventDefault();
        $('.modal-transitions').addClass('d-none');
    });

    $('#map').click(function() {
        $('.results-map').show();
        $('.results-list').hide();
        $('#map').prop('disabled', true);
        $('#list').prop('disabled', false);
        $scope.get_map_space();
    });

    $('#list').click(function() {
        $('.results-list').show();
        $('.results-map').hide();
        $('#list').prop('disabled', true);
        $('#map').prop('disabled', false);
    });

    $('.share_email_list').change(function() {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        email_list = $(this).val();
        email_list = email_list.split(",");
        for(var i = 0; i < email_list.length; i++) {
            if(reg.test(email_list[i].trim()) == false) {
                $('.email_error').show();
                $('.wishlist_share_submit').prop('disabled', true);
                return true;
            }
        }
        $('.wishlist_share_submit').prop('disabled', false);
        $('.email_error').hide()
    });

    function initialize(data) {
        var myOptions = {
            zoom: 10,
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("results_map"), myOptions);
        setMarkers(map, data);

        google.maps.event.addListener(map, 'click', function() {
            if($scope.infowindow != '') {
                $scope.infowindow.close();
            }
        });
    }

    function setMarkers(map, data) {

        var marker, i;
        var bounds = new google.maps.LatLngBounds();

        for (i = 0; i < data.length; i++) {
            var title = data[i]['space']['name'];
            var lat = data[i]['space']['space_address']['latitude'];
            var long = data[i]['space']['space_address']['longitude'];
            var address = data[i]['space']['space_address']['city'];
            var image = data[i]['space']['photo_name'];
            var list_id = data[i]['space_id'];
            var user_id = data[i]['space']['user_id'];
            var price = data[i]['space']['activity_price']['hourly'];
            var currency_code = data[i]['space']['activity_price']['currency_code'];
            var currency_symbol = data[i]['space']['activity_price']['currency_symbol'];
            var wishlist_img = data[i]['space']['photo_name'];
            var booking_type = data[i]['space']['booking_type'];
            var space_photos = data[i]['space']['space_photos'];

            latlngset = new google.maps.LatLng(lat, long);

            var show_marker_class = space_photos.length > 1 ? '': 'd-none';

            var space_image_url = _.pluck(space_photos, 'name');
            space_image_url = space_image_url.join('^>');

            var content = '<div id="info_window_' + list_id + '" class="listing listing-map-popover" data-price="' + currency_symbol + '" data-id="' + list_id + '" data-user="' + user_id + '" data-url="/space/' + list_id + '" data-name="' + title + '" data-lng="' + long + '" data-lat="' + lat + '"><div class="panel-image listing-img">';
            content += '<a class="media-photo media-cover" target="listing_' + list_id + '" href="' + APP_URL + '/space/' + list_id + '"><div class="listing-img-container media-cover text-center"><img id="marker_image_' + list_id + '" space_image = "'+ space_image_url +'" alt="' + title + '" class="img-responsive-height" data-current="0" src="' + image + '"></div></a>';

            content += '<div class="target-prev target-control block-link marker_slider '+show_marker_class+'"  data-space_id="' + list_id + '"><i class="icon icon-chevron-left icon-size-2 icon-white"></i></div><a class="link-reset panel-overlay-bottom-left panel-overlay-label panel-overlay-listing-label" target="listing_' + list_id + '" href="' + APP_URL + '/space/' + list_id + '"><div>';

            instant_book = '';

            if (booking_type == 'instant_book')
                instant_book = '<span aria-label="Book Instantly" data-behavior="tooltip" class="h3 icon-beach"><i class="icon icon-instant-book icon-flush-sides"></i></span>';

            content += '<sup>' + currency_symbol + '</sup><span class="price-amount">' + price + '</span><sup></sup>' + instant_book + '</div></a><div class="target-next target-control marker_slider block-link '+show_marker_class+' " data-space_id="' + list_id + '"><i class="icon icon-chevron-right icon-white"></i></div></div>';
            content += '<div class="panel-body panel-card-section"><div class="media"><h3 class="listing-name text-truncate" itemprop="name" title="' + title + '">' + title + '</a></h3>';

            var marker = new google.maps.Marker({
                map: map,
                title: title,
                content: content,
                position: latlngset,
                icon: getMarkerImage('normal'),
            });

            bounds.extend(marker.position);

            google.maps.event.addListener(marker, 'click', function() {
                $scope.infowindow.setContent(this.content);
                $scope.infowindow.open(map, this);
            });
        }

        var listener = google.maps.event.addListener(map, "idle", function() {
            map.setZoom(3);
            map.fitBounds(bounds);
            google.maps.event.removeListener(listener);
        });

    }

    function getMarkerImage(type) {
        var image = 'map-pin-set-3460214b477748232858bedae3955d81.png';

        if (type == 'hover') {
            image = 'hover-map-pin-set-3460214b477748232858bedae3955d81.png';
        }

        var gicons = new google.maps.MarkerImage(APP_URL + "/images/" + image,
            new google.maps.Size(50, 50),
            new google.maps.Point(0, 0),
            new google.maps.Point(9, 20));

        return gicons;
    }

    $(document).on('click', '.marker_slider', function() {

        var space_id = $(this).attr("data-space_id");
        var dataurl = $("#marker_image_" + space_id).attr("space_image");
        var img_url = $("#marker_image_" + space_id).attr("src");

        $(this).parent().addClass("loading");
        var all_image = dataurl.split('^>');
        var rooms_img_count = all_image.length;
        var i = 0;
        var set_img_no = '';
        angular.forEach(all_image, function(img) {
            if ($.trim(img) == $.trim(img_url)) {
                set_img_no = i;
            }
            i++;
        });
        if($(this).is(".target-prev") == true) {
            var cur_img = set_img_no - 1;
            var count = rooms_img_count - 1;
        }
        else {
            var cur_img = set_img_no + 1;
            var count = 0;
        }

        if(typeof(all_image[cur_img]) != 'undefined' && $.trim(all_image[cur_img]) != "null") {
            var img = all_image[cur_img];
        }
        else {
            var img = all_image[count];
        }
        var set_img_url = img;

        $(".panel-image").removeClass("loading");
        $('.listing_slideshow_thumb_view').removeClass("loading");
        $("#marker_image_" + space_id + ",#wishlist_image_" + space_id).attr("src", set_img_url);
    });

    $('.edit_view .delete').click(function() {
        $('.wishlist-delete_confirm').attr('href', APP_URL + "/delete_wishlist/" + wishlist_id);
    });

    $('[id^="wishlist-widget-icon-"]').click(function() {
        if (typeof USER_ID == 'object') {
            window.location.href = APP_URL + '/login';
            return false;
        }
        var name = $(this).data('name');
        var img = $(this).data('img');
        var address = $(this).data('address');
        var host_img = $(this).data('host_img');
        $scope.space_id = $(this).data('space_id');

        $('.background-listing-img').css('background-image', 'url(' + img + ')');
        $('.host-profile-img').attr('src', host_img);
        $('.wl-modal-listing-name').text(name);
        $('.wl-modal-listing__address').text(address);
        $('.wl-modal-footer__input').val(address);
        $('.wl-modal__col').removeClass('d-md-none');
        $('.wl-modal__modal').removeClass('d-none');
        $('.wl-modal__col:nth-child(2)').addClass('d-none');
        $('.row-margin-zero').append('<div id="wish-list-signup-container" style="overflow-y:auto;" class="col-lg-5 wl-modal__col-collapsible"> <div class="loading wl-modal__col"> </div> </div>');
        $http.get(APP_URL + "/wishlist_list?id=" + $(this).data('space_id')+'&type=Space', {}).then(function(response) {
            $('#wish-list-signup-container').remove();
            $('.wl-modal__col:nth-child(2)').removeClass('d-none');
            $scope.wishlist_list = response.data;
        });
    });

    $scope.wishlist_row_select = function(index) {

        $http.post(APP_URL + "/save_wishlist", {
            space_id: $scope.space_id,
            wishlist_id: $scope.wishlist_list[index].id,
            saved_id: $scope.wishlist_list[index].saved_id
        }).then(function(response) {
            var saved_id = (response.data == 'null') ? null : response.data;
            $scope.wishlist_list[index].saved_id = saved_id;
        });

        var saved_id = ($('#wishlist_row_' + index).hasClass('text-dark-gray')) ? null : 1;
        $scope.wishlist_list[index].saved_id = saved_id;
    };

    $(document).on('submit', '.wl-modal-footer__form', function(event) {
        event.preventDefault();
        $('.wl-modal__col:nth-child(2)').addClass('d-none');
        $('.row-margin-zero').append('<div id="wish-list-signup-container" style="overflow-y:auto;" class="col-lg-5 wl-modal__col-collapsible"> <div class="loading wl-modal__col"> </div> </div>');
        $http.post(APP_URL + "/wishlist_create", {
            data: $('.wl-modal-footer__input').val(),
            id: $scope.space_id
        }).then(function(response) {
            $('.wl-modal-footer__form').addClass('d-none');
            $('#wish-list-signup-container').remove();
            $('.wl-modal__col:nth-child(2)').removeClass('d-none');
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

        $('.wl-modal__modal').addClass('d-none');
        $('.wl-modal__modal').show();
    });

    $('#wishlist-modal').on('hidden.bs.modal', function () {
        var null_count = $filter('filter')($scope.wishlist_list, {
            saved_id: null
        });
        var checked = (null_count.length == $scope.wishlist_list.length) ? false : true;
        $('#wishlist-widget-' + $scope.space_id).prop('checked', checked);
    });
    

}]);