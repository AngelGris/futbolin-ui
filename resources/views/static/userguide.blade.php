@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/userguide.js') }}"></script>
@endsection

@section('content-inner')
<div class="accordion accordion-primary">
    <h3><span class="fa fa-home" style="margin-right:10px;"></span><a href="#">Vestuario</a></h3>
    <div>
        <p>El <strong>Vestuario</strong> es el primer lugar que visitas al entrar en <strong>Futbolin</strong>. En tu <strong>Vestuario</strong> puedes tener una vista rápida de la situación de tu equipo, cuál es el próximo partido del torneo y cuáles fueron los últimos partidos que jugaste (incluyendo los amistosos jugados como visitante).</p>
    </div>
    <h3><span class="fa fa-shield" style="margin-right:10px;"></span><a href="#">Equipo</a></h3>
    <div>
        <p>En la página <strong>Equipo</strong> puedes ver y editar la información de tu equipo. Podrás editar el nombre de tu equipo, sus colores o cambiar el escudo cuantas veces quieras pero intenta no perder la identidad que lo representa.</p>
    </div>
    <h3><span class="fa fa-group" style="margin-right:10px;"></span><a href="#">Jugadores</a></h3>
    <div>
        <p>Puedes ver la lista de jugadores y sus atributos en la página <strong>Jugadores</strong>. En dispositivos móbiles sólo se muestra una lista reducida de los atributos, pero presionando sobre el nombre de un jugador podrás ver todos sus atributos.</p>
        <p>Los atributos de los jugadores son los siguientes;</p>
        <ul style="margin-left:10px;">
            <li><strong>Edad:</strong> los jugadores comienzan su carrera entre los 17 y los 20 años de edad y se retiran entre los 33 y los 40 años. Al finalizar un torneo los jugadores cumplen años. Al comenzar una nueva temporada serás notificado sí un jugador decide retirarse al final de la misma.</li>
            <li><strong>Posición (POS):</strong> posición en la que mejor se desempeña el jugador (Arquero, Defensor, Mediocampista o Atacante).</li>
            <li><strong>Energía (ENE):</strong> la energía determina el cansancio del jugador. Un jugador con menos energía rinde menos en los partidos. Los jugadores gastan energías en los partidos de torneo y luego resuperan energía diariamente y al entrenar. Un jugador muy cansado será sustituído durante el partido.</li>
            <li><strong>Media (MED):</strong> es el promedio ponderado de los atributos de los jugadores. El promedio se calcula de forma diferente para cada posición, teniendo en cuenta lo que se necesita para jugar bien en cada una de ellas.</li>
            <li><strong>Arquero (ARQ):</strong> la habilidad del jugador para defender el arco (por ejemplo, atajar los remates).</li>
            <li><strong>Defensa (DEF):</strong> capacidad para cortar un ataque rival (por ejemplo, interceptar un pase).</li>
            <li><strong>Gambeta (GAM):</strong> habilidad para evitar que los adversarios le quiten la pelota al correr con ella.</li>
            <li><strong>Cabeceo (CAB):</strong> qué tan bien el jugador cabecea tanto en ataque como en defensa.</li>
            <li><strong>Salto (SAL):</strong> los jugadores que mejor saltan tienen más posibilidades de cabecear un corner o un tiro libre en ataque y en defensa.</li>
            <li><strong>Pase (PAS):</strong> un valor alto incrementa las posibilidades de que los pases dados por el jugador lleguen a destino sin ser interceptados.</li>
            <li><strong>Precisión (PRE):</strong> precisión al tirar al arco. A mayor precisión mayor probabilidad de que el tiro acabe siendo gol.</li>
            <li><strong>Velocidad (VEL):</strong> velocidad del jugador al correr.</li>
            <li><strong>Fuerza (FUE):</strong> jugadores con más fuerza pueden disparar al arco desde más lejos. Jugadores con poca fuerza deberán llegar más cerca del arco para rematar.</li>
            <li><strong>Quite (QUI):</strong> habilidad para quitarle la pelota a un rival.</li>
            <li><strong>Experiencia (EXP):</strong> al jugar partidos de campeonato (amistosos y partidos con sparrings no cuentan) y al entrenar los jugadores ganan experiencia. Al tener 100 puntos de experiencia suben de nivel mejorando sus atributos.</li>
        </ul>
        <p>Algunas veces encontrarás junto al nombre del jugador unos íconos que quieren decir lo siguiente:</p>
        <ul style="list-style:none;">
            <li><span class="fa fa-user-times" style="color:#f00;"></span> = jugadores que se retiran al final de la temporada</li>
            <li><span class="fa fa-arrow-circle-up" style="color:#080;"></span> = jugadores mejorados después del último partido</li>
            <li><span class="fa fa-arrow-down" style="color:#f00;"></span> = jugadores con poca energía</li>
        </ul>
    </div>
    <h3><span class="fa fa-gears" style="margin-right:10px;"></span><a href="#">Estrategia</a></h3>
    <div>
        <p>En la página <strong>Estrategia</strong> determinas cómo va a jugar tu equipo eligiendo una formación (3-4-3, 4-4-2, etc.) y cuáles serán los jugadores titulares y los suplentes. Simplemente tienes que arrastrar los jugadores hasta la posición que quieres que ocupen en la cancha, o hasta el banco de suplentes.</p>
        <p>Los suplentes serán utilizados en remplazo de algún titular que esté muy cansado, y también ganan experiencia aunque no jueguen así que elije bien quiénes serán los suplentes.</p>
    </div>
    <h3><span class="fa fa-handshake-o" style="margin-right:10px;"></span><a href="#">Amistosos</a></h3>
    <div>
        <p>Aquí puedes jugar partidos con Sparrings de 40 o 60 puntos de promedio para probar tus estrategias, o incluso jugar un amistoso contra los otros equipos.</p>
        <p>Puedes jugar todos los partidos con sparrings que quieras, pero sólo podrás jugar un partido amistoso contra cada equipo cada 24 horas.</p>
        <p>En estos partidos los jugadores no se cansan ni ganan experiencia.</p>
        <p>También presionar en el ícono de estadísticas (<span class="fa fa-bar-chart"></span>) para ver tus estadísticas contra cada equipo.
    </div>
    <h3><span class="fa fa-trophy" style="margin-right:10px;"></span><a href="#">Torneos</a></h3>
    <div>
        <p>Toda la información de los torneos que jugás está en ésta página: resultados próximos partidos y tabla de posiciones.</p>
        <p>Los partidos de torneo se juegan lunes, miércoles y viernes a las 20:00 horas (horario de Argentina).</p>
    </div>
    <h3><span class="fa fa-star" style="margin-right:10px;"></span><a href="#">Entrenar</a></h3>
    <div>
        <p>Cada 24 horas podrás entrenar a tu equipo para que tus jugadores ganen más experiencia y recuperen energía apretando el botón con la estrella (<span class="fa fa-star"></span>) en la parte superior de la pantalla.</p>
        <p>Si entrenas todos los días la cantidad de experiencia y energía será mayor.</p>
        <p>¡No te olvides de entrenar todos los días!</p>
    </div>
</div>
@endsection