<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء محتوى - MarketLink</title>
    
    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
        
        body {
            background: #f5f5f5;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .content-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .content-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        
        .generation-input {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(0, 0, 0, 0.1);
        }
        
        .generation-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white py-2 px-4 text-center text-sm font-semibold">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
            <span>🎉 عرض محدود: خصم 85% على باقة Nano Banana Pro - السنة الجديدة</span>
            <span class="bg-white/20 px-2 py-1 rounded text-xs">00:15:32</span>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                        <span class="material-icons text-white text-xl">auto_awesome</span>
                    </div>
                    <span class="text-xl font-bold text-gray-800">إنشاء محتوى</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-icons">dashboard</span>
                    </a>
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center cursor-pointer">
                        <span class="material-icons text-gray-600 text-sm">person</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Content Grid -->
        <div id="content-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
            <!-- Content cards will be added here dynamically -->
        </div>
    </div>

    <!-- AI Generation Interface - Fixed Bottom -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col gap-4">
                <!-- Input and Button Row - First -->
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <!-- Input Field -->
                    <div class="flex-1 w-full">
                        <input 
                            type="text" 
                            id="generation-prompt" 
                            placeholder="+ صف المشهد الذي تتخيله"
                            class="w-full px-4 py-3 generation-input rounded-xl text-sm focus:ring-2 focus:ring-purple-500"
                        />
                    </div>
                    
                    <!-- Generate Button -->
                    <button id="generate-btn" onclick="generateContent()" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-icons text-sm" id="generate-icon">auto_awesome</span>
                        <span id="generate-text">إنشاء</span>
                        <span class="material-icons text-sm animate-spin hidden" id="generate-spinner">sync</span>
                    </button>
                </div>
                
                <!-- Selection Row - Dropdowns Side by Side - Below -->
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Platform Selection -->
                    <div class="flex flex-col gap-2 min-w-[150px]">
                        <label class="text-sm font-medium text-gray-700">نوع المنصة:</label>
                        <select id="platform-select" class="px-4 py-2.5 border-2 border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white transition-all hover:border-purple-400">
                            <option value="">اختر المنصة</option>
                            <option value="facebook">فيسبوك</option>
                            <option value="instagram">إنستغرام</option>
                            <option value="twitter">تويتر</option>
                            <option value="linkedin">لينكد إن</option>
                            <option value="tiktok">تيك توك</option>
                            <option value="youtube">يوتيوب</option>
                            <option value="snapchat">سناب شات</option>
                            <option value="general">عام</option>
                        </select>
                    </div>
                    
                    <!-- Content Type Selection -->
                    <div class="flex flex-col gap-2 min-w-[150px]">
                        <label class="text-sm font-medium text-gray-700">نوع المحتوى:</label>
                        <select id="content-type-select" class="px-4 py-2.5 border-2 border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white transition-all hover:border-purple-400">
                            <option value="">اختر نوع المحتوى</option>
                            <option value="post">بوست</option>
                            <option value="story">قصة</option>
                            <option value="reel">ريل</option>
                            <option value="ad">إعلان</option>
                            <option value="caption">تعليق</option>
                            <option value="article">مقال</option>
                            <option value="video_script">سيناريو فيديو</option>
                            <option value="carousel">كروسول</option>
                            <option value="general">عام</option>
                        </select>
                    </div>
                    
                    <!-- Word Count Selection -->
                    <div class="flex flex-col gap-2 min-w-[150px]">
                        <label class="text-sm font-medium text-gray-700">عدد الكلمات:</label>
                        <select id="word-count-select" class="px-4 py-2.5 border-2 border-gray-300 rounded-lg text-sm font-medium focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white transition-all hover:border-purple-400">
                            <option value="">اختر عدد الكلمات</option>
                            <option value="50">50 كلمة</option>
                            <option value="100">100 كلمة</option>
                            <option value="150">150 كلمة</option>
                            <option value="200">200 كلمة</option>
                            <option value="300">300 كلمة</option>
                            <option value="500">500 كلمة</option>
                            <option value="750">750 كلمة</option>
                            <option value="1000">1000 كلمة</option>
                            <option value="custom">مخصص</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add bottom padding to prevent content from being hidden behind fixed bottom bar -->
    <div class="h-32"></div>

    <!-- Content Modal -->
    <div id="content-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-gray-900 rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden flex flex-col md:flex-row">
            <!-- Sidebar -->
            <div class="bg-gray-800 w-full md:w-80 flex-shrink-0 border-l border-gray-700 flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                            <span class="material-icons text-gray-900 text-sm">auto_awesome</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-semibold">المحتوى المُنشأ</p>
                            <p class="text-gray-400 text-xs">Author</p>
                        </div>
                    </div>
                    <button onclick="closeContentModal()" class="text-gray-400 hover:text-white">
                        <span class="material-icons">close</span>
                    </button>
                </div>

                <!-- PROMPT Section -->
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-gray-400 text-sm">list</span>
                            <h4 class="text-gray-300 text-xs font-semibold uppercase">PROMPT</h4>
                        </div>
                        <button onclick="copyPrompt()" class="text-gray-400 hover:text-white text-xs">
                            <span class="material-icons text-sm">content_copy</span>
                        </button>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-3 mt-2">
                        <p id="modal-prompt" class="text-gray-300 text-sm whitespace-pre-wrap"></p>
                    </div>
                </div>

                <!-- INFORMATION Section -->
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-icons text-gray-400 text-sm">info</span>
                        <h4 class="text-gray-300 text-xs font-semibold uppercase">INFORMATION</h4>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <p class="text-gray-400 text-xs mb-1">Model</p>
                            <p class="text-gray-300 text-sm">Deep Seek Chat</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs mb-1">المنصة</p>
                            <p class="text-gray-300 text-sm" id="modal-platform">عام</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs mb-1">نوع المحتوى</p>
                            <p class="text-gray-300 text-sm" id="modal-content-type">عام</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs mb-1">عدد الكلمات</p>
                            <p class="text-gray-300 text-sm" id="modal-word-count">غير محدد</p>
                        </div>
                    </div>
                </div>

                <!-- ADDITIONAL Section -->
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center justify-between cursor-pointer" onclick="toggleAdditional()">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-gray-400 text-sm">settings</span>
                            <h4 class="text-gray-300 text-xs font-semibold uppercase">ADDITIONAL</h4>
                        </div>
                        <span class="material-icons text-gray-400 text-sm" id="additional-arrow">expand_more</span>
                    </div>
                    <div id="additional-content" class="hidden mt-3 space-y-2">
                        <div>
                            <p class="text-gray-400 text-xs mb-1">تاريخ الإنشاء</p>
                            <p class="text-gray-300 text-sm" id="creation-date"></p>
                        </div>
                    </div>
                </div>

                <!-- IMAGE Section -->
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center justify-between cursor-pointer" onclick="toggleImageSection()">
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-gray-400 text-sm">image</span>
                            <h4 class="text-gray-300 text-xs font-semibold uppercase">الصورة</h4>
                        </div>
                        <span class="material-icons text-gray-400 text-sm" id="image-arrow">expand_more</span>
                    </div>
                    <div id="image-content" class="hidden mt-3">
                        <div class="bg-gray-900 rounded-lg p-3 mb-2">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-gray-300 text-xs font-semibold">🎨 Design Prompt</p>
                                <button onclick="copyImagePrompt()" class="text-gray-400 hover:text-white text-xs flex items-center gap-1">
                                    <span class="material-icons text-sm">content_copy</span>
                                    <span>نسخ</span>
                                </button>
                            </div>
                            <p id="image-prompt" class="text-gray-300 text-xs whitespace-pre-wrap leading-relaxed"></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="p-4 mt-auto space-y-3">
                    <button onclick="recreateContent()" class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 rounded-lg flex items-center justify-center gap-2 transition-colors">
                        <span class="material-icons text-sm">refresh</span>
                        إعادة إنشاء
                    </button>
                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="publishContent()" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex flex-col items-center gap-1 transition-colors" title="نشر">
                            <span class="material-icons text-sm">publish</span>
                            <span class="text-xs">نشر</span>
                        </button>
                        <button onclick="downloadContent()" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex flex-col items-center gap-1 transition-colors" title="تحميل">
                            <span class="material-icons text-sm">download</span>
                            <span class="text-xs">تحميل</span>
                        </button>
                        <button onclick="copyContent()" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex flex-col items-center gap-1 transition-colors" title="نسخ">
                            <span class="material-icons text-sm">content_copy</span>
                            <span class="text-xs">نسخ</span>
                        </button>
                        <button onclick="editContent()" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex flex-col items-center gap-1 transition-colors" title="تعديل">
                            <span class="material-icons text-sm">edit</span>
                            <span class="text-xs">تعديل</span>
                        </button>
                        <button onclick="shareContent()" class="bg-gray-700 hover:bg-gray-600 text-white p-3 rounded-lg flex flex-col items-center gap-1 transition-colors" title="مشاركة">
                            <span class="material-icons text-sm">share</span>
                            <span class="text-xs">مشاركة</span>
                        </button>
                        <button onclick="deleteContent()" class="bg-gray-700 hover:bg-red-600 text-white p-3 rounded-lg flex flex-col items-center gap-1 transition-colors" title="حذف">
                            <span class="material-icons text-sm">delete</span>
                            <span class="text-xs">حذف</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 bg-gray-900 overflow-y-auto">
                <div class="p-6">
                    <div id="modal-content" class="text-gray-200 whitespace-pre-wrap leading-relaxed text-base"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentContent = '';
        let currentPrompt = '';
        let activeLoadingCards = 0;
        const MAX_CONCURRENT_REQUESTS = 5;

        // Handle generation prompt submission
        document.getElementById('generation-prompt').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                generateContent();
            }
        });

        function generateContent() {
            const promptInput = document.getElementById('generation-prompt');
            const prompt = promptInput.value.trim();
            
            if (!prompt) {
                alert('يرجى إدخال وصف للمحتوى');
                return;
            }

            // Check if we've reached the maximum concurrent requests
            if (activeLoadingCards >= MAX_CONCURRENT_REQUESTS) {
                alert(`يمكنك إنشاء ${MAX_CONCURRENT_REQUESTS} محتويات في نفس الوقت. يرجى الانتظار حتى يكتمل أحدها.`);
                return;
            }

            // Clear input immediately
            promptInput.value = '';

            // Disable button if we're at max capacity
            const generateBtn = document.getElementById('generate-btn');
            const generateIcon = document.getElementById('generate-icon');
            const generateText = document.getElementById('generate-text');
            const generateSpinner = document.getElementById('generate-spinner');
            
            if (activeLoadingCards >= MAX_CONCURRENT_REQUESTS - 1) {
                generateBtn.disabled = true;
                generateIcon.classList.add('hidden');
                generateText.textContent = `جاري الإنشاء (${activeLoadingCards + 1}/${MAX_CONCURRENT_REQUESTS})...`;
                generateSpinner.classList.remove('hidden');
            }

            // Increment active loading cards counter
            activeLoadingCards++;

            // Create loading card immediately
            const loadingCardId = 'loading-' + Date.now();
            const loadingCard = createLoadingCard(loadingCardId, prompt);
            const contentGrid = document.getElementById('content-grid');
            contentGrid.insertBefore(loadingCard, contentGrid.firstChild);

            // Scroll to top to show loading card
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Get platform, content type, and word count
            const platform = document.getElementById('platform-select').value;
            const contentType = document.getElementById('content-type-select').value;
            const wordCount = document.getElementById('word-count-select').value;

            // Make API call - use current origin to ensure HTTPS
            const apiUrl = window.location.origin + '/content-creation/generate';
            
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    prompt: prompt,
                    platform: platform,
                    content_type: contentType,
                    word_count: wordCount
                })
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || 'حدث خطأ أثناء إنشاء المحتوى');
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    // Remove loading card
                    const loadingCardElement = document.getElementById(loadingCardId);
                    if (loadingCardElement) {
                        loadingCardElement.remove();
                    }

                    // Create success card
                    const successCard = createSuccessCard(data.content, loadingCardId, prompt, platform, contentType, wordCount);
                    contentGrid.insertBefore(successCard, contentGrid.firstChild);
                } else {
                    throw new Error(data.error || 'فشل في إنشاء المحتوى');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Remove loading card
                const loadingCardElement = document.getElementById(loadingCardId);
                if (loadingCardElement) {
                    loadingCardElement.remove();
                }

                alert(error.message || 'حدث خطأ أثناء إنشاء المحتوى');
            })
            .finally(() => {
                // Decrement active loading cards counter
                activeLoadingCards--;
                
                // Re-enable button if we're below max capacity
                if (activeLoadingCards < MAX_CONCURRENT_REQUESTS) {
                    generateBtn.disabled = false;
                    generateIcon.classList.remove('hidden');
                    generateText.textContent = 'إنشاء';
                    generateSpinner.classList.add('hidden');
                } else {
                    // Update button text to show current count
                    generateText.textContent = `جاري الإنشاء (${activeLoadingCards}/${MAX_CONCURRENT_REQUESTS})...`;
                }
            });
        }

        function createLoadingCard(id, prompt) {
            const promptPreview = prompt.length > 40 ? prompt.substring(0, 40) + '...' : prompt;
            const card = document.createElement('div');
            card.id = id;
            card.className = 'content-card bg-gray-900 rounded-2xl overflow-hidden shadow-md aspect-[4/5] relative';
            card.innerHTML = `
                <div class="absolute inset-0 flex flex-col items-center justify-center p-4">
                    <div class="bg-gray-800 rounded-lg px-3 py-1.5 mb-4">
                        <span class="text-white text-xs font-semibold">In progress</span>
                    </div>
                    <div class="w-16 h-16 border-4 border-gray-700 border-t-purple-500 rounded-full animate-spin mb-4"></div>
                    <p class="text-gray-400 text-xs text-center px-2 line-clamp-2">${promptPreview}</p>
                </div>
            `;
            return card;
        }

        function createSuccessCard(content, id, prompt, platform, contentType, wordCount) {
            const promptPreview = prompt.length > 50 ? prompt.substring(0, 50) + '...' : prompt;
            const platformNames = {
                'facebook': 'فيسبوك',
                'instagram': 'إنستغرام',
                'twitter': 'تويتر',
                'linkedin': 'لينكد إن',
                'tiktok': 'تيك توك',
                'youtube': 'يوتيوب',
                'snapchat': 'سناب شات',
                'general': 'عام'
            };
            const contentTypeNames = {
                'post': 'بوست',
                'story': 'قصة',
                'reel': 'ريل',
                'ad': 'إعلان',
                'caption': 'تعليق',
                'article': 'مقال',
                'video_script': 'سيناريو فيديو',
                'carousel': 'كروسول',
                'general': 'عام'
            };
            const platformName = platformNames[platform] || '';
            const contentTypeName = contentTypeNames[contentType] || '';
            const wordCountText = wordCount && wordCount !== 'custom' ? `${wordCount} كلمة` : '';
            
            const card = document.createElement('div');
            card.id = 'success-' + id;
            card.className = 'content-card bg-white rounded-2xl overflow-hidden shadow-md aspect-[4/5] relative cursor-pointer';
            card.onclick = () => showContentModal(content, prompt, platform, contentType, wordCount);
            card.innerHTML = `
                <div class="absolute inset-0 flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-emerald-100">
                    <div class="text-center w-full">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-icons text-white text-3xl">check</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">تم إنشاء المحتوى</h3>
                        <p class="text-xs text-gray-600 px-2 line-clamp-2 mb-2">${promptPreview}</p>
                        ${platformName ? `<p class="text-xs text-gray-500">${platformName}</p>` : ''}
                        ${contentTypeName ? `<p class="text-xs text-gray-500">${contentTypeName}</p>` : ''}
                        ${wordCountText ? `<p class="text-xs text-gray-500">${wordCountText}</p>` : ''}
                    </div>
                </div>
            `;
            return card;
        }

        function showContentModal(content, prompt, platform, contentType, wordCount) {
            currentContent = content;
            currentPrompt = prompt || '';
            
            const platformNames = {
                'facebook': 'فيسبوك',
                'instagram': 'إنستغرام',
                'twitter': 'تويتر',
                'linkedin': 'لينكد إن',
                'tiktok': 'تيك توك',
                'youtube': 'يوتيوب',
                'snapchat': 'سناب شات',
                'general': 'عام'
            };
            const contentTypeNames = {
                'post': 'بوست',
                'story': 'قصة',
                'reel': 'ريل',
                'ad': 'إعلان',
                'caption': 'تعليق',
                'article': 'مقال',
                'video_script': 'سيناريو فيديو',
                'carousel': 'كروسول',
                'general': 'عام'
            };
            
            // Generate image prompt
            const imagePrompt = generateImagePrompt(prompt, platform, contentType, content);
            currentImagePrompt = imagePrompt;
            document.getElementById('image-prompt').textContent = imagePrompt;
            
            document.getElementById('modal-content').textContent = content;
            document.getElementById('modal-prompt').textContent = prompt || '';
            document.getElementById('modal-platform').textContent = platformNames[platform] || 'عام';
            document.getElementById('modal-content-type').textContent = contentTypeNames[contentType] || 'عام';
            document.getElementById('modal-word-count').textContent = (wordCount && wordCount !== 'custom') ? `${wordCount} كلمة` : 'غير محدد';
            document.getElementById('creation-date').textContent = new Date().toLocaleDateString('ar-SA', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('content-modal').classList.remove('hidden');
        }

        function generateImagePrompt(prompt, platform, contentType, content) {
            const platformNames = {
                'facebook': 'Facebook',
                'instagram': 'Instagram',
                'twitter': 'Twitter',
                'linkedin': 'LinkedIn',
                'tiktok': 'TikTok',
                'youtube': 'YouTube',
                'snapchat': 'Snapchat',
                'general': 'Social Media'
            };
            
            const contentTypeNames = {
                'post': 'post',
                'story': 'story',
                'reel': 'reel',
                'ad': 'advertisement',
                'caption': 'post',
                'article': 'article',
                'video_script': 'video',
                'carousel': 'carousel',
                'general': 'content'
            };
            
            const platformName = platformNames[platform] || 'Social Media';
            const contentTypeName = contentTypeNames[contentType] || 'content';
            
            // Extract Arabic text from content for headlines
            const arabicText = extractArabicText(content);
            const headline = arabicText.length > 0 ? arabicText[0] : 'محتوى تسويقي جذاب';
            const secondaryText = arabicText.length > 1 ? arabicText[1] : '';
            
            // Build the design prompt
            let designPrompt = `🎨 Design Prompt (انسخ-الصق):\n\n`;
            designPrompt += `A bold, eye-catching ${platformName} ${contentTypeName} design for digital marketing.\n`;
            designPrompt += `Based on the concept: "${prompt}"\n\n`;
            
            if (contentType === 'ad' || contentType === 'post') {
                designPrompt += `A compelling visual representation that captures the essence of the marketing message.\n`;
            } else if (contentType === 'story') {
                designPrompt += `A vertical, mobile-optimized story design with engaging visuals.\n`;
            } else if (contentType === 'reel') {
                designPrompt += `A dynamic, attention-grabbing reel design optimized for short-form video.\n`;
            } else {
                designPrompt += `A professional and engaging design that communicates the marketing message effectively.\n`;
            }
            
            designPrompt += `Strong emotional expression and visual impact.\n`;
            designPrompt += `Big bold Arabic headline in Egyptian dialect:\n`;
            designPrompt += `"${headline}"\n`;
            
            if (secondaryText) {
                designPrompt += `Secondary small text: "${secondaryText}"\n`;
            }
            
            designPrompt += `Modern social media style, high contrast colors (black, yellow, white, or brand colors).\n`;
            designPrompt += `Clean layout, dramatic lighting, cinematic look.\n`;
            designPrompt += `Flat illustration or semi-realistic style.\n`;
            
            if (platform === 'instagram' || platform === 'facebook') {
                designPrompt += `Designed for ${platformName} feed, square 1:1 ratio, high readability on mobile.\n`;
            } else if (platform === 'story' || contentType === 'story') {
                designPrompt += `Designed for ${platformName} story, vertical 9:16 ratio, mobile-first design.\n`;
            } else {
                designPrompt += `Designed for ${platformName}, optimized for mobile viewing.\n`;
            }
            
            return designPrompt;
        }

        function extractArabicText(text) {
            // Extract Arabic sentences/phrases from content
            const arabicRegex = /[\u0600-\u06FF]+[^\u0600-\u06FF]*[\u0600-\u06FF]+/g;
            const matches = text.match(arabicRegex) || [];
            
            // Filter and clean Arabic text
            const arabicSentences = matches
                .map(s => s.trim())
                .filter(s => s.length > 10 && s.length < 100)
                .slice(0, 2);
            
            return arabicSentences;
        }

        function closeContentModal() {
            document.getElementById('content-modal').classList.add('hidden');
        }

        function copyContent() {
            navigator.clipboard.writeText(currentContent).then(() => {
                alert('تم نسخ المحتوى بنجاح');
            }).catch(() => {
                alert('فشل في نسخ المحتوى');
            });
        }

        function copyPrompt() {
            navigator.clipboard.writeText(currentPrompt).then(() => {
                alert('تم نسخ الـ Prompt بنجاح');
            }).catch(() => {
                alert('فشل في نسخ الـ Prompt');
            });
        }

        function toggleAdditional() {
            const content = document.getElementById('additional-content');
            const arrow = document.getElementById('additional-arrow');
            content.classList.toggle('hidden');
            arrow.textContent = content.classList.contains('hidden') ? 'expand_more' : 'expand_less';
        }

        function toggleImageSection() {
            const content = document.getElementById('image-content');
            const arrow = document.getElementById('image-arrow');
            content.classList.toggle('hidden');
            arrow.textContent = content.classList.contains('hidden') ? 'expand_more' : 'expand_less';
        }

        let currentImagePrompt = '';

        function copyImagePrompt() {
            navigator.clipboard.writeText(currentImagePrompt).then(() => {
                alert('تم نسخ Design Prompt بنجاح');
            }).catch(() => {
                alert('فشل في نسخ Design Prompt');
            });
        }

        function recreateContent() {
            if (currentPrompt) {
                document.getElementById('generation-prompt').value = currentPrompt;
                closeContentModal();
                generateContent();
            }
        }

        function publishContent() {
            alert('ميزة النشر قريباً');
        }

        function downloadContent() {
            const blob = new Blob([currentContent], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'content-' + Date.now() + '.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function editContent() {
            alert('ميزة التعديل قريباً');
        }

        function shareContent() {
            if (navigator.share) {
                navigator.share({
                    title: 'محتوى مُنشأ',
                    text: currentContent
                }).catch(() => {
                    copyContent();
                });
            } else {
                copyContent();
            }
        }

        function deleteContent() {
            if (confirm('هل أنت متأكد من حذف هذا المحتوى؟')) {
                const successCard = document.querySelector('[onclick*="showContentModal"]');
                if (successCard && successCard.closest('.content-card')) {
                    successCard.closest('.content-card').remove();
                }
                closeContentModal();
            }
        }


        // Close modal when clicking outside
        document.getElementById('content-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContentModal();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeContentModal();
            }
        });
    </script>
</body>
</html>

