<?php

// /////////////////////////////////////////////////////////////////////////////
// PLEASE DO NOT RENAME OR REMOVE ANY OF THE CODE BELOW.
// YOU CAN ADD YOUR CODE TO THIS FILE TO EXTEND THE FEATURES TO USE THEM IN YOUR WORK.
// /////////////////////////////////////////////////////////////////////////////

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;

            $isPlayerApiRequest = str_starts_with($request->path(), 'api/player');
            $isValidationException = $e instanceof ValidationException;

            if ($isPlayerApiRequest && $isValidationException) {
                $errors = $e->errors();
                $firstErrorKey = array_key_first($errors);
                $invalidValue = optional(data_get($request->all(), $firstErrorKey), fn($value) => $value);

                if (str_contains($firstErrorKey, '.')) {
                    [$parentKey, $nestedField] = explode('.', $firstErrorKey, 2);
                    $invalidValue = optional(data_get($request->input($parentKey), '0.' . $nestedField), fn($value) => $value);
                }

                return response()->json([
                    'message' => "Invalid value for {$firstErrorKey}: {$invalidValue}"
                ], $statusCode);
            }

            return response()->json([
                'message' => $e->getMessage(),
            ], $statusCode);
        });
    }
}
