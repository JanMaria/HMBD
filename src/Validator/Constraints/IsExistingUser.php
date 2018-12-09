<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsExistingUser extends Constraint
{
    public $message = 'Autor o mailu "{{ string }}" nie istnieje.';
}
