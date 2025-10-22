<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('client')->latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::where('status', 'active')->get();
        $selectedClientId = $request->get('client_id');
        return view('projects.create', compact('clients', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'business_name' => 'required|string|max:255',
                'business_description' => 'nullable|string',
                'website_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'tiktok_url' => 'nullable|url',
                'status' => 'required|in:active,inactive,pending',
                'authorized_persons' => 'nullable|array',
                'authorized_persons.*.name' => 'nullable|string|max:255',
                'authorized_persons.*.phone' => 'nullable|string|max:20',
                'project_accounts' => 'nullable|array',
                'project_accounts.*.username' => 'nullable|string|max:255',
                'project_accounts.*.password' => 'nullable|string|max:255',
                'project_accounts.*.url' => 'nullable|string|max:500'
            ]);

            // تحضير البيانات للـ JSON fields
            $data = $request->all();
            
            // تنظيف البيانات الفارغة للأشخاص الموثقين
            if (isset($data['authorized_persons']) && is_array($data['authorized_persons'])) {
                $data['authorized_persons'] = array_filter($data['authorized_persons'], function($person) {
                    return !empty($person['name']) && !empty($person['phone']);
                });
                $data['authorized_persons'] = array_values($data['authorized_persons']); // إعادة ترقيم المصفوفة
            }
            
            // تنظيف البيانات الفارغة للحسابات
            if (isset($data['project_accounts']) && is_array($data['project_accounts'])) {
                $data['project_accounts'] = array_filter($data['project_accounts'], function($account) {
                    return !empty($account['username']) && !empty($account['password']) && !empty($account['url']);
                });
                $data['project_accounts'] = array_values($data['project_accounts']); // إعادة ترقيم المصفوفة
            }

            Project::create($data);

            return redirect()->route('projects.index')
                ->with('success', 'تم إضافة المشروع بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة المشروع: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load('client');
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $clients = Client::where('status', 'active')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        try {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'business_name' => 'required|string|max:255',
                'business_description' => 'nullable|string',
                'website_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'twitter_url' => 'nullable|url',
                'linkedin_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'tiktok_url' => 'nullable|url',
                'status' => 'required|in:active,inactive,pending',
                'authorized_persons' => 'nullable|array',
                'authorized_persons.*.name' => 'nullable|string|max:255',
                'authorized_persons.*.phone' => 'nullable|string|max:20',
                'project_accounts' => 'nullable|array',
                'project_accounts.*.username' => 'nullable|string|max:255',
                'project_accounts.*.password' => 'nullable|string|max:255',
                'project_accounts.*.url' => 'nullable|string|max:500'
            ]);

            // تحضير البيانات للـ JSON fields
            $data = $request->all();
            
            // تنظيف البيانات الفارغة للأشخاص الموثقين
            if (isset($data['authorized_persons']) && is_array($data['authorized_persons'])) {
                $data['authorized_persons'] = array_filter($data['authorized_persons'], function($person) {
                    return !empty($person['name']) && !empty($person['phone']);
                });
                $data['authorized_persons'] = array_values($data['authorized_persons']); // إعادة ترقيم المصفوفة
            }
            
            // تنظيف البيانات الفارغة للحسابات
            if (isset($data['project_accounts']) && is_array($data['project_accounts'])) {
                $data['project_accounts'] = array_filter($data['project_accounts'], function($account) {
                    return !empty($account['username']) && !empty($account['password']) && !empty($account['url']);
                });
                $data['project_accounts'] = array_values($data['project_accounts']); // إعادة ترقيم المصفوفة
            }

            $project->update($data);

            return redirect()->route('projects.index')
                ->with('success', 'تم تحديث المشروع بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المشروع: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }
}
