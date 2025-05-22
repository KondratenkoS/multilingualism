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
                        'uk' => 'Українська',
                        'en' => 'English',
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
                    'slug' => $post->slug,
                ],
        ];
    }

    public function delete(Request $request)
    {
//        dd($request->all());
        Post::find($request->input('post.id'))->delete();
    }

    public function update(Request $request): void
    {
//        dd($request->all());
        Post::find($request->input('post.id'))->update($request->post);
    }

    public function createPost(Request $request, Post $post): void
    {
        $data = $request->all();
//        dd($data['body']);
        $post->setTranslation('title', $data['lang'], $data['title']);
        $post->setTranslation('body', $data['lang'], $data['body']);
        $post->slug = $data['slug'];
        $post->save();
    }
}
