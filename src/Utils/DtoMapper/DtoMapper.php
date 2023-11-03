<?php

namespace App\Utils\DtoMapper;

use App\Utils\FieldNormalizerCollection;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class DtoMapper
{
    public function __construct(
        private PropertyInfoExtractorInterface $propertyInfoExtractor,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer,
        private PropertyAccessorInterface $propertyAccessor,
        private FieldNormalizerCollection $fieldNormalizerCollection
    )
    {
    }

    public function mapFromDto(object $from, object $to, ?MapOptions $options = null): void
    {
        $this->map($from, $to, get_class($from), $options);
    }

    public function mapToDto(object $from, object $to, ?MapOptions $options = null): void
    {
        $this->map($from, $to, get_class($to), $options);
    }

    private function map(object $from, object $to, string $dtoClass, ?MapOptions $options = null): void
    {
        $ignoredAttributes = $options?->getIgnoredAttributes() ?? [];
        $attributes = $this->getAttributes($dtoClass, $ignoredAttributes);
        $fromData = $this->normalizer->normalize($from, null, [
            DateTimeNormalizer::TIMEZONE_KEY => $options?->getTimezone() ?? null,
            AbstractNormalizer::ATTRIBUTES => $attributes,
            AbstractNormalizer::CALLBACKS => $this->getCallbacks($options),
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER =>
                fn($object) => $this->propertyAccessor->getValue($object, 'id')
        ]);

        $this->denormalizer->denormalize($fromData, get_class($to), null, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $to,
        ]);
    }

    private function getCallbacks(?MapOptions $mapOptions): array
    {
        $resultCallbacks = [];
        $callbacks = $mapOptions?->getCallbacks();
        $normalizers = $mapOptions?->getFieldNormalizers();
        $denormalizers = $mapOptions?->getFieldDenormalizers();

        if ($callbacks) {
            $resultCallbacks = $callbacks;
        }

        if ($normalizers) {
            foreach ($normalizers as $property => $normalizerClass) {
                if (!isset($resultCallbacks[$property])) {
                    $normalizer = $this->fieldNormalizerCollection->get($normalizerClass);
                    $resultCallbacks[$property] = function (mixed $value) use ($normalizer): mixed {
                        return $normalizer->normalize($value);
                    };
                }
            }
        } else if ($denormalizers) {
            foreach ($denormalizers as $property => $denormalizerClass) {
                if (!isset($resultCallbacks[$property])) {
                    $denormalizer = $this->fieldNormalizerCollection->get($denormalizerClass);
                    $resultCallbacks[$property] = function (mixed $value) use ($denormalizer): mixed {
                        return $denormalizer->denormalize($value);
                    };
                }
            }
        }

        return $resultCallbacks;
    }

    private function getAttributes(string $class, array $ignoredAttributes = []): array
    {
        $propertyInfoExtractor = $this->propertyInfoExtractor;
        $attributes = [];
        $properties = $propertyInfoExtractor->getProperties($class);
        foreach ($properties as $property) {
            $ignoredValue = $this->findIgnoredAttribute($property, $ignoredAttributes);
            if ($ignoredValue !== null && !is_array($ignoredValue)) {
                continue;
            }

            $type = $propertyInfoExtractor->getTypes($class, $property)[0] ?? null;
            $innerClass = null;
            if ($type->isCollection()) {
                $collectionType = $type->getCollectionValueTypes()[0] ?? null;
                $innerClass = $collectionType?->getClassName();
            } else if ($type->getBuiltinType() == 'object') {
                $innerClass = $type->getClassName();
            }

            $doObjectProcessing = false;
            if ($innerClass) {
                $doObjectProcessing = !$this->instanceOf($innerClass, [\DateTimeInterface::class]);
            }

            if ($doObjectProcessing) {
                $innerIgnoredAttributes = is_array($ignoredValue) ? $ignoredValue : [];
                $attributes[$property] = $this->getAttributes($innerClass, $innerIgnoredAttributes);
            } else {
                $attributes[] = $property;
            }
        }

        return $attributes;
    }

    private function instanceOf(string $class, array $classesOf): bool
    {
        $rc = new \ReflectionClass($class);

        return !empty(array_intersect([$class, ...$rc->getInterfaceNames()], $classesOf));
    }

    private function findIgnoredAttribute(string $property, array $ignoredAttributes): mixed
    {
        $value = $ignoredAttributes[$property] ?? null;
        if ($value === null) {
            $foundKey = array_search($property, $ignoredAttributes);
            if ($foundKey !== false) {
                $value = $ignoredAttributes[$foundKey];
            }
        }

        return $value;
    }
}
