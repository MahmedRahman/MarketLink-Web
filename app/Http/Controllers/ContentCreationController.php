<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentCreationController extends Controller
{
    /**
     * عرض صفحة إنشاء المحتوى
     */
    public function index(): View
    {
        return view('content-creation.index');
    }

    /**
     * إنشاء محتوى باستخدام Deep Seek API
     */
    public function generateContent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'prompt' => 'required|string|max:2000',
                'platform' => 'nullable|string|max:50',
                'content_type' => 'nullable|string|max:50',
                'word_count' => 'nullable|string|max:20',
                'reference_post' => 'nullable|string|max:5000',
            ]);

            $prompt = $request->input('prompt');
            $platform = $request->input('platform');
            $contentType = $request->input('content_type');
            $wordCount = $request->input('word_count');
            $referencePost = $request->input('reference_post');
            $apiKey = config('services.deepseek.api_key');

            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'error' => 'مفتاح API غير موجود. يرجى التحقق من الإعدادات.'
                ], 500);
            }

            // زيادة الـ execution time limit
            set_time_limit(180); // 3 دقائق

            // Platform names mapping
            $platformNames = [
                'facebook' => 'فيسبوك',
                'instagram' => 'إنستغرام',
                'twitter' => 'تويتر',
                'linkedin' => 'لينكد إن',
                'tiktok' => 'تيك توك',
                'youtube' => 'يوتيوب',
                'snapchat' => 'سناب شات',
                'general' => 'عام'
            ];

            // Content type names mapping
            $contentTypeNames = [
                'post' => 'بوست',
                'story' => 'قصة',
                'reel' => 'ريل',
                'ad' => 'إعلان',
                'caption' => 'تعليق',
                'article' => 'مقال',
                'video_script' => 'سيناريو فيديو',
                'carousel' => 'كروسول',
                'general' => 'عام'
            ];

            $platformName = $platformNames[$platform] ?? 'وسائل التواصل الاجتماعي';
            $contentTypeName = $contentTypeNames[$contentType] ?? 'محتوى';

            // Build platform-specific requirements
            $platformRequirements = '';
            if ($platform) {
                switch ($platform) {
                    case 'facebook':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً لفيسبوك: يمكن أن يكون أطول قليلاً، مناسب للمشاركة والتعليقات";
                        break;
                    case 'instagram':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً لإنستغرام: بصري وجذاب، مناسب للهاشتاجات";
                        break;
                    case 'twitter':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً لتويتر: مختصر وواضح (يفضل أقل من 280 حرف)، مناسب للتفاعل السريع";
                        break;
                    case 'linkedin':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً للينكد إن: احترافي، يركز على القيمة المهنية والتنمية";
                        break;
                    case 'tiktok':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً لتيك توك: مرح ومثير للاهتمام، مناسب للجيل الشاب";
                        break;
                    case 'youtube':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً لليوتيوب: تفصيلي ومفيد، مناسب للفيديو";
                        break;
                    case 'snapchat':
                        $platformRequirements = "\n- المحتوى يجب أن يكون مناسباً لسناب شات: قصير ومباشر، مناسب للقصة";
                        break;
                }
            }

            // Build content type-specific requirements
            $contentTypeRequirements = '';
            if ($contentType) {
                switch ($contentType) {
                    case 'post':
                        $contentTypeRequirements = "\n- نوع المحتوى: بوست عادي - يجب أن يكون جذاباً ومقروءاً";
                        break;
                    case 'story':
                        $contentTypeRequirements = "\n- نوع المحتوى: قصة - يجب أن يكون قصيراً ومباشراً (عادة أقل من 100 كلمة)";
                        break;
                    case 'reel':
                        $contentTypeRequirements = "\n- نوع المحتوى: ريل - يجب أن يكون مناسباً للفيديو القصير، جذاب ومثير";
                        break;
                    case 'ad':
                        $contentTypeRequirements = "\n- نوع المحتوى: إعلان - يجب أن يكون مقنعاً ويركز على الفوائد، يحتوي على CTA واضح";
                        break;
                    case 'caption':
                        $contentTypeRequirements = "\n- نوع المحتوى: تعليق - يجب أن يكون مناسباً للصورة أو الفيديو، مكمل للمحتوى البصري";
                        break;
                    case 'article':
                        $contentTypeRequirements = "\n- نوع المحتوى: مقال - يجب أن يكون تفصيلياً ومفيداً، منظم بفقرات";
                        break;
                    case 'video_script':
                        $contentTypeRequirements = "\n- نوع المحتوى: سيناريو فيديو - يجب أن يكون مناسباً للفيديو، يحتوي على حوار أو سرد";
                        break;
                    case 'carousel':
                        $contentTypeRequirements = "\n- نوع المحتوى: كروسول - يجب أن يكون منقسماً إلى نقاط أو أجزاء واضحة";
                        break;
                }
            }

            // Build word count requirement
            $wordCountRequirement = '';
            if ($wordCount && $wordCount !== 'custom' && is_numeric($wordCount)) {
                $wordCountRequirement = "\n7. عدد الكلمات المطلوب: {$wordCount} كلمة تقريباً (يُسمح بزيادة أو نقصان بسيط)";
            }

            // Build reference post section
            $referencePostSection = '';
            if (!empty($referencePost)) {
                $referencePostSection = "\n\n=== البوست المرجعي ===
يوجد بوست مرجعي أدناه. يجب أن يكون المحتوى الجديد مشابهاً له في الأسلوب والنبرة والهيكل، ولكن مع محتوى جديد بناءً على الطلب أعلاه.

البوست المرجعي:
{$referencePost}

ملاحظة مهمة: استخدم هذا البوست كمرجع للأسلوب والنبرة فقط، ولكن أنشئ محتوى جديداً تماماً بناءً على الطلب المحدد أعلاه.";
            }

            $fullPrompt = "أنت مساعد محترف في إنشاء محتوى تسويقي إبداعي. مهمتك هي إنشاء محتوى تسويقي جذاب ومؤثر بناءً على الطلب التالي:

