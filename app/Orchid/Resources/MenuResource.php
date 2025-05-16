<?php

namespace App\Orchid\Resources;

use App\Models\Menu;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class MenuResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Menu::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */

    public function fields(): array
    {
        return [
            Input::make('title_en')
                ->title('Menu item in English')
                ->required(),

            Input::make('title_he')
                ->title('Menu item in Hebrew')
                ->required(),

            Input::make('slug')
                ->title('Slug')
                ->required(),
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('title', 'Menu item in English')->render(fn($menu) => $menu->getTranslation('title', 'en')),
            TD::make('title', 'Menu item in Hebrew')->render(fn($menu) => $menu->getTranslation('title', 'he')),
            TD::make('slug'),
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('title', 'Menu item name in English')->render(fn($menu) => $menu->getTranslation('title', 'en')),
            Sight::make('title', 'Menu item name in Hebrew')->render(fn($menu) => $menu->getTranslation('title', 'he')),
            Sight::make('slug'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [];
    }
}
