<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Client;
use App\Utils\DateTimeUtils;

class ClientNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'client';
    public const TYPE_IN_ORDER = 'in_order';
    public const TYPE_LIST = 'list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Client;
    }

    /**
     * @param Client $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_ORDER:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getEmail(),
                    'fullName' => $object->getFullName(),
                    'phoneNumber' => $object->getPhoneNumber(),
                    'snils' => $object->getSnils(),
                ];
                break;
            case self::TYPE_LIST:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getEmail(),
                    'fullName' => $object->getFullName(),
                    'isActive' => $object->isActive(),
                    'phoneNumber' => $object->getPhoneNumber(),
                    'snils' => $object->getSnils(),
                    'skype' => $object->getSkype(),
                    'whatsapp' => $object->getWhatsapp(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'email' => $object->getEmail(),
                    'lastName' => $object->getLastName(),
                    'firstName' => $object->getFirstName(),
                    'patronymic' => $object->getPatronymic(),
                    'createTime' => $object->getCreateTime(),
                    'fullName' => $object->getFullName(),
                    'phoneNumber' => $object->getPhoneNumber(),
                    'snils' => $object->getSnils(),
                    'skype' => $object->getSkype(),
                    'whatsapp' => $object->getWhatsapp(),
                    'isActive' => $object->isActive(),
                    'birthDate' => DateTimeUtils::formatDate($object->getBirthDate(), DateTimeUtils::FORMAT_DATE)
                ];
        }

        return $result;
    }
}
