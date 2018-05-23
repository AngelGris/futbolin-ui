<?php
/**
 * Format number into currency
 *
 * @param float $value
 * @param integer $decimals
 * @return String
 */
function formatCurrency($value, $decimals = 0)
{
    $funds = number_format($value, $decimals) . ' $';
    if ($value < 0) {
        $funds = '<span style="color:#f00;">' . $funds . '</span>';
    }

    return $funds;
}

/**
 * Get current domain
 *
 * @return String
 */
function getDomain()
{
    $host = parse_url(\Request::server('HTTP_HOST'));
    return str_replace('admin.', '', (!empty($host['host']) ? $host['host'] : $host['path']));
}

/**
 * Get match log from file
 *
 * @param String $file
 * @return Array String
 */
function getMatchLog($file)
{
    $file_name = base_path() . '/python/logs/' . $file;

    $data = [];
    if (is_file($file_name)) {
        $string = file_get_contents($file_name);
        $data = json_decode($string,true);
    }

    return $data;
}

/**
 * Calculate colors luminosity difference
 *
 * @param Array integer $color1
 * @param Array integer $color2
 * @return float
 */
function lumdiff($color1, $color2)
{
    $L1 = 0.2126 * pow($color1[0]/255, 2.2) +
          0.7152 * pow($color1[1]/255, 2.2) +
          0.0722 * pow($color1[2]/255, 2.2);

    $L2 = 0.2126 * pow($color2[0]/255, 2.2) +
          0.7152 * pow($color2[1]/255, 2.2) +
          0.0722 * pow($color2[2]/255, 2.2);

    if($L1 > $L2){
        return ($L1+0.05) / ($L2+0.05);
    }else{
        return ($L2+0.05) / ($L1+0.05);
    }
}

/**
 * Calculate random Gauss number
 *
 * @param float $min
 * @param float $max
 * @param float $std_deviation
 * @param integer #step
 * @return float
 */
function randomGauss($min, $max, $std_deviation, $step=1)
{
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

/**
 * Format time in a readable string
 *
 * @param integer $seconds
 * @param boolean $short
 * @return String
 */
function readableTime($seconds, $short = FALSE)
{
    if ($short) {
        $hours_label = ['h', 'h'];
        $minutes_label = ['m', 'm'];
    } else {
        $hours_label = ['hora', 'horas'];
        $minutes_label = ['minute', 'minutes'];
    }

    if ($seconds > 3600) {
        $hours = (int)($seconds / 3600);
        $output = $hours . ' ' . ($hours > 1 ? $hours_label[1] : $hours_label[0]);
    } else {
        $minutes = (int)($seconds / 60);
        $output = $minutes . ' ' . ($minutes > 1 ? $minutes_label[1] : $minutes_label[0]);
    }
    return $output;
}

/**
 * Calculate text color based on the background color
 *
 * @param Array integer $bgColor
 * @param Array integer $optColor
 * @return String
 */
function textColor($bgColor, $optColor)
{
    if (lumdiff($bgColor, $optColor) >= 5) {
        return '#' . sprintf('%02s', dechex($optColor[0])) . sprintf('%02s', dechex($optColor[1])) . sprintf('%02s', dechex($optColor[2]));
    } else if (lumdiff($bgColor, [0, 0, 0]) >= 5) {
        return '#000000';
    } else {
        return '#ffffff';
    }
}
?>