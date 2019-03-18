<?php

return [
    'finances'              => '<p>Cada movimento em dinheiro é registrado na seção <strong>Finanças</strong>, para que você possa ver todos os pagamentos recebidos e enviados.</p>
                                <p>Cada jogo oficial que você jogar terá renda para os ingressos vendidos. O número de ingressos vendidos dependerá da posição de ambos times no torneio e o dinheiro será dividido igualmente.</p>
                                <p>Os domingos são dias de pagamento e você tem que pagar os salários dos seus jogadores. O salário é o :salary_ratio% do valor de mercado de cada jogador.</p>',
        'financial_fair_play'   => '<p>Para evitar movimentos duvidosos de dinheiro e o acúmulo de poder por alguns times, algumas regras de <strong>Fair Play Financeiro</strong> foram implementadas para tornar o jogo mais justo para todos os usuários.</p>
                                <ol>
                                    <li>Nenhum time pode ter menos de :min_team_players jogadores.</li>
                                    <li>Nenhum time pode ter mais de :max_team_players jogadores.</li>
                                    <li>Nenhum time pode contar com mais de :max_team_funds.</li>
                                    <li>O valor total dos jogadores em um time não pode ser maior que :max_team_value.</li>
                                    <li>O valor de mercado de um jogador não pode ser maior que :max_player_value.</li>
                                    <li>Os times com :max_players_replace jogadores ou menos receberão um juvenil cada vez que um jogador se aposentar.</li>
                                </ol>',
    'friendlies'            => '<p>Aqui você pode disputar partidas amistosas contra Sparrings de 40 ou 60 pontos para testar sua estratégia ou até mesmo jogar contra outros usuários.</p>
                                <p>Você pode jogar quantas partidas quiser contra os Sparrings, mas só poderá jogar uma vez a cada 24 horas contra cada usuário.</p>
                                <p>Nesses <strong>Jogos amistosos</strong>, os jogadores não perdem resistência, mas também não ganham experiência.</p>
                                <p>Você também pode clicar no ícone de estatísticas (<span class="fa-bar-chart"></span>) para ver suas estatísticas em relação a cada time.</p>',
    'locker_room'           => '<p>No seu <strong>vestiário</strong>, você pode ter uma visão rápida da situação atual de seu time, quando é sua próxima partida no torneio e os resultados das suas últimas partidas.</p>',
    'player'                => '<p>Ao clicar no nome de um jogador, você pode acessar suas informações completas.</p>
                                <p>Se for um jogador do seu time, você pode melhorar o contrato, declarar transferível ou liberá-lo.</p>
                                <p>Quando o contrato melhora, o jogador aumenta seu valor de mercado, mas lembre-se de que seu salário será :salary_rate% desse novo valor.</p>
                                <p>Um jogador transferível será colocado à venda por :transferable_period days para o maior lance. Se não houver ofertas no final do tempo, o jogador é retirado do mercado.</p>
                                <p>Ao deixar um jogador livre, ele não fará mais parte do seu time e você não pagará mais seu salário. Para poder libertá-lo, você deve pagar a rescisão do contrato. Os jogadores livres são colocados no mercado, possivelmente com um valor de mercado menor do que eles tinham.</p>',
    'players'               => '<p>Você pode ver a lista de jogadores e seus atributos na página <strong>Jogadores</strong>. Em dispositivos móveis, apenas uma lista reduzida de atributos é mostrada, mas ao clicar no nome de um jogador, você verá todos os atributos dele.</p>
                                <p>Os atributos dos jogadores são os seguintes:</p>
                                <ul style="margin-left:10px;">
                                    <li><strong>Idade:</strong> os jogadores iniciam suas carreiras entre 17 e 20 anos e se aposentam entre 33 e 40 anos. No final de um torneio, os jogadores ficam um ano mais velhos. No início de uma nova temporada, você será notificado se um jogador decide se aposentar no final dela.</li>
                                    <li><strong>Posição (POS):</strong> posição na qual o jogador tem melhor desempenho (Goleiro, Defensor, Meio-Campo ou Atacante).</li>
                                    <li><strong>Energia (ENE):</strong> energia determina a fadiga do jogador. Um jogador com menos energia faz menos nos jogos. Os jogadores gastam energia em jogos de torneios e depois recuperam a energia diária e durante os treinos. Um jogador muito cansado será substituído durante o jogo.</li>
                                    <li><strong>Média (MED):</strong> é a média ponderada dos atributos dos jogadores. A média é calculada de forma diferente para cada posição, tendo em conta o que é necessário para jogar bem em cada uma delas.</li>
                                    <li><strong>Goleiro (GOL):</strong> a habilidade do jogador em defender o gol (por exemplo, defender os chutes).</li>
                                    <li><strong>Defensa (DEF):</strong> capacidade de cortar um ataque rival (por exemplo, interceptar um passe).</li>
                                    <li><strong>Driblar (DRB):</strong> capacidade de impedir que os oponentes roubem a bola ao correr com ela.</li>
                                    <li><strong>Cabeçada (CAB):</strong> quão bom o jogador é com a cabeça em ataque e defesa.</li>
                                    <li><strong>Pular (PUL):</strong> os jogadores que pulam melhor são mais propensos a ganhar a bola nos tiros livres e nos escanteios.</li>
                                    <li><strong>Passe (PAS):</strong> um valor alto aumenta as chances de que os passes dados pelo jogador cheguem ao destino sem serem interceptados.</li>
                                    <li><strong>Precisão (PRE):</strong> precisão ao chutar no gol. Quanto mais preciso, maior a probabilidade de que o tiro termine em gol.</li>
                                    <li><strong>Velocidade ​​(VEL):</strong> a velocidade do jogador para correr.</li>
                                    <li><strong>Força (FOR):</strong> jogadores com mais força podem chutar no gol desde mais longe. Jogadores com pouca força precissam chegar mais perto do gol para chutar.</li>
                                    <li><strong>Entrada (ENT):</strong> capacidade de tirar a bola do adversário.</li>
                                    <li><strong>Experiência (EXP):</strong> ao jogar torneios (amistosos e jogos contra sparrings não contam) e ao treinar os jogadores ganham experiência. Quando atingem 100 pontos de experiência aumentam de nível melhorando seus atributos.</li>
                                </ul>
                                <p>Players can retire between 33 and 40 years old. If your team has :max_players players or less, each time a player retires, a youth player will be incorporated.</p>
                                <p>Os jogadores podem se aposentar entre os 33 e os 40 anos de idade. Se sua equipe tiver :max_players ou menos, cada vez que um jogador se aposentar um jogador juvenil será incorporado no seu time.</p>
                                <p>Às vezes você encontrará alguns ícones ao lado do nome do jogador que significam o seguinte:</p>',
    'shopping'              => '<p>Você pode ajudar sua equipe com os itens no <strong>Shopping</strong></p>
                                <p>Alí você encontrará <strong>In-filtrum</strong> e <strong>In-filtrum Plus</strong> para ajudar os seus jogadores recuperar pontos de energia, ou contratar um <strong>Personal trainer</strong> para treinar seu time por uma semana.</p>
                                <p>Os itens no <strong>Shopping</strong> são comprados usando <strong>Fúlbos</strong></p>',
    'strategy'              => '<p>Na página <strong>Estratégia</strong>, você determina como seu time irá jogar escolhendo uma formação (3-4-3, 4-4-2, etc.) e quais serão os jogadores iniciais e os substitutos. Simplesmente arraste os jogadores e solte-os na posição que você quer que eles ocupem na quadra, ou no banco substituto.</p>
                                <p>Os substitutos serão usados para substituir outro jogador que esteja muito cansado ou ferido. Substitutos também ganham experiência mesmo se eles não jogarem, então escolha bem os substitutos.</p>
                                <p>Quando um jogador precisa ser substituído, o primeiro substituto (da esquerda para a direita) que joga na mesma posição daquele que está saindo (GOL, DEF, MEI, ATA) entrará. Se não houver substituto na mesma posição, o último da lista entrará.</p>',
    'team'                  => '<p>In the <strong>Team</strong> page, you can view and edit your team\'s information. You can edit the name of your team, it\'s colors or change the shield as many times as you want but try not to lose the identity that represents it.</p>',
    'tournaments'           => '<p>Todas as informações dos torneios que você joga estão nesta página: resultados, próximas partidas, posições e tabela de artilheiros.</p>
                                <p>As partidas do torneio são disputadas às segundas, quartas e sextas às 20 horas. (Horário da Argentina, -3 GMT)</p>',
    'train'                 => '<p>A cada 24 horas, você pode treinar sua equipe para que seus jogadores ganhem mais experiência e recuperem energia pressionando o botão na parte superior da tela.</p>
                                <p>Se você treinar todos os dias, a quantidade de experiência e energia será maior.</p>
                                <p>Não se esqueça de treinar todos os dias!</p>
                                <p>Se você não consegue acompanhar todos os dias, você pode usar <strong>Fúlbos</strong> para que seus jogadores recuperarem energias, ou até mesmo contratar um personal trainer para treinar o time para você.</p>',
    'transfers_market'      => '<p>O <strong>Mercado de Transferências</strong> é o lugar onde você pode encontrar os jogadores transferíveis e fazer ofertas para comprá-los.</p>
                                <p>Os jogadores transferíveis são colocados à venda pelo maior lance durante :transferable_period dias. Quando o período terminar, o time que fez o lance mais alto comprará o jogador.</p>
                                <p>O valor que é pago pelo jogador será seu novo valor de mercado e seu salário o :salary_rate% desse valor.</p>',
];