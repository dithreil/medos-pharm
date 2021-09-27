<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Employee;

class EmployeeNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'employee';
    public const TYPE_IN_ORDER = 'in_order';
    public const TYPE_IN_WEEK_SCHEDULE = 'in_week_schedule';
    public const TYPE_LIST = 'list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Employee;
    }

    /**
     * @param Employee $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_ORDER:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getRealEmail(),
                    'fullName' => $object->getFullName(),
                    'phoneNumber' => $object->getRealPhoneNumber(),
                ];
                break;
            case self::TYPE_LIST:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getRealEmail(),
                    'store' => $this->normalizer->normalize(
                        $object->getStore(),
                        $format,
                        [StoreNormalizer::CONTEXT_TYPE_KEY => StoreNormalizer::TYPE_IN_LIST]
                    ),
                    'fullName' => $object->getFullName(),
                    'isActive' => $object->isActive(),
                    'phoneNumber' => $object->getRealPhoneNumber()
                ];
                break;
            case self::TYPE_IN_WEEK_SCHEDULE:
                $result = [
                    'id' => $object->getId(),
                    'fullName' => $object->getFullName()
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getRealEmail(),
                    'store' => $this->normalizer->normalize(
                        $object->getStore(),
                        $format,
                        [StoreNormalizer::CONTEXT_TYPE_KEY => StoreNormalizer::TYPE_IN_LIST]
                    ),
                    'lastName' => $object->getLastName(),
                    'firstName' => $object->getFirstName(),
                    'patronymic' => $object->getPatronymic(),
                    'fullName' => $object->getFullName(),
                    'phoneNumber' => $object->getRealPhoneNumber(),
                    'isActive' => $object->isActive()
                ];
        }

        return $result;
    }
}
