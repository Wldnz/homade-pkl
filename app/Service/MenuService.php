<?php

namespace App\Service;

use App\Models\Menu;
use App\Models\MenuPrice;
use App\Models\MenuSchedule;
use App\TransactionCategory;
use Carbon\Carbon;
use Date;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Log;

class MenuService
{
    public function all(
        string $search = null,
        string $theme = null,
        string $category = null,
        int $limit = 10,
        bool $isActive = true,
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
        return $this->all(limit: 3);
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
        $menus = Menu::whereHas('menu_categories', fn($query) => $query->whereIn('id_category', $categories_id))
            ->with([
                'menu_categories' => fn($query) => $query->whereIn('id_category', $categories_id)->limit(3),
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
            return Carbon::parse($item->date_at)->format('Y-m-d');
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

    public function getByDate(Carbon $date)
    {
        return Menu::with([
             'menu_categories',
            'theme',
            'prices',
            'schedule',
        ])
        ->whereHas('schedule', function($query) use($date){
           return $query->whereDate('date_at', $date); 
        })->get();
    }

    public function menuNonWeekly(
        string $search = null,
        string $theme = null,
        string $category = null,
        int $limit = 10,
        bool $isActive = true,
    ) {

        return Menu::with([
            'menu_categories',
            'theme',
            'prices',
            'schedule',
        ])
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
            ->whereHas('schedule', function ($query) {
                return $query->where('date_at', '<', now()->addDays(1));
            })
            ->paginate($limit);
    }

    public function getByOrderedMenu(array $items)
    {
        // ini gw butuh id_prices, quantity, sama ini

        $items = [
            'prices_id' => [],
            'items' => $items,
            'menus' => []
        ];

        foreach ($items['items'] as $item) {
            foreach ($item['packages'] as $package) {
                array_push($items['prices_id'], $package['id']);
            }
        }

        $menus = Menu::with([
            'prices' => fn($query) => $query->whereIn('id', $items['prices_id']),
            'weekly'
        ])->whereHas('prices', function ($query) use ($items) {
            return $query->whereIn('id', $items['prices_id']);
        })
            ->get();

        $menus = $menus->map(function ($menu) use ($items) {
            $item = array_find($items['items'], function ($item) use ($menu) {
                return $menu->id === $item['id'];
            });
            if ($item) {
                $menu->prices = $menu->prices->map(function ($price) use ($item) {
                    $ordered = array_find($item['packages'], function ($orderedPackage) use ($price) {
                        return $price->id === $orderedPackage['id'];
                    });
                    if ($ordered) {
                        $price->note = $ordered['note'];
                        $price->quantity = $ordered['quantity'];
                        $price->total_price = $ordered['quantity'] * $price->price;
                    };
                    return $price;
                });
            }
            $menu->category = $menu->weekly->isEmpty() ? 'non-weekly' : 'weekly';
            return $menu;
        });


        return $menus;
    }

    public function getOrderedMenu(
        array $items,
        Carbon $delivery_at
    ) {
        // ini gw butuh id_prices, quantity, sama ini

        $items = [
            'prices_id' => [],
            'items' => $items,
            'menus' => []
        ];

        foreach ($items['items'] as $item) {
            foreach ($item['packages'] as $package) {
                array_push($items['prices_id'], $package['id']);
            }
        }

        return Menu::with([
            'prices' => fn($query) => $query->whereIn('id', $items['prices_id']),
            'schedule' => fn($query) => $query->whereAfterToday('date_at')->whereDate('date_at', '>=', $delivery_at)
        ])
            ->whereHas('prices', function ($query) use ($items) {
                return $query->whereIn('id', $items['prices_id']);
            })
            // tambahin validas waktu pengiriman untuk bedain mana yang weekly dan mana yang bukan
            // mendapatkan menu mingguan watknya paling lama dlu baru gas...
            ->whereHas('schedule', function ($query) use ($delivery_at) {
                return $query->whereDate('date_at', '<=',$delivery_at);
            })
            ->afterQuery(function (Collection $menus) use ($items) {
                $menus = $menus->map(function ($menu) use ($items) {
                    $item = array_find($items['items'], fn($item) => $item['id'] === $menu->id);
                    $menu->prices = $menu->prices->map(function ($price) use($item) {
                        $orderedPackage = array_find($item['packages'], fn ($p) => $p['id'] === $price->id);
                        $price->quantity = $orderedPackage['quantity'];
                        $price->note = $orderedPackage['note'] ?? '';
                        return $price;
                    });
                    $menu->category = $menu->schedule->isEmpty() ? 'non-weekly' : 'weekly'; 
                    return $menu;
                });
            })->get();
    }

}
