<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Characteristic;
use App\Utils\DateTimeUtils;

class CharacteristicNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'characteristic';
    public const TYPE_IN_LIST = 'in_list';
    public const TYPE_IN_STOCK_CHANGE = 'in_stock_change';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Characteristic;
    }

    /**
     * @param Characteristic $object
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
                    'serial' => $object->getSerial(),
                    'expireOriginal' => DateTimeUtils::formatDate($object->getExpireTime()),
                    'expire' => DateTimeUtils::formatDate($object->getExpireTime(), DateTimeUtils::FORMAT_EXPIRE),
                    'nomenclature' => $this->normalizer->normalize(
                        $object->getNomenclature(),
                        $format,
                        [NomenclatureNormalizer::CONTEXT_TYPE_KEY => NomenclatureNormalizer::TYPE_IN_CHARACTERISTIC]
                    ),
                ];
                break;
            case self::TYPE_IN_STOCK_CHANGE:
                $result = [
                    'id' => $object->getId(),
                    'serial' => $object->getSerial(),
                    'expireOriginal' => DateTimeUtils::formatDate($object->getExpireTime(), DateTimeUtils::FORMAT_DATE),
                    'expire' => DateTimeUtils::formatDate($object->getExpireTime(), DateTimeUtils::FORMAT_EXPIRE),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'serial' => $object->getSerial(),
                    'expireOriginal' => DateTimeUtils::formatDate($object->getExpireTime()),
                    'expire' => DateTimeUtils::formatDate($object->getExpireTime(), DateTimeUtils::FORMAT_EXPIRE),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'deleteTime' => DateTimeUtils::formatDate($object->getDeleteTime()),
                    'nomenclature' => $this->normalizer->normalize(
                        $object->getNomenclature(),
                        $format,
                        [NomenclatureNormalizer::CONTEXT_TYPE_KEY => NomenclatureNormalizer::TYPE_IN_CHARACTERISTIC]
                    ),
                ];
        }

        return $result;
    }
}
