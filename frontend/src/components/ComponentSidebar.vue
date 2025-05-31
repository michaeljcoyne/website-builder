<!-- Enhanced ComponentSidebar.vue with More Component Types -->
<template>
    <div class="w-64 border-r bg-white flex flex-col">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-sm">Components</h2>
            <div class="mt-2">
                <input
                    v-model="searchTerm"
                    placeholder="Search components..."
                    class="w-full px-2 py-1 text-xs border rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div class="p-2">
                <div v-for="category in filteredCategories" :key="category.name" class="mb-4">
                    <button
                        @click="toggleCategory(category.name)"
                        class="w-full flex items-center justify-between text-xs font-medium text-gray-500 mb-2 px-2 py-1 hover:bg-gray-100 rounded"
                    >
                        <span>{{ category.name }}</span>
                        <ChevronDown
                            :class="['w-3 h-3 transition-transform', expandedCategories.includes(category.name) ? 'rotate-180' : '']"
                        />
                    </button>

                    <div v-show="expandedCategories.includes(category.name)" class="space-y-1">
                        <div
                            v-for="component in category.components"
                            :key="component.id"
                            draggable="true"
                            @dragstart="handleDragStart(component)"
                            @dragend="handleDragEnd"
                            class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-100 cursor-grab active:cursor-grabbing text-sm transition-colors"
                        >
                            <component :is="component.icon" class="h-4 w-4 text-gray-600 flex-shrink-0" />
                            <span class="truncate">{{ component.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="border-t p-4">
            <h3 class="font-semibold text-sm mb-2">Quick Actions</h3>
            <div class="space-y-2">
                <button
                    @click="$emit('loadTemplate', { type: 'blank' })"
                    class="w-full text-left p-2 text-xs hover:bg-gray-100 rounded flex items-center gap-2"
                >
                    <FileText class="w-3 h-3" />
                    Blank Page
                </button>
                <button
                    @click="$emit('loadTemplate', { type: 'landing' })"
                    class="w-full text-left p-2 text-xs hover:bg-gray-100 rounded flex items-center gap-2"
                >
                    <Layout class="w-3 h-3" />
                    Landing Page
                </button>
                <button
                    @click="$emit('loadTemplate', { type: 'contact' })"
                    class="w-full text-left p-2 text-xs hover:bg-gray-100 rounded flex items-center gap-2"
                >
                    <Mail class="w-3 h-3" />
                    Contact Page
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, inject, onMounted } from 'vue'
import {
    Type, ImageIcon, Square, Layout, Grid3X3, Navigation, Video,
    List, Calendar, MapPin, Star, ShoppingCart, Users, Mail,
    Phone, Globe, FileText, MessageSquare, Search, Heart,
    Play, Camera, Music, Monitor, Smartphone, Tablet,
    ChevronDown, CreditCard, Package, TrendingUp, BarChart
} from 'lucide-vue-next'

const { setDraggedItem, setIsDragging } = inject('dragDrop')
const emit = defineEmits(['loadTemplate'])

const searchTerm = ref('')
const expandedCategories = ref(['Basic', 'Layout', 'Forms'])

const componentCategories = [
    {
        name: "Basic",
        components: [
            { id: "text", name: "Text", icon: Type, type: "text" },
            { id: "heading", name: "Heading", icon: Type, type: "heading" },
            { id: "button", name: "Button", icon: Square, type: "button" },
            { id: "image", name: "Image", icon: ImageIcon, type: "image" },
            { id: "divider", name: "Divider", icon: Layout, type: "divider" },
            { id: "spacer", name: "Spacer", icon: Square, type: "spacer" },
        ],
    },
    {
        name: "Layout",
        components: [
            { id: "container", name: "Container", icon: Layout, type: "container" },
            { id: "grid", name: "Grid", icon: Grid3X3, type: "grid" },
            { id: "columns", name: "Columns", icon: Layout, type: "columns" },
            { id: "section", name: "Section", icon: Layout, type: "section" },
            { id: "card", name: "Card", icon: Square, type: "card" },
            { id: "hero", name: "Hero Section", icon: Monitor, type: "hero" },
        ],
    },
    {
        name: "Forms",
        components: [
            { id: "input", name: "Text Input", icon: Square, type: "input" },
            { id: "textarea", name: "Textarea", icon: Square, type: "textarea" },
            { id: "select", name: "Select", icon: List, type: "select" },
            { id: "checkbox", name: "Checkbox", icon: Square, type: "checkbox" },
            { id: "radio", name: "Radio", icon: Square, type: "radio" },
            { id: "form", name: "Contact Form", icon: Mail, type: "form" },
            { id: "newsletter", name: "Newsletter", icon: Mail, type: "newsletter" },
            { id: "search", name: "Search Bar", icon: Search, type: "search" },
        ],
    },
    {
        name: "Navigation",
        components: [
            { id: "navbar", name: "Navigation Bar", icon: Navigation, type: "navbar" },
            { id: "breadcrumb", name: "Breadcrumb", icon: Navigation, type: "breadcrumb" },
            { id: "menu", name: "Menu", icon: List, type: "menu" },
            { id: "footer", name: "Footer", icon: Layout, type: "footer" },
            { id: "sidebar", name: "Sidebar", icon: Layout, type: "sidebar" },
            { id: "pagination", name: "Pagination", icon: Navigation, type: "pagination" },
        ],
    },
    {
        name: "Media",
        components: [
            { id: "video", name: "Video", icon: Video, type: "video" },
            { id: "gallery", name: "Image Gallery", icon: ImageIcon, type: "gallery" },
            { id: "carousel", name: "Carousel", icon: ImageIcon, type: "carousel" },
            { id: "audio", name: "Audio Player", icon: Music, type: "audio" },
            { id: "youtube", name: "YouTube Embed", icon: Play, type: "youtube" },
            { id: "instagram", name: "Instagram Feed", icon: Camera, type: "instagram" },
        ],
    },
    {
        name: "Content",
        components: [
            { id: "testimonial", name: "Testimonial", icon: MessageSquare, type: "testimonial" },
            { id: "review", name: "Review", icon: Star, type: "review" },
            { id: "team-member", name: "Team Member", icon: Users, type: "team-member" },
            { id: "feature", name: "Feature Box", icon: Star, type: "feature" },
            { id: "faq", name: "FAQ", icon: MessageSquare, type: "faq" },
            { id: "blog-post", name: "Blog Post", icon: FileText, type: "blog-post" },
        ],
    },
    {
        name: "E-commerce",
        components: [
            { id: "product-card", name: "Product Card", icon: Package, type: "product-card" },
            { id: "price-table", name: "Pricing Table", icon: CreditCard, type: "price-table" },
            { id: "cart", name: "Shopping Cart", icon: ShoppingCart, type: "cart" },
            { id: "checkout", name: "Checkout Form", icon: CreditCard, type: "checkout" },
            { id: "product-grid", name: "Product Grid", icon: Grid3X3, type: "product-grid" },
            { id: "add-to-cart", name: "Add to Cart", icon: ShoppingCart, type: "add-to-cart" },
        ],
    },
    {
        name: "Social",
        components: [
            { id: "social-links", name: "Social Links", icon: Users, type: "social-links" },
            { id: "share-buttons", name: "Share Buttons", icon: Users, type: "share-buttons" },
            { id: "like-button", name: "Like Button", icon: Heart, type: "like-button" },
            { id: "follow-button", name: "Follow Button", icon: Users, type: "follow-button" },
            { id: "social-feed", name: "Social Feed", icon: Users, type: "social-feed" },
        ],
    },
    {
        name: "Analytics",
        components: [
            { id: "chart", name: "Chart", icon: BarChart, type: "chart" },
            { id: "counter", name: "Number Counter", icon: TrendingUp, type: "counter" },
            { id: "progress-bar", name: "Progress Bar", icon: TrendingUp, type: "progress-bar" },
            { id: "stats", name: "Stats Block", icon: BarChart, type: "stats" },
        ],
    },
    {
        name: "Interactive",
        components: [
            { id: "accordion", name: "Accordion", icon: List, type: "accordion" },
            { id: "tabs", name: "Tabs", icon: Layout, type: "tabs" },
            { id: "modal", name: "Modal", icon: Square, type: "modal" },
            { id: "dropdown", name: "Dropdown", icon: List, type: "dropdown" },
            { id: "tooltip", name: "Tooltip", icon: MessageSquare, type: "tooltip" },
            { id: "popup", name: "Popup", icon: Square, type: "popup" },
        ],
    },
]

const filteredCategories = computed(() => {
    if (!searchTerm.value) return componentCategories

    const searchLower = searchTerm.value.toLowerCase()
    return componentCategories.map(category => ({
        ...category,
        components: category.components.filter(component =>
            component.name.toLowerCase().includes(searchLower) ||
            component.type.toLowerCase().includes(searchLower)
        )
    })).filter(category => category.components.length > 0)
})

const handleDragStart = (component) => {
    setDraggedItem(component)
    setIsDragging(true)
}

const handleDragEnd = () => {
    setDraggedItem(null)
    setIsDragging(false)
}

const toggleCategory = (categoryName) => {
    const index = expandedCategories.value.indexOf(categoryName)
    if (index > -1) {
        expandedCategories.value.splice(index, 1)
    } else {
        expandedCategories.value.push(categoryName)
    }
}

onMounted(() => {
    // Auto-expand Basic and Layout categories
    expandedCategories.value = ['Basic', 'Layout', 'Forms']
})
</script>

<!-- Enhanced Canvas.vue - Add these new component renderers -->
<script>
// Add these to your Canvas.vue getDefaultProperties function:

const getDefaultProperties = (type) => {
    switch (type) {
        // Basic Components
        case "text":
            return { content: "Sample text", fontSize: 16, color: "#000000", fontWeight: "normal", textAlign: "left" }
        case "heading":
            return { content: "Heading", fontSize: 24, color: "#000000", level: 1, textAlign: "left" }
        case "button":
            return { text: "Button", backgroundColor: "#3b82f6", textColor: "#ffffff", fontSize: "16", borderRadius: "4px" }
        case "image":
            return { src: "/placeholder.svg?height=200&width=200", alt: "Image", objectFit: "cover", borderRadius: "0px" }
        case "divider":
            return { height: "2px", backgroundColor: "#e5e7eb", margin: "20px 0" }
        case "spacer":
            return { height: "50px", backgroundColor: "transparent" }

        // Layout Components
        case "container":
            return { backgroundColor: "transparent", padding: "20px", borderRadius: "0px", border: "none" }
        case "section":
            return { backgroundColor: "#f9fafb", padding: "60px 20px", textAlign: "center" }
        case "card":
            return { backgroundColor: "#ffffff", padding: "24px", borderRadius: "8px", border: "1px solid #e5e7eb", shadow: true }
        case "hero":
            return {
                backgroundColor: "#1f2937",
                textColor: "#ffffff",
                title: "Welcome to Our Website",
                subtitle: "Create amazing things with our platform",
                buttonText: "Get Started",
                buttonColor: "#3b82f6"
            }

        // Form Components
        case "input":
            return { placeholder: "Enter text...", label: "Input Label", required: false, type: "text" }
        case "textarea":
            return { placeholder: "Enter your message...", label: "Message", rows: 4, required: false }
        case "select":
            return {
                label: "Select Option",
                options: ["Option 1", "Option 2", "Option 3"],
                placeholder: "Choose an option..."
            }
        case "checkbox":
            return { label: "Check this box", checked: false, required: false }
        case "radio":
            return {
                label: "Radio Group",
                options: ["Option 1", "Option 2", "Option 3"],
                name: "radio-group"
            }
        case "form":
            return {
                title: "Contact Us",
                fields: ["name", "email", "message"],
                submitText: "Send Message",
                submitColor: "#3b82f6"
            }
        case "newsletter":
            return {
                title: "Subscribe to our newsletter",
                placeholder: "Enter your email...",
                buttonText: "Subscribe",
                buttonColor: "#3b82f6"
            }
        case "search":
            return { placeholder: "Search...", buttonText: "Search", buttonColor: "#3b82f6" }

        // Navigation Components
        case "navbar":
            return {
                brand: "Brand",
                links: ["Home", "About", "Services", "Contact"],
                backgroundColor: "#ffffff",
                textColor: "#374151"
            }
        case "footer":
            return {
                backgroundColor: "#1f2937",
                textColor: "#ffffff",
                copyright: "© 2024 Your Company. All rights reserved.",
                links: ["Privacy", "Terms", "Contact"]
            }
        case "breadcrumb":
            return {
                items: ["Home", "Products", "Current Page"],
                separator: "/"
            }

        // Media Components
        case "video":
            return {
                src: "https://www.w3schools.com/html/mov_bbb.mp4",
                controls: true,
                autoplay: false,
                poster: "/placeholder.svg?height=300&width=500"
            }
        case "youtube":
            return {
                videoId: "dQw4w9WgXcQ",
                title: "YouTube Video",
                autoplay: false
            }
        case "gallery":
            return {
                images: [
                    "/placeholder.svg?height=200&width=200",
                    "/placeholder.svg?height=200&width=200",
                    "/placeholder.svg?height=200&width=200"
                ],
                columns: 3
            }

        // Content Components
        case "testimonial":
            return {
                quote: "This is an amazing service that helped us grow our business significantly.",
                author: "John Doe",
                position: "CEO, Company Inc.",
                avatar: "/placeholder.svg?height=60&width=60",
                rating: 5
            }
        case "feature":
            return {
                icon: "⭐",
                title: "Amazing Feature",
                description: "This feature will help you achieve your goals faster and more efficiently.",
                backgroundColor: "#f9fafb"
            }
        case "team-member":
            return {
                name: "Jane Smith",
                position: "Lead Developer",
                bio: "Passionate developer with 5+ years of experience.",
                avatar: "/placeholder.svg?height=150&width=150",
                social: { linkedin: "#", twitter: "#", github: "#" }
            }

        // E-commerce Components
        case "product-card":
            return {
                name: "Product Name",
                price: "$99.99",
                originalPrice: "$129.99",
                image: "/placeholder.svg?height=200&width=200",
                rating: 4,
                badge: "Sale"
            }
        case "price-table":
            return {
                title: "Pro Plan",
                price: "$29",
                period: "/month",
                features: ["Feature 1", "Feature 2", "Feature 3"],
                buttonText: "Choose Plan",
                popular: false
            }

        // Social Components
        case "social-links":
            return {
                platforms: ["facebook", "twitter", "instagram", "linkedin"],
                style: "icons", // icons, buttons, or text
                size: "medium"
            }

        // Interactive Components
        case "accordion":
            return {
                items: [
                    { title: "Question 1", content: "Answer to question 1" },
                    { title: "Question 2", content: "Answer to question 2" },
                    { title: "Question 3", content: "Answer to question 3" }
                ]
            }
        case "tabs":
            return {
                tabs: [
                    { title: "Tab 1", content: "Content for tab 1" },
                    { title: "Tab 2", content: "Content for tab 2" },
                    { title: "Tab 3", content: "Content for tab 3" }
                ]
            }

        // Analytics Components
        case "counter":
            return {
                number: 1000,
                label: "Happy Customers",
                prefix: "",
                suffix: "+",
                duration: 2000
            }
        case "progress-bar":
            return {
                label: "Progress",
                percentage: 75,
                color: "#3b82f6",
                showPercentage: true
            }

        default:
            return {}
    }
}
</script>
