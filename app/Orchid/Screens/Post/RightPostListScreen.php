<?php

namespace App\Orchid\Screens\Post;

use App\Models\LeftPost;
use App\Models\RightPosts;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class RightPostListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => RightPosts::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Right side posts';
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
                TD::make('title.en', 'Post title'),
                TD::make('edit', 'Edit')
                    ->render(function (RightPosts $post) {
                        return
                            ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (RightPosts $post) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
            ]),


            Layout::modal('createPost', [
                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))->mapWithKeys(function ($label, $locale) {
                        return [
                            $label => Layout::rows([
                                Input::make("post.title.$locale")
                                    ->title("Title ($label)")
                                    ->type('text'),

                                Quill::make("post.body.$locale")
                                    ->title("Body ($label)"),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Create page')->applyButton('Create')->async('asyncGetPost'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('post.id')->type('hidden'),
                ]),
                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))->mapWithKeys(function ($label, $locale) {
                        return [
                            $label => Layout::rows([
                                Input::make("post.title.$locale")
                                    ->title("Title ($label)")
                                    ->type('text'),

                                Quill::make("post.body.$locale")
                                    ->title("Body ($label)"),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Edit page translation')->applyButton('Edit')->async('asyncGetPost'),

            Layout::modal('delete', Layout::rows([
                Input::make('post.id')->type('hidden'),
            ]))->title('Confirm deletion')->applyButton('Delete')->async('asyncGetPost'),

        ];

    }

    public function asyncGetPost(RightPosts $post): array
    {
        return [
            'post' => [
                'id' => $post->id,
                'title' => $post->getTranslations('title'),
                'body' => $post->getTranslations('body'),
            ],
        ];

    }

    public function update(Request $request): void
    {
        $data = $request->input('post');
        $post = RightPosts::findOrFail($data['id']);
        $this->createOrSave($post, $data);
    }

    public function createPost(Request $request, RightPosts $post): void
    {
        $data = $request->input('post');
        $this->createOrSave($post, $data);
    }

    private function createOrSave(RightPosts $post, array $data): void
    {
        foreach (['title', 'body'] as $field) {
            $post->setTranslations($field, $data[$field] ?? []);
        }

        $post->save();
    }

    public function delete(Request $request): void
    {
        RightPosts::find($request->input('post.id'))->delete();
    }
}
