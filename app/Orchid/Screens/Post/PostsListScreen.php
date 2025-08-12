<?php

namespace App\Orchid\Screens\Post;

use App\Models\Posts;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PostsListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => Posts::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Настройки постов';
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create')
                ->modal('create')
                ->method('create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('posts', [
                TD::make('id', 'ID'),
                TD::make('title.en', 'Title'),

                TD::make('edit', 'Edit')
                    ->render(function (Posts $posts) {
                        return ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'posts' => $posts->id,
                            ]);
                    }),

                TD::make('delete', 'Delete')
                    ->render(function (Posts $posts) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'posts' => $posts->id,
                            ]);
                    }),
            ]),

            Layout::modal('create', [
                Layout::rows([
                    Input::make('posts.id')->type('hidden'),

                    Input::make('posts.position')
                        ->title('Position')
                        ->type('text'),

                    Input::make('posts.link')
                        ->title('Link')
                        ->type('text'),

                    Input::make('posts.type')
                        ->title('Type')
                        ->type('text'),

                    Upload::make('attachment')
                        ->title('Logo upload')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("posts.title.$locale")
                                        ->title("Title ($label)")
                                        ->type('text'),

                                    Quill::make("posts.body.$locale")
                                        ->title("Body ($label)"),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Добавить настройки')->applyButton('Create')->async('asyncGetPosts'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('posts.id')->type('hidden'),

                    Input::make('posts.position')
                        ->title('Position')
                        ->type('text'),

                    Input::make('posts.link')
                        ->title('Link')
                        ->type('text'),

                    Input::make('posts.type')
                        ->title('Type')
                        ->type('text'),

                    Upload::make('attachment')
                        ->title('Logo upload')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("posts.title.$locale")
                                        ->title("Title ($label)")
                                        ->type('text'),

                                    Quill::make("posts.body.$locale")
                                        ->title("Body ($label)"),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Редактировать настройки')->applyButton('Edit')->async('asyncGetPosts'),

            Layout::modal('delete', Layout::rows([
                Input::make('posts.id')->type('hidden'),
            ]))->title('Подтвердить удаление')->applyButton('Delete')->async('asyncGetPosts'),

        ];

    }


    public function asyncGetPosts(Posts $posts): array
    {
        $data = [
            'posts.id' => $posts->id,
            'posts.position' => $posts->position,
            'posts.link' => $posts->link,
            'posts.type' => $posts->type,

            'attachment' => $posts->attachments->pluck('id')->toArray(),
        ];

        $translatableFields = [
            'title',
            'body',
        ];

        foreach ($translatableFields as $field) {
            foreach ($posts->getTranslations($field) as $locale => $value) {
                $data["posts.$field.$locale"] = $value;
            }
        }

        return $data;
    }

    public function create(Request $request): void
    {
        $data = $request->input('posts');
        $posts = new Posts();
        $this->createOrSave($posts, $data, $request->input('attachment', []));
    }

    public function update(Request $request): void
    {
        $data = $request->input('posts');
        $posts = Posts::findOrFail($data['id']);
        $this->createOrSave($posts, $data, $request->input('attachment', []));
    }

    private function createOrSave(Posts $posts, array $data, array $attachments): void
    {
        $posts->position = $data['position'] ?? null;
        $posts->link = $data['link'] ?? null;
        $posts->type = $data['type'] ?? null;

        $translatableFields = [
            'title',
            'body',
        ];

        foreach ($translatableFields as $field) {
            $posts->setTranslations($field, $data[$field] ?? []);
        }

        $posts->save();
        $posts->attachments()->sync($attachments);
    }

    public function delete(Request $request): void
    {
        $id = $request->input('posts.id');
        $posts = Posts::find($id);

        if ($posts) {
            $posts->attachments()->detach();
            $posts->delete();
        }
    }
}
