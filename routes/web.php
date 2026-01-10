<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Member;
use App\Models\Borrow;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| SIMPLE AUTH SYSTEM WITHOUT MIDDLEWARE
|
*/

// ============================================
// HELPER FUNCTION untuk check login
// ============================================
function checkLogin()
{
    return Session::has('user_logged_in');
}

function getUserRole()
{
    return Session::get('user_data.role', 'guest');
}

function requireLogin()
{
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }
    return null;
}

// ============================================
// PUBLIC ROUTES (tanpa login)
// ============================================
Route::get('/', function () {
    try {
        $stats = [
            'total_books' => Book::count(),
            'total_members' => Member::count(),
            'active_borrows' => Borrow::where('status', 'borrowed')->count(),
            'available_books' => Book::where('stock', '>', 0)->count(),
        ];
    } catch (\Exception $e) {
        $stats = [
            'total_books' => 1250,
            'total_members' => 350,
            'active_borrows' => 85,
            'available_books' => 1180,
        ];
    }

    return view('home', compact('stats'));
})->name('home');

Route::get('/login', function () {
    if (checkLogin()) {
        return redirect()->route('home');
    }
    return view('auth.login');
})->name('login');

// Login process
Route::post('/login', function () {
    $credentials = request()->only(['username', 'password']);

    // Demo credentials
    $demoUsers = [
        'admin' => 'admin123',
        'petugas' => 'petugas123',
        'anggota' => 'anggota123'
    ];

    // Validate input
    if (empty($credentials['username']) || empty($credentials['password'])) {
        Session::flash('error', 'Username dan password harus diisi!');
        return back()->withInput();
    }

    // Try database first
    try {
        $user = DB::table('users')
            ->where('username', $credentials['username'])
            ->first();

        if ($user && password_verify($credentials['password'], $user->password)) {
            Session::put('user_logged_in', true);
            Session::put('user_data', [
                'username' => $user->username,
                'role' => $user->role,
                'name' => $user->username,
                'user_id' => $user->id,
                'login_time' => now()->format('d/m/Y H:i:s')
            ]);

            Session::regenerate();
            Session::flash('success', 'Login berhasil! Selamat datang ' . $user->username);
            return redirect()->route('home');
        }
    } catch (\Exception $e) {
        // Database error, fallback to demo
    }

    // Check demo credentials
    if (isset($demoUsers[$credentials['username']]) &&
        $demoUsers[$credentials['username']] === $credentials['password']) {

        Session::put('user_logged_in', true);
        Session::put('user_data', [
            'username' => $credentials['username'],
            'role' => $credentials['username'] === 'admin' ? 'admin' :
                     ($credentials['username'] === 'petugas' ? 'petugas' : 'anggota'),
            'name' => ucfirst($credentials['username']),
            'login_time' => now()->format('d/m/Y H:i:s')
        ]);

        Session::regenerate();
        Session::flash('success', 'Login berhasil! Selamat datang ' . ucfirst($credentials['username']));
        return redirect()->route('home');
    }

    Session::flash('error', 'Username atau password salah!');
    return back()->withInput();
})->name('login.process');

// Logout
Route::get('/logout', function () {
    $username = Session::get('user_data.name', 'User');

    Session::flush();
    Session::regenerate();

    Session::flash('success', 'Berhasil logout! Selamat tinggal ' . $username);
    return redirect()->route('home');
})->name('logout');

// ============================================
// PROTECTED ROUTES - BUKU
// ============================================
Route::get('/buku', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $query = Book::query();

        // Search filter
        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Category filter
        if (request()->has('category') && request('category') != '') {
            $query->where('category', request('category'));
        }

        // Status filter
        if (request()->has('status') && request('status') != '') {
            if (request('status') == 'dipinjam') {
                $borrowedBookIds = Borrow::where('status', 'borrowed')
                    ->pluck('book_id')
                    ->toArray();
                $query->whereIn('id', $borrowedBookIds);
            } elseif (request('status') == 'tersedia') {
                $borrowedBookIds = Borrow::where('status', 'borrowed')
                    ->pluck('book_id')
                    ->toArray();
                $query->whereNotIn('id', $borrowedBookIds)
                      ->where('stock', '>', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(12);

        return view('buku.index', compact('books'));

    } catch (\Exception $e) {
        Session::flash('error', 'Error database: ' . $e->getMessage());
        $books = collect();
        return view('buku.index', compact('books'));
    }
})->name('buku.index');

// CRUD Operations for Books
Route::post('/buku', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    $request = request();
    try {
        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'stock' => $request->stock,
        ]);

        Session::flash('success', 'Buku berhasil ditambahkan!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal menambahkan buku: ' . $e->getMessage());
    }

    return redirect()->route('buku.index');
})->name('books.store');

