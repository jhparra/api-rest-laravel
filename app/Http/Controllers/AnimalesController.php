<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;

use App\Models\Category;

use App\Models\User;

class AnimalesController extends Controller
{
    public function index() {
        $titulo = 'Los Animales';
        $animales = ['Perro','Gato','Raton','Elefante','Ballena','Tigre'];
        
        return view('animales.index',array(
            'el_titulo' => $titulo,
            'los_animales' => $animales
        ));
    }

    public function testOrm() {
      /*$posts = Post::all();
        var_dump($posts);
        foreach($posts as $post){
            echo "<h1>".$post->title."</h1>";
            echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
            echo "<p>".$post->content."</p>";
            echo "<hr>";
        }*/
        
        $categories = Category::all();
        foreach($categories as $category){
            echo "<h1>{$category->name}</h1>";

            foreach($category->posts as $post){
                echo "<h3>".$post->title."</h3>";
                echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
                echo "<p>".$post->content."</p>";
                echo "<hr>";
            }

        }

        die('Fin');
    }
}