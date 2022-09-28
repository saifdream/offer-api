<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Product;
use Validator;
use Image;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
   	public function create(Request $request){
	 	$validator = Validator::make($request->all(),[
	        'name'			=> 'required',
	        'description'	=> 'required',
	        'isActive' 		=> 'required',
	        'image' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
	     ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $category = new Category;
        if($request->hasfile('image')) {
            $image_file = request()->file('image');
            $image = time()."_".$image_file->getClientOriginalName();
            $img = Image::make($image_file->getRealPath());            
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save( public_path().'/images/categories/' . $image);
            $category->image = $image;
        }

        $category->name = request('name');
        $category->description = request('description');
        $category->isActive = request('isActive');
        DB::beginTransaction();
        try {
              $category->save();
              DB::commit();
              return response()->json(['success' => true, 'message' => 'Category added successfully.']);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([ 'success' => false,'message' => 'Category insertion failed.']);
            }
    }

    public function edit(Request $request,$id){
 		$validator = Validator::make($request->all(),[     
	        'name'			=> 'required',
	        'description'	=> 'required',
	        'isActive' 		=> 'required',
	        //'image' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'    
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $category =  Category::find($id);
        if($request->hasfile('image')) {
            $image_file = request()->file('image');
            $fileName = str_replace(' ', '', $image_file->getClientOriginalName());
            $image = time()."_".$fileName;
            $img = Image::make($image_file->getRealPath());
            if (!empty($category->image)) {
                unlink(public_path() . '/images/categories/' . $category->image);
            }
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save( public_path().'/images/categories/' . $image);
            $category->image = $image;
        }
        
        $category->name = request('title');
        $category->description = request('description');
        $category->isActive = request('isActive');
        DB::beginTransaction();
        try {
              $category->save();
              DB::commit();
              return response()->json(['success' => true, 'message' => 'Category updated successfully.']);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([ 'success' => false,'message' => 'Category updated failed.']);
            }
    }

    public function delete($id){
        $category = Category::find($id);
        $category->delete();
       	return response()->json(['success'=> true, 'message'=> 'Category deleted.']);
    }

    public function list(){
        $categories = DB::select("SELECT * FROM categories WHERE isActive = 1");
        //$categories = Category::all()->Where('isActive', 1);
        return response()->json($categories);
    }

    public function listWithProduct(){
        $categories = Category::all()->Where('isActive', 1);
        $highlighted = DB::select("SELECT * FROM products WHERE isHighlighted = 1 AND isActive = 1");
        $trending = DB::select("SELECT * FROM products WHERE isTrending = 1 AND isActive = 1");
        
        return response()->json(compact('categories', 'highlighted', 'trending'));
    }
}
