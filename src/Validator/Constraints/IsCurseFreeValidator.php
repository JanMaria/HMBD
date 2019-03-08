<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Validator\Constraints\IsCurseFree;

class IsCurseFreeValidator extends ConstraintValidator
{
    private $curses = array(
        "tromtadracja", "autoteliczny", "dezynwoltura", "utracjusz", "egalitaryzm", "eudajmonizm", "mitrężyć",
        "interlokutor", "dekadencja", "mizogin", "spuneryzm", "indyferencja", "ambiwalentny", "imponderabilia",
        "prokrastynacja");

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsCurseFree) {
            throw new UnexpectedTypeException($constraint, IsCurseFree::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $occuringCurses = array_intersect($this->curses, explode(' ', trim(strtolower($value))));

        if (0 < sizeof($occuringCurses)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ occuringCurses }}', implode(", ", $occuringCurses))
                ->setCode(IsCurseFree::CONTAINS_CURSES_ERROR)
                ->addViolation();
        }

        // if ($this->userRepository->findOneBy(['email' => $value]) === null) {
        //     $this->context->buildViolation($constraint->message)
        //         ->setParameter('{{ string }}', $value)
        //         ->addViolation();
        // }
    }
}
