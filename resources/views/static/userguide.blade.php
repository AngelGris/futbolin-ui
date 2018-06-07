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
    <h3><span class="fa fa-money" style="margin-right:10px;"></span><a href="#">Finanzas</a></h3>
    <div>
        <p>Todos los movimientos económicos de tu equipo se muestran en la sección de <strong>Finanzas</strong>, para que puedas entender mejor tus ingresos y egresos.</p>
        <p>Cada partido oficial que juegues tendrás ingresos por venta de entradas. La cantidad de entradas vendidas dependerá de la posición de ambos equipos en la tabla de posiciones y el total recaudado se repartirá entre los dos equipos en partes iguales.</p>
        <p>Los domingos a la noche se paga el salario de los jugadores, que cobran el {{ number_format(config('constants.PLAYERS_SALARY') * 100, 2) }}% de su valor en el mercado.</p>
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
        @include('modules.playerslegends')
        <p>Los jugadores pueden retirarse entre los 32 y los 36 años. Si tu equipo tiene {{ config('constants.MAX_PLAYERS_REPLACE') }} jugadores o menos, cada vez que se retire un jugador un juvenil será incorporado.</p>
    </div>
    <h3><span class="fa fa-retweet" style="margin-right:10px;"></span><a href="#">Mercado de pases</a></h3>
    <div>
        <p>El <strong>Mercado de pases</strong> es e lugar donde puedes encontrar los jugadores transferibles, y hacer ofertas para comprarlos.</p>
        <p>Los jugadores transferibles son puestos en venta al mejor postor durante 7 días. Cuando el periodo termine e equipo que haya hecho la oferta más alta se quedará con el jugador.</p>
        <p>El valor que se pague por el jugador será su nuevo valor de mercado y su salario el {{ number_format(config('constants.PLAYERS_SALARY') * 100, 2) }}% de ése valor.</p>
    </div>
    <h3><span class="fa fa-gears" style="margin-right:10px;"></span><a href="#">Estrategia</a></h3>
    <div>
        <p>En la página <strong>Estrategia</strong> determinas cómo va a jugar tu equipo eligiendo una formación (3-4-3, 4-4-2, etc.) y cuáles serán los jugadores titulares y los suplentes. Simplemente tienes que arrastrar los jugadores hasta la posición que quieres que ocupen en la cancha, o hasta el banco de suplentes.</p>
        <p>Los suplentes serán utilizados en remplazo de algún titular que esté muy cansado o lesionado, y también ganan experiencia aunque no jueguen así que elije bien quiénes serán los suplentes.</p>
        <p>Cuando un jugador necesita ser remplazado, en su lugar ingresará el primer suplente (de izquierda a derecha) que juegue en la misma posición que el que se retira (ARQ, DEF, MED, ATA). Si no hay ningún suplente en la misma posición entrará el último de la lista.</p>
    </div>
    <h3><span class="fa fa-handshake-o" style="margin-right:10px;"></span><a href="#">Amistosos</a></h3>
    <div>
        <p>Aquí puedes jugar partidos con Sparrings de 40 o 60 puntos de promedio para probar tus estrategias, o incluso jugar un amistoso contra los otros usuarios.</p>
        <p>Puedes jugar todos los partidos con sparrings que quieras, pero contra otros usuarios sólo podrás jugar un partido amistoso contra cada equipo a cada 24 horas.</p>
        <p>En estos partidos los jugadores no se cansan ni ganan experiencia.</p>
        <p>También presionar en el ícono de estadísticas (<span class="fa fa-bar-chart"></span>) para ver tus estadísticas contra cada equipo.</p>
    </div>
    <h3><span class="fa fa-trophy" style="margin-right:10px;"></span><a href="#">Torneos</a></h3>
    <div>
        <p>Toda la información de los torneos que jugás está en ésta página: resultados próximos partidos, tabla de posiciones y tabla de goleadores.</p>
        <p>Los partidos de torneo se juegan lunes, miércoles y viernes a las 20:00 horas (horario de Argentina).</p>
    </div>
    <h3><span class="fa fa-shopping-cart" style="margin-right:10px;"></span><a href="#">Shopping</a></h3>
    <div>
        <p>Puedes ayudar a tu equipo con los ítems del <strong>Shopping</strong></p>
        <p>Aquí encontrarás <strong>In-filtrum</strong> e <strong>In-filtrum Plus</strong> para que tus jugadores puedan recuperar energías o contratar un <strong>Personal trainer</strong> para que entrene a tu equipo durante una semana.</p>
        <p>Los ítems del <strong>Shopping</strong> se compran utilizando <strong>Fúlbos</strong></p>
    </div>
    <h3><span class="fa fa-star" style="margin-right:10px;"></span><a href="#">Entrenar</a></h3>
    <div>
        <p>Cada 24 horas podrás entrenar a tu equipo para que tus jugadores ganen más experiencia y recuperen energía apretando el botón con la estrella (<span class="fa fa-star"></span>) en la parte superior de la pantalla.</p>
        <p>Si entrenas todos los días la cantidad de experiencia y energía será mayor.</p>
        <p>¡No te olvides de entrenar todos los días!</p>
        <p>Si no puedes entenar todos los días puedes utilizar <strong>Fúlbos</strong> para que tus jugadores recuperen energías, o incluso para contratar un personal trainer para que entrene al equipo por tí.</p>
    </div>
    <h3><span class="fa fa-user" style="margin-right:10px;"></span><a href="#">Jugador</a></h3>
    <div>
        <p>Haciendo click en el nombre de un jugador puedes acceder a su información completa.</p>
        <p>Si es un jugador de tu equipo puedes mejorarle el contrato, declararlo transferible o dejarlo libre.</p>
        <p>Al mejorarle el contrato el jugador incrementa su valor de mercado, pero recuerda que su salario será el {{ number_format(config('constants.PLAYERS_SALARY') * 100, 2) }}% de ése valor.</p>
        <p>Un jugador declarado transferible será puesto en venta durante una semana al mejor postor. Si no hay ninguna oferta al acabar el tiempo, el jugador es retirado del mercado.</p>
        <p>Al dejar libre a un jugador, dejará de ser parte de tu plantel y no le pagarás más el salario. Para poder liberarlo deberás pagar la rescisión de su contrato. Los jugadores liberados son colocados en el mercado de pase, posiblemente con un valor de mercado inferior al que tenía.</p>
    </div>
    <h3><span class="fa fa-money" style="margin-right:10px;"></span><a href="#">Fair play financiero</a></h3>
    <div>
        <p>Para evitar los movimientos de dinero dudosos y la acumulación de poder por parte de unos pocos equipos se implementaron algunas reglas de <strong>Fair Play financiero</strong> con el fin de hacer el juego más equitativo para todos los usuario.</p>
        <ol>
            <li>Ningún equipo puede tener menos de {{ config('constants.MIN_TEAM_PLAYERS') }} jugadores.</li>
            <li>Ningún equipo puede tener más de {{ config('constants.MAX_TEAM_PLAYERS') }} jugadores.</li>
            <li>Ningún equipo puede contar con fondos superiores a {{ formatCurrency(config('constants.MAX_TEAM_FUNDS')) }}.</li>
            <li>El valor total de los jugadores de un equipo no puede ser superior a {{ formatCurrency(config('constants.MAX_TEAM_VALUE')) }}.</li>
            <li>El valor de mercado de un jugador no puede ser mayor a {{ formatCurrency(config('constants.MAX_PLAYER_VALUE')) }}.</li>
            <li>Los equipos con 21 jugadores o menos, recibirán un juvenil cada vez que se retire un jugador.</li>
        </ol>
    </div>
</div>
@endsection