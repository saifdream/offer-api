<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Validator;
use Image;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
   	public function create(Request $request){
	 	$validator = Validator::make($request->all(),[
	        'merchant_id'	=> 'required',
	        'name'			=> 'required',
	        'description'	=> 'required',
	        'price'	        => 'required',
	        'size'	        => 'required',
	        'color'	        => 'required',
	        'qty'	        => 'required',
	        'isHighlighted'	=> 'required',
	        'isTrending'	=> 'required',
	        'isActive' 		=> 'required',
	        'image' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
	     ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $product = new Product;
        if($request->hasfile('image')) {
            $image_file = request()->file('image');
            $image = time()."_".$image_file->getClientOriginalName();
            $img = Image::make($image_file->getRealPath());            
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save( public_path().'/images/products/' . $image);
            $product->image = $image;
        }

        $product->merchant_id = request('merchant_id');
        $product->name = request('name');
        $product->description = request('description');
        $product->price = request('price');
        $product->size = request('size');
        $product->color = request('color');
        $product->qty = request('qty');
        $product->isHighlighted = request('isHighlighted');
        $product->isTrending = request('isTrending');
        $product->isActive = request('isActive');
        DB::beginTransaction();
        try {
              $product->save();
              DB::commit();
              return response()->json(['success' => true, 'message' => 'Product added successfully.']);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([ 'success' => false,'message' => 'Product insertion failed.']);
            }
    }

    public function edit(Request $request,$id){
 		$validator = Validator::make($request->all(),[     
	        'merchant_id'	=> 'required',
	        'name'			=> 'required',
	        'description'	=> 'required',
	        'price'	        => 'required',
	        'size'	        => 'required',
	        'color'	        => 'required',
	        'qty'	        => 'required',
	        'isHighlighted'	=> 'required',
	        'isTrending'	=> 'required',
	        'isActive' 		=> 'required',
	        //'image' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'    
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $product =  Product::find($id);
        if($request->hasfile('image')) {
            $image_file = request()->file('image');
            $fileName = str_replace(' ', '', $image_file->getClientOriginalName());
            $image = time()."_".$fileName;
            $img = Image::make($image_file->getRealPath());
            if (!empty($product->image)) {
                unlink(public_path() . '/images/products/' . $product->image);
            }
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save( public_path().'/images/products/' . $image);
            $product->image = $image;
        }
        
        $product->merchant_id = request('merchant_id');
        $product->name = request('name');
        $product->description = request('description');
        $product->price = request('price');
        $product->size = request('size');
        $product->color = request('color');
        $product->qty = request('qty');
        $product->isHighlighted = request('isHighlighted');
        $product->isTrending = request('isTrending');
        $product->isActive = request('isActive');
        DB::beginTransaction();
        try {
              $product->save();
              DB::commit();
              return response()->json(['success' => true, 'message' => 'Product updated successfully.']);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([ 'success' => false,'message' => 'Product updated failed.']);
            }
    }

    public function delete($id){
        $product = Product::find($id);
        $product->delete();
       	return response()->json(['success'=> true, 'message'=> 'Product deleted.']);
    }

    public function list($merchantId){
        $products = DB::select("SELECT * FROM products WHERE merchant_id = $merchantId AND isActive = 1");
        //$products = Product::all()->where('merchant_id', $merchantId)->Where('isActive', 1);
        return response()->json($products);
    }
}
