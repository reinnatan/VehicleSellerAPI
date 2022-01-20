<?php

namespace App\Http\Controllers;
use JWTAuth;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleSeller;
use App\Models\UserJWT;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Auth\SecureToken;

class VehicleController extends Controller
{

    function index(){
        echo "This is hello world";
    }

    function createVehicle(Request $request){
        $vehicle = new Vehicle();
        $vehicle->name = $request->get('name');
        $vehicle->type = $request->get('type');
        $vehicle->save();
        echo "Testing create vehicle";
    }

    public function register(Request $request){
        //Request is valid, create new user
         
       SecureToken::register($request->name, $request->email, $request->password);
        return response(['message'=>'User created successfully'], 200)
        ->header('Content-Type', 'application/json');
    }


    public function testing(Request $request){
        echo "fungsi dipanggil";
      
    }

    public function login(Request $request){
        //Request is validated
        //Crean token
        $credentials = $request->only('email', 'password');
        try {
           $token = SecureToken::login($request->email, $request->password);
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function addCar(Request $request){
        $year = $request->get('year');
        $price = $request->get('price');
        $machine = $request->get('machine');
        $passenger = $request->get('passenger');
        $stock = $request->get('stock');

        if (is_numeric($year) == FALSE || is_numeric($price) == FALSE ||
            is_numeric($machine) == FALSE || is_numeric($passenger) == FALSE||
            is_numeric($stock) == FALSE) {
            return response(['message'=>'year price machine passenger and stock is integer input'], 400)
            ->header('Content-Type', 'application/json');
        } 

        $car = new Vehicle();
        $car->year = (int) $year;
        $car->color = $request->get('color');
        $car->price = (int) $price;
        $car->machine = (int) $machine;
        $car->passenger = (int) $passenger;
        $car->type = $request->get('type');
        $car->stock = (int) $stock;
        $car->save();
        return response(['message'=>'successfully add new car'], 200)
        ->header('Content-Type', 'application/json');
    }

    public function addMotorcycle(Request $request){
        $year = $request->get('year');
        $price = $request->get('price');
        $machine = $request->get('machine');
        $stock = $request->get('stock');

        if (is_numeric($year) == FALSE || is_numeric($price) == FALSE ||
            is_numeric($machine) == FALSE || is_numeric($stock) == FALSE) {
            return response(['message'=>'year price machine passenger and stock is integer input'], 400)
            ->header('Content-Type', 'application/json');
        }

        $motorcycle = new Vehicle();
        $motorcycle->year = (int)$year;
        $motorcycle->color = $request->get('color');
        $motorcycle->price = (int) $price;
        $motorcycle->machine = (int) $machine;
        $motorcycle->suppension = $request->get('suppension');
        $motorcycle->transmission = $request->get('transmission');
        $motorcycle->stock = (int)$request->get('stock');
        $motorcycle->save();
        return response(['message'=>'successfully add new motorcycle'], 200)
        ->header('Content-Type', 'application/json');
    }

    public function sellVehicle(Request $request, $id){
        $count = $request->get('count');
        if (is_numeric($count)==FALSE){
           return response(['message'=>'stock is integer input'], 400)
                ->header('Content-Type', 'application/json');
        }

        $spesific_vehicle = Vehicle::find($id);
        $count_parse = (int)$count;
        $qty_stock = (int)$spesific_vehicle['stock'];

        if ($spesific_vehicle){
            if ($qty_stock<$count_parse){
                return response(['message'=>'sell count is more than quantity stock'], 400)
                ->header('Content-Type', 'application/json');
            }else{
            
                $spesific_vehicle->stock=$qty_stock-$count_parse;
                $spesific_vehicle->save();
                $vehicle_seller = new VehicleSeller();
                $vehicle_seller->vehicle_id = $id;
                $vehicle_seller->count = $count_parse;
                $vehicle_seller->save();    
                return response(['message'=>$count_parse." vehicle is sold"], 200)
                ->header('Content-Type', 'application/json');    
            }
        }else{
            return response(['message'=>'vehicle not found with id'.$id], 400)
        ->header('Content-Type', 'application/json');
        }
           
    }

    public function stockVehicle(Request $request){
        $vehicles = Vehicle::all();
        $arr_temp = array();
        foreach($vehicles as $vehicle){
            array_push($arr_temp, $vehicle);
        }
        
        return response(['data'=>$arr_temp], 200)
        ->header('Content-Type', 'application/json');

    }

    public function reportSellerSpecVehicle(Request $request){
        $vehicles = Vehicle::all();
        $arr_temp = array();
        foreach($vehicles as $vehicle){
            $price = $vehicle['price'];
            $id = $vehicle['_id'];
            $total_sold = VehicleSeller::where('vehicle_id', $id)->sum('count');
            array_push($arr_temp, ['id'=>$id,"available_stock"=>$vehicle['stock'], 'item_price'=>$price, 'total_sold'=>$total_sold, 'price_total_sold'=> $total_sold*$price]);
        }

        return response(['data'=>$arr_temp], 200)
        ->header('Content-Type', 'application/json');
    
    }
}


