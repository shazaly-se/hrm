<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SettingsBusiness;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class SettingsBusinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settingsBusiness = SettingsBusiness::all();
        return response()->json([
        "success" => true,
        "message" => "Business Settings List",
        "data" => $settingsBusiness
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
        'logo' => 'file|size:512|mimes:jpeg,png,jpg,gif',
        'landing_page_logo' => 'file|mimes:jpeg,png,jpg,gif|max:2048',
        'favicon' => 'file|mimes:jpeg,png,jpg,gif|max:2048',
        'title_text' => 'required',
        'footer_text' => 'required',
        'default_language' => 'required',
        'rtl' => 'required',
        'gdpr_cookie' => 'required',
        'gdpr_cookie_discription' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        //logo upload
        if ($logo = $request->file('logo')) {
            $destinationPath = 'images/businessSettings/logo';
            $logoImage = date('YmdHis') . "." . $logo->getClientOriginalExtension();
            $logo->move($destinationPath, $logoImage);
            $input['logo'] = "$logoImage";
        }

          //landing page logo upload
          if ($landing_page_logo = $request->file('landing_page_logo')) {
            $destinationPath = 'images/businessSettings/landing_page_logo';
            $landingPageLogoImage = date('YmdHis') . "." . $landing_page_logo->getClientOriginalExtension();
            $landing_page_logo->move($destinationPath, $landingPageLogoImage);
            $input['landing_page_logo'] = "$landingPageLogoImage";
        }

          //logo favicon
          if ($favicon = $request->file('favicon')) {
            $destinationPath = 'images/businessSettings/favicon';
            $faviconImage = date('YmdHis') . "." . $favicon->getClientOriginalExtension();
            $favicon->move($destinationPath, $faviconImage);
            $input['favicon'] = "$faviconImage";
        }

        $settingsBusiness = SettingsBusiness::create($input);
        return response()->json([
        "success" => true,
        "message" => "Business Settings created successfully.",
        "data" => $settingsBusiness
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SettingsBusiness  $settingsBusiness
     * @return \Illuminate\Http\Response
     */
    public function show(SettingsBusiness $settingsBusiness)
    {
        $settingsBusiness = SettingsBusiness::find($id);
        if (is_null($settingsBusiness)) {
        return $this->sendError('Business Settings not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Business Settings retrieved successfully.",
        "data" => $settingsBusiness
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SettingsBusiness  $settingsBusiness
     * @return \Illuminate\Http\Response
     */
    public function edit(SettingsBusiness $settingsBusiness)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SettingsBusiness  $settingsBusiness
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
            'logo' => 'file|size:512|mimes:jpeg,png,jpg,gif',
            'landing_page_logo' => 'file|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'file|mimes:jpeg,png,jpg,gif|max:2048',
            'title_text' => 'required',
            'footer_text' => 'required',
            'default_language' => 'required',
            'rtl' => 'required',
            'gdpr_cookie' => 'required',
            'gdpr_cookie_discription' => 'required',
            'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
         //logo upload
         if ($logo = $request->file('logo')) {
            $destinationPath = 'images/businessSettings/logo';
            $logoImage = date('YmdHis') . "." . $logo->getClientOriginalExtension();
            $logo->move($destinationPath, $logoImage);
            $input['logo'] = "$logoImage";
        }

          //landing page logo upload
          if ($landing_page_logo = $request->file('landing_page_logo')) {
            $destinationPath = 'images/businessSettings/landing_page_logo';
            $landingPageLogoImage = date('YmdHis') . "." . $landing_page_logo->getClientOriginalExtension();
            $landing_page_logo->move($destinationPath, $landingPageLogoImage);
            $input['landing_page_logo'] = "$landingPageLogoImage";
        }

          //logo favicon
          if ($favicon = $request->file('favicon')) {
            $destinationPath = 'images/businessSettings/favicon';
            $faviconImage = date('YmdHis') . "." . $favicon->getClientOriginalExtension();
            $favicon->move($destinationPath, $faviconImage);
            $input['favicon'] = "$faviconImage";
        }
        $settingsBusiness = SettingsBusiness::find($id);
        $settingsBusiness->logo = $input['logo'];
        $settingsBusiness->landing_page_logo = $input['landing_page_logo'];
        $settingsBusiness->favicon = $input['favicon'];
        $settingsBusiness->title_text = $input['title_text'];
        $settingsBusiness->footer_text = $input['footer_text'];
        $settingsBusiness->default_language = $input['default_language'];
        $settingsBusiness->rtl = $input['rtl'];
        $settingsBusiness->gdpr_cookie = $input['gdpr_cookie'];
        $settingsBusiness->gdpr_cookie_discription = $input['gdpr_cookie_discription'];
        $settingsBusiness->branch_id = $input['branch_id'];
        $settingsBusiness->updated_by = $user_id;
        $settingsBusiness->save();
        return response()->json([
        "success" => true,
        "message" => "Business Settings updated successfully.",
        "data" => $settingsBusiness
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SettingsBusiness  $settingsBusiness
     * @return \Illuminate\Http\Response
     */
    public function destroy(SettingsBusiness $settingsBusiness)
    {
        //
    }
}
