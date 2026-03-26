<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.index') : route('home') }}">
                        <i class="menu-icon fa fa-laptop"></i> Dashboard
                    </a>
                </li>


                @if (auth()->user()->role === 'admin')
                    <li>
                        <a href="{{ route('admin.bookings.index') }}">
                            <i class="menu-icon fa fa-users"></i> Bookings
                        </a>
                    </li>
                @endif


                <li class="menu-title">Trainers</li>

                <li>
                    <a href="{{ route('gettrainer') }}">
                        <i class="menu-icon fa fa-users"></i> Trainers
                    </a>
                </li>



                <li>
                    <a href="{{ route('search') }}"> <i class="menu-icon fa fa-search"></i>Search </a>
                </li>

                <li>
                    <a href="{{ route('reviews.trainer') }}" class="menu-link">
                        <i class="menu-icon fa fa-comments"></i>
                        <span>Reviews & Feedback</span>
                    </a>
                </li>

            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>



</aside>
