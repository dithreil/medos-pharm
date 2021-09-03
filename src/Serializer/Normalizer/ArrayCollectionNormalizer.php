<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

class ArrayCollectionNormalizer extends AbstractCustomNormalizer
{
    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ArrayCollection;
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        return array_map(
            fn($item) => $this->normalizer->normalize($item, $format, $context),
            $object->getValues()
        );
    }
}
