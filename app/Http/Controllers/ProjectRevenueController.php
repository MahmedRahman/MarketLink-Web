<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectRevenueController extends Controller
{
    public function index(Project $project)
    {
        $revenues = $project->revenues()->latest()->paginate(10);
        return view('projects.revenues.index', compact('project', 'revenues'));
    }

    public function create(Project $project)
    {
        return view('projects.revenues.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,paypal,western_union,other',
                'transfer_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
                'status' => 'required|in:pending,received,cancelled',
                'notes' => 'nullable|string'
            ]);

            $data = $request->except('transfer_image');

            // رفع صورة التحويل إن وجدت
            if ($request->hasFile('transfer_image')) {
                try {
                    $image = $request->file('transfer_image');
                    
                    // التحقق من أن الملف صحيح
                    if ($image->isValid()) {
                        // التأكد من وجود المجلد
                        $directory = 'revenue_transfers/' . $project->id;
                        if (!Storage::disk('public')->exists($directory)) {
                            Storage::disk('public')->makeDirectory($directory);
                        }
                        
                        $imagePath = $image->store($directory, 'public');
                        $data['transfer_image'] = $imagePath;
                    } else {
                        throw new \Exception('الملف المرفوع غير صحيح');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $project->revenues()->create($data);

            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم إضافة الإيراد بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة الإيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Project $project, ProjectRevenue $revenue)
    {
        return view('projects.revenues.show', compact('project', 'revenue'));
    }

    public function edit(Project $project, ProjectRevenue $revenue)
    {
        return view('projects.revenues.edit', compact('project', 'revenue'));
    }

    public function update(Request $request, Project $project, ProjectRevenue $revenue)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3|in:EGP',
                'revenue_date' => 'required|date',
                'payment_method' => 'required|in:cash,bank_transfer,credit_card,check,vodafone_cash,instapay,paypal,western_union,other',
                'transfer_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
                'delete_transfer_image' => 'nullable|boolean',
                'status' => 'required|in:pending,received,cancelled',
                'notes' => 'nullable|string'
            ]);

            $data = $request->except(['transfer_image', 'delete_transfer_image']);

            // حذف الصورة القديمة إذا طلب المستخدم ذلك
            $shouldDeleteImage = $request->has('delete_transfer_image') && $request->delete_transfer_image == '1';
            
            if ($shouldDeleteImage && $revenue->transfer_image) {
                try {
                    if (Storage::disk('public')->exists($revenue->transfer_image)) {
                        Storage::disk('public')->delete($revenue->transfer_image);
                    }
                    $data['transfer_image'] = null;
                } catch (\Exception $e) {
                    // لا نوقف العملية إذا فشل حذف الصورة
                    \Log::warning('Failed to delete old transfer image: ' . $e->getMessage());
                }
            }

            // رفع صورة التحويل الجديدة إن وجدت
            if ($request->hasFile('transfer_image')) {
                try {
                    $image = $request->file('transfer_image');
                    
                    // التحقق من أن الملف صحيح
                    if ($image->isValid()) {
                        // حذف الصورة القديمة إن وجدت (فقط إذا لم يكن المستخدم قد حذفها بالفعل)
                        if (!$shouldDeleteImage && $revenue->transfer_image && Storage::disk('public')->exists($revenue->transfer_image)) {
                            Storage::disk('public')->delete($revenue->transfer_image);
                        }
                        
                        // التأكد من وجود المجلد
                        $directory = 'revenue_transfers/' . $project->id;
                        if (!Storage::disk('public')->exists($directory)) {
                            Storage::disk('public')->makeDirectory($directory, 0755, true);
                        }
                        
                        $imagePath = $image->store($directory, 'public');
                        $data['transfer_image'] = $imagePath;
                    } else {
                        throw new \Exception('الملف المرفوع غير صحيح أو تالف');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage())
                        ->withInput();
                }
            }

            $revenue->update($data);

            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم تحديث الإيراد بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الإيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Project $project, ProjectRevenue $revenue)
    {
        try {
            $revenue->delete();
            return redirect()->route('projects.revenues.index', $project)
                ->with('success', 'تم حذف الإيراد بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الإيراد: ' . $e->getMessage());
        }
    }
}