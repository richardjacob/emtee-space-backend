@extends('template_three')

@section('main')

<div class="content-wrap">

    <section id="section-01" class="home-main-intro">
        <div class="home-main-intro-container">
            <div class="container">
                <div class="heading mb-9">
                    <h1 class="mb-7">
                        <span class="d-block" data-animate="slideInLeft">Discover Local</span>
                        <span class="font-weight-light d-block" data-animate="fadeInRight">P2P Storage space to let</span>
                    </h1>
                    <p class="h5 font-weight-normal text-secondary mb-0" data-animate="fadeInDown">
                        Find great places to store: <i style="color:#000">furniture, goods, jet skis, clothing etc from local folk.</i>
                    </p>
                </div>
                <div class="form-search form-search-style-02 pb-9" data-animate="fadeInDown">
                    <form action="{{ route('search_page') }}"  method="get">
                        <div class="row align-items-end no-gutters">
                            <div
                                class="col-xl-6 mb-4 mb-xl-0 py-3 px-4 bg-white border-right position-relative rounded-left form-search-item">
                                <label for="key-word"
                                    class="font-size-md font-weight-semibold text-dark mb-0 lh-1">Where</label>
                                <div class="input-group dropdown show">
                                    <input type="text" autocomplete="off"    id="location" name="location"
                                        class="form-control form-control-mini border-0 px-0 bg-transparent"
                                        placeholder="{{ trans('messages.header.anywhere') }}" value="" type="text"  required>
                                </div>
                            </div>
                            <div
                                class="col-xl-4 mb-4 mb-xl-0 py-3 px-4 bg-white position-relative rounded-right form-search-item">
                                <label for="key-word"
                                    class="font-size-md font-weight-semibold text-dark mb-0 lh-1"> @lang('messages.new_space.select_activity')</label>
                                <div class="input-group dropdown show">
                                   
                                        <select name="activity_type" id="activity_select" class="selectpicker" data-show-subtext="true" data-live-search="true" required>
                                            <option value="" disabled selected> </option>
                                            @foreach($header_activties as $activity)
                                                <option value="{{ $activity->id }}" > {{ $activity->name }} </option>
                                            @endforeach
                                        </select>    
                                    
                                    
                                </div>
                            </div>



                            <div class="col-xl-2 button">
                                <button  id="submit_location" type="submit" class="btn btn-primary btn-lg btn-icon-left btn-block"><i
                                        class="fal fa-search"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="font-size-lg mb-4">
                    {{-- Or browse the highlights --}}
                </div>
                {{-- <div class="list-inline pb-8 flex-wrap my-n2">
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 icon-box-style-01 link-hover-dark-white">
                            <div class="card-body p-0">
                                <svg class="icon icon-pizza">
                                    <use xlink:href="#icon-pizza"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Foods
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 icon-box-style-01 link-hover-dark-white">
                            <div class="card-body p-0">
                                <svg class="icon icon-bed">
                                    <use xlink:href="#icon-bed"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Hotels
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 icon-box-style-01 link-hover-dark-white">
                            <div class="card-body p-0">
                                <svg class="icon icon-brush2">
                                    <use xlink:href="#icon-brush2"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Jobs
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 link-hover-dark-white icon-box-style-01">
                            <div class="card-body p-0">
                                <svg class="icon icon-pharmaceutical">
                                    <use xlink:href="#icon-pharmaceutical"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Medicals
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 link-hover-dark-white icon-box-style-01">
                            <div class="card-body p-0">
                                <svg class="icon icon-cog">
                                    <use xlink:href="#icon-cog"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Services
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 link-hover-dark-white icon-box-style-01">
                            <div class="card-body p-0">
                                <svg class="icon icon-bag">
                                    <use xlink:href="#icon-bag"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Shopping
                                </span>
                            </div>
                        </a>
                    </div>
                    <div class="list-inline-item py-2">
                        <a href="explore-sidebar-grid.html"
                            class="card border-0 link-hover-dark-white icon-box-style-01">
                            <div class="card-body p-0">
                                <svg class="icon icon-car">
                                    <use xlink:href="#icon-car"></use>
                                </svg>
                                <span class="card-text font-size-md font-weight-semibold mt-2 d-block">
                                    Automotive
                                </span>
                            </div>
                        </a>
                    </div>
                </div> --}}
            </div>
            <div class="home-main-how-it-work bg-white pt-11">
                <div class="container">
                    <h2 class="mb-8">
                        <span>See</span>
                        <span class="font-weight-light">How It Works</span> 
                        <span>for Local Hosts</span>
                    </h2>
                    <div class="row no-gutters pb-11">
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                    <img src="https://img.icons8.com/color/50/000000/list.png"/>
                                    <span class="number h1 font-weight-bold">1</span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        List Your Space
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        It is free to list and takes less than 10 minutes.
                                        
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                    <img src="https://img.icons8.com/color/50/000000/event-accepted.png"/>
                                    <span class="number h1 font-weight-bold">2</span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        Accept a Booking
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        Accept a booking request that you are comfortable with. <br/>
                                         <a href="{{ url('storage_host_guarantee') }}" target="_blank">Learn More</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                   <img src="https://img.icons8.com/fluent/50/000000/get-revenue.png"/>
                                    <span class="number h1 font-weight-bold">3</span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        Earn
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        We will deposit your monthly earnings into your bank account.
                                        <a href="{{ url('dashboard') }}" target="_blank">Become a Storage Host Now</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom"></div>
                </div>
            </div>
            
             <div class="home-main-how-it-work bg-white pt-11">
                <div class="container">
                    <h2 class="mb-8">
                        <span>See</span>
                        <span class="font-weight-light">How It Works</span> 
                        <span>for Users</span>
                    </h2>
                    <div class="row no-gutters pb-11">
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                    <img src="https://img.icons8.com/doodle/48/000000/search--v1.png"/>
                                    <span class="number h1 font-weight-bold">1</span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        Search 
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                       Enter your location, duration and type of space you want. <a href="{{ url('signup_login') }}">Register Now</a>
                                        
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                   <img src="https://img.icons8.com/cute-clipart/48/000000/view-file.png"/>
                                    <span class="number h1 font-weight-bold">2</span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        View Space
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        View available options 
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                    <img src="https://img.icons8.com/doodle/48/000000/box--v2.png"/>
                                    <span class="number h1 font-weight-bold">3</span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        Book and Enjoy 
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                       Seamless bookings direct to local hosts.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom"></div>
                </div>
            </div>

        </div>
    </section>

    <section>

        <div class="container">
            <div class="mb-7">
                <h2 class="mb-0">
                    <span class="font-weight-semibold">Popular Catagories</span>
                    <span class="font-weight-light"></span>
                </h2>
            </div>
            <div class="row no-gutters pb-11">
                <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                    <div class="media icon-box-style-02 fadeInDown animated" data-animate="fadeInDown">
                        <div class="position-relative store-image">
                            <a href="{{ URL::to('s?activity_type=6') }}">
                                <img  src="images/home_section/residential.png"  alt="store 1" height="20"  class="card-img-top rounded-0">
                                <div class="carousel-caption">
                                    <h1 class="text-white">Residential</h1>
                                  </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                    <div class="media icon-box-style-02 fadeInDown animated" data-animate="fadeInDown">
                        <div class="position-relative store-image">
                            <a href="{{ URL::to('s?activity_type=7') }}">
                                <img  src="images/home_section/commercial.png"  alt="store 1" height="20"  class="card-img-top rounded-0">
                                <div class="carousel-caption">
                                    <h1 class="text-white">Commercial</h1>
                                  </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">    
                    <div class="media icon-box-style-02 fadeInDown animated" data-animate="fadeInDown">
                        <div class="position-relative store-image">
                            <a href="{{ URL::to('s?activity_type=8') }}">
                                <img  src="images/home_section/celebratory.png"  alt="store 1" height="20"  class="card-img-top rounded-0">
                                <div class="carousel-caption">
                                    <h1 class="text-white">Residential</h1>
                                  </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                    <div class="media icon-box-style-02 fadeInDown animated" data-animate="fadeInDown">
                        <div class="position-relative store-image">
                            <a href="{{ URL::to('s?activity_type=9') }}">
                                <img  src="images/home_section/creative.png"  alt="store 1" height="20"  class="card-img-top rounded-0">
                                <div class="carousel-caption">
                                    <h1 class="text-white">Creative</h1>
                                  </div>
                            </a>
                        </div>
                    </div>
                </div>
   
                <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                    <div class="media icon-box-style-02 fadeInDown animated" data-animate="fadeInDown">
                        <div class="position-relative store-image">
                            <a href="{{ URL::to('s?activity_type=10') }}">
                                <img  src="images/home_section/entertainment.png"  alt="store 1" height="20"  class="card-img-top rounded-0">
                                <div class="carousel-caption">
                                    <h1 class="text-white">Entertainment </h1>
                                  </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                    <div class="media icon-box-style-02 fadeInDown animated" data-animate="fadeInDown">
                        <div class="position-relative store-image">
                            <a href="{{ URL::to('s?activity_type=') }}">
                                <img  src="images/home_section/other.png"  alt="store 1" height="20"  class="card-img-top rounded-0">
                                <div class="carousel-caption">
                                    <h1 class="text-white">Other </h1>
                                  </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-bottom "></div>

        </div>


    </section>

    <section id="section-03" class="pb-8 our-directory">
        <br/>         <br/>
        <div class="container">
            <div class="mb-7">
                <h2 class="mb-0">
                    <span class="font-weight-semibold">Most Popular</span>
                    <span class="font-weight-light"></span>
                </h2>
            </div>
            <div class="d-flex align-items-center pb-8">
                <ul class="nav nav-pills tab-style-01" role="tablist">

                    
                    <li class="nav-item">
                        <div class="header-customize-item button">
                            <a href="{{ url('s?activity_type=') }}" class="btn btn-primary btn-icon-right">See All 
                                <i class="far fa-angle-right"></i></a>
                        </div>
                    </li>
                   
                </ul>
                <div class="ml-auto d-flex slick-custom-nav pl-5">
                    <div class="arrow slick-prev disabled" id="previous"><i class="fas fa-chevron-left"></i></div>
                    <div class="arrow slick-next" id="next"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="container container-1720">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all">
                    <div class="slick-slider arrow-top full-slide custom-nav equal-height"
                        data-slick-options='{"slidesToShow": 5,"autoplay":false,"dots":false,"arrows":false,"responsive":[{"breakpoint": 2000,"settings": {"slidesToShow": 4}},{"breakpoint": 1500,"settings": {"slidesToShow": 3}},{"breakpoint": 1000,"settings": {"slidesToShow": 2}},{"breakpoint": 770,"settings": {"slidesToShow": 1}}]}'>
                        
                        @foreach($popular_rooms as $row)
                        <div class="box" data-animate="fadeInUp">
                            <div class="store card border-0 rounded-0">
                                <div class="position-relative store-image">
                                    <a href="{{ URL::to('space/'. $row->id) }}">
                                        <img  src="{{ getFirstHomeImage($row->id) }}" alt="store 1" class="card-img-top rounded-0">
                                    </a>
                                    <div class="image-content position-absolute d-flex align-items-center">
                                        <div class="content-left">
                                            <div class="badge badge-primary"> 
                                            @if($row->booking_type =='instant_book')
                                            <!--{{$row->booking_type}}-->
                                             Instant Booking 
                                            @else
                                             Request To Book
                                            @endif
                                            
                                            </div>
                                        </div>
                                        <div class="content-right ml-auto d-flex w-lg show-link">
                                            <a href="#}" class="item viewing"
                                                data-toggle="tooltip" data-placement="top" title="Quickview"
                                                data-gtf-mfp="true">
                                                <svg class="icon icon-expand">
                                                    <use xlink:href="#icon-expand"></use>
                                                </svg>
                                            </a>
                                            <a href="#" class="item marking" data-toggle="tooltip" data-placement="top"
                                                title="Bookmark"><i class="fal fa-bookmark"></i></a>
                                            <a href="#" class="item" data-toggle="tooltip" data-placement="top"
                                                title="Compare">
                                                <svg class="icon icon-chart-bars">
                                                    <use xlink:href="#icon-chart-bars"></use>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body px-0 pb-0 pt-3">
                                    <a href="{{ URL::to('space/'. $row->id) }}"
                                        class="card-title h5 text-dark d-inline-block mb-2"><span
                                            class="letter-spacing-25">{{$row->name}}</span></a>
                                    <ul
                                        class="list-inline store-meta mb-4 font-size-sm d-flex align-items-center flex-wrap">
                                        <li class="list-inline-item"><span
                                                class="badge badge-success d-inline-block mr-1">R{{getPriceHourly($row->id)}} per hour</span><span>
                                                   </span>
                                        </li>
                                        <li class="list-inline-item separate"></li>
                                        <li class="list-inline-item"><span class="mr-1">Policy</span><span
                                                class="text-danger font-weight-semibold">{{$row->cancellation_policy}}</span></li>
                                        <li class="list-inline-item separate"></li>
                                        <li class="list-inline-item">
                                            <span class="mr-1">Size</span><span
                                                class="text-danger font-weight-semibold">{{$row->sq_ft}} sq ft</span></li>
                                        </li>
                                    </ul>

                                    <div class="media">
                                        {{-- <a href="#" class="d-inline-block mr-3"><img
                                                src="{{ URL::to('vendors/images/listing/testimonial-1.png') }}" alt="testimonial"
                                                class="rounded-circle">
                                        </a> --}}
                                        <div class="media-body lh-14 font-size-sm">

                                            {{ str_limit($row->summary, 100) }}
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
                
            </div>
        </div>


        
    </section>

  

    
    <section id="section-02" class="pb-2 feature-destination pt-55 p-4">
        <div class="container">
            <div class="mb-8">
                <h2 class="mb-0">
                    <span class="font-weight-semibold">Benefits   <br/></span></h2>
                    <br/>
                    <ul class="font-weight-light">
                        <li> More affordable solution; up to 60%* cheaper than traditional self-storage facilities </li>
                        <li> Safe and secure facilities closer to home for faster and easier access </li>
                        <li> Hosts are insured and users are insured </li>
                    </ul>

                
            </div>
            {{-- <div class="slick-slider arrow-center"
                data-slick-options='{"slidesToShow": 4, "autoplay":false,"dots":false,"responsive":[{"breakpoint": 992,"settings": {"slidesToShow": 3,"arrows":false,"dots":true,"autoplay":true}},{"breakpoint": 768,"settings": {"slidesToShow": 2,"arrows":false,"dots":true,"autoplay":true}},{"breakpoint": 400,"settings": {"slidesToShow": 1,"arrows":false,"dots":true,"autoplay":true}}]}'>
                
                @foreach($home_city as $city)
                
                <div class="box" data-animate="zoomIn">
                    <div class="card border-0">
                        <a class="hover-scale" href="{{ URL::to('s?location=' . $city->name) }}">
                            <img  src="{{ asset('/images/home_cities/'.$city->image) }}" alt="{{$city->name}}" class="image">
                        </a>
                        <div class="card-body px-0 pt-4">
                            <h5 class="card-title mb-0">
                                <a  href="{{ URL::to('s?location=' . $city->name) }}" class="font-size-h5 link-hover-dark-primary">
                                   {{ $city->name}}</a>
                            </h5>
                            <span class="card-text font-size-md">

                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div> --}}
        </div>
    </section>



    <section class="home-main-testimonial pt-8 pb-13 d-none" id="section-04">
        <div class="container">
            <h2 class="mb-8">
                <span class="font-weight-semibold">Clients </span>
                <span class="font-weight-light">Review</span>
            </h2>
            <div class="container">
                <div class="row">
                    <div class="col col-md-12">
                        <div class="slick-slider testimonials-slider arrow-top"
                            data-slick-options='{"slidesToShow": 2,"autoplay":false,"dots":false,"responsive":[{"breakpoint": 992,"settings": {"slidesToShow": 1,"arrows":false}}]}'>
                            <div class="box">
                                <div class="card testimonial h-100 border-0 bg-transparent">
                                    <a href="#" class="author-image">
                                        <img src="{{ asset('/images/logos/favicon.png') }}" alt="Testimonial" class="rounded-circle">
                                    </a>
                                    <div class="card-body bg-white">
                                        <div class="testimonial-icon text-right">
                                            <svg class="icon icon-quote">
                                                <use xlink:href="#icon-quote"></use>
                                            </svg>
                                        </div>
                                        <ul class="list-inline mb-4 d-flex align-items-end flex-wrap">
                                            <li class="list-inline-item">
                                                <a href="#"
                                                    class="font-size-lg text-dark font-weight-semibold d-inline-block">Lizwi Khanyile
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <span
                                                    class="h5 font-weight-light mb-0 d-inline-block ml-1 text-gray">/</span>
                                            </li>
                                            <li>
                                                <span class="text-gray">
                                                    Inzalo Construction
                                                </span>
                                            </li>
                                        </ul>
                                        <div class="card-text text-gray pr-4">
                                           The price we paid was more than half of what a storage facility charges. I had the option to book my unit weeks in advance prior to us starting our home renovations.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box">
                                <div class="card testimonial h-100 border-0 bg-transparent">
                                    <a href="#" class="author-image">
                                        <img src="{{ asset('/images/logos/favicon.png') }}" alt="Testimonial" class="rounded-circle">
                                    </a>
                                    <div class="card-body bg-white">
                                        <div class="testimonial-icon text-right">
                                            <svg class="icon icon-quote">
                                                <use xlink:href="#icon-quote"></use>
                                            </svg>
                                        </div>
                                        <ul class="list-inline mb-4 d-flex align-items-end flex-wrap">
                                            <li class="list-inline-item">
                                                <a href="#"
                                                    class="font-size-lg text-dark font-weight-semibold d-inline-block">Anabella
                                                    Kleva
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <span
                                                    class="h5 font-weight-light mb-0 d-inline-block ml-1 text-gray">/</span>
                                            </li>
                                            <li class="list-inline-item">
                                                <span class="text-gray">
                                                    Management at Zack's Catering
                                                </span>
                                            </li>
                                        </ul>
                                        <div class="card-text text-gray pr-4">Emtee Space made it possible for me to store my extra office furniture while we look for a new premises 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box">
                                <div class="card testimonial h-100 border-0 bg-transparent">
                                    <a href="#" class="author-image">
                                        <img src="{{ asset('/images/logos/favicon.png') }}" alt="Testimonial" class="rounded-circle">
                                    </a>
                                    <div class="card-body bg-white">
                                        <div class="testimonial-icon text-right">
                                            <svg class="icon icon-quote">
                                                <use xlink:href="#icon-quote"></use>
                                            </svg>
                                        </div>
                                        <ul class="list-inline mb-4 d-flex align-items-end flex-wrap">
                                            <li class="list-inline-item">
                                                <a href="#"
                                                    class="font-size-lg text-dark font-weight-semibold d-inline-block">Verusha Maharaja
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <span
                                                    class="h5 font-weight-light mb-0 d-inline-block ml-1 text-gray">/</span>
                                            </li>
                                            <li>
                                                <span class="text-gray">
                                                    CEO at Maharajas Caterers and Decor
                                                </span>
                                            </li>
                                        </ul>
                                        <div class="card-text text-gray pr-4">
                                              I lost my job this year but Emtee Space made it possible for me to earn an income as I rented out a room and shed on my property.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box">
                                <div class="card testimonial h-100 border-0 bg-transparent">
                                    <a href="#" class="author-image">
                                        <img src="{{ asset('/images/logos/favicon.png') }}" alt="Testimonial" class="rounded-circle">
                                    </a>
                                    <div class="card-body bg-white">
                                        <div class="testimonial-icon text-right">
                                            <svg class="icon icon-quote">
                                                <use xlink:href="#icon-quote"></use>
                                            </svg>
                                        </div>
                                        <ul class="list-inline mb-4 d-flex align-items-end flex-wrap">
                                            <li class="list-inline-item">
                                                <a href="#"
                                                    class="font-size-lg text-dark font-weight-semibold d-inline-block">Kagiso Maduna
                                                </a>
                                            </li>
                                            <li class="list-inline-item">
                                                <span
                                                    class="h5 font-weight-light mb-0 d-inline-block ml-1 text-gray">/</span>
                                            </li>
                                            <li class="list-inline-item">
                                                <span class="text-gray">
                                                    Managerment at Sound & Light Events
                                                </span>
                                            </li>
                                        </ul>
                                        <div class="card-text text-gray pr-4">
                                                    Amazing. I will never use another storage app! I will highly recommend Emtee Space to anyone who asks. Could not have had a better, easier, faster, no hassle storage experience. Thank you guys all so much!”
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="section-05" class="pt-11 pb-11">
        <div class="container">
            <div class="home-main-how-it-work bg-white pt-11">
                <div class="container">
                    <div class="row no-gutters pb-11">
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                    <svg viewBox="0 0 24 24" role="presentation" aria-hidden="true" focusable="false" style="display:block;fill:currentColor;height:33px;width:33px;" data-reactid="431"><path fill-rule="evenodd" d="M22.786 18.264l-3.44-3.44a1.65 1.65 0 0 0-2.34.004l-.519.518-1.225 1.225a.657.657 0 0 1-.937-.007L7.526 9.766a.658.658 0 0 1-.007-.937l1.743-1.743c.647-.647.65-1.695.004-2.34l-3.44-3.44a1.648 1.648 0 0 0-2.337 0L.893 3.9c-.59.59-.83 1.646-.54 2.425.009.032.042.133.092.276.083.236.183.506.3.806a33.12 33.12 0 0 0 1.235 2.762c1.399 2.788 3.15 5.372 5.28 7.504 2.346 2.344 4.818 4.008 7.265 5.106.86.386 1.656.673 2.37.877.436.124.75.193.926.22.752.185 1.793-.105 2.37-.681l1.366-1.367.707-.707.522-.521a1.652 1.652 0 0 0 0-2.337zM4.196 2.013a.648.648 0 0 1 .922 0l3.44 3.44a.654.654 0 0 1-.003.926l-.518.518-.069-.07-4.225-4.224-.069-.069.522-.521zm15.287 20.476c-.33.33-1.012.52-1.464.41a8.24 8.24 0 0 1-.849-.204 16.65 16.65 0 0 1-2.236-.827c-2.339-1.05-4.71-2.645-6.966-4.901-2.048-2.048-3.74-4.546-5.094-7.246a32.023 32.023 0 0 1-1.197-2.677 23.004 23.004 0 0 1-.379-1.042c-.16-.433-.014-1.078.302-1.394l1.367-1.367.069.07L7.26 7.534l.069.069-.518.518a1.658 1.658 0 0 0 .007 2.35l6.799 6.799a1.657 1.657 0 0 0 2.35.007l.519-.518 4.363 4.362-1.367 1.367zm2.596-2.596l-.522.522-4.363-4.362.518-.518a.65.65 0 0 1 .926-.004l3.44 3.44a.652.652 0 0 1 0 .922z" data-reactid="432"/></svg>
                                    <span class="number h1 font-weight-bold"></span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        24/7 customer support
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        If you need help while traveling or hosting, contact us at our toll free number: 000 87 235 1158
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                      <svg viewBox="0 0 24 24" role="presentation" aria-hidden="true" focusable="false" style="display:block;fill:currentColor;height:33px;width:33px;" data-reactid="441"><path d="M12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 15a7 7 0 1 1 0-14 7 7 0 0 1 0 14zm10.334-4.27c.02-.074.04-.147.07-.222.013-.032.027-.068.039-.094.058-.123.124-.236.206-.327.073-.062.469-.39.536-.45.555-.486.815-.935.815-1.628 0-.682-.253-1.13-.79-1.612-.067-.061-.478-.409-.596-.518a1.014 1.014 0 0 1-.103-.147c-.006-.012-.006-.024-.012-.035a1.955 1.955 0 0 1-.116-.257c-.01-.026-.015-.05-.023-.077a1.885 1.885 0 0 1-.105-.513 1.373 1.373 0 0 1 .01-.367c.01-.019.036-.094.068-.181-.039.105.14-.38.179-.496.067-.199.111-.368.136-.538a1.876 1.876 0 0 0-.251-1.26c-.385-.668-.827-.883-1.75-1.052-.395-.072-.377-.069-.515-.1-.056-.018-.12-.057-.183-.093l-.008-.007a1.671 1.671 0 0 1-.185-.128l-.025-.02a2.524 2.524 0 0 1-.193-.175 2.453 2.453 0 0 1-.203-.241c-.03-.042-.062-.086-.08-.118a1.304 1.304 0 0 1-.123-.253l-.075-.434c-.16-.939-.374-1.385-1.05-1.775a1.883 1.883 0 0 0-1.227-.257 3.008 3.008 0 0 0-.549.13c-.1.033-.468.163-.514.18a5.07 5.07 0 0 1-.247.082c-.086.016-.185.007-.282.002-.07-.003-.15-.01-.24-.026a2.208 2.208 0 0 1-.218-.057 1.957 1.957 0 0 1-.222-.07c-.032-.013-.068-.027-.094-.039a1.366 1.366 0 0 1-.327-.206c-.062-.073-.39-.469-.45-.536C13.153.26 12.703 0 12.01 0c-.682 0-1.13.253-1.612.79-.06.067-.409.478-.518.596a1.011 1.011 0 0 1-.147.103c-.012.006-.024.006-.035.012-.043.025-.136.07-.258.116-.025.01-.05.015-.075.023a2.2 2.2 0 0 1-.353.089 2.132 2.132 0 0 1-.161.016 1.371 1.371 0 0 1-.367-.01c-.019-.01-.094-.036-.181-.068.105.039-.38-.14-.496-.179a3.066 3.066 0 0 0-.538-.136 1.876 1.876 0 0 0-1.26.251c-.668.385-.883.827-1.052 1.75-.072.395-.069.377-.1.515-.018.056-.057.12-.093.183l-.007.008a1.677 1.677 0 0 1-.128.185l-.02.025c-.049.06-.11.127-.175.193a2.45 2.45 0 0 1-.24.202c-.043.031-.087.063-.12.082a1.3 1.3 0 0 1-.252.122l-.434.075c-.939.16-1.385.374-1.775 1.05a1.883 1.883 0 0 0-.257 1.227c.022.173.065.346.13.549.033.1.163.468.18.514.032.09.059.17.083.247.015.086.006.185 0 .282-.002.07-.01.15-.025.24a2.204 2.204 0 0 1-.057.218c-.02.074-.04.147-.07.222-.013.032-.027.068-.039.094a1.368 1.368 0 0 1-.206.327c-.073.062-.469.39-.536.45C.26 10.847 0 11.297 0 11.99c0 .682.253 1.13.79 1.612.067.06.478.409.596.518.032.036.067.087.103.147.006.011.006.024.012.035.025.043.07.136.116.257.01.026.015.05.023.077a1.901 1.901 0 0 1 .105.513c.009.126.013.252-.01.367-.01.019-.036.094-.068.181.039-.105-.14.38-.179.496a3.052 3.052 0 0 0-.136.538c-.064.436.01.843.251 1.26.385.667.827.883 1.75 1.052.395.072.377.069.515.1.056.018.12.056.183.093l.008.007c.048.027.113.072.185.128l.025.02c.06.048.126.11.193.175a2.486 2.486 0 0 1 .203.24c.03.043.062.087.08.119.05.083.095.167.123.253l.075.434c.16.939.374 1.385 1.05 1.775.405.234.803.311 1.227.257.173-.022.346-.065.549-.13.1-.033.468-.163.514-.18.09-.032.17-.059.247-.083.086-.015.185-.006.282 0 .07.002.15.01.24.025.072.014.145.034.218.057.074.02.147.04.222.07.032.013.068.027.094.039.123.058.236.124.327.206.062.073.39.469.45.536.485.555.935.815 1.628.815.682 0 1.13-.253 1.612-.79.06-.067.409-.478.518-.596.036-.032.087-.067.147-.103.011-.006.024-.006.035-.012a1.96 1.96 0 0 1 .257-.116c.026-.01.05-.015.076-.023a1.891 1.891 0 0 1 .514-.105c.126-.009.252-.013.367.01.019.01.094.036.181.068-.105-.039.38.14.496.179.199.067.368.111.538.136.436.064.843-.01 1.26-.251.668-.385.883-.827 1.052-1.75.072-.395.069-.377.1-.515.018-.056.057-.12.093-.183l.007-.008a1.674 1.674 0 0 1 .148-.21c.048-.06.11-.126.175-.193a2.51 2.51 0 0 1 .241-.203c.042-.03.086-.062.118-.08.083-.05.167-.095.253-.123l.434-.075c.939-.16 1.385-.374 1.775-1.05.234-.405.311-.803.257-1.227a3.006 3.006 0 0 0-.13-.549c-.033-.1-.163-.468-.18-.514a7.81 7.81 0 0 1-.083-.247c-.015-.086-.006-.185 0-.282.002-.07.01-.15.025-.24a2.21 2.21 0 0 1 .057-.219zm-.392-1.348c-.125.137-.217.27-.297.405-.002.005-.007.007-.009.01a2.412 2.412 0 0 0-.086.166c-.012.025-.028.048-.04.073-.01.026-.012.049-.023.074-.018.043-.035.08-.053.128a3.324 3.324 0 0 0-.129.445l-.006.034a2.414 2.414 0 0 0-.024.954l.012.056c.04.133.07.225.108.331.253.72.234.664.258.85a.887.887 0 0 1-.131.6c-.204.352-.405.448-1.078.564-.381.065-.376.064-.525.096-.15.048-.274.116-.401.183-.024.01-.05.01-.072.024a.528.528 0 0 0-.042.029c-.04.023-.076.05-.113.076-.09.06-.184.135-.289.225-.028.025-.061.044-.089.07-.02.019-.03.036-.05.055-.027.026-.053.044-.08.072-.042.043-.071.093-.11.138a3.07 3.07 0 0 0-.15.188 2.472 2.472 0 0 0-.137.202l-.011.016c-.005.008-.004.017-.008.025a2.17 2.17 0 0 0-.188.4c-.046.2-.043.182-.119.596-.12.662-.219.862-.568 1.064-.22.126-.4.16-.615.128-.184-.027-.12-.006-.838-.266a8.838 8.838 0 0 0-.265-.092 2.544 2.544 0 0 0-.487-.052c-.009 0-.017-.005-.026-.005-.024 0-.059.007-.084.008a2.626 2.626 0 0 0-.382.037c-.044.007-.084.013-.13.023a3.246 3.246 0 0 0-.427.123c-.023.008-.039.017-.06.025-.038.014-.071.017-.109.033-.02.009-.034.025-.054.034a2.44 2.44 0 0 0-.217.109c-.044.026-.077.062-.11.098-.09.06-.193.1-.272.172-.157.168-.51.584-.557.636-.31.344-.51.458-.868.458-.362 0-.563-.116-.877-.474-.046-.052-.378-.453-.496-.584a2.422 2.422 0 0 0-.406-.297c-.004-.002-.006-.007-.01-.009a2.325 2.325 0 0 0-.163-.085c-.026-.012-.05-.029-.075-.04-.026-.012-.049-.013-.074-.024-.043-.018-.08-.035-.128-.053a3.331 3.331 0 0 0-.445-.129l-.035-.006a2.416 2.416 0 0 0-.953-.024l-.056.012a6.68 6.68 0 0 0-.331.108c-.72.253-.664.234-.85.258a.887.887 0 0 1-.6-.131c-.352-.204-.448-.405-.564-1.078-.065-.381-.064-.376-.096-.525-.048-.15-.116-.274-.183-.401-.01-.024-.011-.05-.024-.072a.528.528 0 0 0-.029-.042c-.023-.04-.05-.076-.076-.113a3.08 3.08 0 0 0-.225-.289c-.025-.028-.044-.061-.07-.089-.019-.02-.036-.03-.055-.05-.026-.027-.044-.053-.072-.08-.043-.042-.093-.071-.137-.11a3.068 3.068 0 0 0-.19-.15c-.066-.048-.131-.096-.2-.137l-.017-.011c-.008-.005-.017-.004-.025-.008a2.17 2.17 0 0 0-.4-.188c-.2-.046-.182-.043-.596-.119-.662-.12-.862-.219-1.064-.568a.88.88 0 0 1-.128-.615c.027-.184.006-.12.266-.838.035-.096.065-.18.092-.265.036-.167.049-.328.052-.487 0-.01.005-.017.005-.026 0-.024-.007-.058-.008-.084a2.63 2.63 0 0 0-.037-.382c-.007-.044-.013-.084-.023-.13a3.249 3.249 0 0 0-.123-.427c-.008-.023-.017-.039-.025-.06-.014-.038-.017-.071-.033-.109-.009-.02-.025-.034-.034-.054a2.46 2.46 0 0 0-.109-.217c-.026-.045-.062-.077-.098-.11-.06-.09-.1-.193-.172-.272-.168-.157-.584-.51-.636-.557-.344-.31-.458-.51-.458-.868 0-.362.116-.563.474-.877.052-.046.453-.379.584-.496.125-.137.217-.27.297-.406.002-.004.007-.006.009-.01.025-.042.055-.1.085-.165.012-.025.029-.048.04-.073.011-.026.013-.048.024-.074.018-.042.035-.08.053-.128a3.33 3.33 0 0 0 .129-.445l.006-.034c.066-.317.084-.637.024-.954l-.012-.056a6.659 6.659 0 0 0-.108-.331c-.253-.72-.234-.664-.258-.85a.887.887 0 0 1 .131-.6c.204-.352.405-.448 1.078-.564.381-.065.376-.064.525-.096.15-.048.274-.116.401-.183.024-.01.049-.01.072-.024.012-.007.03-.021.042-.029.04-.023.076-.05.113-.076.09-.06.184-.135.288-.225.029-.025.062-.043.09-.07.02-.019.031-.036.05-.055.027-.026.053-.044.08-.072.042-.043.071-.093.11-.138.056-.067.106-.127.15-.188.048-.066.096-.132.137-.202l.011-.016c.005-.008.004-.017.008-.025.073-.13.143-.26.188-.4.046-.2.043-.182.119-.596.12-.662.219-.862.568-1.064a.88.88 0 0 1 .615-.128c.184.027.12.006.838.266.096.035.18.065.265.092.167.036.328.049.487.052.009 0 .017.005.026.005.024 0 .058-.007.084-.008.13-.003.257-.016.382-.037.044-.007.084-.013.13-.023.145-.032.287-.071.427-.123l.061-.025c.037-.014.07-.017.108-.033.02-.009.034-.025.054-.034.088-.04.164-.078.217-.11a.465.465 0 0 0 .11-.097c.09-.06.193-.1.272-.172.157-.168.51-.584.557-.636.31-.345.51-.458.868-.458.362 0 .563.116.877.474.046.052.378.453.496.584.137.125.27.217.405.297.004.002.007.007.01.009.043.025.1.054.165.085.025.012.049.029.074.04.026.012.049.013.074.024.043.018.08.035.128.053.147.055.296.095.445.129l.035.006c.316.066.636.084.953.024l.056-.012c.133-.04.225-.07.331-.108.72-.253.664-.234.85-.258a.887.887 0 0 1 .6.131c.352.204.448.405.564 1.078.065.381.064.376.096.525.048.15.116.274.183.4.01.025.01.05.024.073.007.012.021.03.029.042.023.04.05.075.075.113.061.09.136.184.226.289.025.028.044.061.07.089.019.02.036.03.055.05.026.027.044.053.072.08.043.042.093.071.138.11a3 3 0 0 0 .39.287l.016.011c.008.005.017.004.025.008.13.073.26.143.4.188.2.046.182.043.596.119.662.12.862.219 1.064.567.126.22.16.4.128.616-.027.184-.006.12-.266.837a8.839 8.839 0 0 0-.092.266 2.542 2.542 0 0 0-.052.487c0 .01-.005.017-.005.026 0 .024.007.058.008.084.003.13.015.256.037.382.007.044.013.084.023.13.032.145.071.287.123.427.008.023.017.039.025.06.014.037.017.071.033.109.009.02.025.034.035.054.04.088.077.164.108.217a.492.492 0 0 0 .098.11c.06.09.1.193.172.272.168.157.584.51.636.557.344.31.458.51.458.868 0 .362-.116.563-.474.877-.052.046-.453.378-.584.496zm-8.895-2.685C13.617 10.345 14 9.72 14 9a2 2 0 1 0-4 0c0 .72.383 1.345.953 1.697l-1.722 1.378a.663.663 0 0 0-.231.494v3.862c0 .302.229.569.556.569h4.888a.566.566 0 0 0 .556-.57v-3.86a.635.635 0 0 0-.231-.495l-1.722-1.378zM12 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm2 8h-4v-3.26l2-1.6 2 1.6V16z" fill-rule="evenodd" data-reactid="442"/></svg>
      
                                    <span class="number h1 font-weight-bold"></span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        R100,000 storage host guarantee
                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        Storage hosts are protected against storage damages for up to R100,000. <br/>
                                         <a href="{{ url('host_guarantee') }}" target="_blank">Learn More</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 mb-lg-0 px-0 px-lg-4">
                            <div class="media icon-box-style-02" data-animate="fadeInDown">
                                <div class="d-flex flex-column align-items-center mr-6">
                                    <svg viewBox="0 0 24 24" role="presentation" aria-hidden="true" focusable="false" style="display:block;fill:currentColor;height:33px;width:33px;" data-reactid="455"><path d="M22.5 2h-21C.724 2 0 2.724 0 3.5v14.986C0 19.273.72 20 1.5 20h10.672l1.362 1.363a2 2 0 0 0 2.83.001c.379-.379.57-.87.582-1.364H22.5c.78 0 1.5-.727 1.5-1.514V3.5c0-.776-.724-1.5-1.5-1.5zm-6.843 18.657a1 1 0 0 1-1.416-.001l-2.826-2.826a1 1 0 0 1 1.414-1.414l2.827 2.825a.997.997 0 0 1 0 1.416zM23 18.486c0 .237-.275.514-.5.514h-5.8a1.99 1.99 0 0 0-.337-.466l-2.826-2.826a1.996 1.996 0 0 0-2.426-.304l-.736-.736A4.97 4.97 0 0 0 12 11a5 5 0 1 0-5 5c.942 0 1.812-.276 2.564-.729l.082.083.757.756a1.993 1.993 0 0 0 .305 2.427l.464.463H1.5c-.225 0-.5-.277-.5-.514V3.5c0-.224.276-.5.5-.5h21c.224 0 .5.276.5.5v14.986zM7 15a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm1.854-4.854a.5.5 0 0 1 0 .708l-1.951 1.95a.568.568 0 0 1-.808-.005l-.93-.985a.5.5 0 1 1 .728-.686l.617.654 1.636-1.636a.5.5 0 0 1 .708 0zM21 7.5a.5.5 0 0 1-.5.5h-5.974a.5.5 0 0 1 0-1H20.5a.5.5 0 0 1 .5.5zm0 3a.5.5 0 0 1-.5.5h-5.974a.5.5 0 0 1 0-1H20.5a.5.5 0 0 1 .5.5zm0 3a.5.5 0 0 1-.5.5h-5.974a.5.5 0 0 1 0-1H20.5a.5.5 0 0 1 .5.5z" fill-rule="evenodd" data-reactid="456"/></svg>
                                    <span class="number h1 font-weight-bold"></span>
                                </div>
                                <div class="media-body lh-14">
                                    <h5 class="mb-3 lh-1">
                                        Verified ID

                                    </h5>
                                    <p class="font-size-md text-gray mb-0 text-muted">
                                        We aim to build a trusted community by giving you more info when you're deciding who to be a storage host.

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom "></div>
                    <div class="border-bottom d-none" style="text-align:center; margin:20px">
                    
                        <img src="{{ asset('/images/footers/featured.png') }}" style="margin:20px" height="200px" width="80%" alt="Testimonial" class="rounded-circle">

                    </div>
                </div>
            </div>


        </div>
    </section>

</div>

@stop
