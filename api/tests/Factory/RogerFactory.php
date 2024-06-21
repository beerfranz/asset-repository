<?php

namespace App\Tests\Factory;

// use Zenstruck\Foundry\Attributes\LazyValue;
use function Zenstruck\Foundry\lazy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Asset>
 *
 * @method        Asset|Proxy create(array|callable $attributes = [])
 * @method static Asset|Proxy createOne(array $attributes = [])
 * @method static Asset|Proxy find(object|array|mixed $criteria)
 * @method static Asset|Proxy findOrCreate(array $attributes)
 * @method static Asset|Proxy first(string $sortedField = 'id')
 * @method static Asset|Proxy last(string $sortedField = 'id')
 * @method static Asset|Proxy random(array $attributes = [])
 * @method static Asset|Proxy randomOrCreate(array $attributes = [])
 * @method static AssetRepository|RepositoryProxy repository()
 * @method static Asset[]|Proxy[] all()
 * @method static Asset[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Asset[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Asset[]|Proxy[] findBy(array $attributes)
 * @method static Asset[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Asset[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
abstract class RogerFactory extends ModelFactory
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

    protected static function getClass(): string
    {
        return 'fake';
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        
        return [
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            ->withoutPersisting()
            // ->afterInstantiate(function(Asset $asset): void {})
        ;
    }

    protected static function randomString(): string
    {
        return self::faker()->lexify('????????????');
    }

    protected function randomDateTimeImmutable(): string
    {
        return \DateTimeImmutable::createFromMutable(self::faker()->dateTime());
    }

}
