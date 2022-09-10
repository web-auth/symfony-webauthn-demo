<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\PublicKeyCredentialUserEntityRepository;
use function is_string;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueUsernameValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PublicKeyCredentialUserEntityRepository $userRepository
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (! $constraint instanceof UniqueUsername) {
            throw new UnexpectedTypeException($constraint, UniqueUsername::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->userRepository->find($value) !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation()
            ;
        }
    }
}
