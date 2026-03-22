<?php

namespace App\Http\Controllers;

use App\Jobs\SyncTickTickDataJob;
use Illuminate\Http\RedirectResponse;

class SyncController extends Controller
{
    public function store(): RedirectResponse
    {
        SyncTickTickDataJob::dispatch();

        return back()->with('success', 'Sync started — data will refresh shortly.');
    }
}
