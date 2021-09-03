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
                    'speciality' => $object->getSpeciality()->getName(),
                ];
                break;
            case self::TYPE_LIST:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getRealEmail(),
                    'fullName' => $object->getFullName(),
                    'isActive' => $object->isActive(),
                    'phoneNumber' => $object->getRealPhoneNumber(),
                    'speciality' => $object->getSpeciality()->getName(),
                    'code' => $object->getCode(),
                    'areaCode' => $object->getArea()->getCode(),
                    'specialityCode' => $object->getSpeciality()->getCode(),
                ];
                break;
            case self::TYPE_IN_WEEK_SCHEDULE:
                $result = [
                    'id' => $object->getId(),
                    'code' => $object->getCode(),
                    'fullName' => $object->getFullName(),
                    'areaId' => $object->getArea()->getId(),
                    'areaCode' => $object->getArea()->getCode(),
                    'areaName' => $object->getArea()->getName(),
                    'specialityId' => $object->getSpeciality()->getId(),
                    'specialityCode' => $object->getSpeciality()->getCode(),
                    'specialityName' => $object->getSpeciality()->getName(),
                    'weekSchedule' => $object->getWeeklySchedule()
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getRealEmail(),
                    'lastName' => $object->getLastName(),
                    'firstName' => $object->getFirstName(),
                    'patronymic' => $object->getPatronymic(),
                    'fullName' => $object->getFullName(),
                    'phoneNumber' => $object->getRealPhoneNumber(),
                    'speciality' => $object->getSpeciality()->getName(),
                    'code' => $object->getCode(),
                    'areaCode' => $object->getArea()->getCode(),
                    'specialityCode' => $object->getSpeciality()->getCode(),
                    'isActive' => $object->isActive(),
                    'orders' => $this->normalizer->normalize(
                        $object->getOrders(),
                        $format,
                        [OrderNormalizer::CONTEXT_TYPE_KEY => OrderNormalizer::TYPE_IN_EMPLOYEE]
                    ),
                    'reports' => $this->normalizer->normalize(
                        $object->getReports(),
                        $format,
                        [ReportNormalizer::CONTEXT_TYPE_KEY => ReportNormalizer::TYPE_IN_LIST]
                    )
                ];
        }

        return $result;
    }
}
