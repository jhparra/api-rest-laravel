<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function pruebas (Request $request){
        return "Accion de prueba de Usuario";
    }

    public function register (Request $request){
       
      /* -- Prueba con postman para visualizar los datos   
        $name = $request->input('name');
        $surname = $request->input('surname');
        return "Accion de registrar un usuario : $name $surname"; 
      */

      // Pasos para registrar un usuario
      
      //   1. Recoger los datos del usuario por post
      
      $json = $request->input('json', null);
      $params = json_decode($json);
      $params_array = json_decode($json,true);
      //var_dump($params); die('Fin');
      //var_dump($params_array); die('Fin');  

      if(!empty($params) && !empty($params_array)) {

                //   2. Limpiar los datos

                $params_array = array_map('trim',$params_array);
                
                //   3. Validar los datos
                $validate = Validator::make($params_array, [
                    'name'      => 'required|alpha',
                    'surname'   => 'required|alpha',
                    'email'     => 'required|email|unique:users',
                    'password'  => 'required'    
                ]);

                if ($validate->fails()){
                        $data = array(
                            'status'   => 'error',
                            'code'     => 404,    
                            'message'  => 'El usuario no se ha creado',
                            'errors'   => $validate->errors()   
                        );
                        
                } else {
                        // SIN ERRORES

                        //   4. Cifrar cotraseña 
                        $pwd = password_hash($params->password, PASSWORD_BCRYPT,['cost' => 4]);
                        
                        //   5. Validar que el usuario no este duplicado
                        //   R : Ya se tine $validate con la validacion
                        
                        //   6. Crear el usuario y notificar si fue exitoso o no 
                        $Usuario = new User();
                        $Usuario->name = $params_array['name'];
                        $Usuario->surname = $params_array['surname'];
                        $Usuario->role_user = 'ROLE_USUARIO';
                        $Usuario->email = $params_array['email'];
                        $Usuario->password = $pwd;
                         
                        //var_dump($Usuario); die('Fi');

                        // 7. Guardar el usuario
                        $Usuario->save();
                        
                        $data = array(
                            'status'   => 'sucess',
                            'code'     => 200,    
                            'message'  => 'El usuario se ha creado correctamente',
                            'Usuario'  => $Usuario 
                        );
                }
      } else {
        $data = array(
            'status'   => 'error',
            'code'     => 401,    
            'message'  => 'Los datos eviados no so correctos'  
        );
      }

          
      
      return response()->json($data,$data['code']); 
    }

    public function login (Request $request){
        return "Accion de login de un usuario";
    }
}

