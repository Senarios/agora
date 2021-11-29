<?php

namespace App\Http\Controllers\Admin;
use App\Models\Brands;
use App\Models\MailData;
use App\Models\User;
use App\Models\Predictions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Log;
use GuzzleHttp\Client;

class BrandsController extends Controller
{

    public function login(Request $request)
    {

       

        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {

            $response = [
                'success' => false,
                'message' => $validation->errors()->first()
            ];

            return response()->json($response);

            
        }

        $attempt = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,

        ]);
      

        if (!$attempt) {
           
            $response = [
                'success' => false,
                'message' => 'Credentials Did not Match'
            ];

            return response()->json($response);


            
        } else {
            $user = User::where('email', $request->email)->first();
            $user = $request->user();

            $tokenResult = $user->createToken('Laravel Personal Access Client');
            $token = $tokenResult->token;

            
            $user->api_token = $tokenResult->accessToken;

          //dd($token);

            // $token->expires_at = Carbon::now()->addWeeks(1);

           
            $token->save();
            $user->save();
//  dd($user);
            $response = [
                'success' => true,
                'message' => 'Signed in successfully',
                'data' => $user
            ];

            return response()->json($response);

    }

}

public function create(Request $request){


    $validation = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255'],
        'year_founded' => ['required', 'string', 'max:255'],
        'headquarters' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:800'],
        'brand_id'=>['required'],
    
    ]);

    if ($validation->fails()) {

        $response = [
            'success' => false,
            'message' => $validation->errors()->first()
        ];

        return response()->json($response);
    }

        $image=''; 
        if($request->hasFile('image')){
            $image = time().time().'.'.request()->image->getClientOriginalExtension();
            $request->file('image')->storeAs('/attachments', $image,config('filesystems.default'));

        }
        
        $Brands = new Brands();
        $Brands->name = $request->input('name');
        $Brands->year_founded = $request->input('year_founded');
        $Brands->headquarters = $request->input('headquarters');
        $Brands->description = $request->input('description');
        $Brands->brand_category_tag1 = $request->input('brand_category_tag1');
        $Brands->brand_category_tag2 =$request->input('brand_category_tag2');
        $Brands->brand_category_tag3 = $request->input('brand_category_tag3');
        $Brands->brand_id = $request->input('brand_id');
        $Brands->link = $request->input('link');
        $Brands->image = $image;
        $Brands->image_url = $image;

        if($Brands->save()){
            
            $response = [
                'success' => true,
                'message' => 'Brands Saved Successfully',
                'data' => $Brands
            ];

            return response()->json($response);
        }else{
            $response = [
                'success' => false,
                'message' => 'Something Went Wrong'
            ];
    
            return response()->json($response);
        }
}

public function predictions(Request $request){


    $validation = Validator::make($request->all(), [
        'request' => ['required'],
        'response' => ['required'],
        
    ]);

    if ($validation->fails()) {

        $response = [
            'success' => false,
            'message' => $validation->errors()->first()
        ];

        return response()->json($response);
    }
        $Predictions = new Predictions();
        $Predictions->request = $request->input('request');
        $Predictions->response = $request->input('response');

       
        $a= $request->input('response');
        $abbb= $request->input('request');
        $s =json_decode($a);
        

        $new_arr=array();
        $new_array=array();
        
        foreach ($s as $z) {
                if(isset($z->Brand_Id)){
                    array_push($new_arr,$z->Brand_Id);
                }
                else{
                    array_push($new_arr,$z->brand_id);
                }
            }
                $brand=Brands::whereIn('brand_id',$new_arr)->get();

                foreach ($new_arr as $brand_ids){
                   foreach($brand as $get_id){
                    if($get_id -> brand_id==$brand_ids){
                        array_push($new_array,$get_id);
                        break;
                    }
                   } 
                }


            if($Predictions->save()){
            
            $response = [ 
                'success' => true,
                'message' => 'Predictions Saved Successfully',
                'data' => $Predictions,
                'Brand'=>$new_array
            ];

            return response()->json($response);
            }else{
                
            $response = [
                'success' => false,
                'message' => 'Something Went Wrong'
            ];
    
            return response()->json($response);
        }


    



    
}

