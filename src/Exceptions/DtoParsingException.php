<?php

declare(strict_types=1);

namespace Szyfr\Maya\Exceptions;

class DtoParsingException extends MayaException
{
    /**
     * Create a Data parsing exception with field information
     *
     * @param string $dtoClass The Data class name that failed to parse
     * @param string $field The field that caused the error
     * @param string $reason The reason for the parsing failure
     * @param array<string, mixed> $availableData The data that was available
     */
    public static function missingField(
        string $dtoClass,
        string $field,
        string $reason = '',
        array $availableData = []
    ): self {
        $message = "Failed to parse {$dtoClass}: Missing required field '{$field}'";

        if ($reason !== '') {
            $message .= ". {$reason}";
        }

        if (! empty($availableData)) {
            $availableKeys = implode(', ', array_keys($availableData));
            $message .= " Available keys: {$availableKeys}";
        }

        return new self($message, 500);
    }

    /**
     * Create a Data parsing exception for invalid field type
     *
     * @param string $dtoClass The Data class name that failed to parse
     * @param string $field The field that caused the error
     * @param string $expectedType The expected type
     * @param mixed $actualValue The actual value received
     */
    public static function invalidType(
        string $dtoClass,
        string $field,
        string $expectedType,
        mixed $actualValue
    ): self {
        $actualType = get_debug_type($actualValue);
        $message = "Failed to parse {$dtoClass}: Field '{$field}' must be of type {$expectedType}, got {$actualType}";

        return new self($message, 500);
    }
}
