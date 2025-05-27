<?php

namespace App\Orchid\Screens\Post;

use App\Models\IntroPost;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class IntroPostListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => IntroPost::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Welcome and descriptions posts';
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
                TD::make('intro_title.en', 'Welcome post title')
                    ->render(function (IntroPost $post) {
                        return
                            ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'post' => $post->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (IntroPost $post) {
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
                                Input::make("post.intro_title.$locale")
                                    ->title("Title intro ($label)")
                                    ->type('text'),

                                Quill::make("post.intro_post.$locale")
                                    ->title("Body intro ($label)"),

                                Input::make("post.left_intro_title.$locale")
                                    ->title("Title for left intro ($label)")
                                    ->type('text'),

                                Quill::make("post.left_intro_post.$locale")
                                    ->title("Body for left intro ($label)"),

                                Input::make("post.right_intro_title.$locale")
                                    ->title("Title for right intro ($label)")
                                    ->type('text'),

                                Quill::make("post.right_intro_post.$locale")
                                    ->title("Body for right intro ($label)"),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Create page')->applyButton('Create'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('post.id')->type('hidden'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))->mapWithKeys(function ($locale, $label) {
                        return [
                            $label => Layout::rows([
                                Input::make("post.intro_title.$label")
                                    ->title("Title intro ($locale)")
                                    ->type('text'),

                                Quill::make("post.intro_post.$label")
                                    ->title("Body intro ($locale)"),

                                Input::make("post.left_intro_title.$label")
                                    ->title("Title for left intro ($locale)")
                                    ->type('text'),

                                Quill::make("post.left_intro_post.$label")
                                    ->title("Body for left intro ($locale)"),

                                Input::make("post.right_intro_title.$label")
                                    ->title("Title for right intro ($locale)")
                                    ->type('text'),

                                Quill::make("post.right_intro_post.$label")
                                    ->title("Body for right intro ($locale)"),
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

    public function asyncGetPost(IntroPost $post): array
    {
        $locales = array_keys(config('app.locales', ['en' => 'English']));

        $data = [
            'id' => $post->id,
        ];

        foreach (['intro_title', 'intro_post', 'left_intro_title', 'left_intro_post', 'right_intro_title', 'right_intro_post'] as $field) {
            $translations = [];
            foreach ($locales as $locale) {
                $translations[$locale] = $post->getTranslation($field, $locale, false) ?? '';
            }
            $data[$field] = $translations;
        }

        return ['post' => $data];


//        return [
//            'post' => [
//                'id' => $post->id,
//                'intro_title' => $post->getTranslations('intro_title'),
//                'intro_post' => $post->getTranslations('intro_post'),
//                'left_intro_title' => $post->getTranslations('left_intro_title'),
//                'left_intro_post' => $post->getTranslations('left_intro_post'),
//                'right_intro_title' => $post->getTranslations('right_intro_title'),
//                'right_intro_post' => $post->getTranslations('right_intro_post'),
//            ],
//        ];

    }

    public function update(Request $request): void
    {
        $data = $request->input('post');
        $post = IntroPost::findOrFail($data['id']);
        $this->createOrSave($post, $data);
    }

    public function createPost(Request $request, IntroPost $post): void
    {
        $data = $request->input('post');
        $this->createOrSave($post, $data);
    }

    private function createOrSave(IntroPost $post, array $data): void
    {
        foreach (['intro_title', 'intro_post', 'left_intro_title', 'left_intro_post', 'right_intro_title', 'right_intro_post'] as $field) {
            $post->setTranslations($field, $data[$field] ?? []);
        }

        $post->save();
    }

    public function delete(Request $request): void
    {
        IntroPost::find($request->input('post.id'))->delete();
    }
}
