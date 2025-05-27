<?php

namespace App\Orchid\Screens\Post;

use App\Models\LeftPost;
use App\Models\PostContent;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PostContentListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'postContent' => PostContent::all(),
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
            Layout::table('postContent', [
                TD::make('id', 'ID'),
                TD::make('title.en', 'Page title'),
                TD::make('slug', 'Page slug'),
                TD::make('edit', 'Edit')
                    ->render(function (PostContent $postContent) {
                        return ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'postContent' => $postContent->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (PostContent $postContent) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'postContent' => $postContent->id,
                            ]);
                    }),
            ]),

            Layout::modal('createPost', [
                Layout::rows([
                    Input::make('postContent.id')->type('hidden'),
                    Input::make('postContent.slug')->title('Slug')->type('text'),
                    Select::make('postContent.post_id')
                        ->title('Main page')
                        ->options(LeftPost::all()->mapWithKeys(fn ($post) => [
                            $post->id => $post->getTranslation('title', 'en')
                        ])->toArray()),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([
                                    Input::make("postContent.title.$locale")
                                        ->title("Title ($label)")
                                        ->type('text'),

                                    Quill::make("postContent.body.$locale")
                                        ->title("Body ($label)"),

                                    Input::make("postContent.meta_title.$locale")
                                        ->title("Meta tag for title ($label)")
                                        ->type('text'),

                                    Input::make("postContent.meta_description.$locale")
                                        ->title("Meta tag for description ($label)")
                                        ->type('text'),

                                    Input::make("postContent.meta_keywords.$locale")
                                        ->title("Meta tag for keywords ($label)")
                                        ->type('text'),
                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Create page')->applyButton('Create')->async('asyncGetPost'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('postContent.id')->type('hidden'),
                    Input::make('postContent.slug')->title('Slug')->type('text'),
                    Select::make('postContent.post_id')
                        ->title('Main page')
                        ->options(LeftPost::all()->mapWithKeys(fn ($post) => [
                            $post->id => $post->getTranslation('title', 'en')
                        ])->toArray()),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([
                                    Input::make("postContent.title.$locale")
                                        ->title("Title ($label)")
                                        ->type('text'),

                                    Quill::make("postContent.body.$locale")
                                        ->title("Body ($label)"),

                                    Input::make("postContent.meta_title.$locale")
                                        ->title("Meta tag for title ($label)")
                                        ->type('text'),

                                    Input::make("postContent.meta_description.$locale")
                                        ->title("Meta tag for description ($label)")
                                        ->type('text'),

                                    Input::make("postContent.meta_keywords.$locale")
                                        ->title("Meta tag for keywords ($label)")
                                        ->type('text'),
                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Edit page translation')->async('asyncGetPost'),

            Layout::modal('delete', Layout::rows([
                Input::make('postContent.id')->type('hidden'),
            ]))->title('Confirm deletion')->applyButton('Delete')->async('asyncGetPost'),

        ];

    }

    public function asyncGetPost(PostContent $postContent): array
    {
        $data = [
            'postContent.id' => $postContent->id,
            'postContent.slug' => $postContent->slug,
            'postContent.post_id' => $postContent->post_id,
        ];

        foreach (array_keys(config('app.locales', ['en'])) as $locale) {
            $data["postContent.title.$locale"] = $postContent->getTranslation('title', $locale, false);
            $data["postContent.body.$locale"] = $postContent->getTranslation('body', $locale, false);
            $data["postContent.meta_title.$locale"] = $postContent->getTranslation('meta_title', $locale, false);
            $data["postContent.meta_description.$locale"] = $postContent->getTranslation('meta_description', $locale, false);
            $data["postContent.meta_keywords.$locale"] = $postContent->getTranslation('meta_keywords', $locale, false);
        }

        return $data;
    }

    public function update(Request $request): void
    {
        $data = $request->input('postContent');
        $postContent = PostContent::findOrFail($data['id']);
        $this->createOrSave($postContent, $data);
    }

    public function createPost(Request $request, PostContent $postContent): void
    {
        $data = $request->input('postContent');
        $this->createOrSave($postContent, $data);
    }

    private function createOrSave(PostContent $postContent, array $data): void
    {
        $postContent->slug = $data['slug'] ?? $postContent->slug;
        $postContent->post_id = $data['post_id'] ?? $postContent->post_id;

        foreach (['title', 'body', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            $postContent->setTranslations($field, $data[$field] ?? []);
        }

        $postContent->save();
    }

    public function delete(Request $request): void
    {
        PostContent::find($request->input('postContent.id'))->delete();
    }
}
