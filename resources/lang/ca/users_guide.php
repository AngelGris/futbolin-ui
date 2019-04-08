<?php

return [
    'finances'              => '<p>Tots els moviments econònic del teu equip es mostren a la secció de <strong>Finances</strong>, per que puguis entendre millor els teus ingressos e els teus egressos.</p>
                                <p>Cada partit oficial que joguis tindràs ingressos per venda d\'entrades.La quantitat d\'entrades vengudes dependrà de la posició d\' amdós equips a la tabla de posicions i el total recaudat es repartirá entre els dos equips en parts iguals.</p>
                                <p>Els diumenges es paga el salari dels jugadorss, que cobren el :salary_ratio% del seu valor en el mercat.</p>',
    'financial_fair_play'   => '<p>Per evitar els moviments de diners duptosos i la acumulació de poder per part de uns pocs equips  es van implementar algunes regles de <strong>Fair Play financiero</strong> amb la finalitat de fer el joc més equitatiu per tots i totes els usuaris.</p>
                                <ol>
                                    <li>Cap equip pot tindre menys de :min_team_players jugadors.</li>
                                    <li>Cap equip pot tindre més de :max_team_players jugadors.</li>
                                    <li>Cap equip pot contar amb fons superiors a :max_team_funds.</li>
                                    <li>El valor total dels jugadors d\'un equip no pot ser superior a :max_team_value.</li>
                                    <li>El valor de mercat d\'un jugador no pot ser mayor a :max_player_value.</li>
                                    <li>Els equips amb :max_players_replace jugadors o menys, reuràn un juvenil cada cop que es retire un jugador.</li>
                                </ol>',
    'friendlies'            => '<p>Aquí pots jugar partits amb Sparrings de 40 o 60 punts de promig per provar les teves estrategies, o inclòs jugar un amistòs contra els altres usuaris.</p>
                                <p>Pots jugar tots els partits amb sparrings que vulguis, però contra altres usuaris sólsament podràs jugar un partit amistòs contra cada equip a cada 24 hores.</p>
                                <p>En aquests partits els jugadors no es cansen ni guanyen experiència.</p>
                                <p>També pressionar a l\'icon d\'estadístiques (<span class="fa fa-bar-chart"></span>) per veure les teves estadístiques contra cada equip.</p>',
    'locker_room'           => '<p>En el teu <strong>Vestuari</strong> pots tindre una vista rápida de la situació del teu equip, qual es el próxim partit del torneig y quals van ser els últims partits que vas jugar.</p>',
    'player'                => '<p>Fent click al nom d\'un jugador pots accedir a la seva informació completa.</p>
                                <p>Si es un jugador del teu equip pots mejorar-li el contracte, declarar-lo transferible o deixar-lo lliure.</p>
                                <p>Al millorar-li el contracte el jugador incrementa el seu valor de mercat, però enrecorda que el seu salari será el :salary_rate% d\'aquest valor.</p>
                                <p>Un jugador declarat transferible será posat a la venda durant :transferable_period dies al millor postor. Si no hi ha cap oferta al acabar el temps, el jugador es retirat del mercat.</p>
                                <p>Al deixar lliure un jugador, deixará de ser part del teu planté i no l\'hi pagaràs més el salari. Per poder alliberar-ho deuràs pagar la rescissió del contracte. Els jugadors alliberats són colocats al mercat de fitxatges, possiblement amb un valor de mercat inferior al que tenia.</p>',
    'players'               => '<p>Pots veure la llista de jugadors e els seus atributs a la página <strong>Jugadors</strong>. En dispositius mòbils solsament es mostra una llista reduida dels atributs, però pressionant sobre el nom d\'un jugador podràs veure tots else seus atributs.</p>
                                <p>Els atributs dels jugadors són els següents:</p>
                                <ul style="margin-left:10px;">
                                    <li><strong>Edat:</strong> els jugadors comencen la seva carrera entre els 17 i els 20 anys d\'edat i es retiren entre els 33 e els 40 anys. Al finalitzar un torneig, els jugadors cumpleixen anys. Al començar una nova temporada seràs notificat sí un jugador decideix retirar-se al final de la mateixa.</li>
                                    <li><strong>Posició (POS):</strong> posició a la que millor es exerceix el jugador (Porter, Defensa, Migcampista o Davanter).</li>
                                    <li><strong>Energia (ENE):</strong> la energia determina el cansanci del jugador. Un jugador amb menys energia rendeix menys als partits. Els jugadors gasten energies als partits de torneig i després recuperen energia diariament i al entrenar. Un jugador molt cansat será sustituít durant el partit.</li>
                                    <li><strong>Mitja (MIT):</strong> es la mitjana ponderada dels atributs dels jugadors. La mitjana es calcula de forma diferent per cada posició, tenint en compte el que es necessita per jugar bé en cada una d\'ellas.</li>
                                    <li><strong>Porter (POR):</strong> la habilitat del jugador per defensar la porteria (per exemple, aturar les rematades).</li>
                                    <li><strong>Defensa (DEF):</strong> capacitat per tallar un atac rival (per exmple, interceptar una passada).</li>
                                    <li><strong>Regatejar (REG):</strong> habilitat per evitar que els adversaris l\'hi treguin la pilota al correr amb ella.</li>
                                    <li><strong>Cop de Cap (CAP):</strong> Quant de bé el jugador fa un cop de cap tant en atac com en defensa.</li>
                                    <li><strong>Salt (SAL):</strong> els jugadores que millor salten tenen més possibilitats rematar de cap un corner o un tir lliure tant en atac com en defensa.</li>
                                    <li><strong>Passada (PAS):</strong> un valor alt incrementa les possibilitats de que les passades donades pel jugador arribin al destí sense ser interceptades.</li>
                                    <li><strong>Precisió (PRE):</strong> precisió al rematar a porteria. Amb major precisió, major probabilitat de que el tir acabi sent gol.</li>
                                    <li><strong>Velocitat (VEL):</strong> velocitat del jugador al correr.</li>
                                    <li><strong>Força (FOR):</strong> jugadors amb més força poden disparar a porteria des de més lluny. Jugadors amb poca força deuràn arribar més a prop de la porteria per poder rematar.</li>
                                    <li><strong>Entrada (ENT):</strong> habilitat per treure-li la pilota a un rival.</li>
                                    <li><strong>Experiència (EXP):</strong> al jugar partits de campeonat (amistosos e partidos amb sparrings no conten) i al entrenar, els jugadors guanyen experiència. Al arriban als 100 punts de experiència pujen de nivell millorant els seus atributs.</li>
                                </ul>
                                <p>Els jugadors poden retirar-se entre els 33 i els 40 anys. Si el teu equip té :max_players jugadors o menys, cada cop que es retiri un jugador, un juvenil será incorporat.</p>
                                <p>Algunes vegades trobaràs al costat el nom del jugador unes icones que volen dir el següent:</p>',
    'shopping'              => '<p>Pots ajudar el teu equip amb els items del <strong>Shopping</strong></p>
                                <p>Aquí trobaràs <strong>In-filtrum</strong> i <strong>In-filtrum Plus</strong> per que els teus jugadors puguin recuperar energies, o contractar un <strong>Personal trainer</strong> per que entreni al teu equip durant una setmana.</p>
                                <p>Els items del <strong>Shopping</strong> es compran utilizant <strong>Fúlbos</strong></p>',
    'strategy'              => '<p>A la página <strong>Estratègia</strong> determines com jugarà el teu equip triant una formació (3-4-3, 4-4-2, etc.) i quins seran els jugadors titulars i els suplents. Simplement tens que arrossegar els jugadors fins a la posició que vols que ocupin al camp, o fins i tot al banc de suplents.</p>
                                <p>Els suplents seran utilizats en reeemplaçament d\'algun titular que estigui molt cansat o lesionat, i també guanyen experiència encara que  no juguin. LLavors tria bé qui seran els suplents.</p>
                                <p>Quan un jugador necessita se substituït, en el seu lloc entrarà el primer suplent (de esquerda a dreta) que jugui a la mateixa posició que el que es retira (POR, DEF, MIT, DAV). Si no n\'hi ha cap suplent a la mateixa posició entrará  l\'últim de la llista.</p>',
    'team'                  => '<p>A la pàgina <strong>Equip</strong> pots veure i editar la informació del teu equip. Podràs editar el nom del tu equip, els seus colors o canviar el escut tantes vegades com vulguis però intenta no perdre la identitat que o representa.</p>',
    'tournaments'           => '<p>Tota la informació dels tornejos que jugas és en aquesta pàgina: resultats, próxims partits, tabla de posicions i tabla de golejadors.</p>
                                <p>Els partits del torneig es jugan dilluns, dimecres i divendres a les 20:00 hores (horari da l\'Argentina, -3 GMT).</p>',
    'train'                 => '<p>Cada 24 hores podràs entrenar al teu equip per que els teus jugadors guanyen més experiència i recuperin energia prenent el botò a la part superior de la pantalla.</p>
                                <p>Si entrenes tots els dias la quantitat de experiència i energia será major.</p>
                                <p>¡No t\'oblidis d\'entrenar tots els dias!</p>
                                <p>Si no pots entrenar tots els dias pots utilitzar <strong>Fúlbos</strong> per a que els teus jugadors recuperin energies, o inclús contractar un personal trainer per que entreni al equip por tu.</p>',
    'transfers_market'      => '<p>El <strong>Mercat de fitxatges</strong> es el lloc on pots trobar els jugadors transferibles, i fer ofertes per comprar-los.</p>
                                <p>Els jugadors transferibles son posats a la venda al millor postor durant :transferable_period dies. Quan el període termini el equip que tingui fet la oferta més alta es quedará amb el jugador.</p>
                                <p>El valor que es pagui pel jugador será el seu nou valor de mercat i el seu salari el :salary_rate% d\'aquest valor.</p>',
];