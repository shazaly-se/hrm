<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use App\Models\Time_Category;
use Illuminate\Http\Request;

class CompanyInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $company = CompanyInfo::leftJoin("time_categories","time_categories.id","=","companyinfos.late_category")
        ->first(array("companyinfos.*","time_categories.id as late_category","time_categories.name as late_category_name"));
        $time_categories = Time_Category::all();
        return response()->json(['company' => $company,"time_categories"=>$time_categories]);
    }

    public function updatecompany(Request $request)
    {
       
        $company = CompanyInfo::first();
        $company->name= $request->name;
        $company->fulladdress= $request->fulladdress;
        $company->email= $request->email;
        $company->phone= $request->phone;
        $company->website_url= $request->website_url;
        $company->ip_address= $request->ip_address;
        $company->start_time= $request->start_time;
        $company->last_time_allow= $request->last_time_allow;
        $company->amount= $request->amount;
        $company->total_break_time= $request->total_break_time;
        $company->check_out_time= $request->check_out_time;

        if($request->get('imageUpload'))
        {
           $image = $request->get('imageUpload');
           $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           \Image::make($request->get('imageUpload'))
           ->resize(292, 220, function ($constraint) {
              $constraint->aspectRatio();
          })->save(public_path('uploads/logo/').$name);
           $company->logo=$name;
         }

        $company->update();
        return response()->json(['company' => $company]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = CompanyInfo::first();
        return response()->json(['company' => $company]);

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
