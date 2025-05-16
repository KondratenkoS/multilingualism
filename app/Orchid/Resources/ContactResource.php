<?php

namespace App\Orchid\Resources;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Orchid\Crud\Resource;
use Orchid\Crud\ResourceRequest;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;

class ContactResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Contact::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('copyright_en')
                ->title('Copyright line in English')
                ->required(),
            Input::make('copyright_he')
                ->title('Copyright line in Hebrew')
                ->required(),
            Input::make('email')
                ->title('Email')
                ->required(),
            Input::make('phone_for_call')
                ->title('Phone for calls')
                ->required(),
            Input::make('phone_for_chat')
                ->title('Phone for chats')
                ->required(),

            Upload::make('attachment')
                ->title('Logo download')
                ->acceptedFiles('image/*')
                ->maxFiles(3),
        ];
    }

    public function onSave(ResourceRequest $request, Model $model)
    {
        $model->email = $request->input('email');
        $model->phone_for_call = $request->input('phone_for_call');
        $model->phone_for_chat = $request->input('phone_for_chat');
        $model->setTranslation('copyright', 'en', $request->input('copyright_en'));
        $model->setTranslation('copyright', 'he', $request->input('copyright_he'));
        $model->save();

        $attachmentIds = $request->input('attachment', []);

        $model->attachment()->sync($attachmentIds);
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('copyright.en'),
            TD::make('copyright.he'),
            TD::make('email'),
            TD::make('phone_for_call'),
            TD::make('phone_for_chat'),

            TD::make('logo', 'Логотипи')->render(function (Contact $contact) {
                return $contact->attachment->map(function ($attachment) {
                    return "<img src='{$attachment->url()}' width='50' style='margin-right: 5px;' alt='logo'>";
                })->implode(' ');
            })->width('150px'),


//
            TD::make('debug', 'Debug')->render(function (Contact $contact) {
                $attachment = $contact->attachments()->first();
                if ($attachment) {
                    return $attachment->url();
                }
                return 'No attachment found';
            }),




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
            Sight::make('copyright.en', 'Copyright in English')->render(fn($menu) => $menu->getTranslation('copyright', 'en')),
            Sight::make('copyright.he', 'Copyright in Hebrew')->render(fn($menu) => $menu->getTranslation('copyright', 'he')),
            Sight::make('email', 'Email'),
            Sight::make('phone_for_call', 'Phone for calls'),
            Sight::make('phone_for_chat', 'Phone for chats'),

            Sight::make('logo', 'Логотипи')->render(function (Contact $contact) {
                if ($contact->attachment->isEmpty()) {
                    return 'Логотипів не знайдено';
                }

                return $contact->attachment->map(function ($attachment) {
                    return "<img src='{$attachment->url()}' style='max-width: 100px; margin: 5px;' alt='logo'>";
                })->implode('');
            }),

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
