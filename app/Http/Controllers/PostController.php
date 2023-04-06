<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
//use App\Http\Controllers\JwtAuth;

class PostController extends Controller
{
    public function __constructor() {
        // Cuado el metodo es publico se debe excluir del middleware
        $this->middleware('api.auth',['except'=>['index',
                                                 'show',
                                                 'getImage',
                                                 'getPostsByCategory',
                                                 'getPostsbyUser']]);
    }

    public function index() {
       $posts = Post::all()->load('category'); // carga la categoria

       return response()->json([
        'code' => 200,
        'status' => 'success',
        'posts' => $posts
       ], 200);
    }

    public function show($id){
        $post=Post::find($id)->load('category'); // carga la categoria;

        if(is_object($post)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $post
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La entrada no existe'
            ];
        }

        return response()->json($data,$data['code']);

    }

    public function store (Request $request){
        // Recoger los datos
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true); 

       
        
        if(!empty($params_array)){
            // Coseguir usuario idetificado
            $user = $this->getIdentity($request);
            // Validar los datos
            $validate = \Validator::make($params_array,[
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);
            

            if ($validate->fails()){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el post, datos icorrectos'
                ];
            } else {
                // Guardar los datos
                $post = new Post();
                $post->user_id  = $user->sub;
                $post->category_id  = $params->category_id;
                $post->title  = $params->title;
                $post->content  = $params->content;
                $post->image  = $params->image;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
            

        } else {
            $data = [
                'code' => 400,
                'status' => 'success',
                'message' => 'No se ha guardado el post, falta datos'
            ];

        }
        return response()->json($data,$data['code']);

    }

    public function update($id, Request $request) {
        // Recoger los datos que viene por Post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true); 

     
        $data = [
           'code' => 404,
           'status' => 'error',
           'message' => 'No has enviado ninguna categoria'
        ];
       
        if(!empty($params_array)){
            // Validar los datos
            $validate = \Validator::make($params_array,[
                    'title' => 'required',
                    'content' => 'required',
                    'category_id' => 'required',
            //        'image' => 'required'
                    ]);
            
            if ($validate->fails()){
                $data['errors']= $validate->errors();
                return response()->json($validate->errors(),400);
            }
            // Quitar lo que o quiero actualizar
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            // Coseguir usuario idetificado
            $user = $this->getIdentity($request);

            // Coseguir el registro
            $post = Post::where('id',$id)
                        ->where('user_id',$user->sub)->first();
            
            if (!empty($post) && is_object($post)){ 

                $post->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'change' => $params_array
                ];

            }

            // Actualizar el registro (categoria)
            /*
            $where = [
                'id'=>$id,
                'user_id'=>$user->sub
            ];
            $post = Post::updateOrCreate($where,$params_array);*/

                       
            /*$post = Post::where('id',$id)
                        ->where('user_id',$user->sub)
                        ->update($params_array);
*/            // Devolver respuesta
            
        }

        return response()->json($data,$data['code']);

    }

    public function destroy($id, Request $request) {
        // Coseguir usuario idetificado

        $user = $this->getIdentity($request);
        
        // Coseguir el registro
        $post = Post::where('id',$id)
                    ->where('user_id',$user->sub)->first();

        // Coseguir el registro
        $post = Post::find($id);
        if (!empty($post)) {
            // Borrarlo
            $post->delete();
            // devolver algo
            $data = [
                'code'=>200,
                'status'=>'success',
                'post'=>$post
            ];
        } else {
            $data = [
                'code'=>404,
                'status'=>'error',
                'message'=>'El post o existe'
            ];
        }
        

        return response()->json($data,$data['code']);
    }

    private function getIdentity($request){
        $jwtAuth = new \JwtAuth();
        $token = $request->header('Autentication',null);
        $user= $jwtAuth->checkToken($token,true);

        return $user;

    }

    public function upload(Request $request){

        //Recoger datos de la peticio
        $image = $request->file('file0');

        // Validacion de imagen

        $validate = \Validator::make($request->all(),[
            'file0' => 'required|image:jpg,jpeg,png,gif'
        ]);
        

        //Guardar image
        if(!$image || $validate->fails()){

            $data = array (
                'code'    => 400,
                'status'  => 'error',
                'message' => 'Error al subir imagen'
            );
            
            
        }else {
            
            $image_name=time().$image->getClientOriginalName();
            
            \Storage::disk('images')->put($image_name, \File::get($image));
            
            $data = array(
                'code'    => 200,
                'status'  => 'success',
                'image' => $image_name
            );
            
        }

        

        return response()->json($data,$data['code']); 
    }   

    public function getImage ($filename) {

        //Comprobar si existe la image
        $isset = \Storage::disk('images')->exists($filename);
        
        if($isset){
            
            // Coseguir la image
            $file = \Storage::disk('images')->get($filename);
            // Devolver algo 
            return new Response ($file, 200);
        }else {
            $data = array(
                'code'    => 404,
                'status'  => 'ERROR',
                'image' => 'Imagen no existe'
            );

            return response()->json($data,$data['code']); 
        }
        
    }

    public function getPostsByCategory($id){
        $posts = Post::where('category_id',$id)->get();
       
        return response()->json([
                            'status' => 'success',
                            'post' => $posts
                           ],200); 
    }

    public function getPostsbyUser($id){
        $posts = Post::where('user_id',$id)->get();
        
        return response()->json([
                            'status' => 'success',
                            'post' => $posts
                           ],200); 

    }

}