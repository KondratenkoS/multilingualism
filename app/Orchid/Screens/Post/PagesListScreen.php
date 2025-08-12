<?php

namespace App\Orchid\Screens\Post;

use App\Models\Pages;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PagesListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'pages' => Pages::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Страницы сайта';
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
            Layout::table('pages', [
                TD::make('id', 'ID'),
                TD::make('title.en', 'Title'),

                TD::make('edit', 'Edit')
                    ->render(function (Pages $pages) {
                        return ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'pages' => $pages->id,
                            ]);
                    }),

                TD::make('delete', 'Delete')
                    ->render(function (Pages $pages) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'pages' => $pages->id,
                            ]);
                    }),
            ]),

            Layout::modal('create', [
                Layout::rows([
                    Input::make('pages.id')->type('hidden'),

                    Input::make('pages.slug')
                        ->title('Slug')
                        ->type('text'),

                    Upload::make('attachment')
                        ->title('Image upload')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("pages.meta_title.$locale")
                                        ->title("Meta title ($label)")
                                        ->type('text'),

                                    Input::make("pages.meta_description.$locale")
                                        ->title("Meta description ($label)")
                                        ->type('text'),

                                    Input::make("pages.meta_keywords.$locale")
                                        ->title("Meta keywords ($label)")
                                        ->type('text'),

                                    Input::make("pages.title.$locale")
                                        ->title("Title ($label)")
                                        ->type('text'),

                                    Quill::make("pages.body.$locale")
                                        ->title("Body ($label)"),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Добавить страницу')->applyButton('Create')->async('asyncGetPages'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('pages.id')->type('hidden'),

                    Input::make('pages.slug')
                        ->title('Slug')
                        ->type('text'),

                    Upload::make('attachment')
                        ->title('Image update')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("pages.meta_title.$locale")
                                        ->title("Meta title ($label)")
                                        ->type('text'),

                                    Input::make("pages.meta_description.$locale")
                                        ->title("Meta description ($label)")
                                        ->type('text'),

                                    Input::make("pages.meta_keywords.$locale")
                                        ->title("Meta keywords ($label)")
                                        ->type('text'),

                                    Input::make("pages.title.$locale")
                                        ->title("Title ($label)")
                                        ->type('text'),

                                    Quill::make("pages.body.$locale")
                                        ->title("Body ($label)"),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Редактировать страницы')->applyButton('Edit')->async('asyncGetPages'),

            Layout::modal('delete', Layout::rows([
                Input::make('pages.id')->type('hidden'),
            ]))->title('Подтвердить удаление страницы')->applyButton('Delete')->async('asyncGetPages'),

        ];

    }


    public function asyncGetPages(Pages $pages): array
    {
        $data = [
            'pages.id' => $pages->id,
            'pages.slug' => $pages->slug,
            'attachment' => $pages->attachments->pluck('id')->toArray(),
        ];

        $translatableFields = [
            'meta_title',
            'meta_description',
            'meta_keywords',
            'title',
            'body',
        ];

        foreach ($translatableFields as $field) {
            foreach ($pages->getTranslations($field) as $locale => $value) {
                $data["pages.$field.$locale"] = $value;
            }
        }

        return $data;
    }

    public function create(Request $request): void
    {
        $data = $request->input('pages');
        $pages = new Pages();
        $this->createOrSave($pages, $data, $request->input('attachment', []));
    }

    public function update(Request $request): void
    {
        $data = $request->input('pages');
        $pages = Pages::findOrFail($data['id']);
        $this->createOrSave($pages, $data, $request->input('attachment', []));
    }

    private function createOrSave(Pages $pages, array $data, array $attachments): void
    {
        $pages->slug = $data['slug'] ?? null;

        $translatableFields = [
            'meta_title',
            'meta_description',
            'meta_keywords',
            'title',
            'body',
        ];

        foreach ($translatableFields as $field) {
            $pages->setTranslations($field, $data[$field] ?? []);
        }

        $pages->save();
        $pages->attachments()->sync($attachments);
    }

    public function delete(Request $request): void
    {
        $id = $request->input('pages.id');
        $pages = Pages::find($id);

        if ($pages) {
            $pages->attachments()->detach();
            $pages->delete();
        }
    }
}
