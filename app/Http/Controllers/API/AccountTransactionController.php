<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountTransaction;
use App\Models\Account;
class AccountTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = AccountTransaction::all();
        foreach($transactions as $transaction){
            $transaction->credit_account= Account::where("id",$transaction->credit_account_id)->get();


            $transaction->debit_account= Account::where("id",$transaction->debit_account_id)->get();
        }
        return response()->json(["success"=>true,"transactions"=>$transactions]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $accountTransaction = new AccountTransaction;
        $accountTransaction->date= $request->date;
        $accountTransaction->debit_account_id= $request->debit_account_id;
        $accountTransaction->credit_account_id= $request->credit_account_id;
        $accountTransaction->amount= $request->amount;
        $accountTransaction->transaction_type= 1;
        $accountTransaction->narration= $request->narration;

        $accountTransaction->save();

    }

    public function expense(Request $request)
    {
        $accountTransaction = new AccountTransaction;
        $accountTransaction->date= $request->date;
        $accountTransaction->account_id= $request->account_id;
        $accountTransaction->debit= $request->debit;
        $accountTransaction->credit= 0;
        $accountTransaction->transaction_type= 2;
        $accountTransaction->narration= $request->narration;

        $accountTransaction->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = AccountTransaction::where("id",$id)->first();
        $credit_account = Account::where("id",$transaction->credit_account_id)->first();
        $debit_account = Account::where("id",$transaction->debit_account_id)->first();
        $transaction["credit_account_name"] = $credit_account->account_Name;
        $transaction["debit_account_name"] = $debit_account->account_Name;
        return response()->json($transaction);  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