public function downloadPredictions(Request $request){

// dd($request->query('start_date'));
   


    $Predictions = Predictions::whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')])->get();
      
    $filename = 'csv-predictions-'.time() . ".csv";
    $filepath = storage_path($filename);
    $handle = fopen($filepath, 'w+');
    foreach ($Predictions as $Prediction) {
        $pp = json_decode($Prediction->request);
        fputcsv($handle,array($Prediction-> created_at, $pp->ip_address,$Prediction->id,$Prediction->request,$Prediction->response));
        // fputcsv($handle,array($Prediction-> created_at));

    }

   

    fclose($handle);
    $headers = array(
        'Content-Type' => 'text/csv',
    );
    return \Response::download($filepath, $filename, $headers)->deleteFileAfterSend(true);

    



    
}


    public function getBrands(){

        $brands = Brands::all();

        $response = [
            'success' => true,
            'message' => 'Brands Data',
            'data' => $brands
        ];

        return response()->json($response);

    }


    public function update(Request $request){

        $Brands = Brands::findOrFail($request->id);

        
            if($request->hasFile('image')){
                Storage::disk(config('filesystems.default'))->delete('attachments/'. $Brands->image);
                
                $image = time().time().'.'.request()->image->getClientOriginalExtension();
                
                $request->file('image')->storeAs('/attachments', $image,config('filesystems.default'));

                $Brands->image = $image;
                $Brands->image_url = $image;
            }
            
            $Brands->name = $request->input('name');
            $Brands->brand_id = $request->input('brand_id');
            $Brands->year_founded = $request->input('year_founded');
            $Brands->headquarters = $request->input('headquarters');
            $Brands->description = $request->input('description');
            $Brands->brand_category_tag1 = $request->input('brand_category_tag1');
            $Brands->brand_category_tag2 =$request->input('brand_category_tag2');
            $Brands->brand_category_tag3 = $request->input('brand_category_tag3');
            $Brands->link = $request->input('link');
            

            if($Brands->save()){
            
                $response = [
                    'success' => true,
                    'message' => 'Brands Saved Successfully',
                    'data' => $Brands
                ];
    
                return response()->json($response);
            }else{
                $response = [
                    'success' => false,
                    'message' => 'Something Went Wrong'
                ];
                return response()->json($response);
            }
    }

    public function editBrand(Request $request){

        $Brands = Brands::findOrFail($request->id);
                    $response = [
                    'success' => true,
                    'message' => 'Data',
                    'data' => $Brands
                ];
    
                return response()->json($response);
    }


    public function deleteBrand(Request $request){

        $Brands = Brands::findOrFail($request->id);

        Storage::disk(config('filesystems.default'))->delete('attachments/'. $Brands->image);
        
        if($Brands->delete()){

            $response = [
                'success' => true,
                'message' => 'Brand has been deleted successfully'
            ];
    
            return response()->json($response);
            
        }else{

            $response = [
                'success' => false,
                'message' => 'Something went wrong'
            ];
    
            return response()->json($response);
            

        }
    }
    public function saveEmail(Request $request){

        $validation = Validator::make($request->all(), [
        'email' => ['required', 'string', 'max:255'],
    
    ]);

    if ($validation->fails()) {

        $response = [
            'success' => false,
            'message' => $validation->errors()->first()
        ];

        return response()->json($response);
    }

        $Email = new MailData();

        $Email->email = $request->input('email');

        if($Email->save()){
            
            $response = [
                'success' => true,
                'message' => 'Email Submitted Successfully',
                'data' => $Email
            ];

            return response()->json($response);
        }else{
            $response = [
                'success' => false,
                'message' => 'Something Went Wrong'
            ];
    
            return response()->json($response);
        }


    }

    public function getEmails(){

        $email = MailData::all();

        $response = [
            'success' => true,
            'message' => 'Email Data',
            'data' => $email
        ];

        return response()->json($response);

    }

    

    public function offlineBrands(Request $request){

       $validation = Validator::make($request->all(), [
        'age' => [ 'string' ],
    
    ]);

    if ($validation->fails()) {

        $response = [
            'success' => false,
            'message' => $validation->errors()->first()
        ];

        return response()->json($response);
    }   
    
        $age = $request->input('age');

        if($age==1 || $age==2 || $age==3 || $age==4 || $age==5){


            $brand=Brands::whereIn('brand_id',array(1000,1001,1002,1003))->get();

            $sortedBrandarray = array();
            $counter = 0;
                   foreach($brand as $get_id){
                    if($get_id -> brand_id==1000){



                        $temp = $brand[0];
                        $brand[0] = $get_id;
                        $brand[$counter] = $temp;


                        // array_push($sortedBrandarray,$get_id);
                        break;
                    }
                    $counter++;
                   } 

        }


        if($age==6 || $age==7 || $age==8 || $age==9 || $age==10 ){

            $brand=Brands::whereIn('brand_id',array(1002,1004,1005,38))->get();

                $sortedBrandarray = array();
                $counter = 0;
                   foreach($brand as $get_id){
                    if($get_id -> brand_id==1002){



                        $temp = $brand[0];
                        $brand[0] = $get_id;
                        $brand[$counter] = $temp;


                        // array_push($sortedBrandarray,$get_id);
                        break;
                    }
                    $counter++;
                   } 

                //    foreach($brand as $get_id){
                //     if($get_id -> brand_id != 1002){
                //         array_push($sortedBrandarray,$get_id);
                //     }
                //    } 

                // $brand=$sortedBrandarray;
            
        }

        if($age==11 || $age==12 || $age==13 || $age==14){

            $brand=Brands::whereIn('brand_id',array(1006,36,1007,1008))->get();


        }

        
            
            $response = [
                'success' => true,
                'message' => 'Record Fetch Successfully',
                'data' => $brand
            ];

            return response()->json($response);
        
    }