الطلب: {$prompt}

=== معلومات السياق ===
" . ($platform ? "المنصة المستهدفة: {$platformName}" : "المنصة: عام") . "
" . ($contentType ? "نوع المحتوى: {$contentTypeName}" : "نوع المحتوى: عام") . "
" . ($wordCount && $wordCount !== 'custom' ? "عدد الكلمات المطلوب: {$wordCount} كلمة" : "") . "
{$referencePostSection}

=== المتطلبات ===
1. المحتوى يجب أن يكون جذاباً ومؤثراً
2. يجب أن يكون مناسباً لوسائل التواصل الاجتماعي
3. استخدم لغة عربية سليمة وواضحة
4. اجعل المحتوى إبداعياً ومميزاً
5. أضف دعوة للعمل (Call to Action) إذا كان مناسباً{$platformRequirements}{$contentTypeRequirements}{$wordCountRequirement}
6. تأكد من أن المحتوى مناسب تماماً للمنصة المحددة ونوع المحتوى المطلوب" . (!empty($referencePost) ? "\n7. استخدم البوست المرجعي كدليل للأسلوب والنبرة، ولكن أنشئ محتوى جديداً تماماً" : "") . "

=== المخرجات المطلوبة ===
أرجع المحتوى الكامل الجاهز للاستخدام بدون أي نص إضافي أو شرح أو عناوين.";

            Log::info('Sending request to DeepSeek API for content generation', [
                'prompt_length' => strlen($prompt),
                'full_prompt_length' => strlen($fullPrompt)
            ]);

            $response = Http::timeout(150)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $fullPrompt
                        ]
                    ],
                    'temperature' => 0.8,
                    'max_tokens' => 2000
                ]);

            if (!$response->successful()) {
                Log::error('DeepSeek API error for content generation', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في إنشاء المحتوى. يرجى المحاولة مرة أخرى.',
                ], $response->status());
            }

            $responseData = $response->json();
            $content = $responseData['choices'][0]['message']['content'] ?? '';
            
            if (empty($content)) {
                return response()->json([
                    'success' => false,
                    'error' => 'لم يتم إنشاء محتوى. يرجى المحاولة مرة أخرى.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'content' => trim($content),
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating content', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء إنشاء المحتوى: ' . $e->getMessage(),
            ], 500);
        }
    }
}

