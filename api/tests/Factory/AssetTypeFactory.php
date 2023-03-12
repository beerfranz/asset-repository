<?php

namespace App\Tests\Factory;

use App\Entity\AssetType;
use App\Repository\AssetTypeRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<AssetType>
 *
 * @method        AssetType|Proxy create(array|callable $attributes = [])
 * @method static AssetType|Proxy createOne(array $attributes = [])
 * @method static AssetType|Proxy find(object|array|mixed $criteria)
 * @method static AssetType|Proxy findOrCreate(array $attributes)
 * @method static AssetType|Proxy first(string $sortedField = 'id')
 * @method static AssetType|Proxy last(string $sortedField = 'id')
 * @method static AssetType|Proxy random(array $attributes = [])
 * @method static AssetType|Proxy randomOrCreate(array $attributes = [])
 * @method static AssetTypeRepository|RepositoryProxy repository()
 * @method static AssetType[]|Proxy[] all()
 * @method static AssetType[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AssetType[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static AssetType[]|Proxy[] findBy(array $attributes)
 * @method static AssetType[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static AssetType[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class AssetTypeFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->lexify('????????'),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(AssetType $assetType): void {})
        ;
    }

    protected static function getClass(): string
    {
        return AssetType::class;
    }
}
