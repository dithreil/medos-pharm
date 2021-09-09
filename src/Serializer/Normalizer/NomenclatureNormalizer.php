<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\DataProvider\NomenclatureDataProvider;
use App\Entity\Nomenclature;

class NomenclatureNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'nomenclature';
    public const TYPE_IN_CHARACTERISTIC = 'in_characteristic';
    public const TYPE_IN_LIST = 'in_list';

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Nomenclature;
    }

    /**
     * @param Nomenclature $object
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
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms($object->getMedicalForm()),
                    'isVat' => $object->isVat(),
                    'producer' => $this->normalizer->normalize(
                        $object->getProducer(),
                        $format,
                        [ProducerNormalizer::CONTEXT_TYPE_KEY => ProducerNormalizer::TYPE_IN_NOMENCLATURE]
                    ),
                ];
                break;
            case self::TYPE_IN_CHARACTERISTIC:
                $result = [
                    'id' => $object->getId(),
                    'name' => $object->getName(),
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms($object->getMedicalForm()),
                    'isVat' => $object->isVat(),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'name' => $object->getName(),
                    'medicalForm' => NomenclatureDataProvider::getStringValueOfMedForms($object->getMedicalForm()),
                    'isVat' => $object->isVat(),
                    'createTime' => $object->getCreateTime(),
                    'updateTime' => $object->getUpdateTime(),
                    'deleteTime' => $object->getDeleteTime(),
                    'producer' => $this->normalizer->normalize(
                        $object->getProducer(),
                        $format,
                        [ProducerNormalizer::CONTEXT_TYPE_KEY => ProducerNormalizer::TYPE_IN_NOMENCLATURE]
                    ),
                ];
        }

        return $result;
    }
}
