<?php

namespace App\Orchid\Screens\Post;

use App\Models\Menu;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class MenuListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'menus' => Menu::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Menu list';
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Create')
                ->modal('createMenu')
                ->method('createMenu'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('menus', [
                TD::make('id', 'ID'),
                TD::make('title.en', 'Menu title'),
                TD::make('slug', 'Menu slug'),
                TD::make('edit', 'Edit')
                    ->render(function (Menu $menu) {
                        return
                            ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'menu' => $menu->id,
                            ]);
                    }),
                TD::make('delete', 'Delete')
                    ->render(function (Menu $menu) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'menu' => $menu->id,
                            ]);
                    }),
            ]),

            Layout::modal('createMenu', Layout::rows([
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

                Input::make('slug')
                    ->title('Slug')
                    ->type('text'),
            ]))->title('Create menu')->applyButton('Create'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('menu.id')->type('hidden'),
                    Input::make('menu.slug')->title('Slug')->type('text'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en']))->mapWithKeys(function ($locale) {
                        return [
                            strtoupper($locale) => Layout::rows([
                                Input::make("menu.title.$locale")
                                    ->title("Title ($locale)")
                                    ->type('text'),
                            ]),
                        ];
                    })->toArray()
                ),
            ])->title('Edit page translation')->async('asyncGetPost'),

            Layout::modal('delete', Layout::rows([
                Input::make('menu.id')->type('hidden'),
            ]))->title('Confirm deletion')->applyButton('Delete')->async('asyncGetPost'),

        ];

    }

    public function asyncGetPost(Menu $menu): array
    {
        return [
            'menu' =>
                [
                    'id' => $menu->id,
                    'title' => $menu->getTranslations('title'),
                    'slug' => $menu->slug,
                ],
        ];
    }

    public function delete(Request $request)
    {
//        dd($request->all());
        Menu::find($request->input('menu.id'))->delete();
    }

    public function update(Request $request): void
    {
//        dd($request->all());
        Menu::find($request->input('menu.id'))->update($request->menu);
    }

    public function createMenu(Request $request, Menu $menu): void
    {
        $data = $request->all();
//        dd($data['body']);
        $menu->setTranslation('title', $data['lang'], $data['title']);
        $menu->slug = $data['slug'];
        $menu->save();
    }
}
