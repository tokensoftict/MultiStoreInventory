<nav class="navbar navbar-inverse yamm navbar-default hori-nav " role="navigation">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#main-navigation">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="main-navigation">
                <ul class="nav navbar-nav" style="font-size: 12.5px">
                  {!! getUserMenu() !!}
                </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
