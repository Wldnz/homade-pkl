<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ResponseData;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;

class MainController extends Controller
{

    private ResponseData $responseData;

    public function __construct() {
        $this->responseData = new ResponseData();
    }

    public function _index(){
        try{

            // transaksi
            //  - total transaksi
            //  - total transaksi menunggu invoice, menunggu konfirmasi bukti, di prosess, menunggu kurir, dikirimkan.
            //  menu
            //  - total paket, tema, kategori
            //  - total menu
            //  -  menu populer? & menu tidak populer berdasarkan jumlah dibelinya
            //  - total jadwal menu - menu yang aktif ambil di setiap minggunya
            // filterkan berdasarkan hari? minggu? atau gimana idk ya
            // perlu statistika penjualan?
            // import & export data?
            // atau buatkan lainnya dong

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Data',
                // data: data,
                isJson:false
            );
            
            return view('admin.index', compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson:false,
            );
            return view('admin.index', compact('response'));
        }
    }

    public function index(Request $request) 
{
    try {
        // ini jujur say pakai ai untuk generate, karena gak tau mau di isi apa...
        // 1. Setup Filter Tanggal (Default: Bulan Ini)
        $period = $request->query('period', 'this_month'); // today, this_week, this_month
        
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        if ($period == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($period == 'this_week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        // 2. QUERY TRANSAKSI & OPERASIONAL (Menggunakan agregasi sekali jalan untuk efisiensi)
        $transactionStats = DB::table('transactions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                COUNT(id) as total_transactions,
                COALESCE(SUM(CASE WHEN status IN ('paid', 'success') THEN total_price ELSE 0 END), 0) as total_revenue,
                SUM(CASE WHEN status_delivery = 'wait_for_confirmation' THEN 1 ELSE 0 END) as waiting_process,
                SUM(CASE WHEN status_delivery = 'wait_for_pick_up' THEN 1 ELSE 0 END) as waiting_courier,
                SUM(CASE WHEN status_delivery = 'process' THEN 1 ELSE 0 END) as on_process
            ")
            ->first();

        // Operasional Khusus: Berapa pesanan yang harus dikirim HARI INI?
        $deliveryToday = DB::table('transactions')
            ->whereDate('delivery_at', Carbon::today())
            ->whereNotIn('status', ['failed', 'cancelled_by_admin', 'cancelled_by_customer'])
            ->count();

        // 3. QUERY MASTER DATA
        $masterStats = [
            'total_menus' => DB::table('menus')->where('is_active', true)->count(),
            'total_categories' => DB::table('categories')->count(),
            'total_packages' => DB::table('packages')->count(),
            'scheduled_this_week' => DB::table('menu_schedules')
                ->whereBetween('date_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count()
        ];

        // 4. MENU POPULER (Top 5)
        $popularMenus = DB::table('transaction_orders as to')
            ->join('menus as m', 'to.id_menu', '=', 'm.id')
            ->join('transactions as t', 'to.id_transaction', '=', 't.id')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('t.status', '!=', 'failed') // Hanya hitung yang nggak gagal
            ->select('m.name', DB::raw('SUM(to.quantity) as total_sold'))
            ->groupBy('m.id', 'm.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // 5. DATA CHART STATISTIK PENJUALAN (Contoh 7 Hari Terakhir)
        // Grouping berdasarkan tanggal (date) untuk chart.js
        $salesChart = DB::table('transactions')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->whereIn('status', ['paid', 'success'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as daily_revenue'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Kumpulkan semua data untuk dilempar ke frontend
        $dashboardData = [
            'period' => $period,
            'stats' => clone $transactionStats, // Casting kalau perlu
            'delivery_today' => $deliveryToday,
            'master' => $masterStats,
            'popular_menus' => $popularMenus,
            'chart_data' => $salesChart
        ];

        // Format Response sesuai standar API kamu
        $response = $this->responseData->create(
            'Berhasil Mendapatkan Data Dashboard',
            data: $dashboardData,
            isJson: false
        );
        
        return view('admin.index', compact('response'));

    } catch(Exception $e) {
        Log::error('Dashboard Error: ' . $e->getMessage());
        $response = $this->responseData->create(
            'Telah Terjadi Kesalahan Pada Server',
            status: 'error',
            status_code: 500,
            isJson: false,
        );
        return view('admin.index', compact('response'));
    }
}
}
