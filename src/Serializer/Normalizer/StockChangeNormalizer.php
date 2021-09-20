<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\DataProvider\NomenclatureDataProvider;
use App\Entity\Change\StockChange;
use App\Utils\DateTimeUtils;

class StockChangeNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'stock_change';
    public const TYPE_IN_INCOME_ROWS = 'in_income_rows';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof StockChange;
    }

    /**
     * @param StockChange $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_INCOME_ROWS:
                $result = [
                    'id' => $object->getId(),
                    'nomenclature' => $this->normalizer->normalize(
                        $object->getCharacteristic()->getNomenclature(),
                        $format,
                        [NomenclatureNormalizer::CONTEXT_TYPE_KEY => NomenclatureNormalizer::TYPE_IN_LIST]
                    ),
                    'characteristic' => $this->normalizer->normalize(
                        $object->getCharacteristic()->getNomenclature(),
                        $format,
                        [CharacteristicNormalizer::CONTEXT_TYPE_KEY => CharacteristicNormalizer::TYPE_IN_STOCK_CHANGE]
                    ),
                    'value' => $object->getValue(),
                    'purchasePrice' => $object->getPriceChange()->getOldValue(),
                    'retailPrice' => $object->getPriceChange()->getNewValue(),
                    'amount' => $object->getPriceChange()->getOldValue() * $object->getValue(),
                ];
                break;
            case self::TYPE_IN_LIST:
                $result = [
                    'id' => $object->getId(),
                    'nomenclature' => $this->normalizer->normalize(
                        $object->getCharacteristic()->getNomenclature(),
                        $format,
                        [NomenclatureNormalizer::CONTEXT_TYPE_KEY => NomenclatureNormalizer::TYPE_IN_LIST]
                    ),
                    'type' => $object->getDocument()->getType(),
                    'value' => $object->getValue(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'store' => $object->getDocument()->getStore()->getName(),
                    'nomenclature' => $this->normalizer->normalize(
                        $object->getCharacteristic()->getNomenclature(),
                        $format,
                        [NomenclatureNormalizer::CONTEXT_TYPE_KEY => NomenclatureNormalizer::TYPE_IN_LIST]
                    ),
                    'characteristic' => $this->normalizer->normalize(
                        $object->getCharacteristic()->getNomenclature(),
                        $format,
                        [CharacteristicNormalizer::CONTEXT_TYPE_KEY => CharacteristicNormalizer::TYPE_IN_STOCK_CHANGE]
                    ),
                    'type' => $object->getDocument()->getType(),
                    'value' => $object->getValue(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                ];
        }

        return $result;
    }
}
