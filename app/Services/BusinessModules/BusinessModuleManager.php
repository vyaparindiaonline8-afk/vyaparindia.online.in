<?php

namespace App\Services\BusinessModules;

use App\Models\SellerPage;

class BusinessModuleManager
{
    /**
     * @var BusinessModuleInterface[]
     */
    protected static array $modules = [];

    /**
     * Boot and register available business modules
     */
    protected static function bootModules(): void
    {
        if (!empty(static::$modules)) {
            return;
        }

        $registered = [
            new HardwarePipesModule(),
            new FashionLifestyleModule(),
            new FoodDiningModule(),
            new AnajMandiModule(),
            new GroceryFmcgModule(),
        ];

        foreach ($registered as $module) {
            static::$modules[$module->getId()] = $module;
        }
    }

    /**
     * Get all registered modules
     *
     * @return BusinessModuleInterface[]
     */
    public static function all(): array
    {
        static::bootModules();
        return static::$modules;
    }

    /**
     * Find a module by ID with graceful fallback to hardware_pipes
     */
    public static function get(?string $id): BusinessModuleInterface
    {
        static::bootModules();
        if ($id && isset(static::$modules[$id])) {
            return static::$modules[$id];
        }

        return static::$modules['hardware_pipes'];
    }

    /**
     * Get active module for a seller page
     */
    public static function forSellerPage(?SellerPage $page): BusinessModuleInterface
    {
        $type = $page ? ($page->business_type ?? 'hardware_pipes') : 'hardware_pipes';
        return static::get($type);
    }
}
