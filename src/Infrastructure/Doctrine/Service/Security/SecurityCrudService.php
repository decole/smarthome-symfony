<?php


namespace App\Infrastructure\Doctrine\Service\Security;


use App\Application\Helper\StringHelper;
use App\Application\Http\Web\Security\Dto\CrudSecurityDto;
use App\Application\Service\Validation\ValidationDtoInterface;
use App\Domain\Doctrine\DeviceCommon\Entity\StatusMessage;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Infrastructure\Doctrine\Interfaces\EntityInterface;
use App\Infrastructure\Doctrine\Service\Security\Factory\SecurityCrudFactory;
use App\Infrastructure\Doctrine\Traits\CommonCrudFieldTraits;
use App\Infrastructure\Doctrine\Traits\StatusMessageTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

final class SecurityCrudService
{
    use StatusMessageTrait, CommonCrudFieldTraits;

    public function __construct(private SecurityCrudFactory $crud)
    {
    }

    public function validate(CrudSecurityDto $relayDto, bool $isUpdate = false): ConstraintViolationListInterface
    {
        $this->crud->getValidationService()->setValue($relayDto);

        return $this->crud->validate($isUpdate);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function create(ValidationDtoInterface $dto): EntityInterface
    {
        assert($dto instanceof CrudSecurityDto);

        $entity = $this->getNewEntityByDto($dto);

        return $this->crud->save($entity);
    }

    public function list(): array
    {
        return $this->crud->list();
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function update(string $id, CrudSecurityDto $dto): EntityInterface
    {
        $entity = $this->crud->getRepository()->findById($id);

        assert($entity instanceof Security);

        $entity->setType($dto->type);

        $this->setDtoToEntityCommonParams($entity, $dto);

        $entity->setDetectPayload($dto->detectPayload);
        $entity->setHoldPayload($dto->holdPayload);
        $entity->setLastCommand($dto->lastCommand);
        $entity->setParams($dto->params);

        $entity->setStatusMessage(new StatusMessage(
            $dto->message_info,
            $dto->message_ok,
            $dto->message_warn
        ));

        $entity->setStatus($dto->status === 'on' ? Security::STATUS_ACTIVE : Security::STATUS_DEACTIVATE);
        $entity->setNotify($dto->notify === 'on');
        $entity->setUpdatedAt();

        return $this->crud->save($entity);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    public function delete(string $id): void
    {
        $entity = $this->crud->getRepository()->findById($id);

        if ($entity) {
            $this->crud->delete($entity);
        }
    }

    public function getTypes(): array
    {
        return Security::SECURITY_TYPES;
    }

    public function createSecurityDto(?Request $request): CrudSecurityDto
    {
        $dto = new CrudSecurityDto();

        if ($request === null) {
            return $dto;
        }

        foreach ($request->request as $param => $value) {
            if (property_exists($dto, $param)) {
                $dto->$param = StringHelper::sanitize($value);
            }
        }

        return $dto;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function entityRelayDto(string $id): CrudSecurityDto
    {
        $entity = $this->crud->getRepository()->findById($id);

        assert($entity instanceof Security);

        $dto = new CrudSecurityDto();

        $this->setEntityToDtoCommonParams($dto, $entity);

        $dto->detectPayload = $entity->getDetectPayload();
        $dto->holdPayload = $entity->getHoldPayload();
        $dto->lastCommand = $entity->getLastCommand();

        $dto->params = $entity->getParams();

        $this->setStatusMessage($dto, $entity);

        $dto->status = $entity->getStatus() === Security::STATUS_ACTIVE ? 'on' : null;

        return $dto;
    }

    /**
     * @param CrudSecurityDto $dto
     * @return Security
     */
    public function getNewEntityByDto(CrudSecurityDto $dto): Security
    {
        Assert::inArray($dto->type, $this->getTypes());

        return new Security(
            securityType: $dto->type,
            name: $dto->name,
            topic: $dto->topic,
            payload: $dto->payload,
            detectPayload: $dto->detectPayload,
            holdPayload: $dto->holdPayload,
            lastCommand: $dto->lastCommand,
            params: $dto->params,
            statusMessage: new StatusMessage(
                $dto->message_info,
                $dto->message_ok,
                $dto->message_warn
            ),
            status: $dto->status === 'on' ? Security::STATUS_ACTIVE : Security::STATUS_DEACTIVATE,
            notify: $dto->notify === 'on',
        );
    }
}