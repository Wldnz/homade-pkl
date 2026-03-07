<?php

namespace App\Service;

use App\Models\Menu;
use App\Models\MenuSchedule;

class MenuService
{
    public function all(
        ?string $search,
        ?string $theme,
        ?string $category,
        int $page,
        int $limit = 10,
        ?bool $isActive = true,
    ) {

        $menus = Menu::with(['menu_categories', 'theme'])
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(name) LIKE ? ', ["%$search%"]);
            })->where('is_active', $isActive)
            ->when($theme, function ($query, $theme) {
                return $query->whereHas('theme', function ($q) use ($theme) {
                    return $q->where('name', $theme);
                });
            })
            ->when($category, function ($query, $category) {
                return $query->whereHas('menu_categories', function ($q) use ($category) {
                    return $q->whereHas('categories', function ($qc) use ($category) {
                        return $qc->where('name', $category);
                    });
                });
            })
            ->paginate($limit);

        return $menus;

    }

    public function getWeeklyPopuler()
    {
        // ini dapet bisa dikategorikan populer dari mana?
        // sementara gini dlu
        return $this->all(
            null, null, null, 1, 3
        );
    }

    public function searchByID(string|int $id)
    {
        return Menu::findOrFail($id)
            ->with([
                'menu_categories',
                'theme',
                'prices',
            ])->first();
    }

    public function withThemeAndCategory(
        string $search = '',
        bool $isActive = true,
    ) {
        return Menu::with(['menu_categories', 'theme'])
            ->where('is_active', $isActive)
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(name) LIKE ? ', ["%$search%"]);
            })->get();
    }

    public function getByRelevantCategoriesAndTheme(
        $categories_id,
        string $menu_id
    ) {
        // limit tiga aja kali ya
        // berdasarkan tema dan kategori
        $menus = Menu::whereHas('menu_categories', fn ($query) => $query->whereIn('id_category', $categories_id))
            ->with([
                'menu_categories' => fn ($query) => $query->whereIn('id_category', $categories_id)->limit(3),
                'theme',
            ])
            ->whereNot('id', $menu_id)->get();

        return $menus;
    }

    public function getWeeklyMenus(
        int $week = 1
    ) {
        $startTime = now()->addDays(7 * ($week - 1))->format('Y-m-d');
        $endTime = now()->addDays(7 * $week)->format('Y-m-d');

        $schedules = MenuSchedule::whereBetween('date_at', [$startTime, $endTime])
            ->with('menu')
            ->get();

        $schedules = $schedules->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date_at)->format('Y-m-d');
        })->map(function ($item, $date) {
            return [
                'date' => $date,
                'menus' => $item->map(function ($schedule) {
                    return $schedule->menu;
                }),
            ];
        })->values();

        return $schedules;
    }

    public function getByDate(string $date)
    {
        return MenuSchedule::where('date_at', 'Like', "%$date%")
            ->with(['menu'])
            ->get();
    }

    public function usingFilter(
        ?string $date
    ) {
        // return
    }
}
