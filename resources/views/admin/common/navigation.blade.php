 <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ url('admin_assets/dist/img/avatar04.png') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::guard('admin')->user()->username }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">

        <li class="header">MAIN NAVIGATION</li>

        <li class="{{ (Route::currentRouteName() == 'admin_dashboard') ? 'active' : ''  }}"><a href="{{ route('admin_dashboard') }}"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>

        @if(Auth::guard('admin')->user()->can('manage_admin'))
          <li class="treeview {{ (Route::currentRouteName() == 'admin_users' || Route::currentRouteName() == 'roles') ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-user-plus"></i> <span>Manage Admin</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Route::currentRouteName() == 'admin_users') ? 'active' : ''  }}"><a href="{{ route('admin_users') }}"><i class="fa fa-circle-o"></i><span>Admin Users</span></a></li>
            <li class="{{ (Route::currentRouteName() == 'roles') ? 'active' : ''  }}"><a href="{{ route('roles') }}"><i class="fa fa-circle-o"></i><span>Roles & Permissions</span></a></li>
          </ul>
          </li>
        @endif

        @if(Auth::guard('admin')->user()->can('users'))
          <li class="{{ (Route::currentRouteName() == 'users') ? 'active' : ''  }}"><a href="{{ route('users') }}"><i class="fa fa-users"></i><span>Manage Users</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('space'))
          <li class="{{ (Route::currentRouteName() == 'admin.space') ? 'active' : ''  }}"><a href="{{ route('admin.space') }}"><i class="fa fa-home"></i><span>Manage Spaces</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('reservations'))
          <li class="treeview {{ (Route::currentRouteName() == 'reservations' || Route::currentRouteName() == 'host_penalty') ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-plane"></i> <span>Reservations & Penalty</span><i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Route::currentRouteName() == 'reservations') ? 'active' : ''  }}"><a href="{{ route('reservations') }}"><i class="fa fa-circle-o"></i><span>Reservations</span></a></li>
              <li class="{{ (Route::currentRouteName() == 'host_penalty') ? 'active' : ''  }}"><a href="{{ route('host_penalty') }}"><i class="fa fa-circle-o"></i><span>Host Penalty</span></a></li>
          </ul>
          </li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_disputes'))
          <li class="{{ (Route::currentRouteName() == 'admin.disputes') ? 'active' : ''  }}"><a href="{{ route('admin.disputes') }}"><i class="fa fa-hand-peace-o"></i><span>Manage Disputes</span></a></li>          
        @endif
        
        @if(Auth::guard('admin')->user()->can('email_settings') || Auth::guard('admin')->user()->can('send_email'))
          <li class="treeview {{ (Route::currentRouteName() == 'email_settings' || Route::currentRouteName() == 'send_email') ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-envelope-o"></i> <span>Manage Emails</span><i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            @if(Auth::guard('admin')->user()->can('send_email'))
            <li class="{{ (Route::currentRouteName() == 'send_email') ? 'active' : ''  }}"><a href="{{ route('send_email') }}"><i class="fa fa-circle-o"></i><span>Send Email</span></a></li>
            @endif
            @if(Auth::guard('admin')->user()->can('email_settings'))
              <li class="{{ (Route::currentRouteName() == 'email_settings') ? 'active' : ''  }}"><a href="{{ route('email_settings') }}"><i class="fa fa-circle-o"></i><span>Email Settings</span></a></li>
            @endif
          </ul>
          </li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_reviews'))
          <li class="{{ (Route::currentRouteName() == 'reviews') ? 'active' : ''  }}"><a href="{{ route('reviews') }}"><i class="fa fa-eye"></i><span>Reviews</span></a></li>
        @endif
        
        @if(Auth::guard('admin')->user()->can('manage_referrals'))
        <li class="{{ (Route::currentRouteName() == 'referrals') ? 'active' : ''  }}"><a href="{{ route('referrals') }}"><i class="fa fa-group"></i><span>Referrals</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_wishlists'))
          <li class="{{ (Route::currentRouteName() == 'wishlists') ? 'active' : ''  }}"><a href="{{ route('wishlists') }}"><i class="fa fa-heart"></i><span>Wish Lists</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_coupon_code'))
          <li class="{{ (Route::currentRouteName() == 'coupon_code') ? 'active' : ''  }}"><a href="{{ route('coupon_code') }}"><i class="fa fa-ticket"></i><span>Manage Coupon Code</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('reports'))
          <li class="{{ (Route::currentRouteName() == 'reports') ? 'active' : ''  }}"><a href="{{ route('reports') }}"><i class="fa fa-file-text-o"></i><span>Reports</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_home_sliders'))
          <li class="{{ (Route::currentRouteName() == 'homepage_sliders') ? 'active' : ''  }}"><a href="{{ route('homepage_sliders') }}"><i class="fa fa-globe"></i><span>Manage Home Page Sliders</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_login_sliders'))
          <li class="{{ (Route::currentRouteName() == 'slider') ? 'active' : ''  }}"><a href="{{ route('slider') }}"><i class="fa fa-image"></i><span>Manage Login Sliders</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_our_community_banners'))
          <li class="{{ (Route::currentRouteName() == 'our_community_banners') ? 'active' : ''  }}"><a href="{{ route('our_community_banners') }}"><i class="fa fa-image"></i><span>Manage Our Communities</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_help'))
          <li class="treeview {{ (Route::currentRouteName() == 'help' || Route::currentRouteName() == 'help_category' || Route::currentRouteName() == 'help_subcategory') ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-support"></i> <span>Manage Help</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Route::currentRouteName() == 'help') ? 'active' : ''  }}"><a href="{{ route('help') }}"><i class="fa fa-circle-o"></i><span>Help</span></a></li>
            <li class="{{ (Route::currentRouteName() == 'help_category') ? 'active' : ''  }}"><a href="{{ route('help_category') }}"><i class="fa fa-circle-o"></i><span>Category</span></a></li>
            <li class="{{ (Route::currentRouteName() == 'help_subcategory') ? 'active' : ''  }}"><a href="{{ route('help_subcategory') }}"><i class="fa fa-circle-o"></i><span>Subcategory</span></a></li>
          </ul>
          </li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_amenities'))
          <li class="{{ (Route::currentRouteName() == 'amenities') ? 'active' : ''  }}"><a href="{{ route('amenities') }}"><i class="fa fa-bullseye"></i><span> Amenities </span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_kind_of_space'))
          <li class="{{ (Route::currentRouteName() == 'kind_of_space') ? 'active' : ''  }}"><a href="{{ route('kind_of_space') }}"><i class="fa fa-building"></i><span> Manage Space Type </span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_guest_access'))
          <li class="{{ (Route::currentRouteName() == 'guest_access') ? 'active' : ''  }}"><a href="{{ route('guest_access') }}"><i class="fa fa-home"></i><span>Manage Guest Access</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_services'))
          <li class="{{ (Route::currentRouteName() == 'services') ? 'active' : ''  }}"><a href="{{ route('services') }}"><i class="fa fa-hotel"></i><span>Manage Services & Extras</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_style'))
          <li class="{{ (Route::currentRouteName() == 'styles') ? 'active' : ''  }}"><a href="{{ route('styles') }}"><i class="fa fa-hotel"></i><span> Manage Space Styles </span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_special_feature'))
          <li class="{{ (Route::currentRouteName() == 'special_features') ? 'active' : ''  }}"><a href="{{ route('special_features') }}"><i class="fa fa-hotel"></i><span> Manage Special Features </span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_space_rules'))
          <li class="{{ (Route::currentRouteName() == 'space_rules') ? 'active' : ''  }}"><a href="{{ route('space_rules') }}"><i class="fa fa-hotel"></i><span> Manage Space Rules </span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_activities'))
          <li class="treeview {{ (Route::currentRouteName() == 'activity_types' || Route::currentRouteName() == 'activities' || Route::currentRouteName() == 'sub_activities') ? 'active' : ''  }}">
          <a href="#">
            <i class="fa fa-support"></i> <span>Manage Activities</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Route::currentRouteName() == 'activity_types') ? 'active' : ''  }}"><a href="{{ route('activity_types') }}">
              <i class="fa fa-hotel"></i><span> Activity Types </span></a>
            </li>
            <li class="{{ (Route::currentRouteName() == 'activities') ? 'active' : ''  }}"><a href="{{ route('activities') }}">
              <i class="fa fa-hotel"></i><span> Activities </span></a>
            </li>
            <li class="{{ (Route::currentRouteName() == 'sub_activities') ? 'active' : ''  }}"><a href="{{ route('sub_activities') }}">
              <i class="fa fa-hotel"></i><span> Sub Activities </span></a>
            </li>
          </ul>
          </li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_pages'))
          <li class="{{ (Route::currentRouteName() == 'pages') ? 'active' : ''  }}"><a href="{{ route('pages') }}"><i class="fa fa-newspaper-o"></i><span>Manage Static Pages</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_currency'))
          <li class="{{ (Route::currentRouteName() == 'currency') ? 'active' : ''  }}"><a href="{{ route('currency') }}"><i class="fa fa-dollar"></i><span>Manage Currency</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_language'))
          <li class="{{ (Route::currentRouteName() == 'language') ? 'active' : ''  }}"><a href="{{ route('language') }}"><i class="fa fa-language"></i><span>Manage Language</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_country'))
          <li class="{{ (Route::currentRouteName() == 'country') ? 'active' : ''  }}"><a href="{{ route('country') }}"><i class="fa fa-globe"></i><span>Manage Country</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_referral_settings'))
          <li class="{{ (Route::currentRouteName() == 'referral_settings') ? 'active' : ''  }}"><a href="{{ route('referral_settings') }}"><i class="fa fa-users"></i><span>Manage Referrals Settings</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_fees'))
          <li class="{{ (Route::currentRouteName() == 'fees') ? 'active' : ''  }}"><a href="{{ route('fees') }}"><i class="fa fa-dollar"></i><span>Manage Fees</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('manage_metas'))
          <li class="{{ (Route::currentRouteName() == 'metas') ? 'active' : ''  }}"><a href="{{ route('metas') }}"><i class="fa fa-bar-chart"></i><span>Manage Metas</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('api_credentials'))
          <li class="{{ (Route::currentRouteName() == 'api_credentials') ? 'active' : ''  }}"><a href="{{ route('api_credentials') }}"><i class="fa fa-facebook"></i><span>Api Credentials</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('payment_gateway'))
          <li class="{{ (Route::currentRouteName() == 'payment_gateway') ? 'active' : ''  }}"><a href="{{ route('payment_gateway') }}"><i class="fa fa-paypal"></i><span>Payment Gateway</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('join_us'))
          <li class="{{ (Route::currentRouteName() == 'join_us') ? 'active' : ''  }}"><a href="{{ route('join_us') }}"><i class="fa fa-share-alt"></i><span>Join Us Links</span></a></li>
        @endif

        @if(Auth::guard('admin')->user()->can('site_settings'))
          <li class="{{ (Route::currentRouteName() == 'site_settings') ? 'active' : ''  }}"><a href="{{ route('site_settings') }}"><i class="fa fa-gear"></i><span>Site Settings</span></a></li>
        @endif

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>