<?php

namespace App\Tasks\Entity;

use App\Tasks\Repository\TaskTagRepository;

use App\Tasks\Entity\Task;
use App\Tasks\Entity\TaskTemplate;

use Beerfranz\RogerBundle\Entity\RogerEntity;
// use Beerfranz\RogerBundle\Entity\RogerTagEntity;
use Beerfranz\RogerBundle\Entity\RogerIdTrait;
use Beerfranz\RogerBundle\Entity\RogerIdentifierTrait;
use Beerfranz\RogerBundle\Entity\RogerTagEntityTrait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskTagRepository::class)]
class TaskTag extends RogerEntity
{

	use RogerIdTrait;

	use RogerTagEntityTrait;

	/**
	 * @var Collection<int, Task>
	 */
	#[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'tags')]
	private Collection $tasks;

	/**
	 * @return Collection<int, Task>
	 */
	public function getTasks(): Collection
	{
		return $this->tasks;
	}

	public function addTask(Task $entity): static
	{
		if (!$this->tasks->contains($entity)) {
			$this->tasks->add($entity);
			$entity->addTag($this);
		}

		return $this;
	}

	public function removeTask(Task $entity): static
	{
		if ($this->tasks->removeElement($entity)) {
			$entity->removeTag($this);
		}

		return $this;
	}

	/**
	 * @var Collection<int, TaskTemplate>
	 */
	#[ORM\ManyToMany(targetEntity: TaskTemplate::class, mappedBy: 'tags')]
	private Collection $taskTemplates;

	/**
	 * @return Collection<int, TaskTemplate>
	 */
	public function getTaskTemplates(): Collection
	{
		return $this->taskTemplates;
	}

	public function addTaskTemplate(TaskTemplate $entity): static
	{
		if (!$this->taskTemplates->contains($entity)) {
			$this->taskTemplates->add($entity);
			$entity->addTag($this);
		}

		return $this;
	}

	public function removeTaskTemplate(TaskTemplate $entity): static
	{
		if ($this->taskTemplates->removeElement($entity)) {
			$entity->removeTag($this);
		}

		return $this;
	}
}
