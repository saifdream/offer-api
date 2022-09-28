<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Merchant;
use Validator;
use Image;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
   	public function create(Request $request){
	 	$validator = Validator::make($request->all(),[
	        'category_id'	=> 'required',
	        'name'			=> 'required',
	        'address'	    => 'required',
	        'contact'	    => 'required',
	        'email'	        => 'required',
	        'web'	        => 'required',
	        'lat'	        => 'required',
	        'lng'	        => 'required',
	        'isActive' 		=> 'required',
	        'logo' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
	     ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $merchant = new Merchant;
        if($request->hasfile('logo')) {
            $logo_file = request()->file('logo');
            $logo = time()."_".$logo_file->getClientOriginalName();
            $img = Image::make($logo_file->getRealPath());            
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save( public_path().'/images/merchants/' . $logo);
            $merchant->logo = $logo;
        }

        $merchant->category_id = request('category_id');
        $merchant->name = request('name');
        $merchant->address = request('address');
        $merchant->contact = request('contact');
        $merchant->email = request('email');
        $merchant->web = request('web');
        $merchant->lat = request('lat');
        $merchant->lng = request('lng');
        $merchant->isActive = request('isActive');
        DB::beginTransaction();
        try {
            $merchant->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Merchant added successfully.']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([ 'success' => false,'message' => 'Merchant insertion failed.']);
        }
    }

    public function edit(Request $request,$id){
 		$validator = Validator::make($request->all(),[     
	        'category_id'	=> 'required',
	        'name'			=> 'required',
	        'address'	    => 'required',
	        'contact'	    => 'required',
	        'email'	        => 'required',
	        'web'	        => 'required',
	        'lat'	        => 'required',
	        'lng'	        => 'required',
	        'isActive' 		=> 'required',
	        //'logo' 		=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'   
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        $merchant =  Merchant::find($id);
        if($request->hasfile('logo')) {
            $logo_file = request()->file('logo');
            $fileName = str_replace(' ', '', $logo_file->getClientOriginalName());
            $logo = time()."_".$fileName;
            $img = Image::make($logo_file->getRealPath());
            if (!empty($merchant->logo)) {
                unlink(public_path() . '/images/merchants/' . $merchant->logo);
            }
            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
            })->save( public_path().'/images/merchants/' . $logo);
            $merchant->logo = $logo;
        }
        
        $merchant->category_id = request('category_id');
        $merchant->name = request('name');
        $merchant->address = request('address');
        $merchant->contact = request('contact');
        $merchant->email = request('email');
        $merchant->web = request('web');
        $merchant->lat = request('lat');
        $merchant->lng = request('lng');
        $merchant->isActive = request('isActive');
        DB::beginTransaction();
        try {
            $merchant->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Merchant updated successfully.']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([ 'success' => false,'message' => 'Merchant updated failed.']);
        }
    }

    public function delete($id){
        $merchant = Merchant::find($id);
        $merchant->delete();
       	return response()->json(['success'=> true, 'message'=> 'Merchant deleted.']);
    }

    public function list($categoryId){
        $merchants = DB::select("SELECT * FROM merchants WHERE category_id = $categoryId AND isActive = 1");
        //$merchants = Merchant::all()->where('category_id', $categoryId)->Where('isActive', 1);
        return response()->json($merchants);
    }

    public function listByLatLng($categoryId, $lat, $lng){
        $merchants = DB::select("SELECT * FROM
        ( SELECT *, ( 3959 * acos( cos( radians($lat) ) * cos( radians(lat) ) * cos( radians(lng) - radians($lng) ) + sin( radians($lat) ) * sin( radians(lat) ) ) ) distance
        FROM merchants m WHERE m.category_id = $categoryId AND m.isActive = 1) t WHERE t.distance <= 5 ORDER BY t.distance");
        return response()->json($merchants);
    }
}
