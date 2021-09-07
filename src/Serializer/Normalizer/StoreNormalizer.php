<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Store;

class StoreNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'store';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Store;
    }

    /**
     * @param Store $object
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
                    'name' => $object->getName(),
                    'address' => $object->getAddress()
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'name' => $object->getName(),
                    'address' => $object->getAddress(),
                    'description' => $object->getDescription(),
                    'createTime' => $object->getCreateTime(),
                    'updateTime' => $object->getUpdateTime()
                ];
        }

        return $result;
    }
}
