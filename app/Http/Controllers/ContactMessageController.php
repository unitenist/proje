<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Mesajınız için teşekkürler, en kısa sürede dönüş yapacağız.');
    }

    /**
     * Auto-generated lead from the AI robot guide's quick-reply questions.
     */
    public function storeRobotLead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['string', 'max:500'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $message = "AI asistan üzerinden otomatik oluşturuldu:\n\n".implode("\n", $data['answers']);
        if (! empty($data['notes'])) {
            $message .= "\n\nEk talep: {$data['notes']}";
        }

        ContactMessage::create([
            'name' => $data['name'] ?: 'AI Asistan (Web Sitesi Ziyaretçisi)',
            'email' => $data['email'] ?: 'assistant@unitenist.local',
            'phone' => $data['phone'] ?? null,
            'website' => null,
            'message' => $message,
        ]);

        return response()->json(['ok' => true]);
    }
}