public function sagemaker(Request $request){
    $json = $request->input('json');
    $json = json_encode($json,JSON_UNESCAPED_SLASHES);
    // return $json;
    Log::Debug($json);
    $headers1 = $this->signRequest($json);
    $client = new Client([
        'base_uri' => config('recomendation.lambda_url'),
    ]);
    try {
        $response = $client->request('POST', '', 
        [  
            'headers' => [
                "Content-Type" => "application/json",
                "X-Amz-Content-Sha256" => $headers1['x-amz-content'],
                "X-Amz-Date" => $headers1['x-amz-date'],
                'Authorization' => $headers1['Authorization'],
            ],
            'body' => $json]);
        return $response;

    } catch (\Exception $e) {
        // $this->failure('davinci failure: ' . $e->getMessage());
        return  $e->getMessage();
    }
}

private function signRequest($param){
        $method ='POST';
        $uri = config('recomendation.uri');
        $secretKey  = config('recomendation.secret_key');
        $access_key = config('recomendation.access_key');
        $region = config('recomendation.region');
        $service = config('recomendation.service');
        $options = array(); $headers = array();
        $host = config('recomendation.host');


        $alg = 'sha256';

        $date = new \DateTime( 'UTC' );

        $dd = $date->format( 'Ymd\THis\Z' );

        $amzdate2 = new \DateTime( 'UTC' );
        $amzdate2 = $amzdate2->format( 'Ymd' );
        $amzdate = $dd;

        $algorithm = 'AWS4-HMAC-SHA256';


        $requestPayload = strtolower($param);
        $hashedPayloads = hash("sha256",$requestPayload);
        $hashedPayloads1 = strtolower(hash("sha256",$param));

        $canonical_uri = $uri;
        $canonical_querystring = '';

        $canonical_headers = "content-type:"."application/json"."\n"."host:".$host."\n"."x-amz-content-sha256:".$hashedPayloads1 ."\n"."x-amz-date:".$amzdate."\n";
        $signed_headers = 'content-type;host;x-amz-content-sha256;x-amz-date';
        $canonical_request = "".$method."\n".$canonical_uri."\n".$canonical_querystring."\n".$canonical_headers."\n".$signed_headers."\n".$hashedPayloads1;
        

        $credential_scope = $amzdate2 . '/' . $region . '/' . $service . '/' . 'aws4_request';
        $string_to_sign  = "".$algorithm."\n".$amzdate ."\n".$credential_scope."\n".hash('sha256', $canonical_request)."";

        $kSecret = 'AWS4' . $secretKey;
        $kDate = hash_hmac( $alg, $amzdate2, $kSecret, true );
        $kRegion = hash_hmac( $alg, $region, $kDate, true );
        $kService = hash_hmac( $alg, $service, $kRegion, true );
        $kSigning = hash_hmac( $alg, 'aws4_request', $kService, true );     
        $signature = hash_hmac( $alg, $string_to_sign, $kSigning ); 
        $authorization_header = $algorithm . ' ' . 'Credential=' . $access_key . '/' . $credential_scope . ', ' .  'SignedHeaders=' . $signed_headers . ', ' . 'Signature=' . $signature;

        $headers = [
                    'content-type'=>'application/json', 
                    'x-amz-date'=>$amzdate, 
                    'x-amz-content'=>$hashedPayloads1,
                    'Authorization'=>$authorization_header];
        return $headers;
    }




}