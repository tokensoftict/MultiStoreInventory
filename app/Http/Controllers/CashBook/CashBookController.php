<?php

namespace App\Http\Controllers\CashBook;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cashbook;
use Illuminate\Http\Request;

class CashBookController extends Controller
{

    public function index(Request $request)
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
        $data['transactions'] = Cashbook::whereBetween('transaction_date',[ $data['from'],$data['to']])->where('bank_account_id',$data['bank_account_id'])->get();
        return view('cashbook.list-cashbook',$data);
    }


    public function create()
    {
        $data['title'] = 'New Transaction';
        $data['transaction']= new Cashbook();
        $data['banks'] = BankAccount::where('status',1)->get();
        return setPageContent('cashbook.new-transaction',$data);
    }

    public function store(Request $request)
    {
        $request->validate(Cashbook::$validation);
        $data = $request->only(Cashbook::$fields);

        $data['last_updated'] = auth()->id();
        $data['user_id'] =  auth()->id();

        Cashbook::create($data);

        return redirect()->route('cashbook.index')->with('success','Transaction has been added successfully!.');
    }

    public function edit($id){
        $data['title'] = 'Update Transaction';
        $data['transaction']= Cashbook::findorfail($id);
        $data['banks'] = BankAccount::where('status',1)->get();
        return setPageContent('cashbook.new-transaction',$data);
    }


    public function update(Request $request, $id){

        $request->validate(Cashbook::$validation);

        $cashbook =  Cashbook::findorfail($id);

        $data = $request->only(Cashbook::$fields);

        $data['last_updated'] = auth()->id();

        $cashbook->update($data);

        return redirect()->route('cashbook.index')->with('success','Transaction has been updated successfully!.');
    }


    public function destroy($id){
        $cash = Cashbook::findorfail($id);
        $cash->delete();
        return redirect()->route('cashbook.index')->with('success','Transaction has been deleted successfully!.');
    }

}
