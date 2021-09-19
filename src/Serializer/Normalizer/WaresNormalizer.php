<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\DataProvider\NomenclatureDataProvider;
use App\Entity\View\Wares;
use App\Utils\DateTimeUtils;

class WaresNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'wares';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Wares;
    }

    /**
     * @param Wares $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_LIST:
                $result = [
                    'nomenclature' => $object->getName(),
                    'serial' => $object->getSerial(),
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms(
                        $object->getMedicalForm()
                    ),
                    'expire' =>  DateTimeUtils::formatDate(
                        $object->getExpireTime(),
                        DateTimeUtils::FORMAT_EXPIRE
                    ),
                    'stock' => $object->getStock(),
                    'retailPrice' => $object->getRetailPrice(),
                ];
                break;
            default:
                $result = [
                    'characteristic' => $object->getCharacteristic(),
                    'store' => $object->getStore(),
                    'nomenclature' => $object->getName(),
                    'serial' => $object->getSerial(),
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms(
                        $object->getMedicalForm()
                    ),
                    'expire' =>  DateTimeUtils::formatDate(
                        $object->getExpireTime(),
                        DateTimeUtils::FORMAT_EXPIRE
                    ),
                    'stock' => $object->getStock(),
                    'retailPrice' => $object->getRetailPrice(),
                ];
        }

        return $result;
    }
}
