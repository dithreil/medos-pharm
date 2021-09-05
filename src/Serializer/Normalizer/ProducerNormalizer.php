<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Producer;

class ProducerNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'producer';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Producer;
    }

    /**
     * @param Producer $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_LIST:
                $result = [
                    'id' => $object->getId(),
                    'shortName' => $object->getShortName(),
                    'country' => $object->getCountry()
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'shortName' => $object->getShortName(),
                    'country' => $object->getCountry(),
                    'fullName' => $object->getFullName()
                ];
        }

        return $result;
    }
}
