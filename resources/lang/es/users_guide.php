<?php

return [
    'finances'              => '<p>Todos los movimientos económicos de tu equipo se muestran en la sección de <strong>Finanzas</strong>, para que puedas entender mejor tus ingresos y egresos.</p>
                                <p>Cada partido oficial que juegues tendrás ingresos por venta de entradas. La cantidad de entradas vendidas dependerá de la posición de ambos equipos en la tabla de posiciones y el total recaudado se repartirá entre los dos equipos en partes iguales.</p>
                                <p>Los domingos se paga el salario de los jugadores, que cobran el :salary_ratio% de su valor en el mercado.</p>',
    'financial_fair_play'   => '<p>Para evitar los movimientos de dinero dudosos y la acumulación de poder por parte de unos pocos equipos se implementaron algunas reglas de <strong>Fair Play financiero</strong> con el fin de hacer el juego más equitativo para todos los usuario.</p>
                                <ol>
                                    <li>Ningún equipo puede tener menos de :min_team_players jugadores.</li>
                                    <li>Ningún equipo puede tener más de :max_team_players jugadores.</li>
                                    <li>Ningún equipo puede contar con fondos superiores a :max_team_funds.</li>
                                    <li>El valor total de los jugadores de un equipo no puede ser superior a :max_team_value.</li>
                                    <li>El valor de mercado de un jugador no puede ser mayor a :max_player_value.</li>
                                    <li>Los equipos con :max_players_replace jugadores o menos, recibirán un juvenil cada vez que se retire un jugador.</li>
                                </ol>',
    'friendlies'            => '<p>Aquí puedes jugar partidos con Sparrings de 40 o 60 puntos de promedio para probar tus estrategias, o incluso jugar un amistoso contra los otros usuarios.</p>
                                <p>Puedes jugar todos los partidos con sparrings que quieras, pero contra otros usuarios sólo podrás jugar un partido amistoso contra cada equipo a cada 24 horas.</p>
                                <p>En estos partidos los jugadores no se cansan ni ganan experiencia.</p>
                                <p>También presionar en el ícono de estadísticas (<span class="fa fa-bar-chart"></span>) para ver tus estadísticas contra cada equipo.</p>',
    'locker_room'           => '<p>En tu <strong>Vestuario</strong> puedes tener una vista rápida de la situación de tu equipo, cuál es el próximo partido del torneo y cuáles fueron los últimos partidos que jugaste.</p>',
    'player'                => '<p>Haciendo click en el nombre de un jugador puedes acceder a su información completa.</p>
                                <p>Si es un jugador de tu equipo puedes mejorarle el contrato, declararlo transferible o dejarlo libre.</p>
                                <p>Al mejorarle el contrato el jugador incrementa su valor de mercado, pero recuerda que su salario será el :salary_rate% de ése valor.</p>
                                <p>Un jugador declarado transferible será puesto en venta durante :transferable_period días al mejor postor. Si no hay ninguna oferta al acabar el tiempo el jugador es retirado del mercado.</p>
                                <p>Al dejar libre a un jugador, dejará de ser parte de tu plantel y no le pagarás más el salario. Para poder liberarlo deberás pagar la rescisión de su contrato. Los jugadores liberados son colocados en el mercado de pase, posiblemente con un valor de mercado inferior al que tenía.</p>',
    'players'               => '<p>Puedes ver la lista de jugadores y sus atributos en la página <strong>Jugadores</strong>. En dispositivos móbiles sólo se muestra una lista reducida de los atributos, pero presionando sobre el nombre de un jugador podrás ver todos sus atributos.</p>
                                <p>Los atributos de los jugadores son los siguientes:</p>
                                <ul style="margin-left:10px;">
                                    <li><strong>Edad:</strong> los jugadores comienzan su carrera entre los 17 y los 20 años de edad y se retiran entre los 33 y los 40 años. Al finalizar un torneo los jugadores cumplen años. Al comenzar una nueva temporada serás notificado sí un jugador decide retirarse al final de la misma.</li>
                                    <li><strong>Posición (POS):</strong> posición en la que mejor se desempeña el jugador (Arquero, Defensor, Mediocampista o Atacante).</li>
                                    <li><strong>Energía (ENE):</strong> la energía determina el cansancio del jugador. Un jugador con menos energía rinde menos en los partidos. Los jugadores gastan energías en los partidos de torneo y luego recuperan energía diariamente y al entrenar. Un jugador muy cansado será sustituído durante el partido.</li>
                                    <li><strong>Media (MED):</strong> es el promedio ponderado de los atributos de los jugadores. El promedio se calcula de forma diferente para cada posición, teniendo en cuenta lo que se necesita para jugar bien en cada una de ellas.</li>
                                    <li><strong>Arquero (ARQ):</strong> la habilidad del jugador para defender el arco (por ejemplo, atajar los remates).</li>
                                    <li><strong>Defensa (DEF):</strong> capacidad para cortar un ataque rival (por ejemplo, interceptar un pase).</li>
                                    <li><strong>Gambeta (GAM):</strong> habilidad para evitar que los adversarios le quiten la pelota al correr con ella.</li>
                                    <li><strong>Cabeceo (CAB):</strong> qué tan bien el jugador cabecea tanto en ataque como en defensa.</li>
                                    <li><strong>Salto (SAL):</strong> los jugadores que mejor saltan tienen más posibilidades de cabecear un corner o un tiro libre tanto en ataque como en defensa.</li>
                                    <li><strong>Pase (PAS):</strong> un valor alto incrementa las posibilidades de que los pases dados por el jugador lleguen a destino sin ser interceptados.</li>
                                    <li><strong>Precisión (PRE):</strong> precisión al tirar al arco. A mayor precisión mayor probabilidad de que el tiro acabe siendo gol.</li>
                                    <li><strong>Velocidad (VEL):</strong> velocidad del jugador al correr.</li>
                                    <li><strong>Fuerza (FUE):</strong> jugadores con más fuerza pueden disparar al arco desde más lejos. Jugadores con poca fuerza deberán llegar más cerca del arco para poder rematar.</li>
                                    <li><strong>Quite (QUI):</strong> habilidad para quitarle la pelota a un rival.</li>
                                    <li><strong>Experiencia (EXP):</strong> al jugar partidos de campeonato (amistosos y partidos con sparrings no cuentan) y al entrenar los jugadores ganan experiencia. Al alcanzar 100 puntos de experiencia suben de nivel mejorando sus atributos.</li>
                                </ul>
                                <p>Los jugadores pueden retirarse entre los 33 y los 40 años. Si tu equipo tiene :max_players jugadores o menos, cada vez que se retire un jugador un juvenil será incorporado.</p>
                                <p>Algunas veces encontrarás junto al nombre del jugador unos íconos que quieren decir lo siguiente:</p>',
    'shopping'              => '<p>Puedes ayudar a tu equipo con los ítems del <strong>Shopping</strong></p>
                                <p>Aquí encontrarás <strong>In-filtrum</strong> e <strong>In-filtrum Plus</strong> para que tus jugadores puedan recuperar energías, o contratar un <strong>Personal trainer</strong> para que entrene a tu equipo durante una semana.</p>
                                <p>Los ítems del <strong>Shopping</strong> se compran utilizando <strong>Fúlbos</strong></p>',
    'strategy'              => '<p>En la página <strong>Estrategia</strong> determinas cómo va a jugar tu equipo eligiendo una formación (3-4-3, 4-4-2, etc.) y cuáles serán los jugadores titulares y los suplentes. Simplemente tienes que arrastrar los jugadores hasta la posición que quieres que ocupen en la cancha, o hasta el banco de suplentes.</p>
                                <p>Los suplentes serán utilizados en remplazo de algún titular que esté muy cansado o lesionado, y también ganan experiencia aunque no jueguen así que elije bien quiénes serán los suplentes.</p>
                                <p>Cuando un jugador necesita ser remplazado, en su lugar ingresará el primer suplente (de izquierda a derecha) que juegue en la misma posición que el que se retira (ARQ, DEF, MED, ATA). Si no hay ningún suplente en la misma posición entrará el último de la lista.</p>',
    'team'                  => '<p>En la página <strong>Equipo</strong> puedes ver y editar la información de tu equipo. Podrás editar el nombre de tu equipo, sus colores o cambiar el escudo cuantas veces quieras pero intenta no perder la identidad que lo representa.</p>',
    'tournaments'           => '<p>Toda la información de los torneos que jugás está en ésta página: resultados, próximos partidos, tabla de posiciones y tabla de goleadores.</p>
                                <p>Los partidos de torneo se juegan lunes, miércoles y viernes a las 20:00 horas (horario de Argentina, -3 GMT).</p>',
    'train'                 => '<p>Cada 24 horas podrás entrenar a tu equipo para que tus jugadores ganen más experiencia y recuperen energía apretando el botón en la parte superior de la pantalla.</p>
                                <p>Si entrenas todos los días la cantidad de experiencia y energía será mayor.</p>
                                <p>¡No te olvides de entrenar todos los días!</p>
                                <p>Si no puedes entenar todos los días puedes utilizar <strong>Fúlbos</strong> para que tus jugadores recuperen energías, o incluso contratar un personal trainer para que entrene al equipo por tí.</p>',
    'transfers_market'      => '<p>El <strong>Mercado de pases</strong> es el lugar donde puedes encontrar los jugadores transferibles, y hacer ofertas para comprarlos.</p>
                                <p>Los jugadores transferibles son puestos en venta al mejor postor durante :transferable_period días. Cuando el periodo termine el equipo que haya hecho la oferta más alta se quedará con el jugador.</p>
                                <p>El valor que se pague por el jugador será su nuevo valor de mercado y su salario el :salary_rate% de ése valor.</p>',
];