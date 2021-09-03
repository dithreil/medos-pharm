<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ArrayUtils
{
    /**
     * @param ConstraintViolationListInterface $object
     * @return array
     */
    public static function constraintsToArray(ConstraintViolationListInterface $object): array
    {
        $result = [];

        if ($object instanceof ConstraintViolationList) {
            foreach ($object->getIterator() as $error) {
                $result[$error->getPropertyPath()][] = [
                    'error' => $error->getMessage(),
                ];
            }
        }

        return $result;
    }
}
