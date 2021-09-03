<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Model\PaginatedDataModel;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class PaginatedDataModelNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'paginated_data_model';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof PaginatedDataModel;
    }

    /**
     * @param PaginatedDataModel $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            default:
                $result = [
                    'total' => $object->total,
                    'pages' => $object->pages,
                    'limit' => $object->limit,
                    'page'  => $object->page,
                    'prev'  => $object->prev,
                    'next'  => $object->next,
                    'items' => $this->normalizer->normalize($object->items, $format, $context),
                ];
        }

        return $result;
    }
}
