    <h1>{{$el_titulo}}</h1>

    <ul>
        @foreach($los_animales as $animal)
        <li> {{$animal}} </li>
        @endforeach
    </ul>