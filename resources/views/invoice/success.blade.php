<div class="col-sm-12">
    <br/>
    <h3 class="text-muted text-success text-center">Invoice Has Been Generated Successfully!</h3>

    <div class="row">
        @if(userCanView('invoiceandsales.pos_print'))
            <div class="col-md-6">
                <a href="{{ route('invoiceandsales.pos_print',$invoice_id) }}" onclick="open_print_window(this); return false" class="btn btn-success btn-lg btn-block">Print Invoice Pos <i class="fa fa-print"></i> </a>
            </div>
        @endif
        @if(userCanView('invoiceandsales.print_afour'))
            <div class="col-md-6">
                <a href="{{ route('invoiceandsales.print_afour',$invoice_id) }}" onclick="open_print_window(this); return false" class="btn btn-primary btn-lg btn-block">Print Invoice A4 <i class="fa fa-print"></i></a>
            </div>
        @endif
    </div>

    <div class="row mtop-20">
        <div class="col-md-12">
            <a href="#" onclick="window.location.reload();" class="btn btn-info btn-lg btn-block">New Sales / Invoice <i class="fa fa-plus"></i></a>
        </div>
    </div>
    <br/>
</div>

