<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\DataProvider\NomenclatureDataProvider;
use App\Entity\Change\PriceChange;
use App\Utils\DateTimeUtils;

class PriceChangeNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'price_change';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof PriceChange;
    }

    /**
     * @param PriceChange $object
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
                    'nomenclature' => $object->getCharacteristic()->getNomenclature()->getName(),
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms(
                        $object->getCharacteristic()->getNomenclature()->getMedicalForm()
                    ),
                    'type' => $object->getDocument()->getType(),
                    'oldValue' => $object->getOldValue(),
                    'newValue' => $object->getNewValue(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'store' => $object->getDocument()->getStore()->getName(),
                    'nomenclature' => $object->getCharacteristic()->getNomenclature()->getName(),
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms(
                        $object->getCharacteristic()->getNomenclature()->getMedicalForm()
                    ),
                    'type' => $object->getDocument()->getType(),
                    'oldValue' => $object->getOldValue(),
                    'newValue' => $object->getNewValue(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                ];
        }

        return $result;
    }
}
