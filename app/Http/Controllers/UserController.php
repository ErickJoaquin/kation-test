<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $json = $request->input('json',null);
        $params = json_decode($json); 
        $params_array = json_decode($json,true);

        if(!empty($params) && !empty($params_array)){
            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'email' => 'required|unique:users', 
                'password' => 'required',
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            }
            else{
                $pwd = hash('sha256',$params->password);
                $user = new User();
                $user->name = $params_array['name'];
                $user->email = $params_array['email']; 
                $user->password = $pwd;
                $user->save();
                
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado',
                    'user' => $user
                );
            }

        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos',
            ); 
        }

        return response()->json($data,$data['code']);
    }
    public function login(Request $request){
        $json = $request->input('json',null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json,true);
         
         $validate = \Validator::make($params_array, [
            'email' => 'required|email', 
            'password' => 'required',
        ]);

        if($validate->fails()){
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha identificado',
                'errors' => $validate->errors()
            );
        }
        else{
            //validacion correcta
            //cifrar password
            $pwd = hash('sha256',$params->password);
            $user = User::where('email',$params->email)->where('password',$pwd)->first();
            if($user){
                $signup = array(
                    'status' => 'sucess',
                    'code' => 200,
                    'message' => 'El usuario se ha identificado correctamente',
                    'user' => $user
                );
            }
            else{
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Los datos no han coincidido con nuestros registros',
                );
            }


            }
        return response()->json($signup,$signup['code']);
    }
    public function listar()
    {
        $users = User::all();
        if($users){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Listado',
                'user' => $users
            );
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No hay usuarios registrados'            );
        }

        return response()->json($data);

    }
    public function getById($id)
    {
        $user = User::where('id',$id)->first();
        if($user){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Listado',
                'user' => $user
            );
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No encontramos el usuario'            
            );
        }

        return response()->json($data);
    }
    public function update($id,Request $request){
         $json = $request->input('json',null);
         $params = json_decode($json); 
         $params_array = json_decode($json,true);
 
         if(!empty($params_array)){

             $validate = \Validator::make($params_array, [
                 'name' => 'required',
                 'email' => 'email',  
             ]);
             
             unset($params_array['id']);
             unset($params_array['password']);
             unset($params_array['created_at']);
             unset($params_array['updated_at']);
             unset($params_array['email_verified_at']);
             unset($params_array['remember_token']);
 
             if($validate->fails()){
                 $data = array(
                     'status' => 'error',
                     'code' => 404,
                     'message' => 'El usuario no se ha actualizado',
                     'errors' => $validate->errors()
                 );
             }
             else{
                $user_update = User::where('id',$id)->update($params_array);
                $data = array(
                    'code' => 200,
                    'status' => 'succes',
                    'user' => $user_update
                );
             }
 
         }
         else{
             $data = array(
                 'status' => 'error',
                 'code' => 400,
                 'message' => 'no se pudo actualizar el usuario',
             ); 
         }
 
         return response()->json($data,$data['code']);


    }
    public function updatePassword($id,Request $request){
        $json = $request->input('json',null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json,true);
        
        if(!empty($params_array)){

            //Validar datos
          $validate = \Validator::make($params_array, [
               'email' => 'required',
               'password' => 'required',
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La contrasena no ha sido cambiada',
                    'errors' => $validate->errors()
                );
            }
            else{
               $user_update = User::where('email',$params_array['email'])->first();
               $user_update->password = hash('sha256',$params->password);
               $user_update->save();
               $data = array(
                   'code' => 200,
                   'status' => 'succes',
                   'user' => $user_update
               );
            }

        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'no se pudo cambiar la contrasena',
            ); 
        }

        return response()->json($data,$data['code']);

   }
    public function delete($id)
    {
        $user = User::where('id',$id)->first();
        if($user){
            $user->delete();
            $data = array(
                'status' => 'succes',
                'code' => 200,
                'message' => 'Usuario Eliminado'            
            );
            
        }
        else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se ha encontrado el usuario'
            );
        }
        return response()->json($data);        
        
    }
}
