<?php

declare(strict_types=1);

namespace App\Entity\Document;

use App\DataProvider\DocumentDataProvider;
use App\Entity\Change\StockChange;
use App\Exception\AppException;
use App\Utils\DateTimeUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\MappedSuperclass()
 */
class BaseDocument
{
    /**
     * @var string
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id()
     */
    protected string $id;

    /**
     * @var bool
     * @ORM\Column(name="is_set", type="boolean")
     */
    protected bool $isSet;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=30)
     */
    protected string $type;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="create_time", type="datetime_immutable")
     */
    protected \DateTimeImmutable $createTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(name="update_time", type="datetime_immutable")
     */
    protected \DateTimeImmutable $updateTime;

    /**
     * @param string $type
     * @param bool $isSet
     * @throws AppException
     */
    public function __construct(string $type, bool $isSet = false)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->type = $type;
        $this->isSet = $isSet;

        $date = DateTimeUtils::now();
        $this->createTime = $date;
        $this->updateTime = $date;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return $this->isSet;
    }

    /**
     * @param bool $isSet
     */
    public function setIsSet(bool $isSet): void
    {
        $this->isSet = $isSet;
    }

    /**
     * @param string $type
     * @throws AppException
     */
    public function setType(string $type): void
    {
        if (!DocumentDataProvider::isTypeAllowed($type)) {
            throw new AppException("Document type is not allowed!");
        }

        $this->type = $type;
    }

    /**
     * @ORM\PrePersist()
     * @throws AppException
     */
    public function onPrePersist(): void
    {
        $this->createTime = DateTimeUtils::now();
        $this->updateTime = DateTimeUtils::now();
    }

    /**
     * @ORM\PreUpdate()
     * @throws AppException
     */
    public function onPreUpdate(): void
    {
        $this->updateTime = DateTimeUtils::now();
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreateTime(): \DateTimeImmutable
    {
        return $this->createTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdateTime(): \DateTimeImmutable
    {
        return $this->updateTime;
    }
}
