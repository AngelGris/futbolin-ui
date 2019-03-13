<?php

return [
    'finances'              => '<p>Every money movement is registered in the <strong>Finances</strong> section, so you can see all your incoming and outgoing payments.</p>
                                <p>Each official match you play you will have incomes for tickets sold. The number of tickets sold will depend on the position of both teams in the tournament and the money will be split equally.</p>
                                <p>Sundays are paydays and you have to pay your players\' salaries. Their salary is the :salary_ratio% of their market value.</p>',
    'financial_fair_play'   => '<p>To avoid dubious money movements and the accumulation of power by a few teams, some <strong>Financial Fair Play</strong> rules were implemented in order to make the game more equitable for all users.</p>
                                <ol>
                                    <li>No team can have less than :min_team_players players.</li>
                                    <li>No team can have more than :max_team_players players.</li>
                                    <li>No team can count with found over :max_team_funds.</li>
                                    <li>The total value of the players in a team cannot be grater than :max_team_value.</li>
                                    <li>The market value of a player con not be greater than :max_player_value.</li>
                                    <li>The teams with :max_players_replace players or less, will receive a juvenile each time a player retires.</li>
                                </ol>',
    'friendlies'            => '<p>Here you can play friendly matches against 40 or 60 average points Sparrings to try your strategy, or even play against other users.</p>
                                <p>You can play as many matches against Sparrings as you want, but you will only be able to play once every 24 hours against each user.</p>
                                <p>In those <strong>Friendlies</strong> the players don\'t loose stamina but also don\'t gain experience.</p>
                                <p>You can also click on the statistics icon (<span class="fa fa-bar-chart"></span>) to see your stats against each team.</p>',
    'locker_room'           => '<p>In your <strong>Locker room</strong> you can have a quick view of the current situation of your team, when is your next match in the tournament and the results for your last matches.</p>',
    'player'                => '<p>By clicking on the name of a player you can access their complete information.</p>
                                <p>If it is a player in your team, you can improve the contract, declare it transferable or set it free.</p>
                                <p>When the contract improves the player increases his market value, but remember that his salary will be :salary_rate% of that value.</p>
                                <p>A transferable player will be put on sale for :transferable_period days to the highest bidder. If there are no offers at the end of the time the player is removed from the market.</p>
                                <p>By setting a player free, it will no longer be part of your squad and you will not pay it\'s salary any more. To be able to set it free, you must pay the termination of your contract. Free players are placed in the market, possibly with a lower market value than they had.</p>',
    'players'               => '<p>You can see the list of players and their attributes in the <strong>Players</strong> page. In mobile devices only a reduced list of attributes is shown, but by clicking on a player\'s name you will see all their attributes.</p>
                                <p>The attributes of the players are the following:</p>
                                <ul style="margin-left:10px;">
                                    <li><strong>Age:</strong> Players start their careers between 17 and 20 years old and retire between 33 and 40 years old. At the end of a tournament the players get one year older. At the beginning of a new season you will be notified if a player decides to retire at the end of it.</li>
                                    <li><strong>Position (POS):</strong> position in which the player performs best (Goalkeeper, Defender, Midfielder or Forward).</li>
                                    <li><strong>Stamina (STA):</strong> stamina determines the player\'s fatigue. A player with less stamina performs less in games. Players spend stamina in tournament games and then recover stamina daily and when training. A very tired player will be replaced during the game.</li>
                                    <li><strong>Average (AVG):</strong> is the weighted average of the attributes of the players. The average is calculated differently for each position, taking into account what is needed to play well in each of them.</li>
                                    <li><strong>Goalkeeping (GKE):</strong> the player\'s ability to defend the goal (eg, save the shots).</li>
                                    <li><strong>Defending (DEF):</strong> ability to cut a rival attack (eg, intercept a pass).</li>
                                    <li><strong>Dribbling (DRB):</strong> ability to prevent opponents from stealing the ball when running with it.</li>
                                    <li><strong>Heading (HED):</strong> how good the player is heading in both attack and defense.</li>
                                    <li><strong>Jumping (JUM):</strong> players who jump better are more likely to head a corner or a free kick in both attack and defense.</li>
                                    <li><strong>Passing (PAS):</strong> a high value increases the chances that the passes given by the player reach destination without being intercepted.</li>
                                    <li><strong>Precision (PRE):</strong> precision when shooting to teh goal. The more precise the higher the probability that the shot ends up scoring.</li>
                                    <li><strong>Speed ​​(SPE):</strong> the player\'s speed when running.</li>
                                    <li><strong>Strength (STR):</strong> Players with more strength can shoot from a further. Players with little strength need to get closer to the goal in order to finish.</li>
                                    <li><strong>Tackling (TAK):</strong> ability to take the ball away from an opponent.</li>
                                    <li><strong>Experience (EXP):</strong> when playing tournament games (friendlies and sparring matches do not count) and when training players gain experience. When they reach 100 experience points they level up improving their attributes.</li>
                                </ul>
                                <p>Players can retire between 33 and 40 years old. If your team has :max_players players or less, each time a player retires, a youth player will be incorporated.</p>
                                <p>Sometimes you will find some icons next to the player\'s name that mean the following:</p>',
    'shopping'              => '<p>You can help your team with the items in the <strong>Shopping</strong></p>
                                <p>Here you will find <strong>In-filtrum</strong> and <strong>In-filtrum Plus</strong> to help your player regain their stamina, or hire a <strong>Personal trainer</strong> to train your team for a week.</p>
                                <p>The items in the <strong>Shopping</strong> are purchased using <strong>Fúlbos</strong></p>',
    'strategy'              => '<p>On the <strong>Strategy</strong> page, you determine how your team will play by choosing a formation (3-4-3, 4-4-2, etc.) and which will be the starting players and the substitutes. Simply drag the players and drop them in the position you want them to occupy on the court, or to the substitutes bench.</p>
                                <p>The substitutes will be used to replace another player who is too tired or injured. Substitutes also gain experience even if they do not play, so choose well who the substitutes will be.</p>
                                <p>When a player needs to be replaced, the first substitute (from left to right) who plays in the same position as the one going out (GOA, DEF, MID, FOR) will enter. If there is no substitute in the same position, the last one from the list will enter.</p>',
    'team'                  => '<p>In the <strong>Team</strong> page, you can view and edit your team\'s information. You can edit the name of your team, it\'s colors or change the shield as many times as you want but try not to lose the identity that represents it.</p>',
    'tournaments'           => '<p>All the information of the tournaments you play is in this page: results, next matches, positions and scorers table.</p>
                                <p>The tournament matches are played on Mondays, Wednesdays and Fridays at 8:00p.m. (Argentine time, -3 GMT)</p>',
    'train'                 => '<p>Every 24 hours you can train your team so that your players gain more experience and recover stamina by pressing the button at the top of the screen.</p>
                                <p>If you train every day, the amount of experience and stamina will be greater.</p>
                                <p>Don\'t forget to train every day!</p>
                                <p>If you can not catch up every day you can use <strong>Fúlbos</strong> for your players to recover their energy, or even hire a personal trainer to train the team for you.</p>',
    'transfers_market'      => '<p>The <strong>Transfers Market</strong> is the place where you can find the transferable players, and make offers to buy them.</p>
                                <p>Transferable players are put on sale to the highest bidder during :transferable_period days. When the period ends the team that made the highest bid will buy the player.</p>
                                <p>The value that is paid for the player will be it\'s new market value and it\'s salary :salary_rate% of that value.</p>',
];