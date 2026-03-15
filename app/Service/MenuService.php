<?php

namespace App\Service;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuPrice;
use App\Models\MenuSchedule;
use App\TransactionCategory;
use App\Utils\CloudinaryClient;
use Carbon\Carbon;
use Date;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Log;

class MenuService
{
    public function all(
        string $search = null,
        string $theme = null,
        string $category = null,
        int $limit = 10,
        string|null $status_active = 'active',
        bool $is_has_limit = true,
    ) {

        $menus = Menu::with(['menu_categories', 'theme'])
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(name) LIKE ? ', ["%$search%"]);
            })
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
            ->when($status_active != 'all', function ($query) use ($status_active) {
                $is_active = $this->getStatusActive($status_active);
                return $query->where('is_active', $is_active);
            });

        if ($is_has_limit) {
            return $menus->paginate($limit);
        }

        return $menus->get();

    }

    public function getWeeklyPopuler()
    {
        // ini dapet bisa dikategorikan populer dari mana?
        // sementara gini dlu
        return $this->all(limit: 3);
    }

    public function searchByID(string|int $id)
    {
        return Menu::with([
            'menu_categories',
            'theme',
            'prices' => function ($query) {
                if (!auth()->user() || !auth()->user()->isAdminOrOwner()) {
                    return $query->where('price', '>', 0);
                }
                return $query;
            },
        ])->find($id);
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

    public function getBySingleDate(Carbon|string $date)
    {
        return Menu::with([
            'theme',
            'menu_categories',
            'prices',
            'schedule'
        ])
        ->whereHas('schedule', function($query)use($date){
            $query->whereDate('date_at', $date);
        })->get();
    }

    public function getByMultipleDay(array $date)
    {
        $schedules = MenuSchedule::with([
            'menu',
        ])->whereBetween('date_at', $date)->get();
        $schedules = $schedules->groupBy(function ($schedule) {
            return $schedule->date_at;
        })->map(function ($schedule, $key) {
            return [
                'date' => Carbon::parse($key)->format('d-m-Y'),
                'menus' => $schedule->map(function ($s) {
                    return $s->menu;
                }),
            ];
        });
        return $schedules;
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
            // ->whereHas('schedule', function ($query) use ($delivery_at) {
            //     return $query->whereDate('date_at', '<=', $delivery_at);
            // })
            ->afterQuery(function (Collection $menus) use ($items) {
                $menus = $menus->map(function ($menu) use ($items) {
                    $item = array_find($items['items'], fn($item) => $item['id'] === $menu->id);
                    $menu->prices = $menu->prices->map(function ($price) use ($item) {
                        $orderedPackage = array_find($item['packages'], fn($p) => $p['id'] === $price->id);
                        $price->quantity = $orderedPackage['quantity'];
                        $price->note = $orderedPackage['note'] ?? '';
                        return $price;
                    });
                    $menu->category = $menu->schedule->isEmpty() ? 'non-weekly' : 'weekly';
                    return $menu;
                });
            })->get();
    }

    public function save(
        array $data,
    ) {
        $cloudinary = new CloudinaryClient();
        $uplouded = null;

        if (isset($data['image'])) {
            $uplouded = $cloudinary->uploud(
                $data['image']->getRealPath(),
                'menus'
            );
            if (!$uplouded) {
                return [
                    'is_success' => false,
                    'message' => 'Tidak berhasil dalam mengunggah gambar!'
                ];
            }
        }

        try {

            return DB::transaction(function () use ($cloudinary, $uplouded, $data) {

                $menu = new Menu();

                $menu->name = $data['name'];
                $menu->id_theme = $data['theme_id'];
                $menu->description = $data['description'];
                $menu->side_dish = $data['side_dish'];
                $menu->vegetable = $data['vegetable'] ?? $menu->vegetable;
                $menu->chili_sauce = $data['sauce'] ?? $menu->chili_sauce;
                $menu->fruit = $data['fruit'] ?? $menu->fruit;
                $menu->is_active = $this->getStatusActive($data['status_active']);

                if ($uplouded) {
                    $menu->image_url = $uplouded['secure_url'];
                    $menu->image_public_id = $uplouded['public_id'];
                }

                $menu->setCreatedAt(now());
                $menu->setUpdatedAt(now());
                $menu->save();

                if (isset($data['category_ids'])) {
                    foreach ($data['category_ids'] as $category_id) {
                        MenuCategory::create([
                            'id_menu' => $menu->id,
                            'id_category' => $category_id,
                            'created_at' => now(),
                            'update_at' => now(),
                        ]);
                    }
                }

                // cek packages
                if (isset($data['packages'])) {
                    foreach ($data['packages'] as $package) {
                        MenuPrice::create([
                            'id_menu' => $menu->id,
                            'id_package' => $package['package_id'],
                            'price' => $package['price'],
                            'created_at' => now(),
                            'update_at' => now(),
                        ]);
                    }

                }



                return [
                    'is_success' => true,
                    'message' => "Berhasil menambahkan menu baru"
                ];
            });

        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            if ($uplouded) {
                $cloudinary->delete($uplouded['public_id']);
            }
            return [
                'is_success' => false,
                'message' => 'Telah Terjadi kesalahan saat ingin merubah data menu!'
            ];
        }

    }

    public function edit(
        Menu $menu,
        array $data,
    ) {
        $cloudinary = new CloudinaryClient();
        $uplouded = null;
        $old_public_id = $menu['image_public_id'];

        if (isset($data['image'])) {
            $uplouded = $cloudinary->uploud(
                $data['image']->getRealPath(),
                'menus'
            );
            if (!$uplouded) {
                return [
                    'is_success' => false,
                    'message' => 'Tidak berhasil dalam mengunggah gambar!'
                ];
            }
        }

        try {

            return DB::transaction(function () use ($cloudinary, $uplouded, $menu, $data, $old_public_id) {
                // ini saya gak pakai ai ya!, emng pengen nulis komen aja, biar tau todo nya apa aja hehe...

                // apakah id dari tema berbeda? jika iya ubah
                if ($menu['id_theme'] != $data['theme_id']) {
                    $menu->id_theme = $data['theme_id'];
                }
                // dapatkan value dari status
                $isActive = $this->getStatusActive($data['status_active']);
                // cek category
                if (isset($data['category_ids'])) {

                    if (count($data['category_ids']) == 0) {
                        $menu->menu_categories->each(fn($c) => $c->delete());
                    } else {
                        // jika datanya ada...
                        foreach ($data['category_ids'] as $category_id) {
                            // jika datanya blm ada create, kalo ada tapi gak ada maka hapus
                            $category = $menu->menu_categories->filter(fn($category) => $category->categories->id === $category_id)->first();
                            if (!$category) {
                                MenuCategory::create([
                                    'id_menu' => $menu->id,
                                    'id_category' => $category_id,
                                    'created_at' => now(),
                                    'update_at' => now(),
                                ]);
                            }
                        }
                        $categories = $menu->menu_categories()->get();
                        $must_deleted_categories = $categories->filter(fn($c) => !in_array($c->categories->id, $data['category_ids']));
                        foreach ($must_deleted_categories as $delete_c) {
                            $delete_c->delete();
                        }
                    }

                }
                // cek packages
                if (isset($data['packages'])) {
                    foreach ($data['packages'] as $package) {
                        $price = $menu->prices->filter(fn($price) => $price->package->id === $package['package_id'])->first();
                        if ($price) {
                            if ($package['price'] <= 0) {
                                $price->delete();
                            } else {
                                $price->price = $package['price'];
                                $price->save();
                            }
                        } else {
                            MenuPrice::create([
                                'id_menu' => $menu->id,
                                'id_package' => $package['package_id'],
                                'price' => $package['price'],
                                'created_at' => now(),
                                'update_at' => now(),
                            ]);
                        }
                    }

                }
                // change the other stuff...
                $menu->name = $data['name'];
                $menu->description = $data['description'];
                $menu->side_dish = $data['side_dish'];
                $menu->vegetable = $data['vegetable'] ?? $menu->vegetable;
                $menu->chili_sauce = $data['sauce'] ?? $menu->chili_sauce;
                $menu->fruit = $data['fruit'] ?? $menu->fruit;
                $menu->is_active = $isActive;
                if ($uplouded) {
                    $menu->image_url = $uplouded['secure_url'];
                    $menu->image_public_id = $uplouded['public_id'];
                }
                // save data menu
                $menu->save();
                if ($uplouded) {
                    $deleted = $cloudinary->delete($old_public_id);
                }

                $message = $uplouded ? 'Berhasil Merubah Data Menu & Mengunggah Gambar Baru!' : 'Berhasil Merubah Data Menu!';

                return [
                    'is_success' => true,
                    'message' => $message
                ];
            });

        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            if ($uplouded) {
                $cloudinary->delete($uplouded['public_id']);
            }
            return [
                'is_success' => false,
                'message' => 'Telah Terjadi kesalahan saat ingin merubah data menu!'
            ];
        }

    }

    public function delete()
    {
    }

    public function saveWeeklyMenu(
        string $start_date,
        string $end_date,
        array|null $data
    ) {
        try {

            return DB::transaction(function () use ($start_date, $end_date, $data) {
                $schedules = MenuSchedule::whereBetween('date_at', [$start_date, $end_date])->get();
                $schedules->each(fn($schedule) => $schedule->delete());
                if ($data || $data && count($data) > 0) {
                    foreach ($data as $key => $menu_ids) {
                        foreach ($menu_ids as $menu_id) {
                            MenuSchedule::create([
                                'id_menu' => $menu_id,
                                'date_at' => Carbon::parse($key),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                $date = Carbon::parse($start_date);
                return [
                    'is_success' => false,
                    'message' => 'Berhasil dalam mengubah data menu minggu ke ' . $date->weekOfMonth
                ];
            });

        } catch (Exception $e) {
            Log::error($e->getMessage());
            Db::rollBack();
            return [
                'is_success' => false,
                'message' => 'Telah Terjadi Kesalahan Saat Ingin Merubah Data Menu Mingguan!',
            ];
        }
    }

    private function getStatusActive($status_active)
    {
        return strtolower($status_active) === 'active' ? 1 : 0;
    }

}
