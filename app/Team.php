<?php

namespace App;

use App\TeamFundMovement;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'user_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'formation' => 'array',
        'playable'  => 'boolean'
    ];

    /**
     * Carbon instance fields
     */
    protected $dates = [
        'last_trainning', 'trainer', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user', 'players', 'strategy', 'last_trainning', 'trainer', 'trainning_count', 'created_at', 'updated_at'
    ];

    /**
     * Attributes to be append to arrays.
     *
     * @var array
     */
    protected $appends = [
        'average', 'user_name'
    ];

    /**
     * Get team funds movements
     *
     * @return Collection TeamFundMovement
     */
    public function fundMovements()
    {
        return $this->hasMany(TeamFundMovement::class)->orderBy('created_at', 'DESC');
    }

    /**
     * Get the players associated with the team
     *
     * @return Collection Player
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Get the team's positions
     *
     * @return Collection TournamentPosition
     */
    public function positions()
    {
        return $this->hasMany(TournamentPosition::class)->orderBy('category_id');
    }

    /**
     * Get the team's strategy
     *
     * @return Strategy
     */
    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    /**
     * Get the user associated with the team
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate team's spending margin
     *
     * @param integer $value
     * @param boolean $funds
     * @return integer
     */
    public function calculateSpendingMargin($value = 0, $funds = TRUE)
    {
        $values = [
            \Config::get('constants.MAX_PLAYER_VALUE'),
            \Config::get('constants.MAX_TEAM_VALUE') - $this->value + $value
        ];
        if ($funds) {
            $values[] = $this->funds - PlayerSelling::where('best_offer_team', '=', $this->id)->sum('best_offer_value');
        }

        return max(0, min($values));
    }

    /**
     * Create new player in the team
     *
     * @return Player
     */
    public function createPlayer($number, $position, $max_age = FALSE)
    {
        return Player::create($this->id, $number, $position, $max_age);
    }

    /**
     * Get a free number for a player
     *
     * @return integer
     */
    public function freeNumber()
    {
        $numbers = [];
        for ($i = 1; $i <= 99; $i++) {
            $numbers[$i] = 1;
        }
        foreach ($this->players as $p) {
            unset($numbers[$p['number']]);
        }
        return array_rand($numbers);
    }

    /**
     * Get team's average attribute
     *
     * @return integer
     */
    public function getAverageAttribute()
    {
        if (count($this->formation) == 0) {
            return 0;
        } else {
            $lineup = array_slice($this->formation, 0, 11);
            $total = $count = 0;
            if ($this->user_id > 1) {
                foreach ($this->players as $player) {
                    if (in_array($player->id, $lineup))
                    {
                        $count++;
                        $total += $player->average;
                    }
                }
            } else {
                foreach ($lineup as $p) {
                    if ($p > 0) {
                        $count++;
                        $player = Player::find($p);
                        $total += $player->average;
                    }
                }
            }
            if ($count > 0) {
                return (int)($total / $count);
            } else {
                return 0;
            }
        }
    }

    /**
     * Get if a team can hire new players
     *
     * @return boolean
     */
    public function getCanHireAttribute()
    {
        return (\Config::get('constants.MAX_TEAM_PLAYERS') - $this->players()->count() - DB::table('player_sellings')->where('best_offer_team', $this->id)->count()) > 0;
    }

    /**
     * Funds with HTML format
     *
     * @return String
     */
    public function getFormattedFundsAttribute()
    {
        $funds = number_format($this->funds, 0) . ' $';
        if ($this->funds < 0) {
            $funds = '<span style="color:#f00;">' . $funds . '</span>';
        }

        return $funds;
    }

    public function getFormationAttribute($value)
    {
        $value = json_decode($value);
        if (!empty($value)) {
            return array_map('intval', $value);
        } else {
            return [];
        }
    }

    /**
     * Get formation with Player objects
     *
     * @return Array Player
     */
    public function getFormationObjectsAttribute()
    {
        $formation = [null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null];
        foreach($this->players as $player) {
            $pos = array_search($player->id, $this->formation);
            if ($pos !== FALSE) {
                $formation[$pos] = $player;
            } else {
                $formation[] = $player;
            }
        }

        return $formation;
    }

    /**
     * Injured players in the team
     *
     * @return Collection Player
     */
    public function getInjuredPlayersAttribute()
    {
        return $this->players()->where('recovery', '>', 0)->get();
    }

    /**
     * Check if team is in train spam
     *
     * @return boolean
     */
    public function getInTrainningSpamAttribute()
    {
        if (is_null($this->last_trainning) || $this->last_trainning->timestamp < $_SERVER['REQUEST_TIME'] - \Config::get('constants.TIME_TO_TRAIN') - \Config::get('constants.TRAIN_TIME_SPAM')) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Get live match for team
     *
     * @return Matches
     */
    public function getLiveMatchAttribute()
    {
        $match = Matches::where(function ($query) {
                            $query->where('local_id', $this->id)
                                  ->orWhere('visit_id', $this->id);
                        })->where('type_id', '>', 2)->where('created_at', '>=', Carbon::now()->subMinutes(\Config::get('constants.LIVE_MATCH_DURATION')))->first();

        return $match;
    }

    /**
     * Get number of players that can be sold
     *
     * @return integer
     */
    public function getSellabelCountAttribute()
    {
        return $this->players()->leftJoin('player_sellings', 'players.id', '=', 'player_sellings.player_id')->whereNull('player_sellings.id')->count() - \Config::get('constants.MIN_TEAM_PLAYERS');
    }

    /**
     * Get SVG file for the team shield
     *
     * @return String
     */
    public function getShieldFileAttribute()
    {
        $file = '/img/shield/shield-' . sprintf('%02d', $this->shield) . '.svg';

        return $file;
    }

    /**
     * Get the public strategy info
     *
     * @return Array
     */
    public function getStrategyPublicAttribute()
    {
        $team = $this;
        $match = Matches::where(function($query) use ($team) {
            $query->where('local_id', '=', $team->id);
            $query->orWhere('visit_id', '=', $team->id);
        })
        ->where('type_id', '>', 1)
        ->latest()
        ->first();

        /**
         * Load strategy from last official match
         */
        $strategy = [];
        $data = getMatchLog($match['logfile']);
        if (!empty($data)) {
            if ($match->local_id == $this->id) {
                foreach ($data['local']['formation'] as $form) {
                    $player = Player::where('team_id', '=', $this->id)->where('number', '=', $form['number'])->first();
                    $strategy[] = [
                        'left' => number_format($form['top'] * 1.5, 2),
                        'top' => number_format($form['left'], 2),
                        'position' => (!empty($player) ? $player->position : ''),
                        'number' => (!empty($player) ? $form['number'] : ''),
                    ];
                }
            } else {
                foreach ($data['visit']['formation'] as $form) {
                    $player = Player::where('team_id', '=', $this->id)->where('number', '=', $form['number'])->first();
                    $strategy[] = [
                        'left' => (100 - $form['top']) * 1.5,
                        'top' => 100 - $form['left'],
                        'position' => (!empty($player) ? $player->position : ''),
                        'number' => (!empty($player) ? $form['number'] : ''),
                    ];
                }
            }
        }

        /**
         * If $strategy is empty, load it from the DB
         */
        if (empty($strategy)) {
            $strategy = $this->strategy->positionsToPercetages();
            for ($i = 0; $i < 11; $i++) {
                $player = Player::find($this->formation[$i]);
                $strategy[$i]['position'] = (!empty($player) ? $player['position'] : '');
                $strategy[$i]['number'] = (!empty($player) ? $player['number'] : '');
            }
        } else { // else, cut it to 11 players
            $strategy = array_slice($strategy, 0, 11);
        }

        return $strategy;
    }

    /**
     * Check if team can train
     *
     * @return boolean
     */
    public function getTrainableAttribute()
    {
        if (
            (
                $this->last_trainning &&
                $_SERVER['REQUEST_TIME'] - $this->last_trainning->timestamp < \Config::get('constants.TIME_TO_TRAIN')
            ) ||
            $this->trainer > Carbon::now()
        ) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Get remaining time for the trainer
     *
     * @return String
     */
    public function getTrainerRemainingAttribute()
    {
        $now = Carbon::now();
        if ($this->trainer <= $now) {
            return 0;
        }

        $diff = $now->diffInDays($this->trainer);
        if ($diff > 0) {
            return $diff . ' días';
        }

        $diff = $now->diffInHours($this->trainer);
        if ($diff > 0) {
            return $diff . ' horas';
        }

        $diff = $now->diffInMinutes($this->trainer);
        if ($diff > 0) {
            return $diff . ' minutos';
        }

        $diff = $now->diffInSeconds($this->trainer);
        if ($diff > 0) {
            return $diff . ' segundos';
        }
    }

    /**
     * Remaining time to become trainable
     *
     * @return integer
     */
    public function getTrainableRemainingAttribute()
    {
        if ($this->trainable) {
            return 0;
        } else if ($this->last_trainning) {
            return \Config::get('constants.TIME_TO_TRAIN') - ($_SERVER['REQUEST_TIME'] - $this->last_trainning->timestamp);
        } else {
            return \Config::get('constants.TIME_TO_TRAIN');
        }
    }

    /**
     * Get the team's trophies
     *
     * @return Array TournamentPosition
     */
    public function getTrophiesAttribute()
    {
        $positions = $this->positions;
        $trophies = [];
        foreach ($positions as $position) {
            if (!$position->category->isOpen) {
                $trophies[] = $position->makeVisible('tournamentName');
            }
        }

        return $trophies;
    }

    /**
     * Get team's user name
     *
     * @return String
     */
    public function getUserNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Get team's value
     *
     * @return integer
     */
    public function getValueAttribute()
    {
        return $this->players()->sum('value');
    }

    /**
     * Keep train run
     *
     * @return integer trainning points
     */
    public function keepTrainning()
    {
        $this->last_trainning = Carbon::now();
        $this->train(TRUE);

        return \Config::get('constants.TRAINNING_POINTS') * min(5, $this->trainning_count);
    }

    /**
     * Process money income
     *
     * @param $amount
     * @param $movement_type
     * @param array $variables
     */
    public function moneyMovement($amount, $movement_type, $variables = [])
    {
        // Limit the funds in the team
        $amount = min($amount, \Config::get('constants.MAX_TEAM_FUNDS') - $this->funds);

        $this->funds += $amount;
        $this->save();
        TeamFundMovement::create([
            'team_id'       => $this->id,
            'amount'        => $amount,
            'balance'       => $this->funds,
            'movement_type' => $movement_type,
            'variables'     => $variables
        ]);
    }

    /**
     * Pay salaries
     *
     * @return void
     */
    public function paySalaries()
    {
        $salaries = (int)($this->players()->sum('value') * \Config::get('constants.PLAYERS_SALARY'));
        $this->moneyMovement(-$salaries, \Config::get('constants.MONEY_MOVEMENTS_OUTCOME_SALARIES_PAID'));
    }

    /**
     * Replace player with a new one
     *
     * @param $player_id integer
     * @return void
     */
    public function replacePlayer($player_id)
    {
        $player = $this->players->find($player_id);

        if ($this->players->count() <= \Config::get('constants.MAX_PLAYERS_REPLACE')) {
            $arqs = $this->players->where('position', '=', 'ARQ')->count();
            $defs = $this->players->where('position', '=', 'DEF')->count();
            $meds = $this->players->where('position', '=', 'MED')->count();
            $atas = $this->players->where('position', '=', 'ATA')->count();
            $number = $this->freeNumber();

            $position = FALSE;
            switch ($player->position) {
                case 'ARQ':
                    if ($arqs <= 2) {
                        $position = 'ARQ';
                    }
                    break;
                case 'DEF':
                    if ($defs <= 5) {
                        $position = 'DEF';
                    }
                    break;
                case 'MED':
                    if ($meds <= 5) {
                        $position = 'MED';
                    }
                    break;
                case 'ATA':
                    if ($atas <= 3) {
                        $position = 'ATA';
                    }
                    break;
            }

            if (!$position) {
                $probs = [];
                if ($arqs < 3) {
                    $probs[] = 'ARQ';
                }
                if ($defs < 10) {
                    $probs[] = 'DEF';
                }
                if ($meds < 10) {
                    $probs[] = 'MED';
                }
                if ($atas < 6) {
                    $probs[] = 'ATA';
                }
                $position = $probs[array_rand($probs)];
            }

            $newbie = $this->createPlayer($number, $position, TRUE);

            $pos = array_search($player->id, $this->formation);
            if (!empty($this->formation) && $pos !== FALSE) {
                $formation = $this->formation;
                $formation[$pos] = $newbie->id;
                $this->formation = $formation;
                $this->save();
            }

            Notification::create([
                'user_id' => $this->user->id,
                'title' => $player->full_name . ' se ha retirado',
                'message' => $player->full_name . ' ha decidido dejar las canchas y <a href="/jugador/' . $newbie->id . '/">' . $newbie->full_name . '</a> ha sido incorporado al equipo.',
            ]);
        } else {
            $pos = array_search($player->id, $this->formation);
            if (!empty($this->formation) && $pos !== FALSE) {
                $replacement = $this->players->whereNotIn('id', $this->formation)->random(1);
                $formation = $this->formation;
                $formation[$pos] = $replacement[0]->id;
                $this->formation = $formation;
                $this->save();
            }

            Notification::create([
                'user_id' => $this->user->id,
                'title' => $player->full_name . ' se ha retirado',
                'message' => $player->full_name . ' ha decidido dejar las canchas a los ' . $player->age . ' años.',
            ]);
        }

        $player->delete();
    }

    /**
     * Train the team
     *
     * @param boolean $force Force trainning (used for personal trainer)
     * @return boolean Team trained
     */
    public function train($force = FALSE)
    {
        if ($force || $this->trainable) {
            if (!$this->inTrainningSpam) {
                $this->trainning_count = 1;
            } else {
                $this->trainning_count++;
            }
            $trainning_points = \Config::get('constants.TRAINNING_POINTS') * min(5, $this->trainning_count);

            DB::table('players')->where('team_id', $this->id)->where('recovery', 0)->whereNull('deleted_at')->increment('experience', $trainning_points);
            DB::table('players')->where('team_id', $this->id)->where('recovery', 0)->where('stamina', '<', 100)->whereNull('deleted_at')->increment('stamina', $trainning_points);
            DB::table('players')->where('team_id', $this->id)->where('stamina', '>', 100)->whereNull('deleted_at')->update(['stamina' => 100]);
            $players = $this->players()->where('experience', '>=', 100)->get();
            foreach ($players as $player) {
                $player->upgrade();
            }

            $this->last_trainning = Carbon::now();
            $this->save();

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Update if the team meats the requirements to play a match
     *
     * @return void
     */
    public function updatePlayable()
    {
        $playable = FALSE;

        if (count($this->formation) >= 11) {
            $lineup = array_slice($this->formation, 0, 11);
            $count = 0;
            foreach ($lineup as $player) {
                if ($player != 0) {
                    $count++;
                }
            }

            if ($count == 11) {
                $playable = TRUE;
            }
        }

        $this->playable = $playable;
        $this->save();
    }
}
