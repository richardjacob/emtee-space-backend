<!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="{{ asset('admin_assets/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<script src="{{ asset('admin_assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore.js"></script>

<script src="{{ asset('js/angular.js') }}"></script>
<script src="{{ asset('js/angular-sanitize.js') }}"></script>

<script> 
var app = angular.module('App', ['ngSanitize']);
var APP_URL = {!! json_encode(url('/')) !!}; 
var ADMIN_URL =  '{!! ADMIN_URL  !!}';
var csrf_token =  $('meta[name="csrf-token"]').attr('content');
var max_guest_limit = {{ $max_guest_limit }};
</script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
  $(document).ready(function(){
    $('.ui-datepicker').addClass('notranslate');
  })
</script>

<!-- Bootstrap 3.3.5 -->
<script src="{{ asset('admin_assets/bootstrap/js/bootstrap.min.js') }}"></script>

@if (!isset($exception))

    @if(Route::currentRouteName() == 'admin_dashboard')
      <!-- Morris.js charts -->
      <script src="{{ asset('admin_assets/plugins/morris/raphael-min.js') }}"></script>
      <script src="{{ asset('admin_assets/plugins/morris/morris.min.js') }}"></script>
      <!-- datepicker -->
      <script src="{{ asset('admin_assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
      <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('admin_assets/dist/js/dashboard.js') }}"></script>
    @endif

    @if (Route::currentRouteName() == 'admin.add_space' || Route::currentRouteName() == 'admin.edit_space')
      <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ $map_key }}&sensor=false&libraries=places"></script>
      <!-- admin rooms add/edit form array method validation -->      
      <script src="{{ asset('admin_assets/dist/js/jquery.validate.js') }}"></script>
      {!! Html::script('js/manage_space.js?v='.$version) !!}
      <script src="{{ url('admin_assets/dist/js/additional-methods.min.js') }}"></script>
      {!! Html::script('js/underscore-min.js') !!}
      {!! Html::script('js/moment.min.js') !!}
      {!! Html::script('js/moment-range.js') !!}
      <script src="{{ asset('admin_assets/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
      {!! Html::script('plugins/fullcalendar/core/main.js') !!}
      {!! Html::script('plugins/fullcalendar/interaction/main.js') !!}
      {!! Html::script('plugins/fullcalendar/daygrid/main.js') !!}
      {!! Html::script('plugins/fullcalendar/timegrid/main.js') !!}
      {!! Html::script('plugins/fullcalendar/moment/main.js') !!}

      <script src="{{ asset('admin_assets/dist/js/space.js') }}"></script>
      <script type="text/javascript">
        window['moment-range'].extendMoment(moment);
      </script>
    @endif

    @if (Route::currentRouteName() == 'reports')
    <script src="{{ asset('admin_assets/dist/js/reports.js') }}"></script>
    @endif

    @if (Route::currentRouteName() == 'add_page' || Route::currentRouteName() == 'edit_page' || Route::currentRouteName() == 'send_email' || Route::currentRouteName() == 'add_help' || Route::currentRouteName() == 'edit_help')
    <script src="{{ asset('admin_assets/plugins/editor/editor.js') }}"></script>
      <script type="text/javascript"> 
        $("[name='submit']").click(function(){
          $('#content').text($('#txtEditor').Editor("getText"));
          $('#message').text($('#txtEditor').Editor("getText"));
          $('#answer').text($('#txtEditor').Editor("getText"));
        });
      </script>
    @endif

    @php
      $validate_routes = array(
        'create_kind_of_space', 'edit_kind_of_space',
        'create_guest_access', 'edit_guest_access',
        'create_services', 'edit_services',
        'create_style', 'edit_style',
        'create_special_feature', 'edit_special_feature',
        'create_space_rule', 'edit_space_rule',
        'create_activity_type', 'edit_activity_type',
        'create_activity', 'edit_activity',
        'create_sub_activity', 'edit_sub_activity',
        'create_amenity', 'edit_amenity',
      );
    @endphp

    @if(in_array(Route::currentRouteName(),$validate_routes))
    <script src="{{ asset('admin_assets/dist/js/jquery.validate.js') }}"></script>
    <!-- form validation admin side (kind_of_space, guest_access, services, style, special_feature, space_rule, activities, amenities)-->
    <script type="text/javascript">
      $(document).ready(function() {
        // validate the comment form when it is submitted
        $("#form").validate({
            focusInvalid: false,
            rules: {
                "lang_code[]": "required",
                "name[]": "required",
                "status":"required",
                "type_id":"required",
                "icon":"required",
                "image": {
                  required: true,
                  extension:"png|jpg|jpeg|gif"
                },
                "images": { 
                  extension:"png|jpg|jpeg|gif"
                },
            },
            messages: {
              "lang_code[]":"The Language field is required",
              "name[]": "The Name field is required",
              "status": "The status field is required",
              "type_id":"The Type field is required",
              "icon":"The Icon field is required",
              "image": {
                  required: "The Image field is required",
                  extension: "Please upload the images like JPG,JPEG,PNG,GIF File Only."
              },
            }
        });
      });
      $.validator.addMethod("extension", function(value, element, param) {
      param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
      return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
      }, $.validator.format("Please upload the images like JPG,JPEG,PNG,GIF File Only."));
    </script>
    <!-- end script -->
    @endif
   @endif
<!-- AdminLTE App -->
<script src="{{ asset('admin_assets/dist/js/app.js') }}"></script>
<script src="{{ asset('admin_assets/dist/js/common.js') }}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{ asset('admin_assets/dist/js/demo.js') }}"></script>

@stack('scripts')

<script type="text/javascript">
  $('#dataTableBuilder_length').addClass('dt-buttons');
  $('#dataTableBuilder_wrapper > div:not("#dataTableBuilder_length").dt-buttons').css('margin-left','20%');
</script>

</body>
</html>