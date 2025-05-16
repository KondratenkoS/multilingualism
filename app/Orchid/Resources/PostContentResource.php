<?php

namespace App\Orchid\Resources;

use App\Models\Post;
use App\Models\PostContent;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class PostContentResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = PostContent::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Select::make('post_id')
                ->options(
                    Post::all()->mapWithKeys(fn($post) => [
                        $post->id => $post->getTranslation('title', 'en') . ' | ' . $post->getTranslation('title', 'he')
                    ])
                )
                ->title('Select main post in english')
                ->required(),

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
            TD::make('post.title', 'Main Post in en')->render(function ($content) {
                return optional($content->post)->getTranslation('title', 'en');
            }),
            TD::make('post.title', 'Main Post in he')->render(function ($content) {
                return optional($content->post)->getTranslation('title', 'he');
            }),
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
