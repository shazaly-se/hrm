<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountType;
use App\Models\Account;


class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accounttype()
    {
       // $accounttypes = AccountType::all();
        $accounttypes = AccountType::get(array("id","Category"));
        return response()->json(['success' =>true,"accounttypes"=>$accounttypes]);
    }

    public function index()
    {
        $accounts = Account::join("accounttypes","accounttypes.id","accounts.acc_type")
                           ->get(array("accounts.*","accounttypes.Category"));
                           return response()->json(["accounts"=>$accounts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $account = new Account;
        $account->code = $request->code;
        $account->acc_type = $request->acc_type;
        $account->account_Name = $request->account_Name;
        $account->save();
        return response()->json(['success' =>true,"msg"=>"successfully"]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = Account::join("accounttypes","accounttypes.id","accounts.acc_type")
        ->where("accounts.id",$id)
        ->first(array("accounts.*","accounttypes.Category"));
        return response()->json(["account"=>$account]);  
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
        $account =  Account::where("id",$id)->first();
        $account->code = $request->code;
        $account->acc_type = $request->acc_type;
        $account->account_Name = $request->account_Name;
        $account->update();
        return response()->json(['success' =>true,"msg"=>"successfully updated"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = Account::where("id",$id)->first();
        $account->delete();
        $accounts = Account::join("accounttypes","accounttypes.id","accounts.acc_type")
        ->get(array("accounts.*","accounttypes.Category"));
        return response()->json(["accounts"=>$accounts]);
    }
}
