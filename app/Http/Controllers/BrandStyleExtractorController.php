<?php

namespace App\Http\Controllers;

use App\Models\BrandStyleExtractor;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrandStyleExtractorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        
        // جلب جميع المحتويات مع المشاريع
        $extractors = BrandStyleExtractor::where('organization_id', $organizationId)
            ->with(['project', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // تجميع المحتويات حسب المشروع
        $extractorsByProject = $extractors->groupBy('project_id');
        
        // جلب جميع المشاريع
        $projects = Project::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get();
        
        return view('brand-style-extractors.index', compact('extractors', 'extractorsByProject', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Project $project = null)
    {
        $organizationId = $request->user()->organization_id;
        
        // التحقق من أن المشروع ينتمي للمنظمة إذا تم تمريره
        if ($project && $project->organization_id !== $organizationId) {
            abort(403);
        }
        
        $projects = Project::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get();
        
        return view('brand-style-extractors.create', compact('projects', 'project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'content_type' => 'required|string|in:colors,fonts,logos,images,text,icons,patterns,spacing,post,reels,book,other',
            'content' => 'required|string',
            'revenue' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'brand_profile' => 'nullable|array',
            'brand_profile.voice' => 'nullable|string',
            'brand_profile.tone' => 'nullable|string',
            'brand_profile.structure' => 'nullable|string',
            'brand_profile.language_style' => 'nullable|string',
            'brand_profile.cta_style' => 'nullable|string',
            'brand_profile.enemy' => 'nullable|string',
            'brand_profile.values' => 'nullable|string',
            'brand_profile.hook_patterns' => 'nullable|string',
            'brand_profile.phrases' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['organization_id'] = $request->user()->organization_id;
        $data['created_by'] = $request->user()->id;
        
        // تنظيف brand_profile من القيم الفارغة
        if (isset($data['brand_profile']) && is_array($data['brand_profile'])) {
            $data['brand_profile'] = array_filter($data['brand_profile'], function($value) {
                return !empty($value);
            });
            if (empty($data['brand_profile'])) {
                $data['brand_profile'] = null;
            }
        } else {
            $data['brand_profile'] = null;
        }

        BrandStyleExtractor::create($data);

        return redirect()->route('brand-style-extractors.index')
            ->with('success', 'تم إضافة المحتوى بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, BrandStyleExtractor $brandStyleExtractor)
    {
        if ($brandStyleExtractor->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $brandStyleExtractor->load(['project', 'organization', 'creator']);

        return view('brand-style-extractors.show', compact('brandStyleExtractor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, BrandStyleExtractor $brandStyleExtractor)
    {
        if ($brandStyleExtractor->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $organizationId = $request->user()->organization_id;
        $projects = Project::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('business_name')
            ->get();

        return view('brand-style-extractors.edit', compact('brandStyleExtractor', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BrandStyleExtractor $brandStyleExtractor)
    {
        if ($brandStyleExtractor->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'content_type' => 'required|string|in:colors,fonts,logos,images,text,icons,patterns,spacing,post,reels,book,other',
            'content' => 'required|string',
            'revenue' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'brand_profile' => 'nullable|array',
            'brand_profile.voice' => 'nullable|string',
            'brand_profile.tone' => 'nullable|string',
            'brand_profile.structure' => 'nullable|string',
            'brand_profile.language_style' => 'nullable|string',
            'brand_profile.cta_style' => 'nullable|string',
            'brand_profile.enemy' => 'nullable|string',
            'brand_profile.values' => 'nullable|string',
            'brand_profile.hook_patterns' => 'nullable|string',
            'brand_profile.phrases' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // تنظيف brand_profile من القيم الفارغة
        if (isset($data['brand_profile']) && is_array($data['brand_profile'])) {
            $data['brand_profile'] = array_filter($data['brand_profile'], function($value) {
                return !empty($value);
            });
            if (empty($data['brand_profile'])) {
                $data['brand_profile'] = null;
            }
        } else {
            $data['brand_profile'] = null;
        }

        $brandStyleExtractor->update($data);

        return redirect()->route('brand-style-extractors.index')
            ->with('success', 'تم تحديث المحتوى بنجاح');
    }

    /**
     * Analyze content using DeepSeek API
     */
    public function analyzeContent(Request $request, BrandStyleExtractor $brandStyleExtractor)
    {
        if ($brandStyleExtractor->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        try {
            $apiKey = config('services.deepseek.api_key');
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'DeepSeek API key غير موجود'
                ], 400);
            }

            $content = $brandStyleExtractor->content;
            
            $prompt = "قم بتحليل المحتوى التالي واستخراج Brand Profile منه. أرجو إرجاع النتائج بصيغة JSON فقط بدون أي نص إضافي، مع الحقول التالية:
- voice: الصوت/الأسلوب
- tone: النبرة
- structure: البنية/الهيكل
- language_style: أسلوب اللغة
- cta_style: أسلوب الدعوة للعمل
- enemy: العدو/المشكلة التي يحلها
- values: القيم
- hook_patterns: أنماط الجذب
- phrases: العبارات المميزة

المحتوى:
{$content}

أرجو إرجاع JSON فقط بهذا الشكل:
{
  \"voice\": \"...\",
  \"tone\": \"...\",
  \"structure\": \"...\",
  \"language_style\": \"...\",
  \"cta_style\": \"...\",
  \"enemy\": \"...\",
  \"values\": \"...\",
  \"hook_patterns\": \"...\",
  \"phrases\": \"...\"
}";

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                Log::error('DeepSeek API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في تحليل المحتوى. يرجى المحاولة مرة أخرى.',
                ], $response->status());
            }

            $responseData = $response->json();
            $aiResponse = $responseData['choices'][0]['message']['content'] ?? '';
            
            // محاولة استخراج JSON من الرد
            $jsonMatch = [];
            if (preg_match('/\{[^{}]*\}/s', $aiResponse, $jsonMatch)) {
                $brandProfile = json_decode($jsonMatch[0], true);
            } else {
                // محاولة تحليل الرد مباشرة
                $brandProfile = json_decode($aiResponse, true);
            }

            if (!$brandProfile || !is_array($brandProfile)) {
                Log::error('Failed to parse DeepSeek response', [
                    'response' => $aiResponse
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في تحليل استجابة AI',
                ], 500);
            }

            // تنظيف البيانات
            $brandProfile = array_filter($brandProfile, function($value) {
                return !empty($value) && is_string($value);
            });

            if (empty($brandProfile)) {
                return response()->json([
                    'success' => false,
                    'error' => 'لم يتم استخراج أي بيانات من المحتوى',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'brand_profile' => $brandProfile,
            ]);

        } catch (\Exception $e) {
            Log::error('Error analyzing content', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء تحليل المحتوى: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, BrandStyleExtractor $brandStyleExtractor)
    {
        if ($brandStyleExtractor->organization_id !== $request->user()->organization_id) {
            abort(403);
        }

        $brandStyleExtractor->delete();

        return redirect()->route('brand-style-extractors.index')
            ->with('success', 'تم حذف المحتوى بنجاح');
    }
}
