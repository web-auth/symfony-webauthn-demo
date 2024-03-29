<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\PublicKeyCredentialUserEntityRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function is_string;

final class UniqueUsernameValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PublicKeyCredentialUserEntityRepository $userRepository
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
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

        if ($this->userRepository->findOneByUsername($value) !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation()
            ;
        }
    }
}
