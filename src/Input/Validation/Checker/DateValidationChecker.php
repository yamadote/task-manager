<?php

namespace App\Input\Validation\Checker;

class DateValidationChecker
{
    public function isValid(string $date): bool
    {
        $values = explode('/', $date);
        if (count($values) !== 3) {
            return false;
        }
        foreach ($values as $value) {
            if (!ctype_digit($value)) {
                return false;
            }
        }
        list($day, $month, $year) = $values;
        return checkdate($month, $day, $year);
    }
}