<?php

namespace App\Orchid\Resources;

use App\Models\Post;
use App\Models\PostContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Orchid\Crud\Resource;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
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
                        $post->id => $post->title_en . ' | ' . $post->title_he
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
            TextArea::make('body_en')
                ->title('Content for main page in English')
                ->required(),
            TextArea::make('body_he')
                ->title('Content for main page in Hebrew')
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
            'body_en' => ['required', 'string', 'max:5000'],
            'body_he' => ['required', 'string', 'max:5000'],
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
            TD::make('post.title_en', 'Main Post in en')->render(function ($content) {
                return optional($content->post)->getTranslation('title', 'en');
            }),
            TD::make('post.title_he', 'Main Post in he')->render(function ($content) {
                return optional($content->post)->getTranslation('title', 'he');
            }),
            TD::make('title_en', 'Side title in English'),
            TD::make('title_he', 'Side title in Hebrew'),
            TD::make('body_en', 'Side content in English'),
            TD::make('body_he', 'Side content in Hebrew'),
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
            Sight::make('post_id', 'Main Post')->render(function ($content) {
                $post = $content->post;
                return $post ? "{$post->title_en} | {$post->title_he}" : '';
            }),
            Sight::make('title_en', 'Post name in english'),
            Sight::make('title_he', 'Post name in hebrew'),
            Sight::make('body_en', 'Post content in english'),
            Sight::make('body_he', 'Post content in hebrew'),
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
