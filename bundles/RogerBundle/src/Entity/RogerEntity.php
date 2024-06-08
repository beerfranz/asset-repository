<?php

namespace Beerfranz\RogerBundle\Entity;

use Beerfranz\RogerBundle\Message\RogerAsyncMessage;

use JsonSerializable;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class RogerEntity implements RogerEntityInterface, JsonSerializable {
	
	public function __construct(array $data = []) {
		$this->hydrator($data);
	}

	public function __toString() {
		if (method_exists($this, 'getIdentifier')) {
			$identifier = $this->getIdentifier();
		} else {
			$identifier = $this->getId();
		}

		return get_class($this) . '#' . $identifier;
	}
	
	public function hydrator(array $data = []): self
	{
		foreach ($data as $name => $value) {
			$this->__set($name, $value);
		}

		return $this;
	}
	
	protected function __getSerializer(): Serializer
	{

		$classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

		$defaultContext = [
			AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, ?string $format, ?array $context): string {
				return $object->getId();
			},
		];
		// public function __construct(?ClassMetadataFactoryInterface $classMetadataFactory = null, ?NameConverterInterface $nameConverter = null, ?PropertyAccessorInterface $propertyAccessor = null, ?PropertyTypeExtractorInterface $propertyTypeExtractor = null, ?ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, ?callable $objectClassResolver = null, array $defaultContext = [], ?PropertyInfoExtractorInterface $propertyInfoExtractor = null)
		$normalizers = [new DateTimeNormalizer, new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext)];
		$serializer = new Serializer($normalizers, []);

		return $serializer;
	}

	public function __set($name, $value)
	{

		$methodName = 'set' . ucfirst($name);
		
		if (method_exists($this, $methodName)) {
			$this->$methodName($value);
			return $this;
		}
		
		if (property_exists($this, $name)) {
			try {
				$this->$name = $value;
			} catch (\Exception $e) {
				return $this;
			}

			return $this;
		}

		return $this;
		// throw new \Exception('The property ' . $name . ' not exists, or no method ' . $methodName());
	}
	
	public function __get($name) {
		$methodName = 'get' . ucfirst($name);
		
		if (method_exists($this, $methodName)) {
			return $this->$methodName();
		}
		
		if (property_exists($this, $name)) {
			return $this->$name;
		}
		
		throw new \Exception('The property ' . $name . ' not exists, or no method ' . $methodName());
	}

	public function jsonSerialize(): mixed
	{
		$result = array();

		foreach (get_object_vars($this) as $name => $value)
		{
			$result[$name] = $this->__get($name);
		}
		return $result;
	}

	public function toArray($context = []): array
	{
		return $this->__getSerializer()->normalize($this, null, $context);
	}

	static function generateUuid(): string
	{
		return Uuid::v7()->__toString();
	}

	public function getMessengerSerializationGroup(): ?string
	{
		return null;
	}

	public function getMessengerClass(): string
	{
		return RogerAsyncMessage::class;
	}

	public function getSequenceClass(): ?string
	{
		return null;
	}

	public function getSequencedProperties(): array
	{
		return [];
	}

}