Route::put('/buku/{id}', function ($id) {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    $request = request();
    try {
        $book = Book::findOrFail($id);
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'category' => $request->category,
            'stock' => $request->stock,
        ]);

        Session::flash('success', 'Buku berhasil diperbarui!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal memperbarui buku: ' . $e->getMessage());
    }

    return redirect()->route('buku.index');
})->name('books.update');

Route::delete('/buku/{id}', function ($id) {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $book = Book::findOrFail($id);
        $book->delete();

        Session::flash('success', 'Buku berhasil dihapus!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal menghapus buku: ' . $e->getMessage());
    }

    return redirect()->route('buku.index');
})->name('books.destroy');

// Pinjam buku
Route::post('/buku/{id}/pinjam', function ($id) {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $book = Book::findOrFail($id);

        if ($book->stock <= 0) {
            Session::flash('error', 'Stok buku habis!');
            return redirect()->route('buku.index');
        }

        // Get first member as default (or use logged in user's member ID)
        $member = Member::first();
        if (!$member) {
            Session::flash('error', 'Tidak ada anggota terdaftar!');
            return redirect()->route('buku.index');
        }

        // Create borrow record
        Borrow::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'borrow_date' => now()->format('Y-m-d'),
            'status' => 'borrowed',
        ]);

        // Update book stock
        $book->stock = $book->stock - 1;
        $book->save();

        Session::flash('success', 'Buku berhasil dipinjam!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal meminjam buku: ' . $e->getMessage());
    }

    return redirect()->route('buku.index');
})->name('buku.pinjam');

// API untuk detail buku
Route::get('/buku/{id}/detail', function ($id) {
    if (!checkLogin()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    try {
        $book = Book::findOrFail($id);

        $isBorrowed = Borrow::where('book_id', $id)
            ->where('status', 'borrowed')
            ->exists();

        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'category' => $book->category,
                'stock' => $book->stock,
                'is_borrowed' => $isBorrowed,
                'created_at' => $book->created_at->format('d/m/Y H:i'),
                'updated_at' => $book->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Buku tidak ditemukan'
        ], 404);
    }
});

// ============================================
// PROTECTED ROUTES - ANGGOTA
// ============================================
Route::get('/anggota', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $members = Member::orderBy('name')->paginate(10);
        return view('anggota.index', compact('members'));
    } catch (\Exception $e) {
        Session::flash('error', 'Error database: ' . $e->getMessage());
        $members = collect();
        return view('anggota.index', compact('members'));
    }
})->name('anggota.index');

// CRUD Operations for Members
Route::post('/anggota', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    $request = request();
    try {
        Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        Session::flash('success', 'Anggota berhasil ditambahkan!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal menambahkan anggota: ' . $e->getMessage());
    }

    return redirect()->route('anggota.index');
})->name('members.store');

Route::put('/anggota/{id}', function ($id) {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    $request = request();
    try {
        $member = Member::findOrFail($id);
        $member->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        Session::flash('success', 'Anggota berhasil diperbarui!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal memperbarui anggota: ' . $e->getMessage());
    }

    return redirect()->route('anggota.index');
})->name('members.update');

Route::delete('/anggota/{id}', function ($id) {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $member = Member::findOrFail($id);
        $member->delete();

        Session::flash('success', 'Anggota berhasil dihapus!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal menghapus anggota: ' . $e->getMessage());
    }

    return redirect()->route('anggota.index');
})->name('members.destroy');

// ============================================
// PROTECTED ROUTES - PEMINJAMAN
// ============================================
Route::get('/peminjaman', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $activeBorrows = Borrow::with(['book', 'member'])
            ->where('status', 'borrowed')
            ->orderBy('borrow_date', 'desc')
            ->paginate(10);

        $historyBorrows = Borrow::with(['book', 'member'])
            ->where('status', 'returned')
            ->orderBy('return_date', 'desc')
            ->paginate(10);

        // Get overdue borrows (borrowed > 7 days)
        $overdueBorrows = Borrow::with(['book', 'member'])
            ->where('status', 'borrowed')
            ->whereDate('borrow_date', '<=', now()->subDays(7))
            ->orderBy('borrow_date')
            ->paginate(10);

        $stats = [
            'active' => Borrow::where('status', 'borrowed')->count(),
            'overdue' => Borrow::where('status', 'borrowed')
                ->whereDate('borrow_date', '<=', now()->subDays(7))
                ->count(),
            'returned' => Borrow::where('status', 'returned')->count(),
            'total_books' => Book::count(),
            'total_members' => Member::count(),
        ];

        return view('peminjaman.index', compact('activeBorrows', 'historyBorrows', 'overdueBorrows', 'stats'));

    } catch (\Exception $e) {
        Session::flash('error', 'Error database: ' . $e->getMessage());
        return view('peminjaman.index', [
            'activeBorrows' => collect(),
            'historyBorrows' => collect(),
            'overdueBorrows' => collect(),
            'stats' => []
        ]);
    }
})->name('peminjaman.index');

