<?php

namespace App\Http\Controllers\CashBookReport;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cashbook;
use Illuminate\Http\Request;

class CashBookReportController extends Controller
{
    public function list_all(Request $request)
    {
        if($request->get('from') && $request->get('to')){
            $data['from'] = $request->get('from');
            $data['to'] = $request->get('to');
            $data['bank_account_id'] = $request->bank_account_id;
        }else{
            $data['from'] = date('Y-m-01');
            $data['to'] = date('Y-m-t');
            $data['bank_account_id'] = 1;
        }
        $credit_bal = Cashbook::where('transaction_date','<', $data['from'])->where('bank_account_id',$data['bank_account_id'])->where('type','Credit')->sum('amount');
        $debit_bal = Cashbook::where('transaction_date','<', $data['from'])->where('bank_account_id',$data['bank_account_id'])->where('type','Debit')->sum('amount');
        $data['opening'] = $credit_bal - $debit_bal;
        $data['title'] = 'List Cashbook';
        $data['banks'] = BankAccount::all();
        $data['transactions'] = Cashbook::whereBetween('transaction_date',[ $data['from'],$data['to']])->where('bank_account_id', $data['bank_account_id'])->get();
        return view('cashbook.list-cashbook',$data);
    }
}
