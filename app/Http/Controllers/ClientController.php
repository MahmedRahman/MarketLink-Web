<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $clients = Client::where('organization_id', $organizationId)
            ->with('creator')
            ->latest()
            ->paginate(10);
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,pending'
        ]);

        $data = $request->all();
        $data['organization_id'] = $request->user()->organization_id;
        $data['created_by'] = $request->user()->id;

        Client::create($data);

        return redirect()->route('clients.index')
            ->with('success', 'تم إضافة العميل بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Client $client)
    {
        // التأكد من أن العميل يتبع نفس المنظمة
        if ($client->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $client->load('projects');
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Client $client)
    {
        // التأكد من أن العميل يتبع نفس المنظمة
        if ($client->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        // التأكد من أن العميل يتبع نفس المنظمة
        if ($client->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,pending'
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Client $client)
    {
        // التأكد من أن العميل يتبع نفس المنظمة
        if ($client->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'تم حذف العميل بنجاح');
    }
}
