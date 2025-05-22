<?php

namespace App\Orchid\Screens\Post;

use App\Models\Contact;
use App\Models\Post;
use App\Models\PostContent;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ContactsListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'contact' => Contact::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Contacts';
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create')
                ->modal('createContact')
                ->method('createContact'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('contact', [
                TD::make('id', 'ID'),
                TD::make('copyright.en', 'Copyright'),
                TD::make('email', 'Email'),
                TD::make('edit', 'Edit')
                    ->render(function (Contact $contact) {
                        return ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'contact' => $contact->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (Contact $contact) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'contact' => $contact->id,
                            ]);
                    }),
            ]),


            Layout::modal('createContact', Layout::rows([
                Select::make('lang')
                    ->title('Language')
                    ->options([
                        'uk' => 'Українська',
                        'en' => 'English',
                        'fr' => 'French',
                        'sp' => 'Spanish',
                    ]),

                Input::make('copyright')
                    ->title('Copyright')
                    ->type('text'),

                Input::make('email')
                    ->title('Email')
                    ->type('text'),

                Input::make('phone_for_call')
                    ->title('Phone for calls')
                    ->type('text')
                    ->mask([
                        'mask' => '+99 999 999 99 99',
                    ]),

                Input::make('phone_for_chat')
                    ->title('Phone for chats')
                    ->type('text')
                    ->mask([
                        'mask' => '+99 999 999 99 99',
                    ]),

                Upload::make('attachment')
                    ->title('Logo download')
                    ->acceptedFiles('image/*'),
            ]))->title('Create page')->applyButton('Create'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('contact.id')->type('hidden'),
                    Input::make('contact.email')->type('email')->title('Email'),
                    Input::make('contact.phone_for_call')
                        ->title('Phone for calls')
                        ->type('text')
                        ->mask([
                            'mask' => '+99 999 999 99 99',
                        ]),
                    Input::make('contact.phone_for_chat')
                        ->title('Phone for chats')
                        ->type('text')
                        ->mask([
                            'mask' => '+99 999 999 99 99',
                        ]),
                    Upload::make('attachment')
                        ->title('Logo upload')
                        ->acceptedFiles('image/*'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en']))->mapWithKeys(function ($locale) {
                        return [
                            strtoupper($locale) => Layout::rows([
                                Input::make("contact.copyright.$locale")
                                    ->title("Copyright ($locale)")
                                    ->type('text'),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Edit page translation')->async('asyncGetContact'),

            Layout::modal('delete', Layout::rows([
                Input::make('contact.id')->type('hidden'),
            ]))->title('Confirm deletion')->applyButton('Delete')->async('asyncGetContact'),

        ];

    }

    public function asyncGetContact(Contact $contact): array
    {
        $data = [
            'contact.id' => $contact->id,
            'contact.email' => $contact->email,
            'contact.phone_for_call' => $contact->phone_for_call,
            'contact.phone_for_chat' => $contact->phone_for_chat,
//            'contact.copyright' => $contact->getTranslations('copyright'),
            'attachment' => $contact->attachments->pluck('id')->toArray(),
        ];

        foreach (config('app.locales', ['en']) as $locale) {
            $data["contact.copyright.$locale"] = $contact->getTranslation('copyright', $locale, false);
        }

        return $data;
    }

    public function delete(Request $request)
    {
        Contact::find($request->input('contact.id'))->delete();
    }

    public function update(Request $request): void
    {
        $data = $request->input('contact');
//        dd($request->input('contact'));
        $contact = Contact::findOrFail($data['id']);
        $contact->email = $data['email'];
        $contact->phone_for_call = $data['phone_for_call'];
        $contact->phone_for_chat = $data['phone_for_chat'];
        $contact->setTranslations('copyright', $data['copyright'] ?? []);
        $contact->save();

        $attachmentIds = $request->input('attachment', []);
        $contact->attachments()->sync($attachmentIds);
    }

    public function createContact(Request $request, Contact $contact): void
    {
        $data = $request->all();
//        dd($data);
        $contact->setTranslation('copyright', $data['lang'], $data['copyright']);
        $contact->email = $data['email'];
        $contact->phone_for_call = $data['phone_for_call'];
        $contact->phone_for_chat = $data['phone_for_chat'];
        $contact->save();

        $attachmentIds = $request->input('attachment', []);
        $contact->attachments()->sync($attachmentIds);
    }
}
