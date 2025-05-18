<?php

namespace App\Orchid\Resources;

use App\Models\Menu;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

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

    public function rules(Model $model): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_he' => ['required', 'string', 'max:255'],
            'slug' => ['required',
                'string',
                'max:255',
                'alpha_dash',
                // check for uniqueness and ignore the field if it has not changed
                Rule::unique(self::$model, 'slug')->ignore($model)],
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
            TD::make('title_en', 'Menu item in English'),
            TD::make('title_he', 'Menu item in Hebrew'),
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
            Sight::make('title_en', 'Menu item name in English'),
            Sight::make('title_he', 'Menu item name in Hebrew'),
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
