<div class="col-sm-12">
    <br/>
    <h3 class="text-muted text-success text-center">Deposit Has Been Generated Successfully!</h3>

    <div class="row">
        @if(userCanView('deposit.view'))
            <div class="col-md-4">
                <a href="{{ route('deposit.view',$deposit_id) }}" class="btn btn-success btn-lg btn-block">View Deposit <i class="fa fa-eye"></i> </a>
            </div>
        @endif
        @if(userCanView('deposit.print_afour'))
            <div class="col-md-4">
                <a href="{{ route('deposit.print_afour',$deposit_id) }}" onclick="open_print_window(this); return false" class="btn btn-primary btn-lg btn-block">Print Deposit A4 <i class="fa fa-print"></i></a>
            </div>
        @endif
        @if(userCanView('deposit.print_afive'))
            <div class="col-md-4">
                <a href="{{ route('deposit.print_afive',$deposit_id) }}" onclick="open_print_window(this); return false" class="btn btn-primary btn-lg btn-block">Print Deposit A5 <i class="fa fa-print"></i></a>
            </div>
        @endif
    </div>

    <div class="row mtop-20">
        <div class="col-md-12">
            <a href="#" onclick="window.location.reload();" class="btn btn-info btn-lg btn-block">New Deposit <i class="fa fa-plus"></i></a>
        </div>
    </div>
    <br/>
</div>

