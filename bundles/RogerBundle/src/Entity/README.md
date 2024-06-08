# Roger entity lib

## Tags

### Usage

Put in your target entity (ex: task)
```
<?php

namespace App\Tasks\Entity;

use App\Tasks\Entity\TaskTag;
use App\Tasks\Repository\TaskTagRepository;

#[ORM\Entity(repositoryClass: TaskTagRepository::class)]
class Task extends RogerEntity
{
    /**
     * @var Collection<int, TaskTag>
     */
    #[ORM\ManyToMany(targetEntity: TaskTag::class, inversedBy: 'tasks')]
    private Collection $tags;

    public function initTags()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return Collection<int, TaskTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(TaskTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(TaskTag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}

```


Put in your tag entity:
```
<?php

namespace App\Tasks\Entity;

use App\Tasks\Repository\TaskTagRepository;

#[ORM\Entity(repositoryClass: TaskTagRepository::class)]
class TaskTag extends RogerTagEntity
{

	/**
	 * @var Collection<int, RogerEntity>
	 */
	#[ORM\ManyToMany(targetEntity: RogerEntity::class, mappedBy: 'tags')]
	private Collection $entities;

	/**
	 * @return Collection<int, RogerEntity>
	 */
	public function getEntities(): Collection
	{
		return $this->entities;
	}

	public function addEntity(RogerEntity $entity): static
	{
		if (!$this->entities->contains($entity)) {
			$this->entities->add($entity);
			$entity->addGroup($this);
		}

		return $this;
	}

	public function removeEntity(RogerEntity $entity): static
	{
		if ($this->gens->removeElement($entity)) {
			$entity->removeGroup($this);
		}

		return $this;
	}
}
```

