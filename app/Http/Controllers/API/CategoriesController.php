<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::all();
        return response()->json([
        "success" => true,
        "message" => "Categories List",
        "data" => $categories
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
        'category_name' => 'required',
        'category_type' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $categories = Categories::create($input);
        return response()->json([
        "success" => true,
        "message" => "Categories created successfully.",
        "data" => $categories
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function show(Categories $categories)
    {
        $categories = Categories::find($id);
        if (is_null($categories)) {
        return $this->sendError('Categories not found.');
        }
        return response()->json([
        "success" => true,
        "message" => "Categories retrieved successfully.",
        "data" => $categories
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function updateById(Request $request, $id)
    {
        $user = Auth::user();
        $user_id = Auth::id();
        $input = $request->all();
        $validator = Validator::make($input, [
        'category_name' => 'required',
        'category_type' => 'required',
        'branch_id' => 'required',
        ]);
        if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());       
        }
        $categories = Categories::find($id);
        $categories->category_name = $input['category_name'];
        $categories->category_type = $input['category_type'];
        $categories->branch_id = $input['branch_id'];
        $categories->updated_by = $user_id;
        $categories->save();
        return response()->json([
        "success" => true,
        "message" => "Categories updated successfully.",
        "data" => $categories
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $categories = Categories::find($request->id);
        $categories->delete();
        return response()->json([
        "success" => true,
        "message" => "Categories deleted successfully.",
        "data" => $categories
        ]);
    }

    public function filterByInputs(Request $request)
    {
        $category_name = $request->category_name;
        $category_type = $request->category_type;
        
        $categories = Categories::query();
            if($category_name)
            {
                $categories = $categories->where('category_name',$category_name);
            }
            if($category_type)
            {
                $categories = $categories->where('category_type',$category_type);
            }
            
        $categories = $categories->get();

        return response()->json([
            "success" => true,
            "message" => "Categories filterd successfully.",
            "data" => $categories
            ]);
    }
}
