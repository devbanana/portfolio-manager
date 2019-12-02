<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PortfolioExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('millidate', [$this, 'millidate']),
            new TwigFilter('format_large_number', [$this, 'formatLargeNumber']),
        ];
    }

    /**
     * Converts milliseconds into a date with given format.
     *
     * @param int $milliseconds The milliseconds
     * @param string $format The date format
     *
     * @return string The formatted date
     */
    public function millidate($value, $format): string
    {
        $date = \DateTime::createFromFormat('U.u', bcdiv($value, 1000, 6));
        return $date->format($format);
    }

    public function formatLargeNumber($number, $decimals = 1)
    {
        if ($number < 1000000) {
            return number_format($number);
        } elseif ($number < 1000000000) {
            return number_format($number / 1000000, $decimals) . 'M';
        } elseif ($number < 1000000000000) {
            return number_format($number / 1000000000, $decimals) . 'B';
        } else {
            return number_format($number / 1000000000000, $decimals) . 'T';
        }
    }
}
