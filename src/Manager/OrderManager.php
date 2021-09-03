<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\OrderDataProvider;
use App\Entity\Order;
use App\Entity\User;
use App\Exception\AppException;
use App\Manager\Mail\MailManager;
use App\Model\Order\OrdersCountResultModel;
use App\Model\PaginatedDataModel;
use App\Repository\OrderRepository;
use App\Serializer\Normalizer\OrderNormalizer;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\MailSenderAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderManager
{
    use EntityManagerAwareTrait;
    use MailSenderAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var ClientManager
     */
    private ClientManager $clientManager;

    /**
     * @var EmployeeManager
     */
    private EmployeeManager $employeeManager;

    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;

    /**
     * @var float
     */
    private float $orderDefaultCost;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var NormalizerInterface
     */
    private NormalizerInterface $normalizer;

    /**
     * @param OrderRepository $orderRepository
     * @param EmployeeManager $employeeManager
     * @param ClientManager $clientManager
     * @param LoggerInterface $logger
     * @param NormalizerInterface $normalizer
     * @param float $orderDefaultCost
     */
    public function __construct(
        OrderRepository $orderRepository,
        EmployeeManager $employeeManager,
        ClientManager $clientManager,
        LoggerInterface $logger,
        NormalizerInterface $normalizer,
        float $orderDefaultCost
    ) {
        $this->orderRepository = $orderRepository;
        $this->employeeManager = $employeeManager;
        $this->clientManager = $clientManager;
        $this->logger = $logger;
        $this->normalizer = $normalizer;
        $this->orderDefaultCost = $orderDefaultCost;
    }

    /**
     * @param float $cost
     * @param string $startTime
     * @param string $clientId
     * @param string $employeeId
     * @param string $categoryId
     * @param string $type
     * @param int $duration
     * @param string|null $communication
     * @param string|null $clientTarget
     * @param string|null $clientComment
     * @param string|null $employeeComment
     * @return Order
     * @throws AppException
     */
    public function create(
        float $cost,
        string $startTime,
        string $clientId,
        string $employeeId,
        string $categoryId,
        string $type,
        int $duration,
        ?string $communication = null,
        ?string $clientTarget = null,
        ?string $clientComment = null,
        ?string $employeeComment = null
    ): Order {
        if (!OrderDataProvider::isTypeAllowed($type)) {
            throw new AppException("Selected order type is not allowed!", Response::HTTP_BAD_REQUEST);
        }
        try {
            $orderStartTime = DateTimeUtils::parse($startTime);
        } catch (\Exception $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
        if ($orderStartTime < DateTimeUtils::now()) {
            throw new AppException("Selected start time is not allowed!", Response::HTTP_BAD_REQUEST);
        }

        $client = $this->clientManager->get($clientId);
        $employee = $this->employeeManager->get($employeeId);

        $order = new Order(
            $client,
            $employee,
            $orderStartTime,
            $cost,
            $duration,
            $type,
            OrderDataProvider::STATUS_NEW,
            $communication,
            $clientTarget,
            $clientComment,
            $employeeComment
        );

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $normalizedOrder = $this->normalizer->normalize(
            $order,
            null,
            [OrderNormalizer::CONTEXT_TYPE_KEY => OrderNormalizer::TYPE_IN_EMAIL_ORDER_NOTIFICATION]
        );

        $this->mailManager->sendTwigMailHtml(
            MailManager::ORDER_FOR_PAYMENT_TEMPLATE,
            ['mail_to' => [$order->getClient()->getEmail()]],
            $normalizedOrder
        );

        return $order;
    }

    /**
     * @param string $id
     * @param string $employeeId
     * @param string $status
     * @param string $startTime
     * @param float $cost
     * @param int $duration
     * @param string|null $communication
     * @param string|null $clientTarget
     * @param string|null $clientComment
     * @param string|null $employeeComment
     * @param string|null $type
     * @throws AppException
     */
    public function edit(
        string $id,
        string $employeeId,
        string $status,
        string $startTime,
        float $cost,
        int $duration,
        ?string $communication,
        ?string $clientTarget,
        ?string $clientComment,
        ?string $employeeComment,
        ?string $type = OrderDataProvider::TYPE_ONLINE
    ): Order {

        $order = $this->orderRepository->find($id);
        try {
            $startTime = DateTimeUtils::parse($startTime);
        } catch (\Exception $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
        $employee = $this->employeeManager->get($employeeId);

        if ($status === OrderDataProvider::STATUS_CANCELLED) {
            $this->cancel($id);
        } else {
            $order->setStatus($status);
        }

        $order->setCost($cost);
        $order->setDuration($duration);
        $order->setEmployee($employee);
        $order->setType($type);
        $order->setStartTime($startTime);
        $order->setCommunication($communication);
        $order->setClientTarget($clientTarget);
        $order->setClientComment($clientComment);
        $order->setEmployeeComment($employeeComment);

        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string $id
     * @param string $employeeId
     * @param string $status
     * @param string $startTime
     * @param string|null $communication
     * @param string|null $clientTarget
     * @param string|null $clientComment
     * @param string|null $employeeComment
     * @param string|null $type
     * @throws AppException
     */
    public function adminEdit(
        string $id,
        string $employeeId,
        string $status,
        string $startTime,
        ?string $communication,
        ?string $clientTarget,
        ?string $clientComment,
        ?string $employeeComment,
        ?string $type = OrderDataProvider::TYPE_ONLINE
    ): Order {
        $order = $this->get($id);
        try {
            $startTime = DateTimeUtils::parse($startTime);
        } catch (\Exception $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
        $employee = $this->employeeManager->get($employeeId);

        if ($status === OrderDataProvider::STATUS_CANCELLED) {
            $this->cancel($id);
        } else {
            $order->setStatus($status);
        }

        $order->setEmployee($employee);
        $order->setType($type);
        $order->setStartTime($startTime);
        $order->setCommunication($communication);
        $order->setClientTarget($clientTarget);
        $order->setClientComment($clientComment);
        $order->setEmployeeComment($employeeComment);

        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string|null $orderDate
     * @return mixed[]|object[]
     */
    public function list(?string $orderDate)
    {
        if ($orderDate !== null) {
            return $this->orderRepository->findBy(['start_time' => $orderDate]);
        } else {
            return $this->orderRepository->findAll();
        }
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
            $dateFilter = $filters['startTime'] ?? null;

            if ($dateFilter !== null) {
                $startTime = DateTimeUtils::parse($dateFilter)->modify('today');
                $finalTime = $startTime->modify('tomorrow');
            } else {
                $startTime = null;
                $finalTime = null;
            }

            $items = $this->orderRepository->searchForAdmin($startTime, $finalTime, $page, $limit, $filters);
            $total = $this->orderRepository->countByForAdmin($startTime, $finalTime, $filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
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
        if (!$user instanceof User) {
            throw new AppException('Auth error', Response::HTTP_NOT_FOUND);
        }

        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $includeForthcomingOrders = array_key_exists('forthcoming', $filters)
                ? filter_var($filters['forthcoming'], FILTER_VALIDATE_BOOLEAN)
                : false;
            $includeNotPaidOrders = array_key_exists('notPaid', $filters)
                ? filter_var($filters['notPaid'], FILTER_VALIDATE_BOOLEAN)
                : false;
            $includePastOrders = array_key_exists('past', $filters)
                ? filter_var($filters['past'], FILTER_VALIDATE_BOOLEAN)
                : false;
            $includeNotRatedOrders = array_key_exists('notRated', $filters)
                ? filter_var($filters['notRated'], FILTER_VALIDATE_BOOLEAN)
                : false;
            $includeCancelledOrders = array_key_exists('cancelled', $filters)
                ? filter_var($filters['cancelled'], FILTER_VALIDATE_BOOLEAN)
                : false;

            $idForSearch = $filters['customUserId'] ?? $user->getId();

            $items = $this->orderRepository->search(
                $idForSearch,
                $page,
                $limit,
                $includeForthcomingOrders,
                $includeNotPaidOrders,
                $includePastOrders,
                $includeNotRatedOrders,
                $includeCancelledOrders
            );
            $total = $this->orderRepository->countBy(
                $idForSearch,
                $includeForthcomingOrders,
                $includeNotPaidOrders,
                $includePastOrders,
                $includeNotRatedOrders,
                $includeCancelledOrders
            );

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param string $id
     * @return void
     * @throws AppException
     */
    public function checkOrderForCrossing(string $id): void
    {
        $order = $this->get($id);
        try {
            $result = $this->orderRepository->findCrossingOrder(
                $order->getEmployee()->getId(),
                $order->getStartTime(),
                $order->getDuration()
            );

            if ($result !== null) {
                throw new AppException("This time is no longer available for consultation", Response::HTTP_BAD_REQUEST);
            }
        } catch (NonUniqueResultException $exception) {
            throw new AppException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param string $orderId
     * @throws AppException
     */
    public function paid(string $orderId): void
    {
        $order = $this->get($orderId);
        $normalizedOrder = $this->normalizer->normalize(
            $order,
            null,
            [OrderNormalizer::CONTEXT_TYPE_KEY => OrderNormalizer::TYPE_IN_EMAIL_ORDER_NOTIFICATION]
        );

        $this->mailManager->sendTwigMailHtml(
            MailManager::NEW_ORDER_TEMPLATE,
            ['mail_to' => [$order->getClient()->getEmail()]],
            $normalizedOrder
        );
    }

    /**
     * @param string $orderId
     * @return Order
     * @throws AppException
     */
    public function cancel(string $orderId): Order
    {
        $user = $this->getLoggedInUser();

        if (!$user instanceof User) {
            throw new AppException('Auth error', Response::HTTP_FORBIDDEN);
        }

        $order = $this->get($orderId);
        $order->setStatus(OrderDataProvider::STATUS_CANCELLED);
        $this->entityManager->flush();

        $normalizedOrder = $this->normalizer->normalize(
            $order,
            null,
            [OrderNormalizer::CONTEXT_TYPE_KEY => OrderNormalizer::TYPE_IN_EMAIL_ORDER_NOTIFICATION]
        );

        $this->mailManager->sendTwigMailHtml(
            MailManager::CANCEL_ORDER_TEMPLATE,
            ['mail_to' => [$order->getClient()->getEmail()]],
            $normalizedOrder
        );

        return $order;
    }

    /**
     * @param string $id
     * @return Order
     * @throws AppException
     */
    public function get(string $id): Order
    {
        $order = $this->orderRepository->find($id);
        if (!$order instanceof Order) {
            throw new AppException('Order is not found', Response::HTTP_NOT_FOUND);
        }

        return $order;
    }

    /**
     * @param int $code
     * @return Order|null
     */
    public function findByCode(int $code): ?Order
    {
        $result = $this->orderRepository->findOneBy(['code' => $code]);

        return $result;
    }

    /**
     * @param string $categoryId
     * @param string $employeeId
     * @param string $clientId
     * @param string $startTime
     * @param float $incomingCost
     * @param int $duration
     * @param string $type
     * @param string|null $communication
     * @param string|null $clientTarget
     * @param string|null $comment
     * @return Order
     * @throws AppException
     */
    public function createOnlineOrder(
        string $categoryId,
        string $employeeId,
        string $clientId,
        string $startTime,
        float $incomingCost,
        int $duration,
        string $type,
        ?string $communication,
        ?string $clientTarget,
        ?string $comment
    ): Order {
        $cost = $this->orderDefaultCost;
        $date = DateTimeUtils::parse($startTime);

        if (!OrderDataProvider::isTypeAllowed($type)) {
            throw new AppException("Selected order type is not allowed!", Response::HTTP_BAD_REQUEST);
        }
        if ($incomingCost !== null) {
            $cost = $incomingCost;
        }

        $order = $this->create(
            $cost,
            DateTimeUtils::formatDate($date, DateTimeUtils::FORMAT_DEFAULT),
            $clientId,
            $employeeId,
            $categoryId,
            $type,
            $duration,
            $communication,
            $clientTarget,
            $comment
        );

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string $id
     * @param string $employee
     * @param string $startTime
     * @param float $cost
     * @param int $duration
     * @param string|null $type
     * @param string|null $communication
     * @param string|null $clientTarget
     * @param string|null $clientComment
     * @return Order
     * @throws AppException
     */
    public function editOnlineOrder(
        string $id,
        string $employee,
        string $startTime,
        float $cost,
        int $duration,
        ?string $type,
        ?string $communication,
        ?string $clientTarget,
        ?string $clientComment
    ): Order {
        $order = $this->get($id);

        if ($order->getStatus() !== OrderDataProvider::STATUS_NEW) {
            throw new AppException("Changes for this order are not allowed!", Response::HTTP_BAD_REQUEST);
        }

        $result = $this->edit(
            $id,
            $employee,
            OrderDataProvider::STATUS_NEW,
            $startTime,
            $cost,
            $duration,
            $communication,
            $clientTarget,
            $clientComment,
            null,
            $type
        );

        return $result;
    }

    /**
     * @param array $filters
     * @return OrdersCountResultModel
     * @throws AppException
     */
    public function countOrders(array $filters): OrdersCountResultModel
    {
        $startTime = $filters['startTime'] ?? null;
        $finalTime = $filters['finalTime'] ?? null;

        if ($startTime === null && $finalTime === null) {
            $startTime = DateTimeUtils::now();
            $finalTime = $startTime->modify('+30 day');
        } elseif ($startTime !== null && $finalTime !== null) {
            $startTime = DateTimeUtils::parse($startTime)->setTime(0, 0);
            $finalTime = DateTimeUtils::parse($finalTime)->setTime(24, 0);
        } elseif ($startTime !== null && $finalTime === null) {
            $startTime = DateTimeUtils::parse($startTime)->setTime(0, 0);
            $finalTime = $startTime->modify('+30 day');
        } else {
            throw new AppException('Not correct time interval', Response::HTTP_BAD_REQUEST);
        }
        try {
            $all = $this->orderRepository->countAllOrders($startTime, $finalTime, $filters);
            $notPaid = $this->orderRepository->countNotPaidOrders($startTime, $finalTime, $filters);
            $paid = $this->orderRepository->countPaidOrders($startTime, $finalTime, $filters);

            return new OrdersCountResultModel($all, $notPaid, $paid);
        } catch (NoResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        } catch (NonUniqueResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }

    /**
     * @param string $id
     * @param int $rating
     * @param string|null $ratingComment
     * @throws AppException
     */
    public function rate(string $id, int $rating, ?string $ratingComment): void
    {
        if (OrderDataProvider::isRatingCommentRequired($rating, $ratingComment)) {
            throw new AppException("Rating comment is required!", Response::HTTP_BAD_REQUEST);
        }

        $order = $this->get($id);
        $order->setRating($rating);
        $order->setRatingComment($ratingComment);

        $this->entityManager->flush();
    }
}
