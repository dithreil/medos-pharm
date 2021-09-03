<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Payment;
use App\Utils\DateTimeUtils;

class PaymentNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'payments';
    public const TYPE_IN_ORDER = 'in_order';

    /**
     * @param Payment $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_ORDER:
                $result = [
                    'id'            => $object->getId(),
                    'amount'        => $object->getAmount(),
                    'status'        => $object->getStatus(),
                    'createTime'    => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime'    => DateTimeUtils::formatDate($object->getUpdateTime())
                ];
                break;
            default:
                $result = [
                    'id'            => $object->getId(),
                    'amount'        => $object->getAmount(),
                    'status'        => $object->getStatus(),
                    'orderId'       => $this->normalizer->normalize(
                        $object->getOrder()->getId(),
                        $format,
                        [OrderNormalizer::CONTEXT_TYPE_KEY => OrderNormalizer::TYPE_IN_PAYMENT]
                    ),
                    'createTime'    => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime'    => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'clientName'    => $object->getClientName(),
                    'employeeName'  => $object->getEmployeeName(),
                ];
                break;
        }
        return $result;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Payment;
    }
}
