<?php

namespace App\Common\Entity;

class AssetAttributeType {

  protected ?string $value = null;

  protected ?string $condition = null;

  protected bool $propagateToInstances = false;

  protected bool $childAssetInheritance = false;

  public function __construct($attribute)
  {
    if (is_string($attribute)) {
      $this->value = $attribute;
      $this->condition = "value == '$attribute'";
    } elseif(is_array($attribute)) {
      if (isset($attribute['value']))
        $this->value = $attribute['value'];

      if (isset($attribute['condition']))
        $this->condition = $attribute['condition'];

      if (isset($attribute['propagateToInstances']))
        $this->propagateToInstances = $attribute['propagateToInstances'];

      if (isset($attribute['childAssetInheritance']))
        $this->childAssetInheritance = $attribute['childAssetInheritance'];

    } else {
      throw new \Exception('Unexpected attribute ' . $attribute);
    }

    return $this;
  }

  public function getValue(): ?string
  {
    return $this->value;
  }

  public function getCondition(): ?string
  {
    return $this->condition;
  }

  public function getPropagateToInstances(): bool
  {
    return $this->propagateToInstances;
  }

  public function getChildAssetInheritance(): bool
  {
    return $this->getChildAssetInheritance;
  }

  public function serialize(): array
  {
    return [
      '@type' => (new \ReflectionClass($this))->getShortName(),
      'value' => $this->getValue(),
      'condition' => $this->getCondition(),
      'propagateToInstances' => $this->getPropagateToInstances(),
    ];
  }
}
