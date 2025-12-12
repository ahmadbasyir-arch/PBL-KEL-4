<?php

use Illuminate\Http\Request;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate User 38 (Super Admin)
$user = User::find(38);
Auth::login($user);

echo "User logged in: " . $user->name . " (Role: " . $user->role . ")\n";

$request = Request::create('/admin/dashboard', 'GET');
$middleware = new RoleMiddleware();

try {
    $response = $middleware->handle($request, function ($req) {
        return "Access Granted";
    }, 'admin'); // Passing 'admin' as the role requirement

    echo "Result: " . $response . "\n";
} catch (\Exception $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
}
