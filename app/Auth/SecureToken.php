<?php
namespace App\Auth;
use App\Models\UserJWT;
use DateTime;
use Exception;

class SecureToken
{
    private static $secretKey = 'secret123';
    private static $secretIv = 'www.domain.com';
    private static $encryptMethod = "AES-256-CBC";
    public static function tokenencrypt($time, $data)
    {
        $value = ["exp"=>$time, "data"=>$data];
        $key = hash('sha256', self::$secretKey);
        $iv = substr(hash('sha256', self::$secretIv), 0, 16);
        $result = openssl_encrypt(json_encode($value), self::$encryptMethod, $key, 0, $iv);
        return $result = base64_encode($result);
    }
    public static function tokendecrypt($data)
    {
        $key = hash('sha256', self::$secretKey);
        $iv = substr(hash('sha256', self::$secretIv), 0, 16);
        $result = openssl_decrypt(base64_decode($data), self::$encryptMethod, $key, 0, $iv);
        return $result;
    }

    public static function register($name, $email, $password){
        $user = new UserJWT();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        //$user->password = bcrypt($password);
        //$user->token = SecureToken::tokenencrypt(['name'=>$name, 'email'=>$email]);
        $user->save();
        //return $user->token;
    }

    public static function login($email, $password){
        //$password_crypt = bcrypt($password);
        
        $time = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        $userjwt = UserJWT::where('email','=',$email)
                    //->where('password','=',$password_crypt)
                    ->get();

        //echo $userjwt;
        //echo $userjwt[0]['password'];
        $password_crypt = password_verify(PASSWORD_DEFAULT, $userjwt[0]['password']);            
               
        $name = $userjwt[0]['name'];            
        $token = SecureToken::tokenencrypt($time, ['email'=>$email, 'name'=>$name]);
        $userjwt[0]->token = $token;
        $userjwt[0]->save();             
        return $token;
    }


    public static function verifytoken($token){
        if ($token == ""){
            throw new Exception('Required Authorization Header');
        
        }

        $userjwt = UserJWT::where('token','=',$token)
                    ->get();

        if (count($userjwt)==0){
            throw new Exception('Invalid User');
        }else{
            //echo $userjwt[0]['token'];
            $decode = SecureToken::tokendecrypt($userjwt[0]['token']);
            $obj_parse = json_decode($decode);
            $exp_date = parse_str($obj_parse->exp);
            echo $exp_date;
            $now = new DateTime("now");
            $exp = new DateTime($exp_date);
            
            if($now>$exp){
                throw new Exception('Invalid Date time');
            }
        }

    }
}