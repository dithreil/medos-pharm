<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Model\ApiSchemaInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiSchemaArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ApiSchemaArgumentResolver constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        try {
            $reflection = new \ReflectionClass($argument->getType());
            if ($reflection->implementsInterface(ApiSchemaInterface::class)) {
                return true;
            }

            return false;
        } catch (\ReflectionException $exception) {
            return false;
        }
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator|iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $content = $request->getContent() ?: '{}';
        yield $this->serializer->deserialize($content, $argument->getType(), JsonEncoder::FORMAT);
    }
}
