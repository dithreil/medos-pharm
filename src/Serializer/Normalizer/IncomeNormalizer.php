<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Document\Income;
use App\Utils\DateTimeUtils;

class IncomeNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'stock_document';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Income;
    }

    /**
     * @param Income $object
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
                    'date' => DateTimeUtils::formatDate($object->getDate()),
                    'isSet' => $object->isSet(),
                    'amount' => $object->getAmount(),
                    'store' => $this->normalizer->normalize(
                        $object->getStore(),
                        $format,
                        [StoreNormalizer::CONTEXT_TYPE_KEY => StoreNormalizer::TYPE_IN_LIST]
                    ),
                    'supplier' => $this->normalizer->normalize(
                        $object->getSupplier(),
                        $format,
                        [SupplierNormalizer::CONTEXT_TYPE_KEY => SupplierNormalizer::TYPE_IN_LIST]
                    ),
                    'comment' => $object->getComment(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'date' => DateTimeUtils::formatDate($object->getDate()),
                    'isSet' => $object->isSet(),
                    'amount' => $object->getAmount(),
                    'store' => $this->normalizer->normalize(
                        $object->getStore(),
                        $format,
                        [StoreNormalizer::CONTEXT_TYPE_KEY => StoreNormalizer::TYPE_IN_LIST]
                    ),
                    'supplier' => $this->normalizer->normalize(
                        $object->getSupplier(),
                        $format,
                        [SupplierNormalizer::CONTEXT_TYPE_KEY => SupplierNormalizer::TYPE_IN_LIST]
                    ),
                    'rows' => $this->normalizer->normalize(
                        $object->getStockDocument()->getStockChanges(),
                        $format,
                        [StockChangeNormalizer::CONTEXT_TYPE_KEY => StockChangeNormalizer::TYPE_IN_INCOME_ROWS]
                    ),
                    'comment' => $object->getComment(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                ];
        }

        return $result;
    }
}
