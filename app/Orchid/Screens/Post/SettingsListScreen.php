<?php

namespace App\Orchid\Screens\Post;

use App\Models\Settings;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class SettingsListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'settings' => Settings::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Настройки сайта';
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
            Layout::table('settings', [
                TD::make('id', 'ID'),
                TD::make('copyright.en', 'Copyright'),

                TD::make('edit', 'Edit')
                    ->render(function (Settings $settings) {
                        return ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'settings' => $settings->id,
                            ]);
                    }),

                TD::make('delete', 'Delete')
                    ->render(function (Settings $settings) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'settings' => $settings->id,
                            ]);
                    }),
            ]),

            Layout::modal('create', [
                Layout::rows([
                    Input::make('settings.id')->type('hidden'),

                    Input::make('settings.email')
                        ->title('Email')
                        ->type('text'),

                    Input::make('settings.phone_for_call')
                        ->title('Phone for calls')
                        ->type('text')
                        ->mask([
                            'mask' => '+99 999 999 99 99',
                        ]),
                    Input::make('settings.phone_for_chat')
                        ->title('Phone for chats')
                        ->type('text')
                        ->mask([
                            'mask' => '+99 999 999 99 99',
                        ]),

                    Quill::make('settings.gtm_head')
                        ->title('"GTM" Head'),

                    Quill::make('settings.gtm_body')
                        ->title('"GTM" Body'),

                    Upload::make('attachment')
                        ->title('Logo upload')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("settings.copyright.$locale")
                                        ->title("Copyright ($label)")
                                        ->type('text'),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Добавить настройки')->applyButton('Create')->async('asyncGetSettings'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('settings.id')->type('hidden'),

                    Input::make('settings.email')
                        ->title('Email')
                        ->type('text'),

                    Input::make('settings.phone_for_call')
                        ->title('Phone for calls')
                        ->type('text')
                        ->mask([
                            'mask' => '+99 999 999 99 99',
                        ]),
                    Input::make('settings.phone_for_chat')
                        ->title('Phone for chats')
                        ->type('text')
                        ->mask([
                            'mask' => '+99 999 999 99 99',
                        ]),

                    Quill::make('settings.gtm_head')
                        ->title('"GTM" Head'),

                    Quill::make('settings.gtm_body')
                        ->title('"GTM" Body'),

                    Upload::make('attachment')
                        ->title('Logo upload')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("settings.copyright.$locale")
                                        ->title("Copyright ($label)")
                                        ->type('text'),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Редактировать настройки')->applyButton('Edit')->async('asyncGetSettings'),

            Layout::modal('delete', Layout::rows([
                Input::make('settings.id')->type('hidden'),
            ]))->title('Подтвердить удаление')->applyButton('Delete')->async('asyncGetSettings'),

        ];

    }


    public function asyncGetSettings(Settings $settings): array
    {
        $data = [
            'settings.id' => $settings->id,
            'settings.email' => $settings->email,
            'settings.phone_for_call' => $settings->phone_for_call,
            'settings.phone_for_chat' => $settings->phone_for_chat,
            'settings.gtm_head' => $settings->gtm_head,
            'settings.gtm_body' => $settings->gtm_body,
            'attachment' => $settings->attachments->pluck('id')->toArray(),
        ];

        $translatableFields = [
            'copyright',
        ];

        foreach ($translatableFields as $field) {
            foreach ($settings->getTranslations($field) as $locale => $value) {
                $data["settings.$field.$locale"] = $value;
            }
        }

        return $data;
    }

    public function create(Request $request): void
    {
        $data = $request->input('settings');
        $settings = new Settings();
        $this->createOrSave($settings, $data, $request->input('attachment', []));
    }

    public function update(Request $request): void
    {
        $data = $request->input('settings');
        $settings = Settings::findOrFail($data['id']);
        $this->createOrSave($settings, $data, $request->input('attachment', []));
    }

    private function createOrSave(Settings $settings, array $data, array $attachments): void
    {
        $settings->email = $data['email'] ?? null;
        $settings->phone_for_call = $data['phone_for_call'] ?? null;
        $settings->phone_for_chat = $data['phone_for_chat'] ?? null;
        $settings->gtm_head = $data['gtm_head'] ?? null;
        $settings->gtm_body = $data['gtm_body'] ?? null;

        $translatableFields = [
            'copyright',
        ];

        foreach ($translatableFields as $field) {
            $settings->setTranslations($field, $data[$field] ?? []);
        }

        $settings->save();
        $settings->attachments()->sync($attachments);
    }

    public function delete(Request $request): void
    {
        $id = $request->input('settings.id');
        $settings = Settings::find($id);

        if ($settings) {
            $settings->attachments()->detach();
            $settings->delete();
        }
    }
}
