<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Document\PriceDocument;
use App\Utils\DateTimeUtils;

class PriceDocumentNormalizer extends AbstractCustomNormalizer
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
        return $data instanceof PriceDocument;
    }

    /**
     * @param PriceDocument $object
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
                    'store' => $object->getStore(),
                    'type' => $object->getType(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'store' => $object->getStore(),
                    'type' => $object->getType(),
                    'rows' => $this->normalizer->normalize(
                        $object->getPriceChanges(),
                        $format,
                        [PriceChangeNormalizer::CONTEXT_TYPE_KEY => PriceChangeNormalizer::TYPE_IN_LIST]
                    ),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                ];
        }

        return $result;
    }
}
