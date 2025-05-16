<?php

namespace App\Orchid\Resources;

use App\Models\Post;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class PostResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Post::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('title_en')
                ->title('Title for main page in English')
                ->required(),
            Input::make('title_he')
                ->title('Title for main page in Hebrew')
                ->required(),
            Input::make('body_en')
                ->title('Content for main page in English')
                ->required(),
            Input::make('body_he')
                ->title('Content for main page in Hebrew')
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
            TD::make('title.en', 'Menu item in English')->render(fn($menu) => $menu->getTranslation('title', 'en')),
            TD::make('title.he', 'Menu item in Hebrew')->render(fn($menu) => $menu->getTranslation('title', 'he')),
            TD::make('body.en', 'Menu item in English')->render(fn($menu) => $menu->getTranslation('title', 'en')),
            TD::make('body.he', 'Menu item in Hebrew')->render(fn($menu) => $menu->getTranslation('title', 'he')),
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
            Sight::make('title', 'Post name in english')->render(fn($menu) => $menu->getTranslation('title', 'en')),
            Sight::make('title', 'Post name in hebrew')->render(fn($menu) => $menu->getTranslation('title', 'he')),
            Sight::make('body', 'Post content in english')->render(fn($menu) => $menu->getTranslation('title', 'en')),
            Sight::make('body', 'Post content in hebrew')->render(fn($menu) => $menu->getTranslation('title', 'he')),
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
