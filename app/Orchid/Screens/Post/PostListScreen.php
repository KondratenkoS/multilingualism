<?php

namespace App\Orchid\Screens\Post;

use App\Models\Post;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PostListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => Post::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Pages list';
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create')
                ->modal('createPost')
                ->method('createPost'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('posts', [
                TD::make('id', 'ID'),
                TD::make('title.en', 'Page title'),
                TD::make('slug', 'Page slug'),
                TD::make('edit', 'Edit')
                    ->render(function (Post $post) {
                        return
                            ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (Post $post) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
            ]),

            Layout::modal('createPost', Layout::rows([
                Select::make('lang')
                    ->title('Language')
                    ->options([
                        'en' => 'English',
                        'uk' => 'Українська',
                        'fr' => 'French',
                        'sp' => 'Spanish',
                    ]),

                Input::make('title')
                    ->title('Title')
                    ->type('text'),

                Quill::make('body')
                    ->title('Body'),

                Input::make('slug')
                    ->title('Slug')
                    ->type('text'),

                Input::make('meta_title')
                    ->title('Meta tag for "title"')
                    ->type('text'),

                Input::make('meta_description')
                    ->title('Meta tag for "description"')
                    ->type('text'),

                Input::make('meta_keywords')
                    ->title('Meta tag for "keywords"')
                    ->type('text'),
            ]))->title('Create page')->applyButton('Create'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('post.id')->type('hidden'),
                    Input::make('post.slug')->title('Slug')->type('text'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en']))->mapWithKeys(function ($locale) {
                        return [
                            strtoupper($locale) => Layout::rows([
                                Input::make("post.title.$locale")
                                    ->title("Title ($locale)")
                                    ->type('text'),

                                Quill::make("post.body.$locale")
                                    ->title("Body ($locale)"),

                                Input::make("post.meta_title.$locale")
                                    ->title("Meta tag for title ($locale)")
                                    ->type('text'),

                                Input::make("post.meta_description.$locale")
                                    ->title("Meta tag for description ($locale)")
                                    ->type('text'),

                                Input::make("post.meta_keywords.$locale")
                                    ->title("Meta tag for keywords ($locale)")
                                    ->type('text'),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Edit page translation')->async('asyncGetPost'),

            Layout::modal('delete', Layout::rows([
                Input::make('post.id')->type('hidden'),
            ]))->title('Confirm deletion')->applyButton('Delete')->async('asyncGetPost'),

        ];

    }

    public function asyncGetPost(Post $post): array
    {
        return [
            'post' =>
                [
                    'id' => $post->id,
                    'title' => $post->getTranslations('title'),
                    'body' => $post->getTranslations('body'),
                    'meta_title' => $post->getTranslations('meta_title'),
                    'meta_description' => $post->getTranslations('meta_description'),
                    'meta_keywords' => $post->getTranslations('meta_keywords'),
                    'slug' => $post->slug,
                ],
        ];
    }

    public function delete(Request $request)
    {
        Post::find($request->input('post.id'))->delete();
    }

    public function update(Request $request): void
    {
        Post::find($request->input('post.id'))->update($request->post);
    }

    public function createPost(Request $request, Post $post): void
    {
        $data = $request->all();
        $post->setTranslation('title', $data['lang'], $data['title']);
        $post->setTranslation('body', $data['lang'], $data['body']);
        $post->setTranslation('meta_title', $data['lang'], $data['meta_title']);
        $post->setTranslation('meta_description', $data['lang'], $data['meta_description']);
        $post->setTranslation('meta_keywords', $data['lang'], $data['meta_keywords']);
        $post->slug = $data['slug'];
        $post->save();
    }
}
