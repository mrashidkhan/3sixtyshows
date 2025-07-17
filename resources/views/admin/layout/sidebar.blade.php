<div class="main_container">
    <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
                <a href="{{ route('index') }}" class="site_title">
                    <i class="fa fa-paw"></i>
                    <span>3Sixty Shows</span>
                </a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix mb-1">
                <div class="profile_pic">
                    {{-- <img src="admin_theme/images/img.jpg" alt="Profile Image" class="img-circle profile_img"> --}}
                    <img src="{{ asset('assets/images/logo/3sixtyshowslogo.png') }}" alt="Profile Image" width="150" style="display: block; margin: 0 auto;">
                </div>
                <div class="profile_info mt-5">
                    <span>Welcome,</span>
                    <h2>Admin!</h2>
                </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                <div class="menu_section">
                    <h3>General</h3>
                    <ul class="nav side-menu">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-index"></i> Dashboard
                                <span class="fa fa-chevron-down"></span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <i class="fa fa-list"></i> Category Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('showcategory.list') }}">List</a></li>
                                <li><a href="{{ route('showcategory.create') }}">Create</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Venue Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('venues.index') }}">List</a></li>
                                <li><a href="{{ route('venue.create') }}">Create</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Shows Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('show.index') }}">List</a></li>
                                <li><a href="{{ route('show.create') }}">Create</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Photo Gallery Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('photogallery.list') }}">List</a></li>
                                <li><a href="{{ route('photogallery.create') }}">Create</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Photos in Gallery
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('photosingallery.list') }}">List</a></li>
                                <li><a href="{{ route('photosingallery.create') }}">Create</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Video Gallery Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('videogallery.list') }}">List</a></li>
                                <li><a href="{{ route('videogallery.create') }}">Create</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Videos in Gallery
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('videosingallery.list') }}">List</a></li>
                                <li><a href="{{ route('videosingallery.create') }}">Create</a></li>
                            </ul>
                        </li>

                        {{-- <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Customer Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('customer.index') }}">List</a></li>
                                <li><a href="{{ route('customer.create') }}">Create</a></li>
                            </ul>
                        </li> --}}


                        {{-- <li>
                            <a>
                                <i class="fa fa-product-hunt"></i> Orders Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('index') }}">Order List</a></li>
                                <li><a href="{{ route('index') }}">Direct Order List</a></li>
                            </ul>
                        </li> --}}

                        {{-- <li>
                            <a>
                                <i class="fa fa-ticket"></i> Coupon Manager
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('index') }}">List</a></li>
                                <li><a href="{{ route('index') }}">Create</a></li>
                            </ul>
                        </li> --}}

                        <li>
                            <a><i class="fa fa-product-hunt"></i> User Manager<span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('index') }}">List</a></li>

                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /sidebar menu -->

            <!-- menu footer buttons -->
            <div class="sidebar-footer hidden-small">
                <a data-toggle="tooltip" data-placement="top" title="Settings">
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                </a>
                <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                    <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                </a>
                <a data-toggle="tooltip" data-placement="top" title="Lock">
                    <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                </a>
                <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('admin.logout') }}">
                    <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                </a>
            </div>
            <!-- /menu footer buttons -->
        </div>
    </div>
</div>
