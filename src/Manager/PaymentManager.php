<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\PaymentDataProvider;
use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Payment;
use App\Entity\User;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\PaymentRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class PaymentManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var PaymentRepository
     */
    private PaymentRepository $paymentRepository;

    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * @var YookassaManager
     */
    private YookassaManager $yookassaManager;

    /**
     * @param PaymentRepository $paymentRepository
     * @param OrderManager $orderManager
     * @param YookassaManager $yookassaManager
     */
    public function __construct(PaymentRepository $paymentRepository, OrderManager $orderManager, YookassaManager $yookassaManager)
    {
        $this->paymentRepository = $paymentRepository;
        $this->orderManager = $orderManager;
        $this->yookassaManager = $yookassaManager;
    }

    /**
     * @param float $amount
     * @param string $orderId
     * @param string|null $paymentKey
     * @return Payment
     * @throws AppException
     */
    public function create(float $amount, string $orderId, ?string $paymentKey = null): Payment
    {
        $order = $this->orderManager->get($orderId);
        $payment = new Payment($order, $amount, PaymentDataProvider::STATUS_NOT_PAID, $paymentKey);
        $order->addPayment($payment);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }

    /**
     * @param string $id
     * @param string $status
     * @throws AppException
     */
    public function edit(string $id, string $status): void
    {
        $payment = $this->paymentRepository->find($id);

        if ($payment === null) {
            throw new AppException('Payment not found', Response::HTTP_BAD_REQUEST);
        }

        $payment->setStatus($status);
        $this->entityManager->flush();
    }

    /**
     * @param string $orderId
     * @return string
     * @throws AppException
     */
    public function payForOrder(string $orderId): string
    {
        $this->orderManager->checkOrderForCrossing($orderId);

        $order = $this->orderManager->get($orderId);
        $paymentYookassa = $this->yookassaManager->createPayment($order->getCost(), $orderId);
        $this->create($order->getCost(), $orderId, $paymentYookassa->getId());
        $this->orderManager->registerPaidOrder($orderId);

        return $paymentYookassa->getConfirmation()->getConfirmationUrl();
    }

    /**
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function searchForAdmin(array $filters): PaginatedDataModel
    {
        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $createTime = array_key_exists('date', $filters)
                ? DateTimeUtils::parse($filters['date'])->setTime(0, 0)
                : DateTimeUtils::now()->setTime(0, 0);

            $items = $this->paymentRepository->searchForAdmin($createTime, $filters, $page, $limit);
            $total = $this->paymentRepository->countByForAdmin($createTime, $filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException|NonUniqueResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function search(array $filters): PaginatedDataModel
    {
        $user = $this->getLoggedInUser();

        if (!$user instanceof Client) {
            throw new AppException('Auth error', Response::HTTP_FORBIDDEN);
        }
        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $items = $this->paymentRepository->search($user->getId(), $page, $limit);
            $total = $this->paymentRepository->countBy($user->getId());

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException|NonUniqueResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param string $id
     * @return Payment
     * @throws AppException
     */
    public function get(string $id): Payment
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment instanceof Payment) {
            throw new AppException('Payment is not found', Response::HTTP_NOT_FOUND);
        }

        return $payment;
    }
}
