<?php

declare(strict_types=1);

namespace App\Manager;

use App\DataProvider\NomenclatureDataProvider;
use App\Entity\Nomenclature;
use App\Exception\AppException;
use App\Model\PaginatedDataModel;
use App\Repository\NomenclatureRepository;
use App\Traits\EntityManagerAwareTrait;
use App\Traits\TokenStorageAwareTrait;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class NomenclatureManager
{
    use EntityManagerAwareTrait;
    use TokenStorageAwareTrait;

    /**
     * @var ProducerManager
     */
    private ProducerManager $producerManager;

    /**
     * @var NomenclatureRepository
     */
    private NomenclatureRepository $nomenclatureRepository;

    /**
     * @param ProducerManager $producerManager
     * @param NomenclatureRepository $nomenclatureRepository
     */
    public function __construct(ProducerManager $producerManager, NomenclatureRepository $nomenclatureRepository)
    {
        $this->producerManager = $producerManager;
        $this->nomenclatureRepository = $nomenclatureRepository;
    }

    /**
     * @param string $producerId
     * @param string $name
     * @param string $medicalForm
     * @param bool $isVat
     * @return Nomenclature
     * @throws AppException
     */
    public function create(string $producerId, string $name, string $medicalForm, bool $isVat): Nomenclature
    {
        $producer = $this->producerManager->get($producerId);
        $medicalFormIndex = NomenclatureDataProvider::getIntValueOfMedForms($medicalForm);

        $nomenclature = new Nomenclature($producer, $name, $medicalFormIndex, $isVat);

        $this->entityManager->persist($nomenclature);

        $producer->addNomenclature($nomenclature);
        $this->entityManager->flush();

        return $nomenclature;
    }

    /**
     * @param string $id
     * @param string $producerId
     * @param string $name
     * @param string $medicalForm
     * @param bool $isVat
     * @return Nomenclature
     * @throws AppException
     */
    public function edit(string $id, string $producerId, string $name, string $medicalForm, bool $isVat): Nomenclature
    {
        $nomenclature = $this->get($id);
        $producer = $this->producerManager->get($producerId);
        $medicalFormIndex = NomenclatureDataProvider::getIntValueOfMedForms($medicalForm);

        if ($nomenclature->getProducer() !== $producer) {
            $oldProducer = $nomenclature->getProducer();
            $oldProducer->removeNomenclature($nomenclature);
            $producer->addNomenclature($nomenclature);
        }

        $nomenclature->setName($name);
        $nomenclature->setMedicalForm($medicalFormIndex);
        $nomenclature->setIsVat($isVat);

        $this->entityManager->flush();

        return $nomenclature;
    }

    /**
     * @param string $id
     * @return Nomenclature
     * @throws AppException
     */
    public function get(string $id): Nomenclature
    {
        $result = $this->nomenclatureRepository->find($id);

        if (!$result instanceof Nomenclature) {
            throw new AppException("Nomenclature was not found!", Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    /**
     * @param array $filters
     * @return PaginatedDataModel
     * @throws AppException
     */
    public function search(array $filters): PaginatedDataModel
    {
        try {
            $page = intval($filters['page'] ?? 1);
            $limit = intval($filters['limit'] ?? 10);

            $items = $this->nomenclatureRepository->search($filters, $page, $limit);
            $total = $this->nomenclatureRepository->countBy($filters);

            return new PaginatedDataModel($total, $limit, $page, $items);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}
