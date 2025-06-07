<!-- COMPLETE TemplateLibrary.vue - FULL FILE -->
<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <div>
                    <h2 class="text-xl font-semibold">Template Library</h2>
                    <p class="text-sm text-gray-600 mt-1">Choose a template to start your project</p>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-gray-100 rounded-full">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Category Tabs -->
            <div class="border-b">
                <nav class="flex space-x-8 px-6">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="activeCategory = category.id"
                        :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
              activeCategory === category.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
                    >
                        {{ category.name }}
                        <span class="ml-2 bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs">
              {{ getTemplatesByCategory(category.id).length }}
            </span>
                    </button>
                </nav>
            </div>

            <!-- Templates Grid -->
            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Blank Template -->
                    <div
                        v-if="activeCategory === 'all'"
                        @click="selectTemplate(blankTemplate)"
                        class="border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-blue-400 hover:bg-blue-50 cursor-pointer transition-colors group"
                    >
                        <div class="text-center">
                            <Plus class="w-12 h-12 text-gray-400 group-hover:text-blue-500 mx-auto mb-4" />
                            <h3 class="font-medium text-gray-900 mb-2">Start from Scratch</h3>
                            <p class="text-sm text-gray-500">Begin with a blank canvas</p>
                        </div>
                    </div>

                    <!-- Template Cards -->
                    <div
                        v-for="template in filteredTemplates"
                        :key="template.id"
                        @click="selectTemplate(template)"
                        class="group cursor-pointer"
                    >
                        <!-- Preview Image -->
                        <div class="relative bg-gray-100 rounded-lg overflow-hidden mb-3 aspect-[4/3]">
                            <img
                                v-if="template.preview"
                                :src="template.preview"
                                :alt="template.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <FileText class="w-12 h-12 text-gray-400" />
                            </div>

                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-200"></div>

                            <!-- Premium Badge -->
                            <div v-if="template.premium" class="absolute top-2 right-2">
                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                  Premium
                </span>
                            </div>
                        </div>

                        <!-- Template Info -->
                        <div>
                            <h3 class="font-medium text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">
                                {{ template.name }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-2">{{ template.description }}</p>

                            <!-- Tags -->
                            <div class="flex flex-wrap gap-1">
                <span
                    v-for="tag in template.tags?.slice(0, 3)"
                    :key="tag"
                    class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded"
                >
                  {{ tag }}
                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No templates message -->
                <div v-if="filteredTemplates.length === 0" class="text-center py-12">
                    <FileText class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No templates found</h3>
                    <p class="text-gray-500">Try selecting a different category</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t p-6 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        {{ filteredTemplates.length }} template{{ filteredTemplates.length !== 1 ? 's' : '' }} available
                    </div>
                    <div class="flex gap-3">
                        <button
                            @click="$emit('close')"
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="$emit('import-template')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            Import Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { X, Plus, FileText } from 'lucide-vue-next'

const props = defineProps({
    show: Boolean
})

const emit = defineEmits(['close', 'select-template', 'import-template'])

const activeCategory = ref('all')

const categories = [
    { id: 'all', name: 'All Templates' },
    { id: 'landing', name: 'Landing Pages' },
    { id: 'business', name: 'Business' },
    { id: 'portfolio', name: 'Portfolio' },
    { id: 'ecommerce', name: 'E-commerce' },
    { id: 'blog', name: 'Blog' },
    { id: 'agency', name: 'Agency' }
]

const blankTemplate = {
    id: 'blank',
    name: 'Blank Canvas',
    description: 'Start with an empty page',
    category: 'blank',
    elements: []
}

const templates = [
    // Landing Page Templates
    {
        id: 'saas-landing',
        name: 'SaaS Landing Page',
        description: 'Modern landing page for SaaS products',
        category: 'landing',
        tags: ['SaaS', 'Modern', 'CTA'],
        preview: '/templates/saas-landing.jpg',
        elements: [
            {
                id: 'hero-1',
                type: 'hero',
                name: 'Hero Section',
                x: 0,
                y: 0,
                width: 1200,
                height: 600,
                zIndex: 1,
                properties: {
                    title: 'Build Better Products Faster',
                    subtitle: 'The all-in-one platform for modern teams to collaborate, design, and ship amazing products.',
                    buttonText: 'Start Free Trial',
                    buttonColor: '#3b82f6',
                    backgroundColor: '#1f2937',
                    textColor: '#ffffff'
                }
            },
            {
                id: 'features-1',
                type: 'section',
                name: 'Features Section',
                x: 0,
                y: 600,
                width: 1200,
                height: 400,
                zIndex: 2,
                properties: {
                    backgroundColor: '#f9fafb',
                    padding: '80px 20px'
                }
            },
            {
                id: 'cta-1',
                type: 'section',
                name: 'CTA Section',
                x: 0,
                y: 1000,
                width: 1200,
                height: 300,
                zIndex: 3,
                properties: {
                    backgroundColor: '#3b82f6',
                    padding: '60px 20px'
                }
            }
        ]
    },
    {
        id: 'startup-landing',
        name: 'Startup Landing',
        description: 'Clean landing page for startups',
        category: 'landing',
        tags: ['Startup', 'Clean', 'Minimal'],
        preview: '/templates/startup-landing.jpg',
        elements: [
            {
                id: 'navbar-1',
                type: 'navbar',
                name: 'Navigation',
                x: 0,
                y: 0,
                width: 1200,
                height: 80,
                zIndex: 1,
                properties: {
                    brand: 'StartupCo',
                    links: ['Features', 'Pricing', 'About', 'Contact'],
                    backgroundColor: '#ffffff',
                    textColor: '#374151'
                }
            },
            {
                id: 'hero-2',
                type: 'hero',
                name: 'Hero Section',
                x: 0,
                y: 80,
                width: 1200,
                height: 500,
                zIndex: 2,
                properties: {
                    title: 'Revolutionary Startup Solution',
                    subtitle: 'Transform your business with our innovative platform designed for the future.',
                    buttonText: 'Get Started',
                    backgroundColor: '#ffffff',
                    textColor: '#1f2937'
                }
            }
        ]
    },
    {
        id: 'product-launch',
        name: 'Product Launch',
        description: 'Perfect for announcing new products',
        category: 'landing',
        tags: ['Product', 'Launch', 'Announcement'],
        preview: '/templates/product-launch.jpg',
        elements: [
            {
                id: 'countdown-hero',
                type: 'hero',
                name: 'Countdown Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 600,
                zIndex: 1,
                properties: {
                    title: 'Something Big is Coming',
                    subtitle: 'Be the first to experience our revolutionary new product.',
                    buttonText: 'Get Early Access',
                    backgroundColor: '#000000',
                    textColor: '#ffffff'
                }
            }
        ]
    },

    // Business Templates
    {
        id: 'corporate-website',
        name: 'Corporate Website',
        description: 'Professional business website',
        category: 'business',
        tags: ['Corporate', 'Professional', 'Services'],
        preview: '/templates/corporate.jpg',
        elements: [
            {
                id: 'header-1',
                type: 'navbar',
                name: 'Header',
                x: 0,
                y: 0,
                width: 1200,
                height: 100,
                zIndex: 1,
                properties: {
                    brand: 'Corporate Inc.',
                    links: ['Home', 'Services', 'About', 'Contact'],
                    backgroundColor: '#1f2937',
                    textColor: '#ffffff'
                }
            },
            {
                id: 'about-1',
                type: 'section',
                name: 'About Section',
                x: 0,
                y: 100,
                width: 1200,
                height: 400,
                zIndex: 2,
                properties: {
                    backgroundColor: '#ffffff',
                    padding: '80px 20px'
                }
            }
        ]
    },
    {
        id: 'consulting-firm',
        name: 'Consulting Firm',
        description: 'Professional consulting services',
        category: 'business',
        tags: ['Consulting', 'Professional', 'Services'],
        preview: '/templates/consulting.jpg',
        elements: [
            {
                id: 'consulting-hero',
                type: 'hero',
                name: 'Consulting Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 500,
                zIndex: 1,
                properties: {
                    title: 'Strategic Business Consulting',
                    subtitle: 'Drive growth and innovation with our expert consulting services.',
                    buttonText: 'Schedule Consultation',
                    backgroundColor: '#ffffff',
                    textColor: '#1f2937'
                }
            }
        ]
    },

    // Portfolio Templates
    {
        id: 'designer-portfolio',
        name: 'Designer Portfolio',
        description: 'Showcase your creative work',
        category: 'portfolio',
        tags: ['Creative', 'Portfolio', 'Gallery'],
        preview: '/templates/portfolio.jpg',
        elements: [
            {
                id: 'portfolio-hero',
                type: 'hero',
                name: 'Portfolio Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 500,
                zIndex: 1,
                properties: {
                    title: 'Creative Designer',
                    subtitle: 'Crafting beautiful experiences through design',
                    backgroundColor: '#000000',
                    textColor: '#ffffff'
                }
            },
            {
                id: 'gallery-1',
                type: 'gallery',
                name: 'Work Gallery',
                x: 0,
                y: 500,
                width: 1200,
                height: 600,
                zIndex: 2,
                properties: {
                    images: [
                        '/portfolio/work1.jpg',
                        '/portfolio/work2.jpg',
                        '/portfolio/work3.jpg',
                        '/portfolio/work4.jpg'
                    ],
                    columns: 2
                }
            }
        ]
    },
    {
        id: 'photographer-portfolio',
        name: 'Photographer Portfolio',
        description: 'Stunning photography showcase',
        category: 'portfolio',
        tags: ['Photography', 'Visual', 'Gallery'],
        preview: '/templates/photo-portfolio.jpg',
        elements: [
            {
                id: 'photo-hero',
                type: 'hero',
                name: 'Photo Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 600,
                zIndex: 1,
                properties: {
                    title: 'Visual Storyteller',
                    subtitle: 'Capturing moments that matter',
                    backgroundColor: '#ffffff',
                    textColor: '#000000'
                }
            }
        ]
    },

    // E-commerce Templates
    {
        id: 'online-store',
        name: 'Online Store',
        description: 'Complete e-commerce solution',
        category: 'ecommerce',
        tags: ['E-commerce', 'Store', 'Products'],
        preview: '/templates/store.jpg',
        elements: [
            {
                id: 'store-header',
                type: 'navbar',
                name: 'Store Header',
                x: 0,
                y: 0,
                width: 1200,
                height: 80,
                zIndex: 1,
                properties: {
                    brand: 'ShopCo',
                    links: ['Products', 'Categories', 'Sale', 'Cart'],
                    backgroundColor: '#ffffff',
                    textColor: '#374151'
                }
            },
            {
                id: 'product-grid',
                type: 'product-grid',
                name: 'Featured Products',
                x: 0,
                y: 80,
                width: 1200,
                height: 600,
                zIndex: 2,
                properties: {
                    columns: 3,
                    products: [
                        { name: 'Product 1', price: '$99', image: '/products/1.jpg' },
                        { name: 'Product 2', price: '$149', image: '/products/2.jpg' },
                        { name: 'Product 3', price: '$79', image: '/products/3.jpg' }
                    ]
                }
            }
        ]
    },
    {
        id: 'fashion-store',
        name: 'Fashion Store',
        description: 'Trendy fashion e-commerce',
        category: 'ecommerce',
        tags: ['Fashion', 'Trendy', 'Clothing'],
        preview: '/templates/fashion-store.jpg',
        elements: [
            {
                id: 'fashion-hero',
                type: 'hero',
                name: 'Fashion Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 600,
                zIndex: 1,
                properties: {
                    title: 'New Collection',
                    subtitle: 'Discover the latest trends in fashion',
                    buttonText: 'Shop Now',
                    backgroundColor: '#f3f4f6',
                    textColor: '#000000'
                }
            }
        ]
    },

    // Blog Templates
    {
        id: 'tech-blog',
        name: 'Tech Blog',
        description: 'Modern blog for tech content',
        category: 'blog',
        tags: ['Blog', 'Tech', 'Articles'],
        preview: '/templates/blog.jpg',
        elements: [
            {
                id: 'blog-header',
                type: 'navbar',
                name: 'Blog Header',
                x: 0,
                y: 0,
                width: 1200,
                height: 80,
                zIndex: 1,
                properties: {
                    brand: 'TechBlog',
                    links: ['Home', 'Categories', 'About', 'Contact'],
                    backgroundColor: '#f9fafb',
                    textColor: '#374151'
                }
            },
            {
                id: 'blog-hero',
                type: 'section',
                name: 'Featured Post',
                x: 0,
                y: 80,
                width: 1200,
                height: 400,
                zIndex: 2,
                properties: {
                    backgroundColor: '#ffffff',
                    padding: '60px 20px'
                }
            }
        ]
    },
    {
        id: 'lifestyle-blog',
        name: 'Lifestyle Blog',
        description: 'Personal lifestyle blog',
        category: 'blog',
        tags: ['Lifestyle', 'Personal', 'Stories'],
        preview: '/templates/lifestyle-blog.jpg',
        elements: [
            {
                id: 'lifestyle-hero',
                type: 'hero',
                name: 'Lifestyle Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 500,
                zIndex: 1,
                properties: {
                    title: 'Life Adventures',
                    subtitle: 'Stories, tips, and inspiration for everyday living',
                    backgroundColor: '#fef3c7',
                    textColor: '#92400e'
                }
            }
        ]
    },

    // Agency Templates
    {
        id: 'digital-agency',
        name: 'Digital Agency',
        description: 'Showcase agency services',
        category: 'agency',
        tags: ['Agency', 'Services', 'Team'],
        preview: '/templates/agency.jpg',
        elements: [
            {
                id: 'agency-hero',
                type: 'hero',
                name: 'Agency Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 600,
                zIndex: 1,
                properties: {
                    title: 'Digital Agency Excellence',
                    subtitle: 'We create digital experiences that drive results for your business.',
                    buttonText: 'View Our Work',
                    backgroundColor: '#6366f1',
                    textColor: '#ffffff'
                }
            },
            {
                id: 'services-1',
                type: 'section',
                name: 'Services',
                x: 0,
                y: 600,
                width: 1200,
                height: 500,
                zIndex: 2,
                properties: {
                    backgroundColor: '#f9fafb',
                    padding: '80px 20px'
                }
            }
        ]
    },
    {
        id: 'creative-agency',
        name: 'Creative Agency',
        description: 'Bold creative agency showcase',
        category: 'agency',
        tags: ['Creative', 'Bold', 'Artistic'],
        preview: '/templates/creative-agency.jpg',
        elements: [
            {
                id: 'creative-hero',
                type: 'hero',
                name: 'Creative Hero',
                x: 0,
                y: 0,
                width: 1200,
                height: 700,
                zIndex: 1,
                properties: {
                    title: 'We Make Magic',
                    subtitle: 'Transforming ideas into extraordinary creative experiences',
                    buttonText: 'See Our Magic',
                    backgroundColor: '#ec4899',
                    textColor: '#ffffff'
                }
            }
        ]
    }
]

const filteredTemplates = computed(() => {
    if (activeCategory.value === 'all') {
        return templates
    }
    return templates.filter(template => template.category === activeCategory.value)
})

const getTemplatesByCategory = (categoryId) => {
    if (categoryId === 'all') return templates
    return templates.filter(template => template.category === categoryId)
}

const selectTemplate = (template) => {
    emit('select-template', template)
}
</script>
