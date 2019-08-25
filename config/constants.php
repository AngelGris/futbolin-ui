<?php
return [
    'TIME_TO_TRAIN' => 86400, // 24 hours
    'TRAINNING_POINTS' => 2, // trainning points increase per step
    'TRAIN_TIME_SPAM' => 86400, // time spam before train counter resets

    'LIVE_MATCH_DURATION' => 15, // number of minutes for the live match simmulation

    'TEAMS_PER_CATEGORY' => 10, // number of teams to play in each category
    'DEGRADES_PER_CATEGORY' => 2, // minimum number of teams degraded per category
    'YELLOW_CARDS_SUSPENSION' => 3, // number of yellow cards a player must have to get a suspension

    'USER_INACTIVE' => 1209600, // seconds to consider a user inactive

    'CURRENCY' => 'USD', // currency used to buy credits
    'CREDITS_SELL_VALUE' => 200000, // value of credits for sell

    'MAX_TEAM_FUNDS' => 2000000000, // Maximum funds a team can have
    'MAX_PLAYER_VALUE' => 15000000, // Maximum player value
    'MIN_TEAM_PLAYERS' => 18, // Minimum number of players in a team
    'MAX_TEAM_PLAYERS' => 30, // Maximum number of players in a team
    'MAX_TEAM_VALUE' => 50000000, // Maximum total value of players in a team
    'MAX_PLAYERS_REPLACE' => 21, // Maximum number of players a team has to get youth players on retirement
    'STADIUM_SIZE' => 20000, // Stadium capacity
    'TICKET_VALUE' => 10, // Value paid for each ticket
    'PLAYERS_SALARY' => 0.015, // Rate of the player value that is his salary
    'PLAYERS_TRANSFERABLE_PERIOD' => 3, // Number of days a player remains transferable
    'FREE_PLAYERS_GENERATE' => 3, // Number of free players to generate each day

    // Money movements
    'MONEY_MOVEMENTS_INCOME_SELLING_TICKETS'        => 1,
    'MONEY_MOVEMENTS_INCOME_SELLING_PLAYER'         => 2,
    'MONEY_MOVEMENTS_INCOME_SELLING_CREDITS'        => 3,
    'MONEY_MOVEMENTS_OUTCOME_SALARIES_PAID'         => 4,
    'MONEY_MOVEMENTS_OUTCOME_CONTRACT_TERMINATED'   => 5,
    'MONEY_MOVEMENTS_OUTCOME_BUYING_PLAYER'         => 6,

    // Notifications
    'NOTIFICATIONS_PLAYER_RETIRED_AND_REPLACED'     => 1,
    'NOTIFICATIONS_PLAYER_RETIRED'                  => 2,
    'NOTIFICATIONS_OFFER_EXCEEDED'                  => 3,
    'NOTIFICATIONS_YOU_BOUGHT_PLAYER'               => 4,
    'NOTIFICATIONS_NO_OFFER_FOR_PLAYER'             => 5,
    'NOTIFICATIONS_PLAYER_TRANSFERRED'              => 6,

    // Push notifications screens
    'PUSH_NOTIFICATIONS_SCREEN_MARKET'              => 1,
    'PUSH_NOTIFICATIONS_SCREEN_PLAYERS'             => 2,
];