<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    /**
     * Disable timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get the teams using this strategy
     *
     * @return Collection Team
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Convert players positions to percentages
     *
     * @return Array
     */
    public function positionsToPercetages()
    {
        $positions = [];
        for ($i = 1; $i <= 11; $i++) {
            $positions[] = [
                'left' => ($this->{sprintf('j%02d_start_y', $i)} * 100 / 120 ) * 1.5,
                'top' => $this->{sprintf('j%02d_start_x', $i)} * 100 / 90,
            ];
        }
        return $positions;
    }
}
