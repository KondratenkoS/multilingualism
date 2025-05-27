<?php

namespace App\Orchid\Screens\Post;

use App\Models\LeftPost;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class LeftPostListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => LeftPost::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Left side posts';
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
                    ->render(function (LeftPost $post) {
                        return
                            ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (LeftPost $post) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
            ]),


            Layout::modal('createPost', [
                Layout::rows([
                    Input::make('post.slug')->title('Slug')->type('text'),
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

                                Input::make("post.meta_title.$locale")
                                    ->title("Meta tag for title ($label)")
                                    ->type('text'),

                                Input::make("post.meta_description.$locale")
                                    ->title("Meta tag for description ($label)")
                                    ->type('text'),

                                Input::make("post.meta_keywords.$locale")
                                    ->title("Meta tag for keywords ($label)")
                                    ->type('text'),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Create page')->applyButton('Create')->async('asyncGetPost'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('post.id')->type('hidden'),
                    Input::make('post.slug')->title('Slug')->type('text'),
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

                                Input::make("post.meta_title.$locale")
                                    ->title("Meta tag for title ($label)")
                                    ->type('text'),

                                Input::make("post.meta_description.$locale")
                                    ->title("Meta tag for description ($label)")
                                    ->type('text'),

                                Input::make("post.meta_keywords.$locale")
                                    ->title("Meta tag for keywords ($label)")
                                    ->type('text'),
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

    public function asyncGetPost(LeftPost $post): array
    {
        return [
            'post' => [
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

    public function update(Request $request): void
    {
        $data = $request->input('post');
        $post = LeftPost::findOrFail($data['id']);
        $this->createOrSave($post, $data);
    }

    public function createPost(Request $request, LeftPost $post): void
    {
        $data = $request->input('post');
        $this->createOrSave($post, $data);
    }

    private function createOrSave(LeftPost $post, array $data): void
    {
        $post->slug = $data['slug'] ?? $post->slug;

        foreach (['title', 'body', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            $post->setTranslations($field, $data[$field] ?? []);
        }

        $post->save();
    }

    public function delete(Request $request): void
    {
        LeftPost::find($request->input('post.id'))->delete();
    }
}
