<?php

namespace App\Orchid\Screens\Post;

use App\Models\Post;
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

            Layout::modal('createPost', Layout::rows([
                Select::make('post_id')
                    ->title('Main page')
                    ->options(
                        Post::all()->mapWithKeys(fn ($post) => [
                            $post->id => $post->getTranslation('title', 'en')
                        ])
                    ),

                Select::make('lang')
                    ->title('Language')
                    ->options([
                        'en' => 'English',
                        'uk' => 'Українська',
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

                Input::make('meta_title')
                    ->title('Meta tag for "title"')
                    ->type('text'),

                Input::make('meta_description')
                    ->title('Meta tag for "description"')
                    ->type('text'),

                Input::make('meta_keywords')
                    ->title('Meta tag for "keywords"')
                    ->type('text'),
            ]))->title('Create page')->applyButton('Create'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('postContent.id')->type('hidden'),
                    Input::make('postContent.slug')->title('Slug')->type('text'),
                    Select::make('postContent.post_id')
                        ->title('Main page')
                        ->options(Post::all()->mapWithKeys(fn ($post) => [
                            $post->id => $post->getTranslation('title', 'en')
                        ])->toArray()),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en']))->mapWithKeys(function ($locale) {
                        return [
                            strtoupper($locale) => Layout::rows([
                                Input::make("postContent.title.$locale")
                                    ->title("Title ($locale)")
                                    ->type('text'),

                                Quill::make("postContent.body.$locale")
                                    ->title("Body ($locale)"),

                                Input::make("postContent.meta_title.$locale")
                                    ->title("Meta tag for title ($locale)")
                                    ->type('text'),

                                Input::make("postContent.meta_description.$locale")
                                    ->title("Meta tag for description ($locale)")
                                    ->type('text'),

                                Input::make("postContent.meta_keywords.$locale")
                                    ->title("Meta tag for keywords ($locale)")
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

        foreach (config('app.locales', ['en']) as $locale) {
            $data["postContent.title.$locale"] = $postContent->getTranslation('title', $locale, false);
            $data["postContent.body.$locale"] = $postContent->getTranslation('body', $locale, false);
            $data["postContent.meta_title.$locale"] = $postContent->getTranslation('meta_title', $locale, false);
            $data["postContent.meta_description.$locale"] = $postContent->getTranslation('meta_description', $locale, false);
            $data["postContent.meta_keywords.$locale"] = $postContent->getTranslation('meta_keywords', $locale, false);
        }

        return $data;
    }

    public function delete(Request $request)
    {
//        dd($request->all());
        PostContent::find($request->input('postContent.id'))->delete();
    }

    public function update(Request $request): void
    {
        $data = $request->input('postContent');

        $postContent = PostContent::findOrFail($data['id']);

        $postContent->slug = $data['slug'] ?? $postContent->slug;
        $postContent->post_id = $data['post_id'] ?? $postContent->post_id;

        $postContent->setTranslations('title', $data['title'] ?? []);
        $postContent->setTranslations('body', $data['body'] ?? []);
        $postContent->setTranslations('meta_title', $data['meta_title'] ?? []);
        $postContent->setTranslations('meta_description', $data['meta_description'] ?? []);
        $postContent->setTranslations('meta_keywords', $data['meta_keywords'] ?? []);

        $postContent->save();
    }

    public function createPost(Request $request, PostContent $postContent): void
    {
        $data = $request->all();
//        dd($data['body']);
        $postContent->setTranslation('title', $data['lang'], $data['title']);
        $postContent->setTranslation('body', $data['lang'], $data['body']);
        $postContent->setTranslation('meta_title', $data['lang'], $data['meta_title']);
        $postContent->setTranslation('meta_description', $data['lang'], $data['meta_description']);
        $postContent->setTranslation('meta_keywords', $data['lang'], $data['meta_keywords']);
        $postContent->slug = $data['slug'];
        $postContent->post_id = $data['post_id'];
        $postContent->save();
    }
}
