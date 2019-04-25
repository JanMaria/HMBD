<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsCurseFree extends Constraint
{
    const CONTAINS_CURSES_ERROR = 'ea452954cf8e9d5d2e6141f7a8d022db';

    // protected static $errorNames = [
    //     self::CONTAINS_CURSES_ERROR => 'CONTAINS_CURSES_ERROR',
    // ];

    public $message = 'W komentarzu rozpoznano słowa uważane za wulgarne.
        Zastąp dopuszczalnymi synonimami następujące wyrazy: {{ occuringCurses }}';
}
