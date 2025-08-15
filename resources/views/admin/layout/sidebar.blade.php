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
                    <h3>Dashboard</h3>
                    <ul class="nav side-menu">
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu_section">
                    <h3>Content Management</h3>
                    <ul class="nav side-menu">
                        <li>
                            <a>
                                <i class="fa fa-tags"></i> Show Categories
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('showcategory.list') }}">All Categories</a></li>
                                <li><a href="{{ route('showcategory.create') }}">Add Category</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-building"></i> Venue Management
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('venues.index') }}">All Venues</a></li>
                                <li><a href="{{ route('venue.create') }}">Add Venue</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-calendar"></i> Shows Management
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('show.index') }}">All Shows</a></li>
                                <li><a href="{{ route('show.create') }}">Add Show</a></li>
                            </ul>
                        </li>

<li>
    <a>
        <i class="fa fa-ticket"></i> Ticket Management
        <span class="fa fa-chevron-down"></span>
    </a>
    <ul class="nav child_menu" style="display: none;">
        <li><a href="{{ route('admin.ticket-types.all') }}">All Ticket Types</a></li>
        <li><a href="{{ route('admin.ticket-types.create') }}">Create Ticket Type</a></li>
        <li><a href="{{ route('show.index') }}">Manage by Show</a></li>
    </ul>
</li>
                    </ul>
                </div>

                <div class="menu_section">
                    <h3>Booking System</h3>
                    <ul class="nav side-menu">

                        <li>
                            <a>
                                <i class="fa fa-ticket"></i> Bookings
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('admin.bookings.index') }}">All Bookings</a></li>
                                <li><a href="{{ route('admin.scan') }}">Scan Tickets</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-users"></i> Customer Management
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('customer.index') }}">All Customers</a></li>
                                <li><a href="{{ route('customer.create') }}">Add Customer</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-credit-card"></i> Payments
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('admin.bookings.index') }}?filter=payments">Payment History</a></li>
                                <li><a href="{{ route('admin.bookings.index') }}?filter=refunds">Refunds</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-cogs"></i> Seat Management
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('admin.bookings.index') }}">View Bookings</a></li>
                                <li><a href="{{ route('admin.scan') }}">Scan Tickets</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="menu_section">
                    <h3>Reports & Analytics</h3>
                    <ul class="nav side-menu">
                        <li>
                            <a>
                                <i class="fa fa-bar-chart"></i> Reports
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('admin.reports.sales') }}">Sales Report</a></li>
                                <li><a href="{{ route('admin.reports.attendance') }}">Attendance Report</a></li>
                                <li><a href="{{ route('admin.reports.revenue') }}">Revenue Report</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-download"></i> Export Data
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('admin.bookings.export.csv') }}">Export Bookings (CSV)</a></li>
                                <li><a href="{{ route('admin.bookings.export.excel') }}">Export Bookings (Excel)</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="menu_section">
                    <h3>Media Management</h3>
                    <ul class="nav side-menu">
                        <li>
                            <a>
                                <i class="fa fa-camera"></i> Photo Galleries
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('photogallery.list') }}">All Galleries</a></li>
                                <li><a href="{{ route('photogallery.create') }}">Create Gallery</a></li>
                                <li><a href="{{ route('photosingallery.list') }}">Manage Photos</a></li>
                                <li><a href="{{ route('photosingallery.create') }}">Upload Photos</a></li>
                            </ul>
                        </li>

                        <li>
                            <a>
                                <i class="fa fa-video-camera"></i> Video Galleries
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('videogallery.list') }}">All Galleries</a></li>
                                <li><a href="{{ route('videogallery.create') }}">Create Gallery</a></li>
                                <li><a href="{{ route('videosingallery.list') }}">Manage Videos</a></li>
                                <li><a href="{{ route('videosingallery.create') }}">Upload Videos</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="menu_section">
                    <h3>System Management</h3>
                    <ul class="nav side-menu">
                        <li>
                            <a>
                                <i class="fa fa-wrench"></i> System Tools
                                <span class="fa fa-chevron-down"></span>
                            </a>
                            <ul class="nav child_menu" style="display: none;">
                                <li><a href="{{ route('admin.bookings.index') }}">System Status</a></li>
                                <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
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
