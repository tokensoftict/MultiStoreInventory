<header id="header" class="ui-header">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="navbar-header" style="width: 85%;">
                    <a href="{{ asset('') }}" class="navbar-brand">
                        <span class="logo" style="font-size: 22px;font-weight: bolder">{{ getStoreSettings()->name }}</span>
                    </a>

                    <span class="hidden-xs font-bold" style="margin-top: 10px; display: inline-block">Current Store : <b class="text-primary">{{ getActiveStore()->name }}</b></span>
                    &nbsp; &nbsp; &nbsp;
                    <span class="hidden-xs font-bold" style="margin-top: 10px; display: inline-block">User Group : <b class="text-primary">{{ request()->user()->group->name }}</b></span>
                </div>

                <div class="navbar-collapse nav-responsive-disabled">
                    <ul class="nav navbar-nav navbar-right">

                        <li class="dropdown dropdown-usermenu">
                            <a href="#" class=" dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                <div class="user-avatar"><img src="{{ asset('imgs/a0.jpg') }}" alt="..."></div>
                                <span class="hidden-sm hidden-xs">{{ auth()->user()->name }}</span>
                                <!--<i class="fa fa-angle-down"></i>-->
                                <span class="caret hidden-sm hidden-xs"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                                <li><a href="{{ route('myprofile') }}"><i class="fa fa-user"></i> My Profile</a></li>
                                <li class="divider"></li>
                                @if(request()->user()->userstoremappers->count() > 1)
                                    <li><a href="{{ route('switch') }}"><i class="fa fa-refresh"></i>Switch Store</a></li>
                                @endif
                                <li><a href="{{ route('logout') }}"><i class="fa fa-sign-out"></i> Log Out</a></li>
                            </ul>
                        </li>

                    </ul>
                    <!--notification end-->

                </div>
            </div>
        </div>
    </div>
</header>
