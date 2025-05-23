<?php

namespace App\Orchid\Screens\Post;

use App\Models\Contact;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
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

            Layout::modal('createContact', [
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

                    Quill::make('contact.gtm_head')
                        ->title('"GTM" Head'),
                    Quill::make('contact.gtm_body')
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
                                    Input::make("contact.copyright.$locale")
                                        ->title("Copyright ($label)")
                                        ->type('text'),
                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Create contacts')->applyButton('Create')->async('asyncGetContact'),

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

                    Quill::make('contact.gtm_head')
                        ->title('"GTM" Head'),
                    Quill::make('contact.gtm_body')
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
                                    Input::make("contact.copyright.$locale")
                                        ->title("Copyright ($label)")
                                        ->type('text'),
                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Edit contacts')->applyButton('Edit')->async('asyncGetContact'),

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
            'contact.gtm_head' => $contact->gtm_head,
            'contact.gtm_body' => $contact->gtm_body,
            'attachment' => $contact->attachments->pluck('id')->toArray(),
        ];

        foreach ($contact->getTranslations('copyright') as $locale => $value) {
            $data["contact.copyright.$locale"] = $value;
        }

        return $data;
    }

    public function update(Request $request): void
    {
        $data = $request->input('contact');
        $contact = Contact::findOrFail($data['id']);
        $this->createOrSave($contact, $data, $request->input('attachment', []));
    }

    public function createContact(Request $request, Contact $contact): void
    {
        $data = $request->input('contact');
        $this->createOrSave($contact, $data, $request->input('attachment', []));
    }

    private function createOrSave(Contact $contact, array $data, array $attachments): void
    {
        $contact->email = $data['email'];
        $contact->phone_for_call = $data['phone_for_call'];
        $contact->phone_for_chat = $data['phone_for_chat'];
        $contact->gtm_head = $data['gtm_head'] ?? null;
        $contact->gtm_body = $data['gtm_body'] ?? null;
        $contact->setTranslations('copyright', $data['copyright'] ?? []);

        $contact->save();
        $contact->attachments()->sync($attachments);
    }

    public function delete(Request $request): void
    {
        Contact::find($request->input('contact.id'))->delete();
    }
}
