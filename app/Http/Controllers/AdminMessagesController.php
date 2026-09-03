<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminMessagesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Messages', [
            'messages' => ContactMessage::query()->latest()->get(),
        ]);
    }

    public function markRead(ContactMessage $message): RedirectResponse
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return back();
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return back()->with('success', 'Mesaj silindi.');
    }
}