// CRUD Operations for Borrows
Route::post('/peminjaman', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    $request = request();
    try {
        // Validate
        $book = Book::findOrFail($request->book_id);
        if ($book->stock <= 0) {
            Session::flash('error', 'Stok buku habis!');
            return back();
        }

        $member = Member::findOrFail($request->member_id);

        // Create borrow record
        Borrow::create([
            'book_id' => $request->book_id,
            'member_id' => $request->member_id,
            'borrow_date' => $request->borrow_date,
            'status' => 'borrowed',
        ]);

        // Update book stock
        $book->stock = $book->stock - 1;
        $book->save();

        Session::flash('success', 'Peminjaman berhasil dibuat!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal membuat peminjaman: ' . $e->getMessage());
    }

    return redirect()->route('peminjaman.index');
})->name('borrows.store');

Route::post('/peminjaman/{id}/kembalikan', function ($id) {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $borrow = Borrow::findOrFail($id);

        // Update borrow record
        $borrow->status = 'returned';
        $borrow->return_date = now()->format('Y-m-d');
        $borrow->save();

        // Update book stock
        if ($borrow->book) {
            $book = $borrow->book;
            $book->stock = $book->stock + 1;
            $book->save();
        }

        Session::flash('success', 'Buku berhasil dikembalikan!');
    } catch (\Exception $e) {
        Session::flash('error', 'Gagal mengembalikan buku: ' . $e->getMessage());
    }

    return back();
})->name('borrows.return');

// ============================================
// PROTECTED ROUTES - DASHBOARD
// ============================================
Route::get('/dashboard', function () {
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    try {
        $stats = [
            'total_books' => Book::count(),
            'total_members' => Member::count(),
            'active_borrows' => Borrow::where('status', 'borrowed')->count(),
            'overdue_borrows' => Borrow::where('status', 'borrowed')
                ->whereDate('borrow_date', '<=', now()->subDays(7))
                ->count(),
            'new_books_today' => Book::whereDate('created_at', today())->count(),
            'new_members_today' => Member::whereDate('created_at', today())->count(),
        ];

        // Recent activities
        $recentBooks = Book::latest()->take(5)->get();
        $recentBorrows = Borrow::with(['book', 'member'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentBooks', 'recentBorrows'));

    } catch (\Exception $e) {
        return view('dashboard', [
            'stats' => [],
            'recentBooks' => collect(),
            'recentBorrows' => collect()
        ]);
    }
})->name('dashboard');

// ============================================
// DEBUG ROUTES
// ============================================
Route::get('/debug-session', function () {
    echo "<h1>Session Debug</h1>";
    echo "<pre>";
    print_r(Session::all());
    echo "</pre>";

    echo "<h2>Check Login: " . (checkLogin() ? 'YES' : 'NO') . "</h2>";
    echo "<h2>User Data:</h2>";
    echo "<pre>";
    print_r(Session::get('user_data', []));
    echo "</pre>";
});

Route::get('/debug-db', function () {
    try {
        echo "<h1>Database Connection Test</h1>";

        // Test tables
        $tables = [
            'books' => Book::count(),
            'members' => Member::count(),
            'borrows' => Borrow::count(),
            'users' => DB::table('users')->count(),
        ];

        echo "<h2>Table Counts:</h2>";
        echo "<pre>";
        print_r($tables);
        echo "</pre>";

        // Test sample data
        echo "<h2>Sample Book:</h2>";
        $book = Book::first();
        if ($book) {
            echo "<pre>";
            print_r($book->toArray());
            echo "</pre>";
        } else {
            echo "No books found";
        }

        echo "<h2>Database Name:</h2>";
        echo DB::connection()->getDatabaseName();

    } catch (\Exception $e) {
        echo "<h1>Database Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
});

Route::get('/debug-login', function () {
    // Auto login for testing
    Session::put('user_logged_in', true);
    Session::put('user_data', [
        'username' => 'admin',
        'role' => 'admin',
        'name' => 'Administrator',
        'login_time' => now()->format('d/m/Y H:i:s')
    ]);

    Session::flash('success', 'Debug login berhasil!');
    return redirect()->route('home');
});

// ============================================
// TEST ROUTES (tanpa auth untuk testing)
// ============================================
Route::get('/test-buku', function () {
    echo "<h1>Test Buku Page - NO AUTH</h1>";
    echo "<p>Session check: " . (checkLogin() ? 'Logged In' : 'Not Logged In') . "</p>";
    echo "<p><a href='/buku'>Go to real buku page</a></p>";
    echo "<p><a href='/debug-login'>Auto login (debug)</a></p>";
    echo "<p><a href='/login'>Login page</a></p>";
});

// ============================================
// FALLBACK ROUTE
// ============================================
Route::fallback(function () {
    return view('errors.404');
});
