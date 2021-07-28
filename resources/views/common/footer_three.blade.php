<footer class="main-footer main-footer-style-01 bg-pattern-01 pt-12 pb-8">
    <div class="footer-second">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-4 mb-6 mb-lg-0">
                    <div class="mb-8"><img src="{{ url(SECONDARY_LOGO) }}" alt="Emtee Space logo" width="150" height="150"></div>
                   
                    <div class="mb-8"><img src="{{ asset('/images/logos/apple.png') }}"  width="150" height="150" alt="apple"> 
                    <img src="{{ asset('/images/logos/google.png') }}"  width="150" height="150" alt="google app store "></div>


                </div>
                <div class="col-md-6 col-lg mb-6 mb-lg-0">
                    <div class="font-size-md font-weight-semibold text-dark mb-4">
                        Company
                    </div>
                    <ul class="list-group list-group-flush list-group-borderless">
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a href="{{ url('terms_of_service') }}" class="link-hover-secondary-primary">Terms of Service</a>
                        </li>
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a href="{{ url('privacy_policy') }}" class="link-hover-secondary-primary">Privacy Policy</a>
                        </li>
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a  href="{{ url('contact') }}" class="link-hover-secondary-primary">Contact Us</a>
                        </li>
                        
                    </ul>
                </div>
                <div class="col-md-6 col-lg mb-6 mb-lg-0">
                    <div class="font-size-md font-weight-semibold text-dark mb-4">
                        Discover
                    </div>
                    <ul class="list-group list-group-flush list-group-borderless">
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a href="{{ url('invite') }}" class="link-hover-secondary-primary">Rewards Credit</a>
                        </li>
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a href="{{ url('guest_refund') }}" class="link-hover-secondary-primary">Guest Refund</a>
                        </li>
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a href="{{ url('about_us') }}" class="link-hover-secondary-primary">About Us</a>
                        </li>
                        
                    </ul>
                </div>

                <div class="col-md-6 col-lg mb-6 mb-lg-0">
                    <div class="font-size-md font-weight-semibold text-dark mb-4">
                        Storage Hosting
                    </div>
                    <ul class="list-group list-group-flush list-group-borderless">
                        <li class="list-group-item px-0 lh-1625 bg-transparent py-1">
                            <a href="{{url('why_storage_host')}}" class="link-hover-secondary-primary">Why Storage Host</a>
                        </li>
                        
                    </ul>
                </div>

                
            </div>
        </div>
    </div>
    <div class="footer-last mt-8 mt-md-11">
        <div class="container">
            <div class="footer-last-container position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <div class="social-icon text-dark">
                            <ul class="list-inline">
                                <li class="list-inline-item mr-5">
                                    <a target="_blank" title="Twitter" href="#">
                                        <i class="fab fa-twitter">
                                        </i>
                                        <span>Twitter</span>
                                    </a>
                                </li>
                                <li class="list-inline-item mr-5">
                                    <a target="_blank" title="Facebook" href="#">
                                        <i class="fab fa-facebook-f">
                                        </i>
                                        <span>Facebook</span>
                                    </a>
                                </li>
                                <li class="list-inline-item mr-5">
                                    <a target="_blank" title="Google plus" href="#">
                                        <svg class="icon icon-google-plus-symbol">
                                            <use xlink:href="#icon-google-plus-symbol"></use>
                                        </svg>
                                        <span>Google plus</span>
                                    </a>
                                </li>
                                <li class="list-inline-item mr-5">
                                    <a target="_blank" title="Instagram" href="#">
                                        <svg class="icon icon-instagram">
                                            <use xlink:href="#icon-instagram"></use>
                                        </svg>
                                        <span>Instagram</span>
                                    </a>
                                </li>
                                <li class="list-inline-item mr-5">
                                    <a target="_blank" title="Rss" href="#">
                                        <i class="fas fa-rss"></i>
                                        <span>Rss</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-5 mb-3 mb-lg-0">
                        <div>
                            &copy; 2020 <a href="index-2.html" class="link-hover-dark-primary font-weight-semibold">The
                                {{ $site_name }}.</a> All
                            Rights Resevered.
                            </div>
                    </div>
                    <div class="back-top text-left text-lg-right gtf-back-to-top">
                        {{-- <a href="#" class="link-hover-secondary-primary"><i class="fal fa-arrow-up"></i><span>Back To
                                Top</span></a> --}}

                        <div class="mb-8">
                            <img src="{{ asset('/images/footers/comodo.png') }}"  width="100" height="100" alt="apple"> 
                            <img src="{{ asset('/images/footers/master.png') }}"  width="100" height="100" alt="google app store ">
                            <img src="{{ asset('/images/footers/visa.png') }}"  width="100" height="100" alt="google app store ">

                        </div>
                        
                

                               
                </div>
            </div>
        </div>
    </div>
</footer>
</div>