<?php

namespace App\Orchid\Screens\Post;

use App\Models\MenuItems;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class MenuItemsListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'menuItems' => MenuItems::all(),
        ];
    }

    public function name(): ?string
    {
        return 'Пункты меню';
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
            Layout::table('menuItems', [
                TD::make('id', 'ID'),
                TD::make('title.en', 'Title'),

                TD::make('edit', 'Edit')
                    ->render(function (MenuItems $menuItems) {
                        return ModalToggle::make('Edit')
                            ->modal('update')
                            ->method('update')
                            ->asyncParameters([
                                'menuItems' => $menuItems->id,
                            ]);
                    }),

                TD::make('delete', 'Delete')
                    ->render(function (MenuItems $menuItems) {
                        return ModalToggle::make('Delete')
                            ->modal('delete')
                            ->method('delete')
                            ->asyncParameters([
                                'menuItems' => $menuItems->id,
                            ]);
                    }),
            ]),

            Layout::modal('create', [
                Layout::rows([
                    Input::make('menuItems.id')->type('hidden'),

                    Input::make('menuItems.link')
                        ->title('Link')
                        ->type('text'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("menuItems.title.$locale")
                                        ->title("Пункт меню на ($label)")
                                        ->type('text'),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Добавить настройки')->applyButton('Create')->async('asyncGetMenuItems'),

            Layout::modal('update', [
                Layout::rows([
                    Input::make('menuItems.id')->type('hidden'),

                    Input::make('menuItems.link')
                        ->title('Link')
                        ->type('text'),
                ]),

                Layout::tabs(
                    collect(config('app.locales', ['en' => 'English']))
                        ->mapWithKeys(function ($label, $locale) {
                            return [
                                $label => Layout::rows([

                                    Input::make("menuItems.title.$locale")
                                        ->title("Пункт меню на ($label)")
                                        ->type('text'),

                                ]),
                            ];
                        })->toArray()
                ),
            ])->title('Редактировать пункты меню')->applyButton('Edit')->async('asyncGetMenuItems'),

            Layout::modal('delete', Layout::rows([
                Input::make('menuItems.id')->type('hidden'),
            ]))->title('Подтвердить пункт меню')->applyButton('Delete')->async('asyncGetMenuItems'),

        ];

    }


    public function asyncGetMenuItems(MenuItems $menuItems): array
    {
        $data = [
            'menuItems.id' => $menuItems->id,
            'menuItems.link' => $menuItems->link,
        ];

        $translatableFields = [
            'title',
        ];

        foreach ($translatableFields as $field) {
            foreach ($menuItems->getTranslations($field) as $locale => $value) {
                $data["menuItems.$field.$locale"] = $value;
            }
        }

        return $data;
    }

    public function create(Request $request): void
    {
        $data = $request->input('menuItems');
        $menuItems = new MenuItems();
        $this->createOrSave($menuItems, $data);
    }

    public function update(Request $request): void
    {
        $data = $request->input('menuItems');
        $menuItems = MenuItems::findOrFail($data['id']);
        $this->createOrSave($menuItems, $data);
    }

    private function createOrSave(MenuItems $menuItems, array $data): void
    {
        $menuItems->link = $data['link'] ?? null;

        $translatableFields = [
            'title',
        ];

        foreach ($translatableFields as $field) {
            $menuItems->setTranslations($field, $data[$field] ?? []);
        }

        $menuItems->save();
    }

    public function delete(Request $request): void
    {
        $id = $request->input('menuItems.id');
        $menuItems = MenuItems::find($id);

        if ($menuItems) {
            $menuItems->delete();
        }
    }
}
