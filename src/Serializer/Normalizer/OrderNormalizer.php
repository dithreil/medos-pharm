<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\Order;
use App\Utils\DateTimeUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderNormalizer extends AbstractCustomNormalizer
{
    public const CONTEXT_TYPE_KEY = 'orders';
    public const TYPE_IN_PAYMENT = 'in_payment';
    public const TYPE_IN_CLIENT = 'in_client';
    public const TYPE_IN_EMPLOYEE = 'in_employee';
    public const TYPE_IN_REPORT = 'in_report';
    public const TYPE_IN_EMAIL_ORDER_NOTIFICATION = 'in_email_order_notification';
    public const TYPE_LIST = 'list';

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @return bool
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Order;
    }

    /**
     * @param Order $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|void|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        switch ($this->getType($context)) {
            case self::TYPE_IN_PAYMENT:
                $result = [
                    'id' => $object->getId(),
                    'client' => $this->normalizer->normalize(
                        $object->getClient(),
                        $format,
                        [ClientNormalizer::CONTEXT_TYPE_KEY => ClientNormalizer::TYPE_IN_ORDER]
                    ),
                    'employee' => $this->normalizer->normalize(
                        $object->getEmployee(),
                        $format,
                        [EmployeeNormalizer::CONTEXT_TYPE_KEY => EmployeeNormalizer::TYPE_IN_ORDER]
                    ),
                    'category' => $object->getCategory()->getName(),
                    'duration' => $object->getDuration(),
                    'type' => $object->getType(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'startTime' => DateTimeUtils::formatDate($object->getStartTime()),
                    'paymentTime' => DateTimeUtils::formatDate($object->getPaymentTime()),
                    'cost' => $object->getCost(),
                    'status' => $object->getStatus()
                ];
                break;
            case self::TYPE_IN_CLIENT:
                $result = [
                    'id' => $object->getId(),
                    'employee' => $this->normalizer->normalize(
                        $object->getEmployee(),
                        $format,
                        [EmployeeNormalizer::CONTEXT_TYPE_KEY => EmployeeNormalizer::TYPE_IN_ORDER]
                    ),
                    'category' => $object->getCategory()->getName(),
                    'duration' => $object->getDuration(),
                    'type' => $object->getType(),
                    'communication' => $object->getCommunication(),
                    'clientTarget' => $object->getClientTarget(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'startTime' => DateTimeUtils::formatDate($object->getStartTime()),
                    'paymentTime' => DateTimeUtils::formatDate($object->getPaymentTime()),
                    'cost' => $object->getCost(),
                    'status' => $object->getStatus(),
                    'rating' => $object->getRating(),
                    'ratingComment' => $object->getRatingComment(),
                    'payments' => $this->normalizer->normalize(
                        $object->getPayments(),
                        $format,
                        [PaymentNormalizer::CONTEXT_TYPE_KEY => PaymentNormalizer::TYPE_IN_ORDER]
                    )
                ];
                break;
            case self::TYPE_IN_EMPLOYEE:
                $result = [
                    'id' => $object->getId(),
                    'client' => $this->normalizer->normalize(
                        $object->getClient(),
                        $format,
                        [ClientNormalizer::CONTEXT_TYPE_KEY => ClientNormalizer::TYPE_IN_ORDER]
                    ),
                    'category' => $object->getCategory()->getName(),
                    'duration' => $object->getDuration(),
                    'type' => $object->getType(),
                    'communication' => $object->getCommunication(),
                    'clientTarget' => $object->getClientTarget(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'startTime' => DateTimeUtils::formatDate($object->getStartTime()),
                    'paymentTime' => DateTimeUtils::formatDate($object->getPaymentTime()),
                    'cost' => $object->getCost(),
                    'status' => $object->getStatus(),
                    'rating' => $object->getRating(),
                    'ratingComment' => $object->getRatingComment(),
                    'report' => $this->normalizer->normalize(
                        $object->getReport(),
                        $format,
                        [ReportNormalizer::CONTEXT_TYPE_KEY => ReportNormalizer::TYPE_IN_LIST]
                    ),
                    'payments' => $this->normalizer->normalize(
                        $object->getPayments(),
                        $format,
                        [PaymentNormalizer::CONTEXT_TYPE_KEY => PaymentNormalizer::TYPE_IN_ORDER]
                    )
                ];
                break;
            case self::TYPE_IN_REPORT:
                $result = [
                    'id' => $object->getId(),
                    'category' => $object->getCategory()->getName(),
                    'duration' => $object->getDuration(),
                    'type' => $object->getType(),
                    'communication' => $object->getCommunication(),
                    'clientTarget' => $object->getClientTarget(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'startTime' => DateTimeUtils::formatDate($object->getStartTime()),
                    'paymentTime' => DateTimeUtils::formatDate($object->getPaymentTime()),
                    'cost' => $object->getCost(),
                    'status' => $object->getStatus()
                ];
                break;
            case self::TYPE_LIST:
                $result = [
                    'id' => $object->getId(),
                    'client' => $this->normalizer->normalize(
                        $object->getClient(),
                        $format,
                        [ClientNormalizer::CONTEXT_TYPE_KEY => ClientNormalizer::TYPE_IN_ORDER]
                    ),
                    'employee' => $this->normalizer->normalize(
                        $object->getEmployee(),
                        $format,
                        [EmployeeNormalizer::CONTEXT_TYPE_KEY => EmployeeNormalizer::TYPE_IN_ORDER]
                    ),
                    'status' => $object->getStatus(),
                    'rating' => $object->getRating(),
                    'ratingComment' => $object->getRatingComment(),
                    'category' => $object->getCategory()->getName(),
                    'duration' => $object->getDuration(),
                    'type' => $object->getType(),
                    'communication' => $object->getCommunication(),
                    'clientTarget' => $object->getClientTarget(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'startTime' => DateTimeUtils::formatDate($object->getStartTime()),
                    'paymentTime' => DateTimeUtils::formatDate($object->getPaymentTime()),
                    'cost' => $object->getCost(),
                ];
                break;
            case self::TYPE_IN_EMAIL_ORDER_NOTIFICATION:
                $result = [
                    'date' => DateTimeUtils::formatDate($object->getStartTime(), DateTimeUtils::FORMAT_FOR_PATH),
                    'time' => DateTimeUtils::formatDate($object->getStartTime(), DateTimeUtils::FORMAT_TIME),
                    'clientName' => $object->getClient()->getFullName(),
                    'employeeName' => $object->getEmployee()->getFullName(),
                    'specialization' => $object->getEmployee()->getSpeciality()->getName(),
                    'orderUri' => $this->urlGenerator->generate(
                        'app_front_default_index',
                        ['vueRouting' => '/client/cabinet/orders/' . $object->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ];
                break;
            default:
                $result = [
                    'id' => $object->getId(),
                    'client' => $this->normalizer->normalize(
                        $object->getClient(),
                        $format,
                        [ClientNormalizer::CONTEXT_TYPE_KEY => ClientNormalizer::TYPE_IN_ORDER]
                    ),
                    'employee' => $this->normalizer->normalize(
                        $object->getEmployee(),
                        $format,
                        [EmployeeNormalizer::CONTEXT_TYPE_KEY => EmployeeNormalizer::TYPE_IN_ORDER]
                    ),
                    'category' => $object->getCategory()->getName(),
                    'duration' => $object->getDuration(),
                    'type' => $object->getType(),
                    'communication' => $object->getCommunication(),
                    'clientTarget' => $object->getClientTarget(),
                    'createTime' => DateTimeUtils::formatDate($object->getCreateTime()),
                    'updateTime' => DateTimeUtils::formatDate($object->getUpdateTime()),
                    'startTime' => DateTimeUtils::formatDate($object->getStartTime()),
                    'paymentTime' => DateTimeUtils::formatDate($object->getPaymentTime()),
                    'cost' => $object->getCost(),
                    'rating' => $object->getRating(),
                    'ratingComment' => $object->getRatingComment(),
                    'status' => $object->getStatus(),
                    'payments' => $this->normalizer->normalize(
                        $object->getPayments(),
                        $format,
                        [PaymentNormalizer::CONTEXT_TYPE_KEY => PaymentNormalizer::TYPE_IN_ORDER]
                    ),
                    'history' => $this->normalizer->normalize(
                        $object->getHistory(),
                        $format,
                        [OrderHistoryNormalizer::CONTEXT_TYPE_KEY => OrderHistoryNormalizer::TYPE_IN_ORDER]
                    ),
                    'documents' => $this->normalizer->normalize(
                        $object->getDocuments(),
                        $format,
                        [OrderDocumentNormalizer::CONTEXT_TYPE_KEY => OrderDocumentNormalizer::TYPE_IN_ORDER]
                    ),
                    'report' => $this->normalizer->normalize(
                        $object->getReport(),
                        $format,
                        [ReportNormalizer::CONTEXT_TYPE_KEY => ReportNormalizer::TYPE_IN_LIST]
                    )
                ];
        }

        return $result;
    }
}
