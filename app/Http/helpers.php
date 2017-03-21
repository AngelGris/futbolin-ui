<?php
function randomGauss($min, $max, $std_deviation, $step=1) {
    $rand1 = (float)mt_rand()/(float)mt_getrandmax();
    $rand2 = (float)mt_rand()/(float)mt_getrandmax();
    $gaussian_number = sqrt(-2 * log($rand1)) * cos(2 * M_PI * $rand2);
    $mean = ($max + $min) / 2;
    $random_number = ($gaussian_number * $std_deviation) + $mean;
    $random_number = round($random_number / $step) * $step;
    if($random_number < $min || $random_number > $max) {
        $random_number = randomGauss($min, $max,$std_deviation);
    }

    return $random_number;
}
?>