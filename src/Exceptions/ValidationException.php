<?php

declare(strict_types=1);

namespace Szyfr\Maya\Exceptions;

class ValidationException extends MayaException
{
    /**
     * @param array<string, array<string>> $errors
     */
    public static function fromErrors(array $errors): self
    {
        $message = 'Validation failed: '.json_encode($errors);

        return new self($message, 422);
    }
}
