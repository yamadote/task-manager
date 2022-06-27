<?php

namespace App\Input\Validation;

use App\Input\Validation\Checker\DateValidationChecker;
use Symfony\Component\HttpFoundation\Request;

class HistoryInputValidator
{
    private DateValidationChecker $dateValidationChecker;

    public function __construct(DateValidationChecker $dateValidationChecker)
    {
        $this->dateValidationChecker = $dateValidationChecker;
    }

    public function validateInit(Request $request): ?string
    {
        $startFrom = $request->query->get('startFrom');
        if (!empty($startFrom)) {
            if (!$this->dateValidationChecker->isValid($startFrom)) {
                return "`startFrom` parameter is not valid!";
            }
        }
        $taskId = $request->query->get('task');
        if (!empty($taskId)) {
            if (!ctype_digit($taskId)) {
                return "`taskId` parameter is not valid!";
            }
        }
        return null;
    }
}