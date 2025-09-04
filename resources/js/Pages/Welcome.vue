<template>
    <GuestLayout :title="title" :menuLogo="logoUrl" :footerLogo="logoUrl">
        <!-- Hero Section -->
        <section class="relative min-h-screen">
            <div class="relative h-screen">
                <div
                    v-for="(slide, index) in heroSlides"
                    :key="index"
                    class="absolute inset-0 transition-opacity duration-1000"
                    :class="{
                        'opacity-100': activeSlide === index,
                        'opacity-0': activeSlide !== index,
                    }"
                    :style="{
                        backgroundImage: `url(${slide.file_path})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                    }"
                >
                    <div
                        class="absolute inset-0 bg-black/50 flex items-center justify-center"
                    >
                        <div
                            class="text-center text-white px-4 max-w-4xl mx-auto"
                        >
                            <div class="mb-6">
                                <h1
                                    class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight"
                                >
                                    <span
                                        class="bg-clip-text text-transparent bg-gradient-to-r from-green-300 to-blue-400"
                                    >
                                            {{ slide.name }}
                                    </span>
                                </h1>
                                <p
                                    class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto leading-relaxed"
                                >
                                    {{ slide.description }}
                                </p>
                            </div>
                            <div class="mt-10 flex flex-col items-center space-y-4 sm:flex-row sm:justify-center sm:space-x-6 sm:space-y-0 sm:space-x-reverse gap-2">
                                <!-- Show Join the Movement button if not authenticated -->
                                <template v-if="!$page.props.auth.user">
                                    <Link
                                        :href="route('register')"
                                        class="inline-block bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white font-semibold px-8 py-4 rounded-full shadow-lg transform hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-center"
                                    >
                                        Join the Movement
                                    </Link>
                                </template>
                                
                                <!-- Show Services and Departments buttons if authenticated -->
                                <template v-else>
                                    <Link
                                        :href="route('frontend.services')"
                                        class="inline-block bg-gradient-to-r from-green-500 to-blue-600 hover:from-green-600 hover:to-blue-700 text-white font-semibold px-8 py-4 rounded-full shadow-lg transform hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-center"
                                    >
                                        Our Services
                                    </Link>
                                    <Link
                                        :href="route('frontend.departments')"
                                        class="inline-block bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-4 rounded-full backdrop-blur-sm border border-white/20 shadow-lg transform hover:-translate-y-1 transition-all duration-300 w-full sm:w-auto text-center"
                                    >
                                        Our Departments
                                    </Link>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Animated Scroll Down Button -->
                <div
                    class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-10"
                >
                    <button
                        @click="scrollToCounter"
                        class="animate-bounce bg-white/20 backdrop-blur-sm p-3.5 rounded-full hover:bg-white/30 transition-all duration-200 active:scale-95 shadow-lg hover:shadow-xl"
                        aria-label="Scroll down"
                    >
                        <i class="fas fa-chevron-down text-white text-xl"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Single Point of Access Section -->
        <section class="py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
            <div class="container mx-auto px-4 py-8 relative z-10">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                        Unity in Diversity for a Better Kenya
                    </h3>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                    </div>
                    <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                        Welcome to the Forward Kenya Party—a movement built on the principles of Unity in Diversity.
                        We're committed to creating a prosperous, inclusive Kenya where every voice matters and every
                        citizen has the opportunity to thrive.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Card 1 -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300"
                    >
                        <div
                            class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6 mx-auto"
                        >
                            <i
                                class="fas fa-hands-helping text-2xl text-green-600 dark:text-green-400"
                            ></i>
                        </div>
                        <h3
                            class="text-xl font-bold text-center text-gray-900 dark:text-white mb-3"
                        >
                            United We Stand
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 text-center">
                            Our strength lies in our diversity. Join a movement that celebrates Kenya's rich cultural
                            heritage while building bridges across communities for a more united and prosperous nation.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300"
                    >
                        <div
                            class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-6 mx-auto"
                        >
                            <i
                                class="fas fa-id-card text-2xl text-blue-600 dark:text-blue-400"
                            ></i>
                        </div>
                        <h3
                            class="text-xl font-bold text-center text-gray-900 dark:text-white mb-3"
                        >
                            Unified Member Profile
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 text-center">
                            Your Forward Kenya Party profile keeps you
                            connected—making it easy to participate, engage, and
                            contribute to our shared mission.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow duration-300"
                    >
                        <div
                            class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mb-6 mx-auto"
                        >
                            <i
                                class="fas fa-seedling text-2xl text-purple-600 dark:text-purple-400"
                            ></i>
                        </div>
                        <h3
                            class="text-xl font-bold text-center text-gray-900 dark:text-white mb-3"
                        >
                            Sustainable Development
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 text-center">
                            We champion policies that balance economic growth with environmental protection,
                            ensuring Kenya's prosperity today doesn't compromise our children's future.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Counter Section -->
        <section
            ref="counterSection"
            class="py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900"
        >
            <div class="container mx-auto px-4 py-8 relative z-10">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                        Building Kenya's Future Together
                    </h3>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                    </div>
                    <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                        The Forward Kenya Party is driving transformative change across our nation. Our impact is seen in
                        the communities we uplift, the policies we champion, and the inclusive future we're building.
                        Together, we're creating a Kenya where every citizen can thrive in dignity and prosperity.
                    </p>
                </div>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="(stat, key) in [
                            {
                                value: animatedStats.users,
                                label: 'Members Registered',
                                icon: 'fa-users',
                                color: 'from-blue-500 to-blue-600',
                            },
                            {
                                value: animatedStats.services,
                                label: 'Party Services',
                                icon: 'fa-cogs',
                                color: 'from-green-500 to-green-600',
                            },
                            {
                                value: animatedStats.departments,
                                label: 'Party Departments',
                                icon: 'fa-building',
                                color: 'from-purple-500 to-purple-600',
                            },
                            {
                                value: animatedStats.activities,
                                label: 'Party Activities',
                                icon: 'fa-chart-line',
                                color: 'from-red-500 to-red-600',
                            },
                        ]"
                        :key="key"
                        class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                    >
                        <div class="flex flex-col items-center">
                            <div
                                class="w-16 h-16 rounded-full flex items-center justify-center mb-4 text-white"
                                :class="`bg-gradient-to-r ${stat.color}`"
                            >
                                <i class="fas text-2xl" :class="stat.icon"></i>
                            </div>
                            <h3
                                class="text-3xl font-bold mb-2 bg-clip-text text-transparent"
                                :class="`bg-gradient-to-r ${stat.color}`"
                            >
                                {{ stat.value }}
                            </h3>
                            <p
                                class="text-gray-600 dark:text-gray-300 text-sm font-medium"
                            >
                                {{ stat.label }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section
            id="services"
            ref="servicesSection"
            class="py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900"
        >
            <div class="container mx-auto px-4 py-8 relative z-10">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                        Our Pillars of Progress
                    </h3>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                    </div>
                    <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                        The Forward Kenya Party is built on strong foundations that drive our mission forward.
                        Explore how we're working to create a better future for all Kenyans through these key focus areas.
                    </p>
                </div>
                <div class="relative">
                    <!-- Services Grid -->
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="service in services.data"
                            :key="service.id"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
                        >
                            <div class="p-6 flex flex-col h-full">
                                <div class="flex-1">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300"
                                        >
                                            <img
                                                v-if="service.logo_path"
                                                :src="service.logo_path"
                                                :alt="service.name"
                                                class="h-8 w-auto"
                                            />
                                            <i
                                                v-else
                                                class="fas fa-building text-2xl"
                                            ></i>
                                        </div>
                                        <h3
                                            class="ml-4 text-xl font-semibold text-gray-900 dark:text-white"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'frontend.show.service',
                                                        { id: service.id }
                                                    )
                                                "
                                                class="hover:text-blue-600 dark:hover:text-blue-400"
                                            >
                                                {{ service.name }}
                                            </Link>
                                        </h3>
                                    </div>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3"
                                    >
                                        {{
                                            service.short_description ||
                                            service.description ||
                                            "No description available"
                                        }}
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <Link
                                        :href="
                                            route('frontend.show.service', {
                                                id: service.id,
                                            })
                                        "
                                        class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-medium"
                                    >
                                        Learn more
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </Link>
                                </div>
                                <div class="mt-2">
                                    <div
                                        class="text-sm text-gray-400 italic text-right space-x-1"
                                    >
                                        <template
                                            v-if="service.departments?.length"
                                        >
                                            <span
                                                v-for="department in service.departments"
                                                :key="department.id"
                                                class="inline-block"
                                            >
                                                #{{ department.name }}
                                            </span>
                                        </template>
                                        <span v-else>#General</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Pagination with Dots Navigation -->
                    <div class="mt-8">
                        <!-- Show text above pagination on mobile, inline on desktop -->
                        <div
                            class="text-center mb-4 px-4 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Showing {{ services.from }} to {{ services.to }} of
                            {{ services.total }} services
                        </div>

                        <!-- Navigation with Dots in Between -->
                        <div class="flex items-center justify-center space-x-4">
                            <!-- Previous Button -->
                            <button
                                @click="
                                    goToPage(
                                        services.current_page - 1,
                                        'services'
                                    )
                                "
                                :disabled="!services.prev_page_url"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                aria-label="Previous page"
                            >
                                <i class="fas fa-chevron-left mr-1 text-xs"></i>
                                <span>Prev</span>
                            </button>

                            <!-- Dots Navigation -->
                            <div class="flex items-center space-x-2">
                                <button
                                    v-for="page in services.last_page"
                                    :key="page"
                                    @click="goToPage(page, 'services')"
                                    class="h-2.5 w-2.5 rounded-full transition-all"
                                    :class="{
                                        'bg-green-500 w-6':
                                            services.current_page === page,
                                        'bg-gray-300 dark:bg-gray-600':
                                            services.current_page !== page,
                                    }"
                                    :aria-label="`Go to page ${page}`"
                                ></button>
                            </div>

                            <!-- Next Button -->
                            <button
                                @click="
                                    goToPage(
                                        services.current_page + 1,
                                        'services'
                                    )
                                "
                                :disabled="!services.next_page_url"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                aria-label="Next page"
                            >
                                <span>Next</span>
                                <i
                                    class="fas fa-chevron-right ml-1 text-xs"
                                ></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Departments Section -->
        <section
            id="departments"
            ref="departmentsSection"
            class="py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900"
        >
            <div class="container mx-auto px-4 py-8 relative z-10">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                        Our Leadership Structure
                    </h3>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                    </div>
                    <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                        Meet the dedicated teams driving Forward Kenya Party's vision across key sectors.
                        Our leadership structure ensures accountability, transparency, and effective governance at all levels.
                    </p>
                </div>

                <div class="relative">
                    <!-- Departments Grid -->
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="department in departments.data"
                            :key="department.id"
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
                        >
                            <div class="p-6 flex flex-col h-full">
                                <div class="flex-1">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300"
                                        >
                                            <img
                                                v-if="department.logo_path"
                                                :src="department.logo_path"
                                                :alt="department.name"
                                                class="h-8 w-auto"
                                            />
                                            <i
                                                v-else
                                                class="fas fa-building text-2xl"
                                            ></i>
                                        </div>
                                        <h3
                                            class="ml-4 text-xl font-semibold text-gray-900 dark:text-white"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'frontend.show.department',
                                                        { id: department.id }
                                                    )
                                                "
                                                class="hover:text-blue-600 dark:hover:text-blue-400"
                                            >
                                                {{ department.name }}
                                            </Link>
                                        </h3>
                                    </div>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3"
                                    >
                                        {{
                                            department.short_description ||
                                            department.description ||
                                            "No description available"
                                        }}
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <Link
                                        :href="
                                            route('frontend.show.department', {
                                                id: department.id,
                                            })
                                        "
                                        class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 font-medium"
                                    >
                                        Learn more
                                        <i class="fas fa-arrow-right ml-1"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compact Pagination with Dots Navigation -->
                    <div class="mt-8">
                        <!-- Show text above pagination on mobile, inline on desktop -->
                        <div
                            class="text-center mb-4 px-4 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Showing {{ departments.from }} to
                            {{ departments.to }} of
                            {{ departments.total }} departments
                        </div>

                        <!-- Navigation with Dots in Between -->
                        <div class="flex items-center justify-center space-x-4">
                            <!-- Previous Button -->
                            <button
                                @click="
                                    goToPage(
                                        departments.current_page - 1,
                                        'departments'
                                    )
                                "
                                :disabled="!departments.prev_page_url"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                aria-label="Previous page"
                            >
                                <i class="fas fa-chevron-left mr-1 text-xs"></i>
                                <span>Prev</span>
                            </button>

                            <!-- Dots Navigation -->
                            <div class="flex items-center space-x-2">
                                <button
                                    v-for="page in departments.last_page"
                                    :key="page"
                                    @click="goToPage(page, 'departments')"
                                    class="h-2.5 w-2.5 rounded-full transition-all"
                                    :class="{
                                        'bg-green-500 w-6':
                                            departments.current_page === page,
                                        'bg-gray-300 dark:bg-gray-600':
                                            departments.current_page !== page,
                                    }"
                                    :aria-label="`Go to page ${page}`"
                                ></button>
                            </div>

                            <!-- Next Button -->
                            <button
                                @click="
                                    goToPage(
                                        departments.current_page + 1,
                                        'departments'
                                    )
                                "
                                :disabled="!departments.next_page_url"
                                class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-green-600 dark:hover:text-green-400 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                aria-label="Next page"
                            >
                                <span>Next</span>
                                <i
                                    class="fas fa-chevron-right ml-1 text-xs"
                                ></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partners Section -->
        <section
            class="py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900"
        >
            <div class="container mx-auto px-4 py-8 relative z-10">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                        Our Strategic Alliances
                    </h3>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-quote-left text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                    </div>
                    <p class="mb-8 lg:mb-16 font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                        We collaborate with organizations and institutions that share our vision for a united, prosperous Kenya.
                        Together, we're building bridges and creating opportunities for all Kenyans.
                    </p>
                </div>

                <div class="relative">
                    <!-- Navigation Arrows -->
                    <button
                        @click="scrollPartners('left')"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -ml-4 bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg z-10 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Previous partner"
                    >
                        <i
                            class="fas fa-chevron-left text-gray-700 dark:text-gray-200"
                        ></i>
                    </button>

                    <!-- Partners Carousel -->
                    <div
                        ref="partnersContainer"
                        class="flex space-x-8 overflow-x-hidden scrollbar-hide"
                        @mouseenter="isHovering = true"
                        @mouseleave="isHovering = false"
                    >
                        <!-- Partner Items -->
                        <div
                            v-for="(partner, index) in partners"
                            :key="index"
                            class="flex-shrink-0 w-48 h-32 flex items-center justify-center bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1"
                        >
                            <a
                                :href="partner.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full h-full flex items-center justify-center p-2"
                                :title="partner.name"
                            >
                                <img
                                    :src="partner.logo"
                                    :alt="partner.name"
                                    class="h-16 w-full object-contain object-center grayscale hover:grayscale-0 transition-all duration-300"
                                    loading="lazy"
                                    :style="{
                                        'max-width': '100%',
                                        'max-height': '64px',
                                        'object-fit': 'contain',
                                        'object-position': 'center',
                                        'mix-blend-mode': 'multiply',
                                    }"
                                />
                            </a>
                        </div>
                    </div>

                    <button
                        @click="scrollPartners('right')"
                        class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 bg-white dark:bg-800 p-2 rounded-full shadow-lg z-10 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Next partner"
                    >
                        <i
                            class="fas fa-chevron-right text-gray-700 dark:text-gray-200"
                        ></i>
                    </button>
                </div>

                <!-- Dots Navigation -->
                <div class="flex justify-center mt-8 space-x-2">
                    <button
                        v-for="(_, index) in Math.ceil(partners.length / 4)"
                        :key="index"
                        @click="goToPartnerSlide(index)"
                        class="w-3 h-3 rounded-full transition-all"
                        :class="{
                            'bg-green-500 w-6': currentPartnerSlide === index,
                            'bg-gray-300 dark:bg-gray-600':
                                currentPartnerSlide !== index,
                        }"
                        :aria-label="`Go to slide ${index + 1}`"
                    ></button>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ref, reactive, onMounted, onUnmounted, watch, nextTick } from "vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";

// Page props
const props = defineProps({
    title: String,
    logoUrl: String,
    heroSlides: Array,
    stats: Object,
    initialServices: Object,
    initialDepartments: Object,
});

// Refs
const counterSection = ref(null);
const activeSlide = ref(0);
const partnersContainer = ref(null);
const currentPartnerSlide = ref(0);
const autoSlideInterval = ref(null);
const isHovering = ref(false);
const partners = [
    {
        name: "Vision 2030",
        logo: "https://vision2030.go.ke/wp-content/uploads/2020/02/v2030logo.jpg",
        url: "https://vision2030.go.ke/",
    },
    {
        name: "Pariament of Kenya: The National Assembly",
        logo: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAX0AAAGbCAYAAAAoZEDOAAAgAElEQVR4XuxdBYAV5RY+290BS3d3SDcoCCYqTwUTAxQL7AILRFCfBYqBEvazABEBAenu7lhguzveOTPzz/1n7tS9GyzszHsru/fO/DPzz8x3znznO+d4lOIC9mLPgD0D9gzYM1AtZsDDBv1qcZ3tk7RnwJ4BewaEGbBB374R7BmwZ8CegWo0AzboV6OLbZ+qPQP2DNgzYIO+fQ/YM2DPgD0D1WgGbNCvRhfbPlV7BuwZsGfABn37HrBnwJ4Bewaq0QzYoF+NLrZ9qvYM2DNgz4AN+vY9YM+APQP2DFSjGbBBvxpdbPtU7RmwZ8CeARv07XvAngF7BuwZqEYzYIN+NbrY9qnaM2DPgD0DNujb94A9A/YM2DNQjWbABv1qdLHtU7VnwJ4BewZs0LfvAXsG7BmwZ6AazYAN+tXoYtunas+APQP2DNigb98D9gzYM2DPQDWaARv0q9HFtk/VngF7BuwZsEHfvgfsGbBnwJ6BajQDNuhXo4ttn6o9A/YM2DNgg759D9gzYM+APQPVaAZs0K9GF9s+VXsG7BmwZ8AGffsesGfAngF7BqrRDNigX40utn2q9gzYM2DPgA369j1gz4A9A/YMVKMZsEG/Gl1s+1TtGbBnwJ4BG/Tte8CeAXsG7BmoRjNgg341utj2qdozYM+APQM26Nv3gD0D9gzYM1CNZsAG/Wp0se1TtWfAngF7BmzQt+8BewbsGbBnoBrNgA361ehiV7dTzc9Lg/yMg2U6bb/QFuDnH16mMeyN7RmoSjNgg35Vuhr2sRjOQEbCRuH7orwLUJx3Xvg9P+cYlBZlQXFROhTmHajwGfT0DAHfwNbCfjy8g8EvsLHwu09wU/DEv30C4iAgpH6FH4e9A3sG3J0BG/TdnTl7uwqbAQL3gqwjCO4XoTDnKBTk7IOSkky39rf9nK9b27GNOtUucHt7/+DusmEgo+DtVxOCo9q4PZ69oT0D5TEDNuiXxyzaY7g1AwTuzGvPy9gFmVlHwQeSdMc6nOADWYUewvd7EryEfxNyPCAhT9zkz9QSp2293Toyx0ZFqu3b+ntAnQDxGGL98SewVPi9bWyx8G+TyEIIDRA/01u8fWqjAagL/qHtwcs/DnyDmtjGoIzXyd7c+gzYoG99ruw13ZyBrOS9UJR/AQrReyc6Jj/nNJQUHNYdjcD9QrYnnEjDnwyAQ+jk78kTgdQIxEtFLK7wxcMA05mRqOftAa1DPKBRSCnEBAI0iiiGGsHFUDtMNA5aC70Z+AQ2Af/wDuAX0tSmiSr8SlbPHdigXz2ve4WedW7mKchN3QH56TshJ3U5UjNZmvs7l+4FF7O8BK+dPPZjmaWwNqtUE9i1Ad0c5b3B2Os2m4giMN8H6OxDbRyYQRgW4QlB3qXQMBSgYXgJdKhZoPl2wOIH9EbgF94RQmO7mx2u/b09A6YzYIO+6RTZK1iZgbT45ZCXthOy0zY7efEZuR5wNMUHjqd6wYl0kY4hKkbttTsDuzPgGoF4qYcVgLZyNsbr4EOju4K2kVCur2UMiDZqHoJGAA0BUUVasQQPj2DwD+su0EL+YR1tSqjsl7JajmCDfrW87GU/aaJs8tJ3QGbKNijKWuU0IAVQN53zgl0pIHjvtPAgrwR4JVhrAXtZAL0svL6a07c6c1qGwdkgOIwBbwjYPnsHe0BjpIjaxJRAuxqFTtQQvQkERgwCv7AOEBDR0aaDrF6car6eDfrV/Aawevqkec9OWCVQNhkp68GzNEGxKVE1G874wt5kgM2pAKeLHDSNVYC3AuyXC6dvNK9qg6A0BvqGgN4G+sYCtEYjoEUJ+fi3hNBaoyAotr+dW2D1xq6G69mgXw0vutVTJm4+6/wSyE5Z46SBJ8pm5wX05uO9YHtqqRBoZYCsB/K8B28E8FrAXpYgrajzcX3RD7maj6UV7DV6a+ANgZkR4N8E2kcCdKvtTAcFhA+GwMjetgEwv1TVbg0b9KvdJTc+YebRZ8R/7wT0pKrZcM4b9qZ4OHHyDlB2UDVWQN6c13c+Xj0QL4thcOU2MFLvWDEUWlSO1v6ZIdAyAuox2FvAoAZF0Cy2UDGcbQBcubpX/ro26F/519jSGZJmPvviUkhP+gu8PHLlbdac8IOVpz0tUTZmIK/P6ZsDe1kAvap4+kYGwYohsGIE6C1AzwDkF/lCVI1rISCqN4TXGmzpvrBXuvJmwAb9K++aWj4jom+yE9dA8pl5Co6e+PnfD/nCH/F63LyzN69F11gBeTUgWwF3IxAvm0DT2tTpaYRc9fJpb3rbmBkBbTpIPHu2LTMAQ2sDDG5UoAgEl0AghEZdbRsAa5f8ilrLBv0r6nJaOxmSV2YlLIXctOXyBsTRrzvjB78d91Bo5dW0jZE37yrIGwG8kzHQOTUrIk1PKytpjF9iwYLoraLepZlB4EFea12rRsBBBTkbAFIDDa4D0KOu0gCQFDQ45kYIrjHUloFae4Qu67Vs0L+sL5/1gyevPv3Mj5Ce8At4Qra8IUkrV5z0hq/PiyUMlMFYc49eO3grDs8Dtx7Am4G7Fl67C+LWZ8u9NbWMhNooWDUGVo2AVnDYmQbSNwBXN85TJIZRVnBYvXvsRDD3boHLYisb9C+Ly+TeQeoFZYm+WX7cF5aeE8sbOAO3CE3Mq1dTN1aA3grI84CoBsPyBnYvS5m1ynkuLmM2Lz+a2iDonbsZ3WP0FmBuAJzBn47xlhgP6BZXCsOaSUWM8DMm/4ysd6N7N5+9VZWdARv0q+ylcf/ACOwzTn8LGRcWKEog/HnYHzad94CfEh3JUnr0jVWgN/Pm2fflCfDuALj7s2l9S3eMBG8MtObIVSPA8/n8kVvx/ikAPLIBAO/9F0I01Gz0qC39tH4bVPk1bdCv8pfI+gEysE+Jny9TOFaDsuTVuwr0Wt68wghIh8578VY9eKvAbiXwS4fhaX0ahTWd63VqD2Ak3+S3sGoQ3DEC7BjUBsKaAXD2/qlY3ChsCXBTCwf1U1jsDzF174bQerfbiV8u3ktVbXUb9KvaFXHjeBxgjyocyBFGILBfuNfPkKvXom/0qBvZY9cg2dXevKsgbwbwamA3AnA3Y7aWZ90otqtlKIw1/eaRYi0jwJ+jHtBbMQBa3r/aUNwd5wl3tMmXlT8E/hGxQyG8wb122QfLd03VWtEG/ap1PVw6Gj3P3grYW/Hq3QF6M0/eCODNwF0P0K14+5Xp6WtBOW8QymIImBFg+3DVABh5/1rKHxYnIPC/oWmhIvErOOpGG/xdemKrxso26FeN6+DSUVgFey2+Xgvs1aCpB/Z6Hr0R0OuBPL9PNSCrwV0L1F0FcZcm2M2V1Z6+FrjzBsGKITCihdQGgA6bzZ2Wp++69++gfviy0Lc3L4JOdRwdxWzFj5s3zCXazAb9SzTx7uzWHbC3SuFUNNDrgTwP8Gaevu6cWeR0XDEUljh9c3ZGMz6gNgZahkDvbUDPCOi9AVihf7S8fyX14wz+pPm/oSEqfpo7FD8E/hENH7W1/u483JW4jQ36lTjZ7u6KgX36+QVQWio2JNHi7HnPvixg76pHr+XNa4G8HsCbgrEGqJtu4+5ku7mdppEwMQp6nr5VI1AeBkDLKPDSTzPwJ8XPXU1LoVe9fFnvHxI7GiIajbUDvm7eSxW9mQ36FT3DZRw/6fg8SD/7qSy9dBfstSgcXVqHown0qBsjoGeA7DLIq8C9vIGdP5e6fgA9a5bA96ec92IlE9eVy+pkEHSMgZkRMKKDXDEAVr1/V8F/XKsS6NsoX3q7CYSoeuMgutEYV6bKXrcSZsAG/UqYZHd2QU1Kko68KVe6NFPj6Hn2PLBrUTiuePVqoDfy5o04e2E+ygjwamMUjifSLaYUwv1KoWlkEQT7lkJcaAHkF3nAg/8EO12C17rkQ6+G6cLnhcWekJTlK/yej78XFosH5+NVCn5eDqgtwO98pb+Pp/jBK1vRckiLO4ZCYQw0DIEWyFt5CzAyAOoAMG8AtKSf9Jkr4E+tIB9oywV8fZpAjaaT7Axfd0CggraxQb+CJtbdYYnKST3+OWQmzBeGoJo48/f4wyenHQlV9LkIqo7MWT5AS7JLV8Ce4a9Vr56Nrfbo1Z8r5oADeasevJkSiI1PM/NxnxyoG54HIf5Fch7tvgvB8MqGQEiVUIvf74d9c6BljSxhBuPT/eVD9fMugcggR5CSHbYak3edC4Gn1gdYusxWDYJVI8DA2V0DwPP/WoFfd8BfHRcYX88DRrd16PxJ6RPZ9Amb8rF0x1TsSjboV+z8ujQ6FUK7eGS6XPFyzXE/mLbbU+hCRYsDzCse7PW8eneA3grIawH8zyNS4VSKP7y2OUAGbjahemNO654HXepmCKvN2RwD32NZaK3ljxuSYPvZYHh1mwPwab1R9UrggasS5U34Ma6pUQKd44qgW91MWHcqBKbv8pHX09ToG1x9K4bAyAiw79QGQP05OwQt718r+OuO589LPXnwpySvCS0dwV6W4BXTbJxLz4W9cvnOgA365Tufbo1GxdCSj30oV70kKud9pA6oebgS7OkvD6EmjjuevfrVXgtoebB32aO36M0befC89/r79SkQ7FcEKdm+MH19OGxNo9M3luoMrVEKk/qIrRznbEHQ5zh7Hv5f7ZwHU1SAT9vcWh9BvysH+qoxaJ0JLQshBGmkt3aKlJBi0WiarqBpymAI5HFUrx3lZQD4+6M8wF9X5ulVE2o0n2xTPm6hRdk3skG/7HNYphEoUJtw4mPkj0Xp29c7AhCotOrYi2BPCwN8dfasHmfPP8xmQC+O7yhbINMb0i9OfrPe59ys8PvsGgZwXdN8aBBRAGP+DpHXch7XA74clAH1IsSGLsS7z90WiZ67eJZGbw/LbrkorPPHgUj4cJ/DG7dyobRA/0eNYG+4dymkYbyALdYkng605tdvjSGHu1rn47nmQWRgoRA3oPgBxRmyC7xg2zl/mH1YWY+0PAyAnvdfPuDvLPO8O84DHurkoHyoo1dU4wl2Zq+VG7Mc17FBvxwn05WhKFB7/uBkRLOjwmbbz/rCZ3u9hFr2tBhROVqlEggS1Dy+K2Bv5NWXBegpwHoDes/ta+ZDnfB8iJL4coLLQT9il29adLz3LwZmQv1IsawEW7acCYUXNzlz6fy5f4XGok54LuyKD4FJyOnzixnVdAvROypP/wcdishq3R09g1AX3xYmdc2BNjWz5DhEVr43HEgIBF/PUqgThvMVLMYXsvHzjadD4JcjPrBPVO3KixUDwO4FLfqnrOCv5vPVMk8+GEyUz31NsLJnG0d3tvBaD9k1fVwBjzKua4N+GSfQ1c2tBGrVenszKqe8wN6KV28Emsyjv6F2KfTAjM3Y4EKoGZIHfj4i1JC3TkHTrAJPAeheWxULq5PEvWqNSxQMU9jw83wyORBe2xgC5xx5QYrLMGeg+IZABuIljAe4sjzdtgAGN0uVN5m5NhaWXTTP/rKSjcsfR20MJfx3UApq28V+tnmFXrDoYDjMPih69Gy8oTVL4bZW2dAgSjR+NIfvrouCpRe0j0nLABjRP8q6/c4SIuL9jTx/NQ2kVvqo+X72fVs/D3i6IzZ0lzJ7vX1qQ0yLt+3ELlduVjfXtUHfzYlzZzMK1CYem4E98i4ImxsFarWoHCdw5557XnpJH5vROHoUjqtePb+fb4ZkCh42LTx8HLwYDIsOB8JSDjxn98uCf04Ggpo64c9xRo8caF8rE3IREAN8lOryjFwfmLcrHH4/Jx4xv92bXXOhKwZzT6cGwNh/QhWXyszTn0Sg39QB+i+tjIXNac4A666XT9sRNTR7SJqsEiLA/2lvBMw9ptEIUooRvNcrF9riXNBCR/Pf9THw2znxuLSCwkbev1bwt3LAv1TRypGO/ZZoD3iqm4Pyiag30db2uwMuLmxjg74Lk+XuquTdJx58Qxmo3eILf6apqRzxkTYK1Kp5+7KCva7MkuP11eetNigM4Bui93oKve/JXQqgd0OKuorLX4ciYOYeZdCTgI+WVEkTT7+rAXlCq0IY0TIFLmT4w7+nguD6FqnyWwOtn1/oCRtOh8G0Xcqx6Q2hR/10OJuGoL9SCfpm11AN+q/8g6Cfauzpayp3DLJxn2ufDwOaOOZnP0pLn1gbpDg09ZgRmDPwEdJWNULF1xvy+J/6Kxr2O5qgaYK/YBTYyNwx8d4/T/2UF/hrUT56Xn89vImfa18qJ3YR1x/T4iVb3ml2s7r5vQ36bk6c1c0E7n7/JNm75wO1NAbP3VtR5WhROXo6e7USh5dbaoK9QVBWD+hloOZ4+VZBpfDBMFFBQ8vRxCAYv1pMkDLU8nPf07q31SuGsV2SBPXOHX9FQJ+oUnjkqlQIlygRNv7JlEB4YW0IBlbFT0YiL39/l0Rhu9G4nWIxYWqeaqP09An0t2qAvqXALQ+40kEQGH49JB1qSuBNHxOFxL8Fab1F0P5G4Xw8cFWSYl4fXCUaC/VpUcbxnOFJcDI5ABbsDUIarWLAX63zp2O3Rvk4e/2k7R9/lfimSI3b41q9ayt8lHdvufxlg365TKP2IKTMST09U/iSZJivr/eFtdnm3r2WKscV3r48wf72BiVwLN1TkEsyj1AL6OkceU99/jVpEBsipuRTcHLkoiinSQrHE03lEELt6RPIv9hPNB53/F5DAPU6+Dbxaq8MrO/uCATS90T3vL0hAnake0BLtC8zh4gKnmt/qeHSFX4dqaHOdUSdPy2T/q4B+1WBU6cBNbx6PaNwV6NiuKODA7gTMv1gzLJwpyG1Aq70djQPgZyPkQz7JQYvjHgA8jbSaBRE/y8Gw4lyO5cegDGOAPjmsLeQ8yCsW0bPX53kpQZ7PfCnQK+e1987COmezkVyCWcK8tq6fpduYdOVbdA3nSLXVyA6J+XI+5CV/KuwscDd7/KA09JToPbuhedP8pS1smkZ4JtROe6AvRbHzXv1PwxLA2+kFu5bGgHp5EmrlDZa29PxvoUeG/HqbGEBUXYufRHQx3dJg5XHQ+DzI8zMiWvzYy65WQTvjzfFwOJ4xzdqcKZ1iPL4aW8kzD/uBb9en4gy2BIBtA+oQVvP20cQnNo9B9rFidw5LcN1jIYBe6O8YVQrvixRT2yl3agwemZjoAOwTQzIrL5Z0CTGwel8uIG4fW7GOANAp8mXm6B9kgE+leoPP+4PdHj/5QD+WsFedWavVkkHEfxFr58FedVJXYClHOq0m2lLO12HIs0tbNAvp4lkw6ilmJ+geoSVUKB19Ogcq969VpBWL6FKeJy0KBsdvp4He/YQL7g6A+LC8oDKDkzk5I9qsFfvh6iZ+7s6PNoNp8Jgf6IvSjcLoB6Ox94CCPSeQ9ATFg0w/uHaZCFBa/HBSPjkgFJzT/LKW9ukCt/zy7azoagaKhTeBr7cFgM/nzEL3zq2ntpNCfoP/1kDzogvLPqLBlDrGYVpNL4UkKUBVx8Ph6k7HTV82E70MmuZMomt9xGCPgtmKzN4xSNYMDQdakhvXBQQLynxgCBpvnjvP5mmsJzA3xXKx+H1izvnwZ8P8haXBkDN5q9DeK3B5fzEVr/hbNAvx2uecvpXuHBkmpBoRTVzXl/nrxGsFR8Jnr838+55L0rNrTPA50G3PMBegEn06n+9TgRdWmasjYFlFxwAqsfPs88X3yB621rLxUx/CERFTkqOD4xb7UjSEnbLbTCrvyi/JCB/Zauz/LI5UtrPdk+TgY1tKvqPAP/bFwVfqd4k1I1k5G3QS34COf1BTRzqncmrasA2R8xVPjKrnD4PpHQ8z7YvgH6NHOMv2BkNC/DNRA32/JzxBkAN+i+tcA40y3JPLjuZxiOp6/0rg+HBZsXQu0GOTJFRQHwLzu/LKC4wo33Uxkit8dejfNz1+knaOaWbg+6xyzaXHbBs0C/7HAojXNg/Qy6SRolWL23Bmjk6dI6e7l4vwcqKd09QzOvsFb6tRc9ei6tfgdmtzOhQYHQU0jy0mHn6tM7HKMtsHOWgInafD4Et8f6wHGkaooqaI/f+et9kGLVE4vs1PP1Z/UTQP5YcBE/8q6yWyc/jlM45Ci6eXVYyFq9uUyZoGV1y9TiTV9fAeIZx9JclIxkBN/tO/SYhgP4JlVRTw+Nm288drAwCP7C4BpxGQY/Wm8H7vbOhFeZDsGXO5mihLAWtS2d0DeYA3N8xXUgAe3tNjEL7Xx7gT6dh5PXrK3yUdA8d/+QWHo6ELqR74lpMtjX9bmKXDfpuThzbjOrmnN09Uc6sJXXOO8ccT60RnaOnuxfAX9qBGvDNePvyAns2zt8c6NMh8QlPep4+2/YZ9Gr7N3Z4tQLVwme34smRykRNn/Dn8EJHUX5JVMRDq7CGg8EyIq4YxrRPlekLWpUknVN3KIuqCUOos4AlLnx6j2xoEesAyikI+tqevokMiHahqsNDQNojshRe6utQNpEhfJ7RW9x1V5ymdDN0CiuFNwY5tqUs3ZsWR8nad97oROCNt2CE402LDPZtf0pKJo73/xCrk4b5F8PdWBJDV++vMkK8zl8wINz3vOfvjtdvRPcMC/eAl3uJmn6ie6Lrj7c1/W7glw36bkwa24SSrS4cehm8PHIFOufdTf7wU5JanWNM57jj3ZtSOTrSy3sbY5XI2nnwzuZAOI08tZZnzwMu7efvkWIglS0ULH11VTRsR5UMvzjGcnzaE4O1TgC3ydnrVu5TOe5bV2XLgdUx6NUKwWR+UYF3bTQiL3VPl6kLekN4cp2SPtK65GEIkl0jUerZMUVhNDai0Xhrp0lWrxO4GxuE+Vcny7JTornuW6EyZhKIquMCb6CyqBOnLGJBYDoftaf/aMsiuK5Vsnyq606ECQXm1Lz/pLZFsPG8N/zrCL+4BP7sGHnw16J8mNevp/CxHOTFB+aNriVyJi+VbI5rO7kMT3H129QGfTeveeLhWZAW/6mw9eEEH3h1kzfsyXcGfLNEK16Z44p3r0nl6IA9iwO82LEQBiJfnYbyxv9ujIB/kx3gpAZ7Oq8G6CDPGX5RSIJiMkH6nLzu+1eISU9aYE+fs5HnX5MKEYFi/RgGcGwbnp55pl0eTN8teuSeHBH+dLt86CMlei3cHQ3fnVQqfRT4z/3xfAeUXtbOhEQsWjZutbMkUn3ZJ7TKh+5YMjm7wDE+a5oSHZQP6zEQPQP7GtBiXbmjXLNEmhXekNF4s7GS52JU4GjGCaQhSN76bn8KyorJWTS/L2K28Haknvjt2O+fD3AUq6P1H1+qVDFpBX0Z7cPmRu35q5O81Ale7nr95tJOJd1DBmJcfYem38e/JcR1mGUnc6lvap2/bdC3OFFsNZJjJux9DvKyNgof/XnIH57Z7VDJlIXOMeLutbx7MypHrcbpF40a9wEiPUBA/iPKG7/hUv/50gy0TtdwkU44mhQE3lgArAFX/GzZ4Qh4f58yE1Y2IZyjO7Onki6ZtLwGHMxWvyWUwpyB6bBwbyj8k6iMFhDH3rG2KP3MzPfBgGMwJGZ7wUID8GfXKgzFPg0DS2FnhjkVY3gbWEZ5cRTD1aW3gi8GpUFMsCgLIiP8xrpIeV4YLcQD87No/Po2ckSUiRaSVU/SDtn6fH4DOy+idw4lBsB3B/0VEla3wV+D8qkIr5+ne/i3Afp9aISD7iFZZ73On9vAbwHPbNC3MElsFZJjXjz4DJQUxgt0zqfb/eGbeKptL65hBfCt0jla3L2mKkcjSKsGe57G+WFYqqIz1MqjETAVyxhoefr9pOQoKoPw9kYsdzAgUfb4Se/98pooOMzobxWusvEea10AQ7haNn8fiYAP93PGAumZm+sWw53tkuHHfZGyJ98Cs3qf654KYZh9ewEpEAKUi+i1U9nhdWdRFXXeuU6NdWGmCxddY1Vd5Y6JceC/Jhpqev9krMsvFly7iElaX+0Kg7XJyrMg0HsEy1H0x9aO/lL9IXrTelAqL+G0S/xgZq9soSsYW2jscP9C4dqRsT+D22dj0bvDSb4wB5O1aCkP8O8fXQqv9E+CeTuiYM5RT0Oun3+rZZ4+n82rBHiWzOWcxUvJXNP7izy/h0cw1Gwz2w7wmtzeNuhbfP4J8C/sfRiTH7MEOufdbd5ydq07gM+CtQwreaA2kmFa9e7ZQ8Wkl+w0P1ApOuhzkvI9i2UMWHYs28etUjkDomXuXR4GL2MtdAqqsoXeAB5f61DUaNE2FHx8rb8jLpCa4wu/HQpD8C6FRuGF0DgyT/Z4iT+fyvHnzYNL4RD3VqAF6i464RavtvXVtN4hnIyCzkHWQuAfiyWGu9ROF4wa6ehPYZG47Rf8ISPPA+qElkCL6DxoEu1QQKnnXDhSLgZANNCnQ1KwbaRoTCjYOxKDvfT5Q0hhta6RK+dIfLwxBn5FWokPxLoK/mrK5+Y6aKR6JMOJpAB4ek0QJGIMxiHXVNFd+Kea69cDfjoXPa+fz+KlAG+ddnNs4De4hW3Qt/B8E+Cf3f0gBmxzBMB/Yp1XmeSYPOAb6e6ZDNNJJWPVu+eCnAwwH8WuT9e3TnE6a3r9f21tBFIM4le0PqthcwCrZE5cHyTwyPOuVr4pEM/+LerM1dp3npf/eoiD19eabjIqidk+MGt3EJyVEqEUbx4WrpFwzGVkcSzuRrewmXp7/nCUYKpcsxm+1fynRR40R3BnORHCW520Gv1+Oi0Q/j0dCAtPiJ65FgVEn1NpaF4xRZLVlzFBkO2fwP+DAWmQg81ZHlrlCHCr+Xg1+Mv8vXRMmny/dMC/jBAriNI99eOeUPjmpLnXT+eo5fGL5+rI1tUDfira9n6vYqF8Q36RL9Rq9gJE1rvR6iWtVuvZoG9yuTMSNkL8vicFhY5VwOeTrbSqYjLvRsu7p8Nh3Dqjc4y8eyMqh4E3O0UatzV6z+9fI/L6pMThk6eIW/5yewQsTxDhijJe7+ucCIKsUFLdDK9VgkWxHO0EiWcftzxS6CIlA71KUfPFwFTZm6dxydu/iIBwINEPlvaXDagAACAASURBVJ/1gXgu49XMc68sYC8LChj1v9U0BNxJ1/EvhS7RyneFX85Kd5EstVRaNwJCtsVXGCtg2c50Dm+siYX1LGCP+6H1FqDh/vVgmNw/WO3pG3n+RsFe+u6+RiVwNxbJ45fDCUHw/uYg2IWMk5bXz2r2s7Nyle6RyzeoqnVGNZpsA7/GjWyDvsHTTRm2yccnC2tYTbhSA76Vujk8naObZGXg3WtROVocPfuMVXkkyR+16OOrVpIh+P1ABHyJWax3NESuHYuDrcFSAW/v9pN5/4/6YUerCEdHK6IcnlqHNI8K7OsigL3RO1WohX8SyxwfS/GF1ed8BMpGpoJ05t8dcDdpn1sWHFdsq9EG13RsPUNgZgQEJ0BrdAHoleA/HPMUxnEGWY/7pyzmQwjAzFCo5Z60O13PX1XbR1hXOkDKDfgKY0YUh2ELefunU/2wXlAuDPslUjGuFWmnVZ6fAT+tP70dwLDmosqJMnhrtsIqt/Yiz4AN+jo3Aw/47ih0+ICtO3SOy969BpWjpoXY3zOoqBjWfyHa5rNdQTClj6ODE5sOqpWTg0E/KklAv7+FCU6MwqmDbf5mDHCUZ6BtCPjTkT9uiBUd710RKQxD2vfOUSWwMsFTGSjWmHOrIF9ZwO4uRlg1CGZGQM2Vs+NxDtyWAoH4eXxberu3svroqmMR8A7rY8Dx/vK5SZ4/+1sN/np/C+tLbxe82ZnRM09WWtEqFOx/ZGkknJQyhgmQ6TDMpJ1WeX4jZc+kJh5wd0exEqut5VfezTboazzdlQH4RuocXvvOg39XlJsPrFcEc/ZjeVx8B1YnV6m9e72/R6MHP7pjkvBQ3oYlEIjnfaevc7niIvT6vVEtQ/TOC/h6TotA4SDyTmqbB325RimkCjmBvPO3+4JgZ6YIBUbevBnIuwvu7m7nCshbBXZ+TLNttIwAA1QzAzC9e5Yii5jtl+SxvEfPg73CeGiAv6uUz43YIvOxng7aj/b19dZo+PI43gXSzhjY0589MTl4LSZrm9E97lI9NO5dtbAlI3Yco8U/uDvEtplmSzpxLmzQVz3t5/dMlksiV5SH7w6dQyA5ChuMP4hNNIgTn78zDH7jSg07lDPKujhq7T2tR/r1uUNF+eW762NhBbUxxP+/1kVZS55NDQUDp2zDrFQOUcnbn3XNRUjI8kNuPgA+R/03Zcu6C/RWwdrqeo7LWh7RXbNIg2NvZuDO1jRbT20EzAxAKBruB1rmQaMIakCfK0gz31wXI2dOOwV+LXr+VsA/HJVYn2L7R9bEnc7xCPL4rMGLYLS4KaS//7guVegLPOXfUNgjVbJWl3AoD49/ZIyjHaOdxCXefTboc6DPAz6VRJ51yn0Nvh6lowX4amAmAOa9dOYVM9Bnh0zljt/cEiAEUYXnittOTe3wbw+07myN6pW0zr1NMX0f2xLyAd4VSBP8d59Y/tcJQvl9qgyokTdvBbyN19EGc7M3CNUhuvynfpDW2DCYgbzR964YADqKgbElMLRxDrSMyRJKN0+XMomlB144Z/Xbg1XPX4vymdw5X9Eek94gH/0rUmidyS/0RnlT/WLMfM5DlY0oE8vK88aG8GHw0QExWG0G/OI5iEofK6oeWqdXsEPLD141sSPXjGot6bRBX7orecB/Zx12GCpD0pXbgG8SrKUetF9c5yi4RYdOipv52CD8D66RhpZ3z6M1weVrUoco6tx0/4pwheSyJ9agmYBtCVni0OJDkfDZIT6hSpw0Pa9eD3iNQFz/OyW4uwrq1n10bfx39T3B2ShoH4ERyFs1AEbeP+21OUpBAxFod0iJvHzgl1f8CGeu9vw5SoYZCS2vn7J/n+nt6OZF636zPRq+PuYJ9fF+vQb7KjSLLoSaWMkzEstx8OU82IzTeZxP9y+z12/E8RPws45clMRVt9uiakv1VHvQVzctLwvglylgq+Pds+eRJVmpM2rZg7MLlThvbQmEdHX7QQ61hF+lv3npJSuNwEsuWeGyOtiIZCr2cN2QgkcgbUvHooay8gN6xwGbAbwWnJptow3t1j81C8BqjaTcxjUjoGcADL1/bhdagV8z8HfF619wtVIiSrTSUUz2iwwoEprvqBcC95RcjEnleMG+RB/Iw7dUP6SH+tTPF+IS9/4RA8elTphmXr+esqdYOAHnej110QAyLX91LttQrUGfAP/8znFQmHdArpL5c6J1SseKQsdd/l4N9uzh+XKQWEiLXouD/ZUlJ8nrX7A7XCjeJSwSfvJgzzx0UuJ8NzRJSAYiSeaMPUjfcO42g16iClZiPRwtr95VoNf25s1BngehigZ16/CvvSYPxkZvCY71nI2AHtBbMQAK7181tPynht5fy/PXA39G8byJb4t8S0z1jJBck0pnpCDAbzvvC6sueAj9edmbg/CLRpCXV/eUN/C38cfyKYPFsg3VleOv1qB/ZuvDQuE0qqPzzCp/WJdV8YCvoF44z5k9MAzU6Flg3j0Davr3zW7ig7bhZBiczfCGG1sr+Xdah/T307BxiFyGWLUfJr2c2SMLmqN3lYjB2KfWRECbsBLYh03Q+e2sgr0ePeP8uTHIlxXgXaVjXAV5d+giM0Ng9CbgigHQ2o+W8kcP/E2DvbghG4/ROnzshwKzrD7QWczLuGc5VmJVSTuNMnl5dU9FAj/P8RPwN+j+rau3wWW9frUFfcbhX0rA1wrWann4vDKH0uyvbpYqaOwfXxcEV2Ftm6ewXSClvfMLef0L96DXLyl8BEpGoeUvRQOSA21ritIJAssUVAXdg9m1fCDZDIS1wN4VoDcbX+vpUgO7UazAHZDm92lkRLQA2cr+rBsB5Wia+9PYoRb1Yxy4FQdhtI8W+Ku9/rmo1uEzfwnkN2CZiFuxcB4tFMy98Q+pI5pBQpfs9Veyx88Df3XT8VdL0OdbG05eFQCVQemoPXwtwJe9ewGFRbhRSzGvr10Cj3RPBKp8eQ8WQWPrTOuhbOjNgIs09tO3B8oKH6azpyDfpKvShOqLh7HRyLx9gXAoRztTVk2pWPPqpePXQE0GIGZUjRm4a3L6FeyDKQskiDtTH6canM0MAQNpLQOjRwO5YwBofLV8UgnmyixfPcrndUkEwKaaePxnV0ZDXUzlmNjbITQYu0jZytHI6+ePqzw9frPg7uxh1S+Bq9qBPp945WrQ1l0OXw/wKW39w4GZcCjJH97Y4aNJ5wiePxfkJdnb9zeIVSvv+L0GgjnAnY2K4WakeajcgbqeDq1Huv5/ToTA3KO4MSI28fStooog1K8YPtkbIAR/rdA4rnj1akC3AvQ86PH7UoOmIlvZDOTLyveYITa3f7VB4HfNg7TRkOYGoGzevx7nrwf+aq9/eBx2F+uUrFDhkCx06k4/CMMHhN2bNC1zsDnMj9iTV/bmhZtZrBMkHwe340sB/HwCV3Xx+KsV6POAT71sZxytfA6fARbJ2WYOTIMiRPTbl4ZpAr5aesn+Zs2xqal27/o5Qh0cAvvd54NhJpYmntpLWRuH4RKBP3GuGRgEfvAfTInUUeNY8eyVBkDbqzcDeisgbwjwOoBeVpw3siOagG2A4rwhcNUI6BkALe/fiuevy/lzx6/F97fEt8LzKMQJQlCfMcBRtpnmiYK1o/+KkLn+hdgpjVGNFHd6BVs0Mqmng2IyBv6GqCl4EXsVP/1vEKRgGR86prJy/EYePw/8EfUmXvF9d6sN6FN55DO7HsAOULlytyvmRZs1PylvD586WD3ZQyxMRfXO716CVSqLtekctadPf3/cLwsaR2XLXj01yZizIww2pYhjEHd/X5NCuLpJulyqlz6n1/DNmF07Yy9rS6iUXpYH2FsFei1PXhfgVSjuMqi7vAEH+y54+k6r6mwrJzjxu5HW1dudsQFwbOUO+Jvx/W9ehYlemEyVV+TpdD+9syFarOIpHcJnAzOEjGBaSEb8zIZABWBbAX7hHu+bg53acuHhP6PgJA5XFuAXJZz69fjVJRuu9Oqc1QL0xXr4Dwjlkdcc94NHtjFwtNbtioFheSRdPdwcqZg2SsUNUzpolVLgvX1RzUOJVXnQpU66kMa++UyIXAFTHahtjoUvn+kmtuWjiov/3Roq8/Y8uLgD9q7QNwxzGdAr9q3lVnMgbYjXl4Gnrw6Aqk9X602AAbeRAVCfumgUjKkftUFQe/66fD9ueBN2N7upZbqiIiuV53gFM8L5PU/BUh5Mxklxp7v/FuNOvKeuoMF06J5uWGdq2tUJwpvEtLXhsIESzPD03PH4rWbu8tU5r2Tgv+JBn7T4xzbeAj6QJNfDP4M8uFzyWHh6PLDVIVE94qOkVR65PAD/iVZFMLR5GjbECICmMY5uSLTPdSfCsL4NVbJ0Dt7S9wzw6ff7sVTCyDbJApA/+E+o8B07dhaoZeDycPMCyMe3iLnHpJgBBw1lBXsjr94loLcC8lbW0TIgwuV109U3q52g2p8ZwDvBsgaqq98CjAyAlvdvhfrRA38tvp8/xDBMonqivaM+0/tYt+lvqfcCATKtOw0VYVTBlRZS8NyMXbv06vcYef3zhmRCHcxHYeN8tDECFqPOv6KAnx0jD/wxzWZAeK3BenfVZfv5FQ36BPint43FbiFHESC94IGVPsADPgN38d/yB3weVNX6+yld8qEX9j3llwU7ouEb7EIlgAMH/jzg01EOqoEyzZ6iSmL0YgrmMmoIHzvpPBQwJ43linev5uzVzVoEQ6SBpXpg70TdSCtqwrErAK8B6B46rckbo1MaiMDFL01DtPQ4WDAs05ls2p2lQ0AZGAczQ+Dg0J0xhDcAZQV/V2gfmn5dygcHuhaDuWPap0IWdt+auDpcTriiab9PckjY2Ty4xKHg4bl9I55/dr9saCrV5mHjFBR7wXc7w7H3LqY74n5Y7R363iyBSzaOuJ2j7r6j766D/hH3xoD/Sm29eEWD/tmdkyA3bblp8pWXBDJ6DVDoUadVtDpdaXa54gCbB0d6wPmEq1nIzfMePwVj314bjc2xGYhLtzwPkNLvv16XKBRFm7U5BpuEU4kEB1LKv/FvDdJQZt59WcBeTd9YBnorIK8Cdx7YQ9FO1kdArxNYAgEI6s3CiiDKvxhiArmaFBXglyVipmlynhecyfaCFOxpeybHE5IKPOB8IXmk2uSMkRHQMwCuev+8IVXTPk5yUtVhGlE+aq//xa7Z2GTdBz7Y5ysbCVL3jO/mKLH88aYYuS6UmuLRAn4tZ+hIYhDUx17Kvl7FsOJwBExGpZsrHr9ZIxYxyCsu7Ji+7lECneoUCM3Wa3daAAEh9SvgDro0Q16xoO9q8pVZx6vyBnwCxHDvUvhwUDrUCHH0C6Skqqf/iYRzVLZEA+wFI4I/nwvb5cEe1OG/uEWsdW/k3VcE2Gt59a4CvS7xIr+xOKCmLbZ6DESAr43gHulXAtH+2Dg8UpmUZvQYefvUBm+/uopVvP1q4mc1FJ8VF2GQPOeo4rOi/DNQVHjO8lN6ELuE5eAb2Fk0CGfRGOQgquzJcj5bPaC3YgDMvP/yAH8zr1+d0BWCD9LCEaKkmBZqwPM60pbMcBkB/0PNiuTkLrb92hPh8PIWXxhKb7e9kwVHZ+fZEJiARRHLD/jVFTsB+Do9V1q5hisS9BMPz4K0+E+F+4Zp8el38sq1lDpGgE8PH3sToDHMSiMbUTpaCVck3Xx3oLJzFXH1D6zEFHYG5NLTy3P3M7CEAhWoIt70LBaxSkOPcyrKNbWyaY0A3x3Pngd7mdfn4VCPujGkdMQveQ+e6Jj24VilET13M3CnJhke3sHgF9gYfIKbgif+HhTTzTJIu7NiduImKCnKgsKsI1CUfxF/LghlPYyWA2gMjqR7w+EMTycj4IoB0PL+td4tyHN3F/wtef2qOj7kNX+DPXgjsKImLccw8W/CGlQUSEFY+kwL+IfUKIHHuiv1/yexcNv9K4Pldoykenu6VwoEYb2oC3jPv7sxFDZgIxaz4K48VxItJNxnEtVjJOUk4P9uaL5Qp+dK0vBfcaCvlXxlBPj0HQN9M2lmeQM+84r7RJfC0z2V5WmpzMKk9ejBawA+BWtf6JgH3euJMYGDCcHwwc4gOJcvZtTqBVjV9Lfjb4lOkvalu72EZrSdZbDXNQA8HSWORiDfDDn2pqFF0DKiAJPNlDDGPHWfwCbg7V8DfBHcfQLi8N967mB2hW1TlJ8K+RmHoQCNQSkahbyMXWgQnN8UcpEGOpBKRsALDmV6wTGpuqRwv7Kj46ZA6zMr4K8f8BVHtEr5yF6/5jE5snmndM6BznUyhLFZdzZxR8q+vIxK6YSlRF7Clp0E5mwhRdvj/4QIyYe0sHN4uFkxjOqQLMiV31gVBf8kim6CEfDT9kYduLSAnx3b0AgPeGeweGGuFEXPFQX6JM08t+dBBL4c+AkzTacckG5qCV9YwFZLqVNWwOc9bLOiaVoKnbEYALulrVi3hC2sx6lanRPm4wGfDUmEohJPoarmn+fx6CX+3grgm3n3Tm8G0gEZgb0TcWEC9vSo1kRqtmOECPItEOQDNUDeP7Qr+IV1gICIjlUO3F21GgVZpyE3dQfkp+9EQ7DFiS7SMwLCNVW58Vqf8ZmuBORqz18N/q7w/U7bcsfkMEYi8BNtOWuQI4nr571R8MURevqcgb8eJmKp33QJ0F/Gsg5bUaapzt59vWsBdIjLgtfXRMJGzss3An4K1NLtqAX8Rhp+Bvys3+6VEti9YkCflDpntz8IJQWHYftZX7h7g+hHM2mmmVLHSItv5uEzwKWyCnRzpeHdRTchH7S1osF/uXMe9KivVPR8uysaFpwUHxgmx/z2miS4gJUxp2wKkUso8A84D9pG3r1akVNmsLcA9BR07YXN0rvH5kN9BHt+8fQMAb/QbuAf2h78wzpCQGRrV3H1slqfjEB24hrhTSA/A6miEqlvoHQWJ5EG2pTgi4F9L8iQ7ikGnOxE9bx/uhR6nL8rlI+RvJPX9auBn7z3J7ERD9E8pLUfsyxCYYToGKikyCeDHBm8dE70ZkDlvjMwtjV1bQRsUQH/g+jp77joBZukz3leXw/4aV/0nRbwa2n4jQK7V0Id/isG9FkRNaqa+Z+lfprSTF6P76oWX1OlQ3ep5GFTMslbgxPh8aUxsA8l+K4CPqNx5mBGY21sXMIWyqL9ZEu08BpLyVa1MSX+nwTJhJSDd29E5Vj27LXAXhWI7YYg0C2mEDoh2PNLQPjgagPyZhYpN2Uf5KXvEIwAqc74ZftFP9iV6g2rkuVWNwpXvqzgr9b3K2oFqV4ZeK9fD/jp2Mnrf7EDJWtlwtGkQJhIdCX3BqJ1r3+wKRoG1MfkQywfTh7/7M1R8NtZD/16PdKxMXrHFeDXl3JqB3YZv0/3bJ0OM8wuZ5X9/ooA/bT45ZB4eJIwyU8vD4ClqQ5ax5XArZE0kzBM8J4koBV2xgH+831SYe+FQHh5q59bgC/gJv6nDr7uquubkPdzIjUAWmPg9tcDkdiKDnkRA8Avq3dfXmBPHP2AGoXQOSZfwc+TGiK01igIiu2LyhmsAWQvTjNAcYHshDWQEf+90OSHLUQBbUv0g42J3kIgWE3zqMFfrfXXonz0Ar1GXL8e8NNxqr1+ar95f4d0+PNICPx4Gl/1cIVP+mF9qMgcxXmzPBU65smd8oQ8Fso6P5YcAEuOBMCfmJzFjpXtnzc6WsBvpOHXknIa8ftUjplV5byca/Rc9qCfm3kKTm39j1Bioaw8Pt2BetJMxvnLkkQO8J/tnSa8xl7EtPM7/0LVjU5ZZOGB4IwG2xcDfGH/+NMD+45O7K4M7GZjIsy8XZF444vJW3oBWx7w9bh7gXpSkfD0J6+zV0gvJYMnP6E6nj3P0w+slafQyVMQNiBiAITVvfWy5+Yr20YRDZR1fglkJS5SxAEoT2ADvgGsT/KCeBLLcIiuNgZW+H41oLIB9bx+q+oeJuv8uG8G7L7gjw3b86FJtDIjneJX03aJen8CY/r31U5iw3UC/nk7IuB7rNip5vjpWrgD/K4qehi/P66+B4y/SnwTj2s7/7JssH7Zg/7h9feAR8FOocTCyNWObFa6KEY8vquBWz5Qy3v4DPAZEJDMbCyqDkw5fB44pd/5gO04LJ8wvEWKMCwVVJu5Odypbo41KSYqejiANwJ8OQ7BTsZFsB9RuxB6I9izhTj6wIhBEFRjaIXLJysbiC/V/ogCyoz/CXJSVyhiAGvj/WHROR/L4K/F9xtx/WWlewj4B8WUwMNdk4US4PxCPR+ekwqzMRAnkB2NJcP/0y4FftgdCV8dE59tvdLMDPjvrF8Ct7VPh6+3hcNPSAux41Z7/DSUOrBrld//uHMp9G2UD4UQDY27/3TZNVi/rEGf6fGJx39ouT/szXOd1tGqqaMVuJW9Zs7DnzIgCQpRQUOBJ34h4H8QgV+3jo4J4BNrWxt7ec6+5gKcTEXKaEOoU8DW1WCtS9692pM38ezVYE+a+aDYoejV33CpsLFa7Df9zG+Qk7JWwf8T97/igi9SP/qevyuUj5HChzcEVnh+Av4WGJN6sadDx0/U5YPLokRppuThS9gO72BjoM3n/OCHUyLga9Xq4d82XupYCL0aZAjlw2dvJAGE9GYgvQEx4HctsKvN788ZWIixt2K4HPn9yxb0MxI2wsX9Dws3gzoBiz4TE6ochdTKJXDLUTNPYvG0tHxP+Pq4J7zbMxfa11YpLxD4H1oVIjonBpROc4xtRfuXwvoU8d2AKXTmDEiFpGwfeGEzRm8t8vfu0DlWvHsFE4Q7IRqHsmOHY5o6nzRFCSy2V1/59obon7STX0FW8q/yzikJbNEZPwX4a/H9QpxK+oLn+93x+q0CP0k63+4tChaIunlvQxT8K5UeUQM/A3ujWj2034+wFHPrmmTpxOVTDAgvOCEGvbU0/GrgLwu/H17rIYhpNq7yL7ybe7wsQZ+vnPnnIX94Zrd49kp5pnblTDWtIwAth2rk5espdYyybad1FxUH/CJ4/KsdVA8rnMZz+HUxI3f20Ivw1r+xsJHq4asLprkM+MZ0DuPuZdUOO2ANKscVsA9vcK/N1bv5EJbXZobgz/kkeny/WttvpOs34/np3jGSdNI5E8dPDYCo9MjMDRGwLV264ySP30qtHurWNRNrWDWIUgaFJ6+IgRVJ6KBIaiE2ltXArpZ+X12YjW++UqPVbAiN7V5el7JCx7ksQZ8VUuMrZzLQ59U66kJqWjy+VokFI6UObyRkLxk3IEB/q1uuE/CfShE9fi3AZxz+p/3TIdS/CMaviIJ0fM2VwbYMgO82d+9E7Rh79jbYV+jz6dbgeuC/Mt4X9e3iBTby+o0UPjzdUx7AP/WqbGgblyno8/86EiYkcfVEIUPLyGKYc9hbs2QDHT+BeFeUAT/XW2xGpF4mLI6F3ej4W1H00Lbm/L4zzUPbsYqcVJitbrdFlwW/f9mBftLxeZB6eqZwjR/+MwDWZVUMj68VuNUDfOFzepDweXobgb+TlILObkQC/nHo8fMePgN8etX9dHCykIK++FAkfHbIV9ysHAGfGREmxeQVSEpvnjc44jdxmCV7T+N8JxrHBnu38LhSN2Kqn4wLC+WgL9E+c4/6wXmpTh3v9Rtx/TzdU97A/1LHXLmkyAl8O64Tngc/7ImEeVKZcS2P//raJXBvp1ThuSE9f1qOD8RwhQv7/RArWzaHl++oomnE72vp90Uvv5Sr2SNeSr4w2+XC719WoM93wGI9boVLISGXVpkF5snzXr5aj28lcMueVqJ4eA+fAb6A09JxTNUB/vFrRI6fV+mwV9zMfB94bnUknMUSveUN+E5gLxkV/o1CTeVQw4zBscVwfUPHazNxl6H1Rtna+kqF7rLvjDT/Gae/Bx78fzseCH9jZqtWpi+TRWrRPfx94gr488ZFreOnM3ysVQH0a5AGnvhw/LRXAnwusMsD/6Mti+DqpqlCc3YC/PfWRUGvugVyfwr67Oqfo52knHpVOV3l99U0D6/fvxwar1xWoH9y4+1CogpfZoGBviu0jgC8Vnh8LgDLttEDfBYHYP++hVpetcdP1TOf/TdU6IdLHv6bPcUG5sRpvr85EnYQp2nBwzcqlKZQ9UiGSBGsNeLupYHbBZfAXU1yZZ09qXGimz9vc/Zlx99LOgJ5/snHPpTVPkk53vD9MX/YJHHpaq+fBXnLi+4xA/6OoaWQjfTmIZLwS1y8ukjbFEza6tFALFVCz80H2FFrDQaBp16VB12lAoTnsQLnHZQvgwuLK1iheVjQV/CJ8A/WqEXZeEWb5mH1ecCrJtTr+l2VpnkuG9DXKrPAe/nuqnWYl6/F4+sGbnWSr9Tdrt7QAP7UHF/Yei4Is2tzoVZYniXA1064cgRsLfP3POA7gb+HQOXcVr9ALpVACVURDZ+EkLhBlxSs7J2X7wxknl8BqSfekxO9eMqnoukeM+CnjFhGM8kxA9woFL26mf0cJUqE2jzrMPiLMQpanwf9k0in3rscVW/SwgP/QIwX7MJtEil2JgC7w6TpFWbj9ftGNA+TcYbEjoaarcQKAVVxuSxAn2id83tGC/M3eVUA/Jzo4PHpMyu0jhU9vis8vlbyFQNnFrSlY3ujq7PHz24EonReXxuJno2+h+8q4FtS5yjAX3zlGRlXBEPq5MrlEujGjWh0n03lVMWnthyOiVE+rO9ELtZ4WnbGH36O97Ic5HWX57cC/LWxHMkDbXOxgUoAdMSA7cRuaRAZJAYiCPCfWhkJpzAPkEk5P0EFTxOp7zR5+qOXhcrlmHkV0Suo5e9SJwuu/T1C3laP3zeTcappHr4Mc1XO1r0sQP/M1oeF5hS8PJN5+RVJ61DVzEfbFYIfdusJ9i2BXw8HyHpiFrhVgz8P+CxwO5WaRaNCgV+scPhlAXyrwVqicm5pkCdXvCQqJ6Lho1d8hctywM0rYgiifJIOTZWbv1Bph7lY52ZPprKujxWevzw5fvL4F1yTIsTJ4IZ84wAAIABJREFUKNmKOmbRQhTp5HWhcFqqSchomy8HZ2AAWPzwKLZXfGiV2E1Oq0bP4htS4GSKPzy0JlAX+NVlGpyzdbVpHpatS9U4m/b6qUreI1Ue9FlTFK2sW5rRcqd1JE79ptqlMKZjutwBiF09uqHGrw52Odv2Iyowhfw9LZcE8DXoHPLuWaCWSiZENJhoZ9FWyce04g+KKJ/ko1Nklc/fpwNg3mnqPIGLxICUJ/CTQ2Sm46/tW6ooPkjlSJ78JxxSkJpRK3q+wOq09SJE0N9yOgye3eQnqOX4jF3aZiQ+1w9elSIYEuq3+8p2H1OaR63mMSrKxnfbqqpF2ao06FMS1smN1wpNUfTVOg56hy64uguWGa2jVUjtWq4fp9bj9uPuKPgM9cRqb99Ii0/jzBuSAt6oijFT6ZS7h6+ic0iZM76pQ4ZJmbSRTR+zqZyKx9YqvQeifFKPfwmZCfOF46R6/rMOBSjq+VQm8BO41kKa5+3++Nx4Yo/clVGoblN674ze+XlEslwOhUD/eQR9dqwMtPk+u3R+ZBS+3RkFHx0QyzxYoXnUSVtatfdZUbbCYn9oeNX3Va6pepUGfVZbh5Kwhi7DcsLSYtbr1tWsW76uTg+si/9yP7F1G3kWu84HQiPs6sT4QjqEC1hN8+6/w5TlFdhdRDeTpAzipZl3NCyC29qkwAv/xFQYh69J6agAn+ic8S2zBe7e9u6rNAZfsoPjvf4c5Pq/OhgIG0nhU44ev1nJBod3XQoDYkvh8e6J2FQlBtZTuQYNKeeykRflWIQA+puxGTsaDSbSu6dxCfynfYpMEx1JCIJ1+DYzpmMy3PxLDCRL+V1q4DeiefSCunThvu5Rguo9bPlZBWvvV1nQp5LJZ7eJBbvUNfIdHr2y1IJak2/k5TupdSRa56dr0yAquADWnQiDd3f6CRmyhOLkJYztkgrhmP1H2YMjF0XJoK/F4/OAT/XEaX8p2MP2sEHQVrAb0l3KyzIV7Rc5qSkftLUC+DydQzXto5u+aHP3lwxaq/aOieu/uO85uZY/0T3fIN0jLBLoakk6XQnuGgK/oOIRb3bypu9oWAzNowrgJQzs8sdAvxMwL7v5ojyhfyNt8/ZuTHKUlECPo67/mubpAqVDTYn+PBQO7+1zZPsyqaaZmseI5jHS7le1Eg1VFvRZyeSyaPKNkrDUtA4B67u98qBj7QzYdS4EnlofIGXQijcejdU5HCsEYgNnqqr5wJIacAZfNbUAXwRvsaLg06g6oGSR8WvC5Ixcs1r45Q34od4e8EjTPDmrtrLpnDWL/4Am7TpArbp1qzbSVfGjiz9zRjjCypzHxAMzTekevhuWAMKIosw3MQvuugL8LAbAl5Cgz9SgP2dLjFB7n5Yp2IKUmrHQko3O2hdbI+CXc6LMU91qUV2G2ShpS0nzOIK6jG6i4V9t6QG3tME4QxXT7ldJ0OcraN7+R4CiZDJNplHwVt3rVl1bhy+mxtM6Y5uUwJhOScLNQc1Qnl0dCmfRM2eAL9wj+OdMLPfarlYmrD8ZDgeSfKBGUDH4Y6JVbDClg3tAdoEnvg0UQ1xIAcQE5ws32qRV0RAvdQlkGb3CuLzXXl4evipgS9UwH2l1aemce2+6HoID/OHDhT9UcVit2oc34Y7bhAOs7Hm0SvcogqYmwG9Wllkra7eUKwfKA3+XiFJ4bUCCfPEI9H9E0OdrYaWTrv/fCKG3rmCYJNDnf2+IxQ+PS6ognuahdfRq85gFdZl2vypV4qySoL9/1WDwgSRF8JaBrpXGKK7SOsTjvz4oSeb7aF+U7TdvZzj8cU70GJixGNusCG5tm2wJHcjD/3gz9beVyBcOkPXq4YuGSD/xyonSUXD2yto5VYXO2bNzJ9x0403wyrOT4K5xj1iaO3sl5Qz8vmAePPXiK/DLr79A2w4dKn16dOkeFc9fLsAvjakGfj5xi8/YVYP+G6tjYXQbR+XNs2kB8Dj2txBq9hPgc8fMe/v/3JYIjy6JQblq+QV1R8Z4wOT+VavTVpUDfb4xCmtwzkCX/tXz8o2Ct+raOnwSViTGhxdelww5+V4CbUP1PNhC/N/WsyEwebu/3Anr0/6ZTmVctZ5ALcBntE5lAP4DDQrkLlaVTedozceIPr3g9MVEWLpiRaXSE5WOjhWwQ6J1hg4eDC3r1obvl6+sgD1YH5Kne9ae84eFp30gk4Gp5D3zZRvMqB4tj18reYsP7KozdkfWxR68XRLlk0jJ9pUTuYiqnYhdufiOW2rDJBgC/PlpWDo6e95w/0pR419eQV2m3accmLpdZluf7Apas0qBPgVvT2wehR53nqIxCgN9LS/favBWs0Y+eskTWhSjfr4QntvoD32iAZ7s7lyqleriv7ExBG5tUoABoVThLeB0Kr4L4vZBmLQV7Cu+/F3MEhVGF7O8YdlpXzFoS4sUJGYPg9wbV4Pe8ZQ4J3VpBasePvH3E1vkQIMw8UmMajS5Smjvv5n1Mbz29gzo1Kwx/LBseQXdzlfmsLddPRi2Hz4G7775Glx/55hLfpLUsSv15ExB00+yzun7A1wGfqOyzHSvO2n4VYFdnt8fWQ9Bv7MD9GmCyGFbdiQCPtwvBmyFBcdgz6Be0lYwljinrlvzsQGLUYkGvaCuloSzDXbB+3RwHoQGlArPY2S9Gy/pNaxSoH9+z2Sh+49W8JZmyYqX72rwls0+49oj0bLM6OvclIEUO/QmQCngk/7BapjI0UtMjDAEU+uwzlcS1rsE+IzWMQV8siPMYHD0Dg/4JMeMafFOlepN26NtW8CCLzDusQk2zWPxsSdjOeuDDzEBxQc27NljcauKX4169V7cN04T+On+NarQyYK7WsCvFdg14/cJ9O/jQJ/iaHOxkfqvSM0qOm5Jah5lkFk0MLdg0taEXqLhIP7/rkURkIx+k5l230pdHhpTbqiOQd2mfZZW/AUy2EOVAX0+eKuuk0/H70p9HavBWyZzlD1vAanFW+LptgXQv1G6gu6hzw9cDIaJ68XXP7Ue3wnwyRjgcGoPn99WrdRxF/DjUKH2cLNcwcMnwK/RelaVk2O+9cxEWPLHIshDE/nVt99eEm76kj5tLu6cYiH33n47+OEddPsdo+DRl6e4OELFrs4DP1Xs/PhAABzFpHOzBC53FD1G/P74FoUwvEWKcLL0Fj5jfQRskZrFsMxd3ttXJ23RdvOGZEIdKaOX/t4bHwwPr0ZaCBe9oK4r3j6Ns/Rqsa/upfb2qwzos7LJZamvo+fl62ny6UIoNPASGjNjMKRmKTzUORVfy5SdeY4mBcE7W4IFb5/X4zPQl1kbjtZxR6ljqMPnPPzGKF1+um2WnHBVFQGfppa46euvvhqKi4ugdmwMLFq7vmJR6TIffUTvnnAW4yA+vj7w29K/qmQshIA/6cibgp6fErne2R3kNvC7y+/f0aAY7uyQBNSs6Pm1IU5lGti4DPj5pC0C7tZYkPOjax3qnwtYsO1cui88tt5fUaJBT8Jp1duX2yteYm+/SoA+X19HK3ir9vL5JudWJZp88FZdMlnwrjn3my+vUAep+9ekJs48hmTmobpndzgsOS+mcD/eKh++OSImczHapyyBW3cAnxKu4jp8XKXLKZB8M+HYUSjASR8wbBi8MF3sgmYvyhmgt6LlS/6EALyJGrVpU+kyTVeuB5VvOL/zERn4P94XCLuxYJuexy+DMBexldWYXBSYBYHN+P0QBITeMSWwKF5013i+ngBZXadHHdTlm6oXFHvBE0ujYK+g4NHutCV6/+JyOXr7VQL0j627FkoK4+GTzQEw65TjqqvLLej1vHVVoqmmdURKxaHJ5+vis9+fa490T+NUxbNAwaJD6PVTMLdWaB6M+jPGMAHLuaaOtjTTEPDpRpReJfpHYWp5YyqHXAKXA+DT5BFlMWHMnZBXiNbRywvmLFho0zwqhKU5euDOOxBZEFqwldSchVWfCuOBn07ni/1BsCrZUxP4tRQ97vL7ejJOtcJHi+ah574VMrXvXuOQa+87H4yJlIFyTIJtZ8btC4ZAgi4WzGVvAHzCVlXw9i856DMvX6u+Dk2kVS+f1nV4/SIqyuAt0Sz8Oux33stXl0lWNEXBMW7BgNGYDskKPT+Nk1voBdPWR8NOqQMRIT+jeKzQOmoeX9GSkXagocUnwL+3BbUYAqG+R0yLF6q0h8/jGtEWNXIz0dsvhdTAUJvmUYH+oK5dIKooD0LwRsiKwOzSSyzTVB2e4Z8Je6cIYowKAX6Vft9Jxonf80lbLFuXga6C5pGCum93y5c7bpHM+om/ogUv3wH2+n119erysIQtLSUPzcul5vYvOeifWDdc6ODDV9FkgG2lVr6el6+XeWsUvFVXzVQ3RSEg74SlGJ7pkQoh/iLPn4eA/x329PzljEjz8PJMdwCfefl8XED9ezc8hvGts4TdkQY/ts2rrjyXl3xdUqSs+PIzOJyVB3H+vtDiqm7w1qefX/LjqgoH8MJDY+HU5o1wPK8Q6gT4wugXJsMNt4ysCodm+RiMgF8reUsrsGuJ3zeQcWoVZVN7++FYbXbutWJZFVr2XRC9fGY5mAJJ7e3TunyXLb7ZCvP2jYqxMW+/EKKhVf/Kly9fUtBnih2qlc9z+UrQdxRV0+PyaX0tL19RX4fz9tn6Wl4+A3pmNBS1dST0peYqM/ulQY2QfNiIFf3e2snq9EiBXenx0NfjW6R1NDx8Pmh7OQI+Q46uLVvA9aG+cLSwFA7nF8O0Dz+CfgMHWgaWK3HF1StXwnMTHoU6Pp7Qzs8TluSWwobdVUem6cqc88A/ZWswHMVn3EjKaQX4ef2+1WxdtbfPAz9fl4e8/CfRy9+HvpS6Lg+VZ2iJjtai8x6WE7aqsrd/SUH/+KaxUJy7VdPLpxuM6fJd5fKtePlGwVt1r1uZXmFqHLQMU6/KhqjAQpR1hctUjhatUxYeX63F5wGfsvtqdZ7lynNYpdadOPY+8N+9FTbmFMLgIB/4C9+YVm3fUaWOsTIPhpRNY266EQZ7FcL63CJo5OcFTa+/ucrJNF2ZEwb8gqpnVyAcRuAn4OY1/G7x+y7QPHpB3bsbF8PINqlC5U3m5T9KXr608NvdWhvLkfdMgQlLkMLNdMQctbx9q0oe1kj9Unj7lwz0+b63fFE1pZfPc/oiR6/06tV/O7h8PS9fWabYEbylkfS8fKbGEfZNNy2u+M3gFJi6MUKojW9FrWOmx9fl8SVvPxRPaHpnUZZ5uQRtjQCCQO6mq4fAcyE+8GVesQByvk1bwDsLf3QFV66YdamYmueRA7AXAf8+NIIf5ZTAz6jeqcyKmuU9mWpVjzXgF0HVKLDLiX6kzl6OMsxORdkk9Q4bMxzf0ie0y8c+uZky4DPQT8v1gkDs1hWCGfZB+FOMgJNf5AGNo8WOd4cuBgklGvTKM7ii5OE7bFW2bv+SgT7LvtXS5dMEm2XfusLl39lAeLGEExmesBmr7LkSvBWMgfQfhSZfMkBqwOeNklFtfMUbADe+cHcxWocD/IktxcSrKwHwGbjcNngA9M1Khr9yimB4gDd8nZEPzz89Ea6/78Hyxp8qPR4VU/vv22/DTYGesAPpro4+HnCoUYsqLdO0OqE88AslG/YFQDo61wJVoyXPdFHGaUTzkOEYjH0wll8Q5aPX1SqB29umy3V5rJ4Dv97cLdHw6VE5yqbJ7bvq7VPp5crM0r0koM83SLHi5TuMABoDgm+ccylsasrl949Bnf3ABEdUH7dPxoJMpLjJw9fO1Dwvod798VRvSMdSyrtSPOE0Vyef9/KpRv7HfTPgfweDhcqZWsHW8ubxw/BEGeBTpm2tjvPBN7ieO/drlduGOOzJjz0K07094bH8QngjyBdeQgPw81/LLmsP15WJpjeekddeC88hb/xKWi58HuQPjxWWwGdXkJSVgD9+2xhBsMGAPwOBX12qQa3fN5Vx8jSPTlB38U0XYfXxcGgQXgD1I0WPnV+ovMqxpABIyMZ+wNx4+YUecCHbC3ED/4efN40shoaR+diHNw/u/SMaYxTiysxwWdXt8/LNS+XtXxLQr6gaO3pc/uf9s6FprChvtLKQEUjI8hP6cp7FzLw0NAy0NIvOh+igfEGPT0tZaR2m1HG8efClkcXfH2hYKFTLrKqlFazMp9E6Q7p0hvGeBbAIn5qaUvAmo3Y9+PCXRWUd+rLYnoqpNU1zZIN2QZD5LvTykmlamWi+ZMP+ZF94c7/YuFyf31fSPGo1Dx/Upf2LLwgizcNr9/kSDfxxUo7NQWyZ+CZ24kpBCsdKFU7ayZTOBbD7og98L/azEUCf9k3Hw5K52L9S8VFJv1/K6fgdR8K4/cr09isd9KnZ+bF1Q4VKmnwbROGa4cy5UknTUWHTmMuvj17Ue4PTICKwwMr9abjOwt3R8N1Jb4UOn71iqr38svL4DPDpgOLazq9ytXTKPJk4wEfTpsK2n76F5/OK4G5MRvoaE7bG4WP0+FNPXvE0D0lXf/x0FryC80BvOnTuE/GtZ+xLUy47maaVe4EH/n+xLPPs4z66wG+k5jEqyqal3f8Ey6HXj3B4+VR6+asd4bD0IhkISa0jGQ32LOvV3GcKJFrdtZo82t21LoW3X+mgz+rl61XS1EvG4uvla9XYMVPsDMU6Ok/0SBYKqB1NDBLKH/tix6sawYXY+aoEYlF+abacSw+AcavDhNWse/nG8kwtioiMxbWxxTCqqXijVpXyyGbz4+73V7VqCfPRYn4d6A1Z6K1dj0/W89mF8OuyvyuF5lm7ajVMmfwqxMbEwIIfKyeQLNA6w4bCB1gKezZ6L8Tjd8srgcdKPC5bmaaV609duBIPTxJW/fVYIPwYj0+25C2z7V2heawEdZtj1u3U/onCs787PgS9+0C5oYpWwpZWvIGOTS3lNMvSdWTklkKRaN4MvX1vn9rQsNdiK9NYpnUqFfTJyz+58VpUwOQY1ssnSDWTaepV0pRr7EjySpod5oG/2LEQBjZJFWptT18bDWtSEJCl6ZvcJQ961E/HQksBcCFTrItfJ6wAj6MUorHtIW3z7qZo2ICcvzvBW4HC4YK/BOx6tE4TLKD2SidMC8Qlot5EiGg4ukwXuSI2JtB66fnn4cjRoxAaEgrjHxkPw290r074pAfvB7+dW+CptEIY4VUCH/j5wK++nnAUs3W/X7W2Ig5fHvPNV1+F73/8Cfr37Q0XLibAsePH4Y9Fiyrc2BCt0zE9AVog6nyEcYwf8CX0GSwi0/FG92WadE2++vxz4Zrceuutbl+PCp1wHJzq8ScfnyzsZvrOYKFOT3nQPLpBXfzipU550LZmNtyyOErYL6/Xd9XbN6vJo5WsZVSagff2Y5rNgPBagyv0ElQq6OuVXKAz1KJ2tJKx3K2kyQD284FirXwqwfrUykg4h0FbKqr238FiC8SnVkSJtfIlgKb93VyvGDrUKIBXtwW6Hbw1UuvwenwK3L7SLgdiAouF8gpx7d+p0BvA1cEJWB575BE4dOQo5OXny/PUtkl9SEjPgX83bnR1SKH6Jsk3l2CP4W+xlRmpeebmlcI9WG3s+ltuhgdeft3lMa1sMO7++2HPvn0wdeo0iIyKhNbt2sELkybB7wj6b0+bVmGgScXUNiz7C+bmFMM92GDjPn8vaI2/3+3h6bZM85brr4cjaLBycqQmr+hSBAUFQQc8p7kLFliZjkpdh9fwT9wSBJmIpK6qeRQ0j05Ql5dw/nJdIvxxMALmHBYbqxiVZ+C9fUXBN+mtRK8mj7ulGVgTdXrm63SYUaHXolJB/8i/Q5EIu6Dp5dNZllWmqVVJU63Lr0v8/qAUbF5eCNQR66FVIfB+72xoWSMLNpwKg9exNaIrEk2jUgtaXbBY8NaJ1pHkmY9jd65ONfKxZ0ZtTL6aV2Xq6RAwk2a8Z9eukJicIoB9VEgA/Gf4ANi1fSvE1msCm/efhBHDh8NTzz3n8k07etjV0D31AtyeUigDYR0sxflYQXG5q3noXO4eMwbrmRXD/G+/g2NHjsAEVBHVqV0bPvv8S1j59zL4ZNYsuA295SeeftrlczHagIqpPYjF1L7G+VsW7C0buM8ifCC+sXsyTTqfQYMGwcN96kB2QQn8sScJ1WlFkIOlHMiDjggLhc07qlbiGy/l3HbRD949jA0hOJrHKGnLKKhr5O13RGa2Japw5h0XhRnl7e2XJVmLumt9e51osOt1XwV+/ti4u4KWSgN9vnxyr0V+itMxC+Dy5ZNpQ3XJBbN6+Wpd/pAayJ12F/l9Av44rJCZXeANo5dFCMel1xzFikTTKHhrptYZzvH4VSVwS4ByLUoKi4uKwRfrumdkZsnz8+rwhvDRmguQnMm8S4Ce3bu75VmSfPPtJybAPOS112AGDaM8CAwPBIfDNyvWlNsj0LdHD2jcuBF8NX8BfPT++/D5F5/LHnJgYAB8+MFHwr6ef+5Z6NunD7w1o/w8r1GDB8K1mUkwItVBZTVCL38EcvruyjQ//+QTeHvGTPm6kDG+6+Zm8M1vxyE5LVMA/uCgQPDz9YOQkGD4et68CqevrFwsPrD77eFAWHwRJZIawG85qGvB22cxACvF2My8fWZ8XEvW0g7o0nx93aMEOtUpECjd6EYV1xaz0kCfNUnRKqxGJ1zWAK5pvXxV6eQX2ufDgCaYqSUtC3ZEw4KTUgt1KR5Ambe8kSmP4C1/nHwSVlXl8Z8YPx4WYwMPnp6iOQn094FO9SJg7WGH3JAeqOZNmsCiv/6y8sw7rUPyzeegADpkFIn8NgIhLTtq1IUvfit7gIsM2K0jR0KzZk0FwJ86ZQp89c03TsfBgL9x06Zwz5jRUKtWLbcMmdYkEOj/JyMRtuLFp6D1K9iAYREattUxteDLRe610Zv94Ycw87338BrJbgn0bhYLp1Ny4XSSGBtiC12jPmj0vpw/361rVN4bpZ6YD6mnxZ4Kk6UaPTzNYzWoy0s4jbx9+i4MwaYRBne34eOv5+2rG63Q8fGBXFbQTeTv5T0aJmuxejx6Ad3KKrtcKaDPSi7oFVajCbVaZ8eqTNMZrCUAxy+Y0udTlHIRv09B2nc2RMP6ZJRwcVw+gb5WNyz2aLki0TTy8ukmrGo8PtEQU155BfYdPCjQIGxp37AG5i5gQLxlLPhgoHXh35vhkeGd4ePF2wSgadGxB3y/eAWEYXB34qSJLvHiJN/c8eNCmJ5ZBMcDvSADlS2/48R1fvgJuAv597IsBPjXjRgBI2++GV7A4O29o0fDug0bdIck4B97/1i4+ZZb4cH774OAgAD48VexZHBZlreeegw81q2CWjhIj+wiiEIK6x48V1dlmnQ+H/33v7Bx4ybISE+FuDA/OHQ+Ddo2awh7Dp+A5nHhEIilLXacFGNV/OLv74/GbIxbNFxZzl1v2/O7nobctOVALRdfxuKFaPMVDczZ80ZATIucRCVhravcfgvslDV9UAK8+k8MbMZy6FYarfCg3xa3D8JnNtKvFMLw52i6J6xLFQ9GL1nLSq19PqBLb/rBUW0qYropj4FnyCpkH3Bh/wzITJgP6pILwgXEK6rW5lsN4DLwbhsCcE+bPNThF0NqjhdkFnjBx3t8hHRvNbVD+2TAXg9VMu8MEPl96oT18N+RkEZ9K4TjKruXz3vH6to6fPD2iSrG49+JXPb2XbugCCmdoAAf6Ns0CppE+8ENPZpAMVITn+LcFqfFw+874+GennXhkWEt4IcDHpCemgxx6EHlxbaD7MwM+Oy7310OiHZt3QpSs0W6aFRsCATjZdjsHwq/rHZfxcMAn6in519+BZ5E/n7Hrt2WbvbHJkyAR594Au69806Ii4srM9VDvQRuLcyCBbnFUICBW3/U5hd6ecOKLVstHQ+tROfzn9tug1CvfLi/WzTWigE4l1EC287lwuu3tYXnfjgIZ9HDJ0Ow51wGfHRzbTidXgp/7M+EA+czBTqOHvuw0FC46YYb4EV847mUC5+xS/z+e8jvG9E8epm6rnj7s/plQq2wPDidFgBZ+Z7gi2WWIwLEdKoCFBQUFHkK9XdoiQ4uwO9LFFn9/HzRft/7FxPqzjqStawGdPkMXRpzejuAYc3zhJLpcW0nV8hlqRTQZwHcyasC4OdEx6sQK6BmNYCrJdN8sAnW0+iYihfF4Y3STFFH+68xAeP3cxQhdfbymcHoE1UKk3omCfz+qdRAGL86RAD95mjND1MSL25bXly+VvB2ANcMpSrw+L/99DNMwuClh/Qas+ShppCOSpqPN2RgKWkfaBGD2ZRLT0C96BCBOnj7xsbQr6EofPWKbAB/nw+DX1dugXt71YK0mC4wffY8qIsB0nvvu88lr3/J77/Dy09Pgm99vOFhfMrTvHzRCBVBrbia8OXcuS5x0p3bt4d2bdvAm9OmuwT47Ikj4CePn6ieLp07uwT8BNBPP/WUIKPMyc0FT3xrGh4RAD8lZsGRE8ddfqjff+cdmIfUTPu4AJgxIhwGfHISWtQMhVk3R8pjnUjBRupfHxW8ffL+Z41qApvOiHkot7X1hx/25MHc9WJKKfH8L73wPIxCz/9SLsTvn98jSpOJ3190gUogiIsyqFs+3j4Fdd8YeNHyKdOzm4klG0j1Fx1UoCjWdjYVBQ1LQ2Sax2pA1+H9Ow5jaIQHvDM4F0vD+EPjXksrJKBb4aDPUztGAVxem++gcIzr7LRHD/+9YclOgM9fyTmbo+H7016yHl+rkuYTbQrg6mZiK0QCfqrJk1XgCVO2i6VW3eHydb18SaVD49bCeParHbKFdofhtR6CyKYPW74JK2rFoagCOXbipGAnHx1QH0Z38IVJi9Nh++lU+OnuBjB/Zy4s3IQujbSQp/9wN5REccv2BF+ohw9GbI1YWJZWH178cCF07dgRNfAn4JVXXrYM/jf17wOtctJhS24hNG8UCW+Magvvrs+DnccT4LclSyxNAZ1PHBoKAvwxd9wOZ86ds7SdeqVXXnoJBg52xV8SAAAgAElEQVS5Gu4efSfcNmoUPDBunKVxSAJ6fu9amIkA/c6aHPh56znBy+7SrjV8+9sflsYgw/E4Gp5Dh49Abl4e3NOrLoyT5nz7OQz+1WbZJuJwO+JLYdz3R+Wxr2oUDZOHRMCYhWchyM8bfhhTEz4/HAOfL14veNSDB/SHWV98YelYKnIlnt9/elMIxKOdUgM/C+qWl7dPPTHOZfgL3j4tCZi02TCiAJrEZMNZfAv4ZGsIbEJoYLTN610LoHdDRyyQnMu7FkdAMvZUKq+AbkV31qpw0LdG7Tg3SrGSgfvzsAyoia9oRgtZ5rF/RYo8oRyglRqh44aMl/+gTxZSGGJ9HirG9tSKaDhXULFe/vMt81FChl5DFamNTwqasWPHygFB8vIj0e4RWP25LwEGt4jBBBc/wdMn/r4fRsNC/T1lT59dhwXnG8OFo3ugc21/GNgC3wii+4FXQSYcwyfjtTm/wlaMF5gtBHQk//QtzIcifPJ9UH0yb1QN2HOxBF744yTcjIlgb06fbjiMAPg1a8JXqFMf1Lev24BPO+FVPU888Tg8g2Bu5h2/i1r/ud/MgynDagtzNPzzs5CUIWZZR4SFwRdffWWpP3C3Tp3h+tYhAtCvPlEijJWEt+riQwWw8WSWwsunsf84UIRFxsR7mQLtIzvXFsB96f4EqBsRBF//JwY8fYNg2CeHBKrHy9MLJqMxNjsfs2tWHt8zfl+oz7PPr1y9fb4mj2DssALnI12T4IY/YpwCur9clwzFmB1939IISJWK6ExqUwTDWqbIp0nlHF76Jxx2YeMVPqBrVo/HrMHKuPoeMP4qpOB8O0CznnPLY1oVY1Q46B9a1VPIwC0vakfS18D9SOvcixeM2hXujg+CP4/5wy40wM2QlrmuaT5cVS9T7mW78mgETNuJPKEK9FlXLPLkKUFr5sBkoXXa30ci4MMDoqy0orz87hGOlod1Ov9WJSpnCtTOM0jtSK8pG59sKniNE387JWi+yQiQt0hAQUByPDkPnukXgdUHReLqGNIKzy26AGeSs6Bpw3oQFx4AfgXpaERLYMbwMHxLKIAPV56E//3vf6ZgR4lTZw7ugKv7dMVgZzxsTPSDA4dF73Vgs3DYeyEPBt50F4xFdZHW8hR6xjvRuJAO/0WUXhoFba0+VQT8C7D65fFjx+DFF1+Cb79dCK2ROtJayIC+OPFx6NM0AvbGZ0MMEu/ZmHPQqyFmoKILewLpncjaDU2Dw0yDP6ZbLeGN6rf9RbD1bC5sO50OKXgdmsWFYQkRP3iufxjSDuKRpKBdefL3RBjSIgw++ueUkE9BvD5RPqS6mnlDfehYywO6v3dEWJ8A8Lrh18K7qAK61EtB1mmI3zEaSkoyhebqK5MEKYWwMJrHHW9fr5/utO458MyGQKdkrVc75+G1Sod1J8Lg5a1+8EzbIhjawgH4VJTxjVVRWG3X0RGMjlGrHo9RO0UtiofX7BM2BITUL9fLUqGgnxa/XKizQaodV6kdM23+N0OyoBE2N/hsI0otTyF8S4BOs0PbtkLwf71/qhCk3XImFF7YHOBE8fBlEWj7qd1yoG5YPtz9N+r1K5DLRzUiTJaybqsKrUPzRkD5x+LFMuh/e3cTAdAfW4qBrOJcgdP/eZuSHmGc/t6MCJgwfwfkYuEwWhg1lFIaCd/vxiqn0b7w/c5U2H3oBDyLqh49sGZ398DefWBSn1Do2TwK5qyKhzn/noAXhzaEJQczYNZNkTBpURr41W6tSUsQpbLm33/hh5//B1+jBl9LlunuU0TxiXkLvxXG/QmNl17JBpJRblq8QKB1yMMfN6AhzFpzBoa2jIIJ3X1g2TFP+GRjKqwxUBDRMVKZiLnz5kN0aAC8cS0mj+E2D3QLFygdgS6SrkcAlq6YeUM9meohQGdxF3Y9dmKglzx/uq60kBFgS/u2bU0NkLtz5up2KUdmQ1r8p0AdtyZuDhIEGTzoi7+7x+2rvX02rvAv/jAlTwQGdr8aJoL8n4fDYESLNJnHJ0fzs82R8PNZsU6/lnyTefssoKvdYEVfs/9x51Lo2yhfoH1jmlmjEq3Oc4WCPiuh/NPeAJhywBHAFeZJVu24Tu2QWmfWiATYd15qZKwCfBqffM++0QDP9k6C1FxfGPN3mKTKEakdrd6332N8YNnRMJh7FFGZgT57O6Bt5EisQwEkOsWOompWuPxbahfD9Q1zqlzW7ZD+/eHk6TOyuomBPvG/2/YdEuR/t9x0E/z0yy/Qv219SMnzgOd7IgeKhuGcRx34/RiScgmHoFtdXyeemWZpU1I4PPbNZhg+dCi8jwlFRguB/vDmAXBvR094b10+fL/5tLA60Ur01nD3d4mQjnGXt6bPgN79+8lDLUZZ5RRUo8x89z1IS0tF2ahY3Ks8l2ZNMRcBcxfuRX7/0KHDsH7LFqfh27RogdQjBk2RP+fB+f7eDeCBrj7wP1Q7fb4xETZuM1btiHkSS+GlYY3gupaka1Mu9Pa07ngWHLyQAU/0rwM3tBLXIUNTLyoQTifnwMO9asLsdRegZVyIYLif7hsIFOx99H/nJCUPQPs2reEnDJ5XleX0+hFC/f2/TgXAN6ew1r10YBXh7TPQVmv2mbevnpNf90bCe/txnvGgHOUYlBQPbWMU0DWjeEbGeMDk/qhiq4AGKxUK+gdW9sCa9Lnw8J8BsC6r7KodRu3M7JkP3bA42oV0f/h6ZwgswRKpLJSlkEYiAn85KEOQXz2yBl1/XNQNz3n65mlM2JqxG2kdCbn1qB0G7GbZt/zbB1s3DuV1b18lJsxQcaWQuEFV5TmD9q3bII2TKxjMjg2i4D8dwuG7nWmQnFUATVEhsuF4ipy5SoqQmasTYf7tscLx/3q+FvQNixdiAOqF6IYlhwvg74PpEI8a/G0oBzVb7kGJZH04D4/38IVu7x4RLglREzf26QiRRRcx8FYCdevUgfy6PRVvDR3QYx2L1BCpba4ddg1Xi8Zsj6593wsTnL5CFc1AjBXUw/IUfH0bosnmzZoJfbu2gV6Bp2D66lQBfAWpJO5mE9Jm768vgBVHs2Dd5s2mO27fpg10rhsCLwwI05xfNgDj+xno05sBH9Alzz8pKw9u61RToIooQM+S60jFs+fAftNjqawVshM3wcX9orDh1S3BcATZAlpcUfKw7FvhuRf+o6q3jx/yxkRaRdwP/tyAnbYe7ZGoOOUVSP2+scNH9vDZNjSOVoYufc/X2Rf+lnYq/qtdeZO+WTciH0IDSgWcKM8ibBUG+ozaOZfuBUOXiVUr2cJr87VUO0bUDm37100pAvfOFqqMufZEIMw+jCod/F7Q5ksoO7UbyZ9QibMNg0JabwSS9860+epkLDYe7Us7GcsFLx/3VdWCt2wOSZu/eds2RVYn+25o25oweXAIHC1tCJ+vT4Djx49BvcgAWdFDQJ8S1QXe+XEzRHrnwWBML96XUCiAPC0kG2QPR13Mbl259l/F/aD1B5UW2LV0IUy5Ohge/S0Nth5PFCSIPRqGCHJDonp+2ZMOnyz4nyzfvAHLRfj5+cEP+CYyYug1cBiLwlXkQm89d951F9yNP1MmT5ZVScTDP3rXLTCmUxj8n73rgI+i6r6H9N4TEghJ6B1pUqVLF6Uq2FARpYgFsKMoop+CIGChSVEBKQJK7733EnongZDee/vf+yZvM7vZlk2A4N/3/T6BZHbKm9n77px77jkf/n1NQF0SWqnk7YIVLwdgwvZURNv4myXlzLIR96KUzmfG52XGHpmcLYrpibQAXo0hWLKxi6a+8uwf9/BSUx/8dTqeAn2mgIYYx1e/dajnxpr6BfqQaFtpSk6UdO5NFXV1sX2TmjwFQV95FrXhId1sv7F7Psa1iSO2U2GcOXfPBW/vJfxfpynsfkE8UoSttDn79y3omwftFDU+N8XaGUMV9D71Cwsq6gcrlXi0p6iou+yiPc6TYTkH8lVPxeKHQ17YR922xrJ8c5uxzM3ypR6QOL8CmmZzD2IL1KNSP42yUryV88dc9uQU5dz0DS7cDmnqhEmUobYmPPlidJbAkzkIyYCiFUSIs99nxjHci08mzR47ZGeRvAIpPk6eOtUsjj0zX2bOmYMtI2tR12Me5l/1w5y1+zWsoYkbr6MOQSiSusmLxEKSVFhOWXZp4/jGgte8X+dh357d+PufNTh84rhm0/atWuPOvQg807Qy7qXl48iFW+jcIBgjOwTC3yoKPefeRsu2ncwqnjJkNZ26b8Pv3iUHNStkCnXTcqKwru/NavjqOAHF8WLtbGctCsi8aDOkM2SZUpTXN9xcXc1iVpU0mJv7ed2i7nYq6vKwJNvX4AyGsv0CqIYDeB3nfHzRVqkHysH0zZe3ump5+xYN9iWDeHQbtVpTZ+Ks7qUvwnbfgv657S2IP59hENoxpbUjjc91ZRfWPUM3w0n/QytvEMsqXCUhtY1XqeWWxtZI5WFRSyyodW8MZfmabeizhiQX5BuFKSzfjS7oi8fS4eOUU2Y4+eovX73atZFFgVnfYFhFBgrOtr/o4qMVPPj37xGerIs5918Ug7CoOJT39SOopRs2Ejb9ESlwmqO73+fJtmga7IaG1Suirm0Y3Cjwc8afTJz9OqRbsfd6AgJr1sZyCohSFO6rCRNQpWpVvEAqloUSw+aGGMu28/bywkHC9J/q2lV0uEoTlqYEybzgYo0rTrYII6kFJ1srzOynaLmfishHtmdVTF15CJsPnjB5YL6+T2jevOhYkffu4cSp00QnzC0S9Dmof7ElRvNmxXDOrH4E5ay8J46hq8Oje2Cmbm7fsd2sRdnkSZfSBqaKuoayfVPSDPoKuoGE7PLiMIW69N1UAZ+pmW9u8UAsJf0MDOi6avGlyg5icyAefcbp+lg8vN+V7XJRwy9bGCl5BVnmV6F7K+5L0DcF7fBJ6GrtmNOQ1b8SFZ/I/eo1MiamehQGk/RCXf80rVcw3QuMSranbkQXLLpCxucFEguyxVYGapZbDidTdC1oR08BVx3Y1QVcBU5Sjsx/6MvyXwrKQeegdFG8DWpVtvxfFWjnRBFRNRk0uIMzy7smlqzdJq6Rs/v2Nbyw63KclsImQy7qwD/9YBYGPe6HV36/iKSUdOTk5iAwwDS8w05WY98diQ2vBxYJHd3n3IZtTh7GuNhiHOHky4lB8wUxXPz9y+PHWbMfCKyje1IS5nmZulonUBH5MHkKHN+4FqMp0I+II9nuip6Y/5ziq6weu8PsEBvczSSTSWmYu0Hds3bk9kasnacrCfisEi1+shtaN4vnhZg5+Qzt8OB6At83Lu5yrebLTbfFQs7b8eC/29jYoAVJZ5cVMTY5V2GHn0d2xgVR1P2Niro8dAu6/DN9mjwiSItf6sPvC/L/gt/1q5SH15tGa8ktJFGfzzvbvRDG7UB0AN0agCFbRVONWuZo8fBpS85+aers35egbym0wxcpg6c+s5S365C2DhV0FhFFU7Jv+M936uTg8UrpqOheKPGr+wVj1/uwBAesuuiE/eSYJYu0nf3yiSESj5e2UBu7TgFXknVMZfnqoK9dSFYWAXXxtnydWXD2bV4kADzMH7QhTZp70dGFchMFJ8PFWsaBmSHyVD1qQglLERg1B48/ng/ErYRCDj9/hIu/TKeU4721CfihlweO5tTCqB/Xis8lZ+Yi9MIFo5fLLKKBde3Qp7Z2pyl/qAcF/cS0bLzs7YiLxP8/TYYr1oTjr12/4YHCOroXsJrqCGvo/0zjzCQjk3e8HbCIMHePoBBE3b2F9UODkESaUG4Fei78+eORdvhqW7RJ2iZDb7k5mUilwDyqoEt6/rFM9K5jr4F3dLF6huP613PUvJHJf4dG5oqFedZhRYqBf85DUj/Lk2WkJUY49/P5VRd1xx6iTt2CF1JDCpzaP5dFXOUMjRV0PehtfG7XWE0SybXAz3f6kBqnpGZyIVh/tl9aLB5DEE8enFGz/f5SmeZSD/psiXj7UHtxcrqsHTHpZlA1DUE7/FmJ+auDPu+Xg20bomj2qZGOOmSLZksCSYYGF35P3HVEaIwNRjWLwy5qwJhzSUXTLNifXNWNFXDNyfLfqJyNJypmlJnOW/W8cFbNXbh5+drzxXgwFwY3X8kUei2MHV/KqoDBP+8WgYJpf3Jw0VBCB5LmKYKaXSss3X4CUXfCUNnHQeDKPeeGYSVJCLMhi6HRoE5d7BpBUgOr4jTdpn+eyUHzQGsMWnhFdMP+MGUKfvJ0wreZOXh59AdoRJo4DxLW0T13SePkBbRqdipak8Ll9Nh0bNm2TfgRfP9UACKpztS9hpJKfL41GS829cbIv24ZZTMxtNONKK6rBpPcBcE0dUjRLiM7H/UCHIREhhyy0UoXapMMHbmASyYVf5Zpr7LILvfjYO+AxUsWm2yeK5XoU4ydyKIuG6rPvKa8nVhM3zRS0P2MbBXZNpXHugtemH6ezNsL8H7lmPmlCvGYklvmY0oWT2kpb5Z60JdmKZejbNFvt+JQI4euwJoxH1z+jCGzFA02r+LNq1k75MGB9x7LQG2/dHiRBoyxwZILQ7f4IImU9XiUNk2TsfzJTRV9nbKY5XPQn/jVBNwODxeCZjwYt//haV8RZD7uVF5k++J+uPhh1nlX7D9wSOD6shOXA0mP2Up3Jw9mqDQLcRcsnqb1KiM37qb4OVMKx2+8gzPnzxm8JVzAXfHn73iqvkIFHd5C0fWZeSgDAx9zQL/f7+DU2bPoTnoxiCFZgeBAzFm35aHAOroXMaXAbOXS5An4IyYFL778Ct4f9ymYRrpycCDmHk6hDmalbZaDccOKjpi3/w5+njlHq9dAd7+c6Y97sjzql7cSb1d8P2TwjiB9kaO3kjSLrloAj/fzxbZkMMtH/Qa2MrIylm05KO6t7LbmbTlDdia5i5eJLmuJ+5nBm1oKv9CX7Ztb0DUG8Wiw/QKIh7P9Bd2jEZlij6E73LQatsRlWAjx8EelJKRGflmHullopK49YVJ5s7QatUo96IefGiu0sX+hDtiZtwq5+WK+NFl+IWuHfy6DvzFoR0O3lLRLffRL3hmtCBIU4M+8UT0HrYLTDEI/u697YspZypiKBe0UpWlKLF9k/uI8lAWkX4VcPFMlrUxm+fLRYnbIRx9/gswsBf+V2Pw8ghDaV7FD1QKZBRlENp1VCoMye+S/y2yfgbNGjzVAVFQ0IqIiqehui8dDPASv/uDVWJLyNU4LZGinb01brD8Xi98HKoGfx1LK9IM87TBpj9LFKuQJOnbCmNHvgfXhJ0ycqP1NeQj/4m7d7Xv2oEXTpqKQLBc3bjT7oK07ToWn4Y1mSnbOkhWjqDmqTTUvHL+Xb5TGyjWX0NAz6FG/vFA5ZV2dvZei4EjXXd7PFxFU3M3MzkYwFW65GYwHy2eM26A0X+m+md2zpaKkDdUYomJwPTxaaCnxYBrjr2Ss3q5jx4cwe6YPeff4cGSkHII52b4uxKMuwBrl7NMvp7QmpMCKmHayt0dSNJVJKjbEo9udK2UZCoN8PjjdMhT0pbkKa3RVajrL9ESZ2KLUg/6VXQ3FIQetJfiEJHnVw5h2voRtJLTDN0md6ZuCdgqDbWHQV9Mru5C40nP1krWCP7N8Pt3tW0RCufCtofDstamaStA3Bu3w9pzlT6Is36mMZvny6j794AOiOv4laIAMD7Ca5s34PMpME/B1Ny98ujlOaOzsv52N+Qfvapg83B3bq46rKCZKqIDnxJEggucG9MfTxGOfQPz1s6HnBLPlnXfexvODBxt8JNm4ZfALgzCJCpVhiXma7tLDd61JXTILP++N0ujVsGREeHgYpv34831twiruN4xlmHPpjWkuqVZu2bJFwFgctJMirtHbkR9iMmkRDFDYZ4zDe1NBetHBO/hp5myj2T7LMaygekFqaqpGB78hKZdOmPAV4hISRM7Cb2gspsZF3d9OpEAuznxPx3cLEm8KMw4kC9iu3gBqtNvjTcX4PE1RlwNlsyaNzeodKO68lMb26mx/2H5XejsvQUHXCMRTkVg8n7ZIxhvkn602WFEKwqaDPn8HdFk8xenO1cX1S9s/t1SDflLUIdFFp68hS8xXCfB8yYjRMGh0Mn0lABfAEAXHUqiY2rILrJM/spGiqMkyyiP3uGkxb2QXrVZAl8ycgj/1GZ7rK+DKLL8sMnZ0v4SSpy+ZORKz54zxn/M8X/ZCvEs9JH2TYR5dfJizRmdnZzJhccTjlPmakl3g/bZo0hQjWnqSzok2LMhZfpdqNvh0YywcK9QQ3a+M+/+59E8sJm4+y0KUlVFI4exCbltOQs+GA/bBbWsxvXd58frn6aCdDE3Zm46DJJFsTtOa2Bexg5heyzLROaTPL1FOvnfrifHEMNr8w7FF8HqWwebi7bzXq+KxmnnIjbYhx6dg/LH1pOD1cz/FrJmzjC4+D3ueZba/+hrN7R3lOSnM6pV51cfiET8v+I8uA0cX4jEky1Coo6Pg+jx0G7XuN3WzNLpzSzXoR1+eKYSSDDlk8SRZQtWUgVtCKLwfDRQkAzL/0IygLzH7v3uRhMBpL6wOowenlKAdTYmB/uKuyvKZY+te6ZmH/X0xeHyGSrp37450MvngITtw+e8cQI4QX40VNb/u6oV5xwpFvvj3nO2PbOmGmMAnMerrWSLbfJdEjxafTNK0+EuXJnXXqu7JsEDZkfVLqOBJ7hY6Y9f1XHIvssGQRZdIrI0E/JKSsGPnTuHF25DMUR4UJ9/cG8jZfnAw+QiQEidDPFKyetqztWBfLkevLtGA3yMwZORog29C50i6YigV3KNj2f5QsfWUHco7LhN5ggxt5H1jtpX6jYy3q1eBlGhJHptpnFxUf6+zC3KpsPzh9gycvaoYqnDQl28n5l7rg95OZvssxjaaxNj0ZfuGgn5xIB5TxulFqJsFC0pxu3N1JRkMQTyyO9fV70X41ymZnlSpBn1pfq4royzm4z7i+aagHbFIiEWhsFDLQb/POr8iHriWQju6OjuPUpavqGtu0PD01cVADiCRpJczpo2TpijIWjzqRh/m888ZVAUnrBpg4uylqEk6PTxY3OtGTIYm4+Sg8jRp5Otr9W9Exc4f+wWhjo+2AxrvZx+9YGTl2+CL9dcE3ZMZMh98+CFu3bqFGWVADlg38Mlsv2ObNmjRvLm4Xn4z+aF/NdiS8Xs9ognrjiVncvHHsTitrl65DddcPqTmLMbtOXDxfPN4nJrXuNv26M1E9KrvI/R0uHArYR3ehqEdR3If465pHj/sicZ7bX01xXnW3i/E9MuOoYqxxUTy9mW2bzFnX7c7l+OUxO8L/tT8m8OHZPGYAfEU6u0oOzJPY98wri8dtbLyvVG3w/YSrbWlFvTVVM0eqx0QVihZIU6wOHi+PltEY1RN3aBvzCxFwkOexPpKyDG/IauQMaTk82r4Rx+0M7Wx0n1b1rN8vhbG0l8ko3CZ6TMM0CrYDgduZWnomgzz/G97JEY+4Scyf12JZQ4u3ev6aTVsseZMjxp2AkeWgYizfn8/PyxbsUJD25ww7jMc2bkevxeIt6mf6A2X8tCjphWO3bPFxO0x+OZ/3+Jj0sdnLnlJjVFK9M0x8WF22kpITMSfS5YIUTWWZRjc1AOdKgO7rmfjKbom9WBTk5FrUtChxzOaZi3pmBV6/rzGnF5CavxZKY3MiwDfFxbH0zVC5yKuq5sHlh+8jHnPBcPdoRxOWdXHJSoMP1ennDCl4d4LHjakvzOZjGnM6Zi+n3Nnat+JYf8g9voXQnpZZvslhXg0VuEFLB5zIZ7idOeWBNcX39MBimFUSTX2Sy3oyy5cfVRN7aBvWErZED+/tPB8Ce0YMz6XEE1JuPnse/ta7dQy2X1r6AvFDJPwiLvi12+0CUIfao7ydNQAVoIiKHVedCma6n1y8K9XswauhUUgNk7RSOKfyeyf3xJuRScL5gkXe9mUu3WzZsLguzfhzOqRSBZ2vX+9hn8Ig553LB13rCuKfXbu0kXAJ7Nnl5zJYCrAWPp7D3LGmjTlB2FOwl26Bw6QNeHNQ3ijuQsG/X4Lf74cLKQl1OOv8/nEUkoT2D5rCc346Wdhj8jm9LK7ltk4HPjD4lMNauio98kLb6/GFbDzjh0aucYj2IV8emmB4fHrgTgyAc/X+OU6OTkJOuyjMKT0sqFsv7QhHrXsspgfI9RNiesXNmwVZvv8UbXqZnEkGaTGvmfQGPhUsdzTuNSCvrRFNKSdzxeri+cr+juFDVeyfKfL2tHXiMX70zZB0aZq6ivi6gZ9Q4qapnTzdVk7GminAD6a0jgNvk65ZSLLX/LbbwKn/Yb478YaohgyiaSuXB6S4scsEMnFl4FAujLJph51G7/MQqtVq4xY6jr+ff0hId3ABcYPOlUQu7gQlaUJMqz18v3kSfj0s8/w9yuVtIJgEgX8v86m44muPfH97GWoWd4JzrU7YOnyFdhE1zPpf19j2s/GNfkfZvBaRFaIwSGVMZ/MViIiItCWoJ6o09sR4GZH5trZZMhhjVEtFXc2OS7E2uDtVWEkrjZDq2FOwm0899K5jGspLLVsH1ATR9MC8dEkZQFU3w/+t/QfMDQXajiI38BMmbo8zDlVH1ud7b9+0NlyWQZDAmx0MJnFF1lACoK+se7c4hirmCu1XFqSDKUW9E3h+fqCfmnw883C83XZN2KxuT/QTgPXfLz/WAqpIboipM2eh/4dYUZMbHw8WNJ4Edn7GQr80yZPpiahWRpcn4uC3NRTheQEOlV1QIinFViDZ/mJeyLDbFA9BGeu3BSywYKDfyNZ0AXlsA1qSk1Zt3E0gpZf7yqYTV244RFJiIvTlsqws7Wl/oAsjH+qOkFByo06EFYOkUnZ9Lah6KwwtfGvY+GoWrky3Ij6ydLJU2gRG0M4d1keY4mi+kzvvnhr1Ft4rn9/LFv2J0Z3rKTRJ/pmTzaeqW2Pur6FGX/r6ddFQVX6Gsjrc6J+hxrV3OFPtQ1mUrxg7/IAACAASURBVKm7cTlzv+bbGUM+/4k8XL0Ezq/G9Xkxfq25t6DW7rqeh8SMPGy6lIQetdyEuYrQ+acvUptWLY3q7nCx/Rd6RrgfYf3WLQ996mW2/+MZFxwkqQRzIB5RzBVB27AWjy6ur2uuYi510xCuL+92cfn6UnUzD04kyXDA4vkvlaBfmng+35SS8PMN+eDyDBVy7QuonVrMH6XYa5iqabghS83aebNAcoE1sP3qjbf4xpTWB3sSFHKFPF1lt6WXuwdeH/q6XpYId46mpSnG3TyGtQ3GjisJqOnnjNN3krWKt53qVcQVcmuSzUD6zvcE0RCn7Y3Ba8188HjX3ti4ehVq+Fihgb9iEi5NRbip6N0+zXE69BL8yUuyYYB1kTeMl5eSjn9UKkaMUKzjOlADUV2Sai7L492RI+lt5GfRrFWesuiUmDuY3T9AA5PZ+tfBqp1ncD4yEyFednTt1vh5X5SAv/hZlW9I/HbkVj4Y3fwiNf0TY9t7ajXNceC/khOAqnlXBU+f8X5uspNFWp4nIa3RhPD/leHkM+yFv1TWl8bompwQHKW6hFT3DAwIIAhq30Of+ugLU5ActQh7wh0w6zoZm9CXt5ChQ+JousVY+W+doG/QXKWgcKs36PPiYUCSQW5f2nx9Pu1NXbKp1yhXdPe7+bWw6B6UStCXeP6JcNLzOFhUJKss8PNNQTumZZT1N2TpsnZmNVeasVgnw9GrrkU3pTQ/NOObQbhz9yZWb1RMOHhIWEW3YCecs4i2KRdHCStIcS4uGHKHrTWtylwwVOvsvL4uh6wTrbUCER9rd6QHAm3jNT/ngvDhKHss3FkoxcD7lYtHXLYTPiQ+/tynFVlsOXr+GoaYxHRcuXEdbw8fjhkzZ5bmNN2XfUmIh/X2WYjNg9AcNlBRD7W+EP9c3e+gz6uAF9LDYVkaeQr1vviNSPZXLL9ghambLiHQ3w9xSckaWivf00D3cohIzsfYVUoBlwdr7ujKY3AhuddTTyEpWfFZkM9FWRFlS487h4izL4pz42atBC3p46JBn7fTXhiMC7CZKuaag+vrNmlpJBh4PmlVkG8DSkcud+YSHVf8qfxed5SGJEOpBH3Jz9eH5/NJmxv0eVtz9XbuN55vjoyyLmunLBZw5017AfY2VzHhhxit54czu9o1a2qZYQtxr67dkJGpsAQY229GutOSraPu7HxvTTRahLhiAImx+VBdcPrhPIQRV5z9a00NazJY+TvME1/PWa7ZVE0T/f6wNToGZmv47Nwr8MHqqyTv8JiAdpqQn6sHGX7c90EPQR7VHdSDM3ZHajgzZ3ADFQvZjRg5CqPeHiXMTxa9WFnzFvPPebKOJLPyCZ2Va+HrlEwaOf9qYTtjx+SAzwqmcl8nk3wwfN5BrB7dATO238bO00qA53vKmkhJmflarJ1XqVP6/U8/1RyCNZm4g5oDvvq7wHGoWeOy07Ur6Zu/nnPGjliq0mkol4YbtYpAPCXA9S3h6/MkW2qhKCUZbB1qI6TFn+Y8hkW2KZWgH3ZsmNDEmLyfTIwpk1MPQyJr5ujnW6K3oy2XUNT8RK9hCj0FxqGdwm5fzf7pIvnvhTUF4N3qWWhSXnGw96qu+Hs+7KHLwVefjy3h6dWqVNG4T3HQH/7mmzh/8aLGNpGDMXd3si47G2yzLC8Lr11Jc8PrP5GCZD0/TXbZ4ZebGNKqghberHv9SUGdEHtqG6oGlcf43bnYuOeQ2MSQEQv3Caw5G4ebkYkY8uorcCVWTOSfC1BeHYke9iQXHP9CrjYbh5+pDGIpzd17BNVpnlkPv2Mtb6E2qjs44DO0I/sfdGWqjV2i1PH5fVCgWIB5zrh7mustjP2ri+/8VsVCa9I7l59fhvzUrl+yLyCL+gJ4+1RSMmUojgcH1dYtW2h5Aj/M6Y+/sQjxt6fgXIwdvj5PlqhaPHv9EE9ZwvWLK75WicpcG/ooSVn19qcsmvpSCfpXdz9Bk51iUG+Hz8xQJ64pkbXS5uezvgwP7TeFgqYt9c+1ir+mtXbIzwI/tVQMz8uSFSLzw+9GKgJp6sHQwee96+GnvZGUdVhh03al4YM5+wPIVFxKLfOXnv1w9WXw19M9kJCchsZ+ipIpZ5sbL8RR0dDPaOCXDk/6aIcc7FjnR7KGZJbPAXPZ8uWY9d3X6H/uokUPu/zQDftyWJiWhQHOdthGRc9386wQZVsOfiRZXNqD4GY0nPMHPnx/LMFsEUhJTcPSV6ppKZRygZzlEXTvDxvVsEWlPktEuS3DPWP+uS3MzqUiKb9BuDtYkVheIdTKb1frrlnjp5W7RSOXPB574y6nea3fUNHM4oA/ccIXGNbSBzvIuP2zTh7o/9tNNA7yFJ7IXMQ3xwintOfR0P5yMsl0/pAiEMda++GkGViWcP3SbtLi65S4vqVSyyUO+unJtxB+/BkkkblJ63XaFDQ+weI0ZVlUxC2G9IJuls/nJ7F+S/j5aqpmD79cDKpR9tQ0a9eooWns0f3icBPWwJcHY8j42ahVuy5mkkgYB/1RI0YiMiZa8zmZMer74nGB1c/VXrMoXKv7Loa+PdaswM+NQjdy/XGXZJ1PEl1x17lwgT1L16f07FyR+To6EJxCN+h0aCje6dwGfaOTLI4pqbQ4r6paHQ1bPiG4/gf37ydFxXK4ePUqBt++DmedpkJ9B+J93LAph3o6goL6ts20zsfll14T17hr925yEEuBDSk4ciMbS1tcomK4tKLkBa91ZRfhiMWQGitq6lpQqo/BGf28A3dRizqgZ/YtNK8xNjnqDlx2yuKmJBn0ZYY/vW+w2AVDQEnE9JFDFoXt7exx9sJ5i+9BaX9Qau0vvuSEtfes9QZ9EYtUbwGFC0PJcH3dYi4fR031vB/F3N9a5qFxYBYs1eEpcdCX+vmmi7iGTdDNbcpS8+fV+vk80WpevikDdFP8fF2IiCOOrjyDLp7/Vf10hLiXvQ7cppTBJSUrbyD6BssjO7d8Ea+O/kpw5mVxt2bVagRdKd8SKeSl7/NHsmtR9rgHX3bxFdkrQz+rokMw4/dVeKeNv0YpMyYVGL02imCiLL02iMw+SazYBlfT3PHL/MU4e/mGOBwHpZCgINiTO9acefMxp39PdDAj2IbSgnKC4JaXs5XlnAP1BnpI9lIxuBJRPwc+NxAdO3dBhUBFnuDtYW8ilsxeqkff1dr/YdrPTVs7pHv5IppgDh7ptDCx2Nk0TxeTi4Q1nf+K+nXwZL/n8AUpjrZp3RrrN23W4OQc6Jk6aSy488Jav4KLlnGNzPA54H9FmkgM65gzpKkKb8sduNvoDY9pvNJn+NmG3kLO4aeD6Xj+MUdB0+1a3R4z9ieiYzUXwQbimgYvwGVlSM5+dKo13jnuVLq4vg6DR3kmlSuX1M0iuL6KNaQv6Je0mDu2GgkcNkoXMLJvDYXNVpxR4qAvm7J+O0kQwNWir8cPsogrg3Wxgn4p4PnVyERqfBMlsAa12AEbe8/i3IP7uu1rJK+w98DBIv638qAM36x+vyOmHcnD3qOnsYWEzHiwHDB/saVZuq45h/w8QzWTdscLNg9TAt9u5QofD2cccKFO1O9/xPONvVDHV+HbLzmVjOEkzqbW59d38bxw3LiXTAJh8dh+iV7ficL4ROtWaELUx3JLFxrMsDnQO9MjWJky1FnknFa5Q1dU3LdNbL+TfreE9tO6ZUtxSFcXipLlrPARiaLJwM9sm52//CAWCt5XRING6DpoMNqwYYtq8HaLpn2Pj8sp12VscND/w8MJ4xevRLu2bYQsclWiZ7au4iIkKoxBN3K/DJudvZuCd8karnFFK8iA3yTYU/OGxTAY+962re4t7oG+/UoqJ++XF1Nfb28cIFN3Hq+QcUrYlVDBLmK2FrulScOWt/+Jx3ttPDT3OZgWia27dpm69Af6+5t72yIvLxnjj7rgUppih2rMNN0SXL80m7R4ciztzC2pvn6Jg74s4hoSWeOLUzpvjcsv8HZGO3ELgrN6OwWa0a+fr7WdBp+3nJ+vxd9XDquRV305WDE9LyvcfPW3TWZwav697reRuzajk7Nwl4TVjpOaoxwsj6CoOhZqtet+lgMEqzyOaFcJThVrYu6aA/iuq5sIOuvSmmD5mo0IIT9bySqxJBJwsLru3Q5T56/AmzbUJUxBXd9YQ+jigTw7VPAl30wavmTCnnjqCIaR16lTXh5eIDydTczVgm8dqFN29OjRaEKG4BvXrcX8H6fjWTd7XKjRALN++6PIYc6dOYPvSZ8m4uxJNHCwQU/Fd8boWEG+Kff8KqCtf6bZgd7QDntQf0N6Vo5WAf3TzUlkD6o829yUZQiOU/vo6koudKJ5aBZQTjB72HRdNn/x/a3oZkNicDECaiur8stRoV8iJfZvwdn/hewUTQV9nivxFNF/dDN1qcMjg7xR6qYJ8bX70Zkrxdey4YM67beZevyK/L7EQd+UaQof0dIirpb8gk7QL2TNKEG/JE1ZJcXzpxbILjDG5hrQqdg34X5/gLHasWSUkkva68aGl4cnDh0/ptnkG4IjVhJFUtL21E5Z0qiDu3FZDkBmlqH5tTBp2R4tJUf1MfVJO5h7/Tk1umH8nC2oHH4XzfVAPNeoaHr1ic5aQZ1F2fpmKTWAuak5+HHGT1qZ+89PtUPF6Hgco4eAYh660ML3s7MNPli5UfMGoO/87obfwUvPDxL7NobtMzx0zdMB73fwMCurN3cueDtm5XxMPQ3DWnppVDMv+PTA2MkLMLGrn+ZnclsuyHL9gAN3I2ps+2PZMs3h1I15UoaDawZ/HotCenaO+BwHw2bkRbyYxPLK2lBLLr92wNmsoH+/cP2i8sqSk68sL7pOWsVl8PA+pPiaJQyeEgX9lNhQ0RxhrIhbkqBfEuaOrpSyXqomnZzE5vk8zRVZU+P5AZRdTmqWXGZkFwx9GT8hHfo169dr4Bp92+kT3GLP2rnz54sFg/HngQ09sPZ8MqKSMw0G9tNZlfHJ4kNagYebstjblQMJ+7qayz/XPc9td4hCWKEZbpGRii62z7j9pqYtMGPWbM3HnurWFW0TIrEqKQOTf54lAj5n67Kbl4P+wMuFjWv8wQk1AzF97VatQ/Nnbt+8KXyEE0jW4ujRI9i0dZtRXJ8hpTh/Z+FUpathZG7Q5Plyt7cqgvlzwGcP46frexWRZLgRl4fNkV44eeaspl4gs3zOXoe/MbSIB6400eHzkqqdk7bfVVE184UMhmR5mXv+D3I7CfF8dsQFV4lYogRYE3x9ib/zxob4+jq4vq74mqnOXAnj8CFyC94rTCtuGpZZ5v2sbJeLGn7ZFnXmlijoS6es0ijiPmjmjjq714JutKiahfx89TbqoN/BR1HUdPR4EgGPTX6Qz3ixj8UNNx8QdVBANoRPyTngHbV7vAF2HTlN2esL+HziV1r7Znz/yPETAtLiLJBH/3qOmkD2+Vbq+MzK0+DLXJR9a008Qm/cFVTC2n6KLyyPO+UCcDspDxdILtjX1U6Yg7cOsjU7KHLw+rBHCNZEVcL1VeuLBP4F3m7o99ZohFKQPnb8OFJvXUNHUqkMb9hcsxj8NG0a3nr3XXE++oL+4CpBWEfFVjkW/7YQDRs1LiL7sJY1gL77FgOQWSTb3+tkBbc6nuLaQiNzjRZqdW8kvw1tvpIp9IyYwdSxhodmDj0qVsXBkxcEpObtYqdleM73oQnN5zN1lFoDQzNXYzIFdMcCeRz/AsoXFVXj52LY8GF6EwL+DCuiPtWju14fhGI/hPfxA2oWzzpi8fAwGfR5IwnxWBj0LenMLWnQl4qbLN3uFdS7WLNaoqBvfidu6TF3ZDauBOECjJ7vmyzIFvxpVqZfgiKuDJjv1lAaslju1LOy0hJe1gfDPQsXLsTt22FEIUwmaQU7bBxRE9NO2GHVnjPYtHlTEWE2mQlyQZcHi3fx4EBjT/jdmyQZrGaQSPVG6duqnhNeFPKyUoVGzO288jhy/ibicgiboZFKeLVdbroQFWPWiLEMefV1Z6w9dBs1crNQh5YwxvolnZIhxYofjMeF774U+71QrR4WLFqEv5YuRejZM/Dx9RP6PYsnT8SYw9pywhz0v5s0WQT5A3v2IoQaq7jYy5DOun/+Bjct8eDmNp7H0cgWHH/m+h8lOuZl+t2oTv5a8IruMyGuPTFfUDNdKZOXw4X+zm9C3Akt51h37pp9pzTl6Bqe81sB34uqXtai4CuPwdtyp68bdTGvWbu2yL1lWe0Q1xwB0/WYfaWAnZIPhvuCgiphOgmtGVNoLSvPu2zUOnrPHlMu2ZmEeIoUc3WDPl2YrviaoWLuo8TgKVHQjzj7hSie/HLEETNvWc7c4U8Wdugq4VRXQ9+QPaL8uhjsxBU7k5TO4iprmm7Kmt0ihbR28suM1k5xv4AM38ycMwevtg4SjI3+C2+iZcvWgrOvHpwNvkGwwNe9QsSPZUBim7+Z/QKKUAalhgxz7ncMVz7Dg1UeM3Ly8eOeCAxq6of911NQhez7AqhYGOLrCl9fb9S0U3T9zRnM9Dl+OVIUHzccjcTb5BLFXHtm3wR9OB63KegfK18RAdVrU+AOFBk+Qz5Mu/xjyZ+4duUK9n/1AYaGF9JaOei3btkKg4e8jgOkbd+KguIOUpXkYm/tevU0p8WLwE/TfsCFHZuRb2uF2sHuBoO1vmthyIsHw17MvglLpKYqCvoXo7PEfnZeS8VLBA2FJeRrNVq1+OGK+Jza1lLunxuzZh+4p6HF8n5ZsG3B/tv4kvx1dY3pWbd/5syfseylSth/KwcTN15HnVq18P4HH5Zpr1x985mVclv0DLG5irm4vrnF3Pshs8zXoI/BY44GTz/fcviifTocXFqgUtPi+UqUKOhL5s772xyxKd7yoM8XX1INfVPyC6XZiSsXEUnVLCsyyuYESX3bNG/cBFZ5mcJUe2VkZfyx7ZReXXXO9sc9SebeNFi06/WltzDlmaAinq8czMb8c0vTdMQYMVv7cXcpe+wevZUkmCD8xHhTNlmrVk0kJCQgKTFJLPYJ5IHrZm8NP/LFfayik1bGz28Jd7I9Eehpg2//Pk85ti3q0xeAB3PIn6eFg7F+GfTHUC1DzdhhPj5j8VPIwrAXMXl4cL2j4v4tGBirZPCfBPsh5ImOBHdkYujwkdi5bSs6PNlZBH4H4qhzz4CLiwvOENMp8t49Ycyu9gzgYO1PVpHMd1ebz/C+eW7CE3NFYGca5qW7CSID54YnB9ovj8TkJNHExVcl6yi8yHImz3CNlE3mY/7xfGCRAvHpaAf8cSRSwG0c9Hl8sfmuXm69ZO1wjeXZP+4hOdtar2Wjpc/Wg/6clFuWuL4x2iafm6mgXwj9KLx8S2SWDTF4+PiW0jalzHJuviNqdThYrGkuUdA3xtwRE0pP7f2ga5pi7vCxixZl9dM1i8vcUeP5PcorXbiPAp5v7KngbO87CoJLX6mOBJL1+GD9PS3qpvzsY5Tl7hwWKAIJwfE4H51TRG5BSiywfrt/xUB8v/W2aLTi4OVsZ60xSxfPB93IL8d/btAMnHWDdhAf3NvRSiNJvPyGF3o1C8bJG3HIjr6Bjk2q4GyCM3745zhsctIQGZWCL60JRy/I9Fd9/TnyCIb5YPZCQbWMpk7jOb/Oxw/UiObl5YWPKfv9Hzlb2edlo+GGf/AYZdtLvW0xnWScWTe+W9euFPCfhLOzs8jyjx0+jGRalPzKlxfwz6vUB3Ex9BRqB7gK7Jyvu355K9KsV+AbDtY8X/wnz80H6+4hgWiezYkO25XE7Z7p36/IrfmUmFbL/1qp1VvBNMz9N1JEPwQHe35L0udjIBaNcj4YseIGxrR0FveI6wpDVtzV64rFC/mcfv7iHAYuvIoBfbUprcWKJmVgY0ndXH3VCcvvWBfh6svnjv8sNcVNE7RNtZqmLOSay+CRBiv6FDclgyeoxS7YO3iYPfsWB321hn79FQoeqx6mhNZMae6UBl1TBHRD0E7BwiDfTyxh7jyKeL6hJ4O//F1qugtWTfOpl7GbJAPUOC7XAaZ+M14078iMU1c4TDJKHMkVShqqpFZ/GrPWHMOKTbuLHJqpg6+RuuNoI2Yo3DQUExuDvIxUjGnlDMeQRti19xBGdq+F1dfsMZv6ApCtiIE529sghzR1ZNB3f/MdhM8hy8LUXLxB7KNooh1eJm8BORjm4YLt5K+/FgqTw196HndDT6MBBWSnCiG4TNBPgL8/nnr6acL1ybKR7Bk/+0opcvPbQQRl+Tz2HzyIQF8XWNFDz9fOxVf2rOVAz4VnfsNhL1oWVLsbn4bzlxn1NzzeHTGCunY3aUTv1FuqnbAY2nprdRTsshL1aiPxsXnwPX1ixvUix+Uejic7dcK+t6uIoi/TMzdv2fxI4PeGZk9257IA28RzypuTyWLuA2TwlGbQt5TBY3HQN4e5wxNuKUf/QdI19Qd808qas1uULe18o5HExC/ZbOXOndsCf28+9YrIitVZKHf2OiffwJckA8z6LfpkA4avjsPXJAlQLqQFosJva7B5Ngv58Z8TRUTFzLHn46BfgUw7eOzctknAGTc8WuHP1RsxrmsAriAIo35cK35/6L3q+JxqDANI/01m+rnfjkeb+GxcJIOSVxPStYJ+R2pIqlKlMtq374CGjRthEtU3fv9zqdZMMUPn1q1bouh7nRaMfXv24J0xYxEfF4sxLw2Cp40VjiekYe6XQRg6/rb4LBe7Odtn+ekvuviItyc2lR//7TS8T4uFuhfC0G1pUKeuRuJabsNaSQwZySFrAv+cT9HASer98e9PRtDbRxN7cO1lyMjRWm9VbKU5nt5yDo+uIUxt0nPJNP0R8cg1NG8S1+ffD9qtqJmaDPq8UQkZPObSNg0F/eK6aPEpW+qZa3HQj7n+h5A03XPdHiOPq8l/yu0oaaZvMuiXlLlD56irp1O0LlBYVOZr4n/JAnM1x3yMb1p2bBENfQnM/bl02OLAqS/otyX5giGNnUWw33o1F52raevMs6bLvXRrzOjhIjDmpKAnEXr+Clq53BKnwG8BGy5n4RTpx+8jXjyrO35B3rjPvWTc4Fkd9CV2zvtjqV+GOXxc7NG+bgAOXE9EbHQcPK2shGqmbtDnz3zvZYvUhi0FdZOLsP369EYV4p4vJpXJ3Tt2YPOGDeje8ynB5V9DpidsNpNC7KbfKDg+//zzGq2efk/3wso1a/FWs8fEAvMhYf+ghq46VLtg9g3DLhciksU5cj2DB9cw+C2D7SvNCfpqmixn97fJZlLtUsbzyUM2xXWceRPvtQ8sshivOJtNnge24PuT6RKo1VglZbcPj64OLg7rNueZ++yUte0krv+/E1R3SSZPhALgXp+TlkkGj2YxMILpFyws6mYvnpOSC68Z5+pLDR5XvxfhX2es2bfB4qAv6ZqmNXcM0zX5LBnGeSgcfZN0TeP2iP8WPF8+KR2feALpKQmimKsv6DP8s+yFAAFT6KMSMl7tUzEYl61roHLUNhGMEq18cC67IlpZnxaQELf289vAiRuxZmuyy6Bfp25dJM6ejo0E0bz6bDtsOXIdLSPuYTeJqvlX8RB+vrXi0zWdutcpIfb58AvEfzcePeOUAi2PcR62qPv8a4LFw4Ff6u7I37P3brcePbQ4+YzbM91z4dy5eGXoUCHOdp0atcrHRmgE3VhqIc3bCR4kaZx1JwWV6MG+5OmEdlWcMXMnZ9F5OH3pMrjz1ZxsWm1oIzWNLsfko0UQNXxRwFdr6/C/06r1wNl9W9G9SuG18jXxW1nrYBtx377dGatVpFVqOVMo01eCflmxQTQ7ehnY0BSuX0Rtk/djKNPnXxXAP6YYPMWlbZaUqy/lGIrL4LE46IefGov0hG1m0DUfVY6+cXtEiedzc4R7pWdK+pw+9M9zUK9A/rSMxTOm/+uvv6IdQRo8GAaY9/NUgecvO5ON5xrYas73UIwHNpwM02jrMM685VI6Gnsmi8DE/752O1IUNYOI8cPerazSuHEjyRyQcJepIYN+S1KnZPplA9K/n0IFYVAQfY+E0WzoG3mKCr3r07PxLNFDpS7PJdLMv9nmSaTt3YLPCN6RI5y2HWbnRCqbz6FK1aoaBo8M7LzdR2NG49spU8VHeGF44/XX0KBeffEWwNaHyaRa6sFCbkQ9lVLMJ2mROZydh770diB1+WcQDbW6XTnqCM7G78T2WbprPxi20bUlNDQHQj7j/Q/gQbr/vBjzwqluZONgr7sIMyW2HkFL6p6J1edyaKG2Qa9fr2tp5/9DxeKxH7wv4B0O+q50jmrtJVP3pqz+XhfXLymDp6wG/XpEVvizV7pQA6jadq/Zt8PioG8+XbP0g75SoLVAaI2Xc9mJamamr9WtK46rvJ086vx89RPCGvrPURB85rHyQsN9xr5orS//M5T5dqqQITJ13aCfZB+E0w6PY8mCuRqJX6ZVJgd3gvOVNeIwv53IIikCJcs/G56IFsR3n0+ZszlDN+gb0rlxoG92hkp7+3/5ObiekgFn4u1vI7hHjjgKwgu69RDdpayWuWnTRixatlwE9+MkrcALwSrSluGCLf/s24kTsIc09xsRbOPatCUuH9wHR2srDCUYyZT2PjeKyW0WBgdiwT/rixX0+ZwZVouIjKI+ikoY/FRL7Np/FN1rKB236oyfpasXHE/DkZuJ+PYpfy0l09lHMvFmM3uwH69zhRqauee3iXbt2mHjsBr4dDPdm7AEjdSyOfemrG6ji+uXlaAv6Z5qXJ/n0FKuPn/WEg0ei4P+zUODkJ1xAaY5+mUo6BeUHiSrp7h0TTUb6I82SjNPlXYny+qzb/Z5SfnljcOq45udibiaAC2ePmvyb3ndXwQZKbfLO2eJ3yAPJatcEVMLu4+fR//q0GokOnOP6Z3lsP92Nn7ccQuVKlTADmp4MncwffE08eE//OhjLPvwHYGhmxqM508nGWUen48bh2W/zIA/WU1H5eTBztkVsGD0OwAAIABJREFU7foPFPAOyzGsX/w7Rnzymcj4mZHD46133xOwDy8K58+dQ87ereiXno+rjuUQaIaqpr7zm0wsIO7yHTRwkNmZPu9HrZLKBeLb5P7ds6a2JDMH/OErIxBM8NJH7d2LNMrJGgy/Kfx1Ll3r3vIb3ttP+IpTnriRCvVGKLSm5r0s/f767kbidN7cS4bpQmdQv3Wi8hvlP7rwzINQ2+RDl0bQLw5t0+KgLzn6+uiaYg7N5Ojf125cGeRLySJRFn4buubj/YYpYHPiSs2XlKVn3aJz4easuuXtBO2PWR7BNeprskF+C3h36GCsGKxQNaXkLh/oxb+zyALwLv0uRASaUOcnsOb4XQRlXNRsN/94JnrXtheWe/b2zlhNMgbmwDryQhhaWrJkCdZt3qwpnJq6SKmdz9txYxZDQ7IRi3/G4ml/TPwMTW7cQhAVXdf7uWDYwhUiq/9o3Odi98zOYQZTVmIc3gi7Y+qQJn+/kOChvv36YdJ332HvIcUX2NzBXdO/kuidO8llMHuJC+LyPjCcM2nHXbzZSjGskQtAKsFgGwgS4sGLNfcNsKRF57kRWm9xLMFQj/SjmH7LEE9Z1Mo3d57U2909Plz4dn973AUnqZirDvoiPqlwenUBVlkA8qkJi7SpCvj3D6NBy5yuXD5V6aJVvs4suPm1MGuqHnrQ57Msjo6+bmMWf/5BdePKoN/KIw8j66eKFugKTWaaNdFldSPZmDWuexVR8Os19zrmUsHyifbtxCkzwyPhylF8/5QH/grNJqG1QjyfA8xJ9yfx2+IVmPaUpwj8GYGt8dGS02jqFidYLEwxZEmGW3EZ+N83X2ucucydD8ad586dU6ygz/teSLr7++PpBAtGr65dEHjxBOzSlFRdDRMlUhE2afi72LdvH+wIDjp3/oJontpAGkRNqDAr3bfMPWfd7Rjm2de+M7gYPZ/cv4rzpiP3xRAbG9b3bxooxO6YBsrSE/+cidXAOdKdjP0LBjdx0YJ4mIfP9+KZBRH4Zd5vGk9cvr8H9+4g7aVa+OaYEzbsPYYtW7YUa2G2dF7u5+c04msXnbAukpu0lChvkMFTUq6+hbr6fE4lyfQfWNCXHP07idbotqUwCKhvormZfkmDfnGF1sQiUfCf4sI7MugPqJCL3lXThF2ZV/Vh9/PZve/7Vksw6Hv9Z0w9IDtMNPjoZvri3hF+vzkuCLPWHsTKQR5CW+ejf65hUp+qYjE/eCsDfx0Lx5MdOhTR8zHn4hje6N69u5AQ6NO4vqBjmjPuUF9OaJvOOBN6Fv3jIxFF56JPg5/3xW8Gb/y1HnPGjUHIhYs4Rl/gdPr/qwVQUq7MKsw5sJ5tGG7KH/iK8Mm9Ql68K6hAa8loRbWQ6Ng4MbeHb2fAMyAIWw6dFQV2meE/08Bbryk9v3ExX5+tF58f+o6Gr8/z27VLV0zroxTVhy29il49e2Aqiaw9yiPuyiwk3J2Nws7csh30ea4N6eob68qd1ADoXpOeBRJ89KlinP4s76dFmb6pxiyxopoJ7zzooK8O9FpFWg0UpEyNVPDUvEXwYkHb8GdGFyhrPurMHXWWz/z74ati0aBND3xKDTtyMH+/gUeGwaAvtwtN8kQ9t3gRVNjPNcTTlrRzrDFu3S00JLmCkhhvsPwDB/3hfXth4HWlAcrUyCa1y7khVVHuXhiGp2nTGHU/u6dFEwwZ9xVWk/+uOWbnpo6t+3teVJqQ+BubsMeTFr+umJ25++MA/eKg55GeHIeJPQNx5l42lp2IQocaXni1iRPh/WSYTeqa+sa3u1IJ73cWxdzH2vbAJ2SQI0fn9u1Ryz1bNN6x/k5MGkFD1LNQHBjO3Gt4UNslR2xH9OWxkJ25DzvTv19SDJb45VoU9KUZuqHGrH9z0Oe14eM6majjnSUMDJx9mz+o57hUj8MB5OlevRBAgZlpmqwPM37jnSJFRsZ8m5MKpKFMX31SEl+ePaAicuiddejyMHw18ZtiQzq6F9qsUSN89vnn2EpwSz8z8XD2pr1DtE2WRpAUSn0TyHLI/pOmkeBbPNb//AM6keibse0tuQnyTeLdUW+hd+/eBrWGzN03Qz0pMXfg7++AhsSLnbv3NhYPrmbUe3juURKPe9xeyEJE2FbCQjKhkYMVVD8aPQprXqsgBOE4269BLKb1JDD3qA7ppBXFZukEW933oE8TVdKuXEsyfUsatCwK+qYas/7tQV8yd8qaCXpxvqA9O7PsAqlk9g4R0r495xKM0qtvEQOVNi1aoLpnOVHkNWV1yF2fzzd0xa4bmdh+IQY16zUUEgoMaURGRgrlSDsbG7g4u2DRn0vMziR54XltyGuCSVNj7zaDHrnFuX657epK5TFt7RZBz2SZZDvi3+s6clmyX/VnZjrZYumBY+A3lhkzZmj6H0ztlxfm1155BVmZCs7k7eONPlSYrk+Mm1dIs6hBPSc08nPElqMJJn2I1c1xea4VikBMnO33qmalaaBj+uZkKmT3pEXqUR2SwcNyDA+jK7e0bBONwTuWSCzfl6CvK8Fgw3QpYYyuwD5SYkE28pekkFtcTL+k8E4FKvpNav5o0zU5sxtKnaUvtagginuc5U/cFqm3MYcx/7b1AtGBIN8WAab5ivtu5mH6nkjcikoiGWIHEYxYCO2N5p5oUqM8Mjxr4rftF/DPwUs4cPSoWfGEJQlYEbNLt244Tg1aJQ3K7Fv7K1E6WxMHnr2DWS2TlTbvrF9lFiXUrJNWbcQc/bFf/a9YdE0O+F06d8agx/0xnO4RZ+B/n0vBjdh0RJBMZxZFgmwyc+nTKwAByTZoSUqaVYmdY2jwgr3mQgYVfiPh5elbpJjMLK23Xn8Fq19RqLnMtnJycjX7HhV3Th7E9mqZ5ctp2gyeYnXlWijF8CCCviVduQ8k6GsMUgqeybKksFlcsbXHCuiajzJzpwsVVZPjo0WXJw/G4Ws0al2keMcdoe+OHi06Ns0ZvHj8HZqEg1ejUcnbRSOHrO+zuiwSY/tf9scfmE1GL4tIDO2zPt1LzKb5nUxvGr3wmhBRkwG/UlAQ/pr9E3rn55bqm4T07W3WvAXm0DXsIUVOcwb3DFw7sQcz+3oV2ZyD8q/nHbGSXM64zsRGNX8RbVYtzWDoGMYWeH6j6l3LVmT7DANxAX74G28YVUE151oe1jbGaJv/Bf1i3hVzHLN4l/dNYfMhiq31LNDQd/HuDb9644s5cw9/c+a9TyQpYXbAkhrvhrTWGTv2zo/XK9urvhIpplbJ3UbY8rHI2CzSaDcWhMauS4BH9cfNZok0pmx8+vQZmPLxGLyXQdZYJRjMprlD3/o4gplAsglPJySa7K619HD8VsH+vMkpqSTelmI2c6d3z56o7Zxs0EDe2isES0NJkmLZDk3QVzfOGTtfltmYNnVqEeiGn40fp07C+qGVwMqofSbvEEVdc7SCLJ2f+/m56AtTkBy1SDB4loYLfMEwZZNP5CErbVqC6T+wTN8cCYZ/a9Dv/4jTNbkoWo8KgIzR8+Dgm+laSS+7hsXB5g2oYNSnVgb8+QcLLQ7NyTqZJZJl5Wh25svn0raqm3CNau/pDKq/PrQRxBIMZh5/D2nrxzp7Ii0pFhWDquKvNYo0hanB19s40MXogsu6Rp/TIstzwiY1t2PT8FPfiiZN5rn2cpMgIX39AtyhO/hxhfapFHWvEIWzp9mLs6nrepC/l7TNPWEO+PkaU8v/fUGf57O4UgwWwTuPfNA3qbtjmLL5SQFzx7fG93AN6PQgn+ESH0vCNaM6hogvNQdsXREueRBhEhK6D1OoKcvQYAXHeuWtMWRZoTUiOzypu3b1fZaz1LkkyTBv9zUsX7Fc0yhk6Dh8Ln9TsBzWNlAYfXj5VUB9KoqmpKYilbLnsjRukva+eoTfjYCHhzt53Xrg1wORZjWo8X365OMPMaR1RZNzyUqmQxZfQ/i9KHFYNqJnDX9jhvKM7w/+8zb+/PPPInPPkhxRN8/j94F+Yn+ss59N1bhjhPk/akMyeJi2OSGUDVX+C/p8D/8L+jr8fMnLN8TTl0H/UaRrDh8yBNt27iKBreoCemFGx+ITiXo9UVtTR+rXXTxIeVNbsVF+8aWBB/97+LKr4scM66g1300FiRmhnjh5OxH/ECfc2OCsNy1NEZBnI5F7zjWFJn5ZHyz38MKgZ9G9rh9WHlekHKqTA5cpKqQaWzfnGvdRJ9rY5aGaTTe8WR3LzyoduPoGN3J9uysRyfbli8BNooBMvRl/vxosnpGfzrA66BmTbl/mnOeD3ua/oK9/xv8L+v+Pgj531+4/eEjop/Pg13x9X3whpfzTVAT7OOGp2q5aAmpcCGRXKInXf7EtWcAL+oqJ0hvW0JedM843Vur345WfYVZJ3759hXXgp90qY9b+e8KcZN6v84TZSVkeLNfMVooMvfBgf1tfb2/sP3LE4Glz0B3Up5dg0Rgaai0duc26tCaYOFtx/eK3rR417DTieLr3gYu0vs7WmHfgLrZu21aEOss03QFEB+U3tuPJ/hgxdw9WkalMfRLee5TGf0G/FIP+tT1tkJeXjEFrHRFKQk66o8y7ZpUCvPMoZvoi6FNz02Fyx+LBUsf6ONv8pX+dtFsSSTvncnSmRiufPyOzypnPVVP4/fT6z0GYA9vMPgrThN8gWHfn2foOJhklA36LwOff/aCXu87U0mFkWJJJ1ERem/kYVbwdsOtyHJ7uM0CYmpfl0bB+Pfg42cCTePpWRBPjoM/KjZUqVDSov8Nd0qc3LxbdscYGB24WVRvc2EUD5bBgGo9+TSqKAjAH+/mHY/HD076a+3CNFtp9N7OF2cvKM3GoVLNRkQ5hdRGZF+aBC9k+c7JeE/eyPP8PKuh/3SwDHx9xeCjNWTz/DwTTN0dhk0/m38jeeZThHYnps7gayy5wls4aLodPHNd8dxk/P39kN+Y/6y3eBKKSMzX4Lm/EWjsLLlOj1tVLGmVG/jlbF37QqQIuRGWha3V7g5gyw0K8WMgxmOiir7/7aZGAwvaF48d9hjuR9xStJBr1q1VCRnoartyJJa65J2JiYstyzEFwUCXExkahgocTrt5L1Jwr0wUH9O0jNP11B0tJR5zZIwTu5NB1ylL/fMaBZDiTqQyLsLFBDQ922mLVTL6/1XzsteoC3DV9NDwDruQPUNuPzMP19GdwE9mQFn7ic7yA/7jjJnbv3m12M11ZuSlSVz81qxxeO0BMrfuE6f/eJRGLTrthwz1F10uycB4ET5/nelOXbFR0z0VA/UVw8a5ncvotgnf+Pwf9RQU6+o9qNy43W4V4WousnDPBLzfehq+fP3oSQ2Prlq3ITo7BL339RGbIQf8WMUJY0Es9OAh9uD0DZ6+GaX4sg74+K0X1Z/mYYYk5mkJy/4U3xfEzMkg2kkYyFWddHOyI4piCJsGeGNnKndQ900VW+yoFosUnkpDlVQ2RUVEIbZmDS4H6Bf9MPvn3eYPWS+Ox+5+dQs6AYamL0Vm4HkumLhSgy7vaYsPZSDhS81MG+ev6+fggPZ38dMlJLCo6Gnlp8Zo554yeA7qxwuxlp8exeOcFbD6geDsw1FaJGE6+ropctnpw0OeF+dBN8v4l+Y25l32xcm8opv4wTSirSokHec9ZITUiJfeRLOTydcuu3Od28ZvT/Snk/topCfbW+Ri02e2hBP3iKm0+mkGf72aBc9aD7siVQf9RNU9h+QU26B7XvbLI9jnz/mFPNNIpqKoVGjlLbBHkiO+2hWPniJAiITLdtxGu5Adhwo+/C+YIB33WejenQYiPOW7DHdQOcMUTjWvDMyNc7N/azhHt6gcgN+6m1vF4+2CPcpqFyCW4Ad4cMRKD//cWTj3tc5/Dt2W7/y6mBZrVa4Yhrw8RQZ9lq1kHX213KLK0y7lwLOCfZtu54xw5i128m4C0TGoS83HA261czZpTXoh7zFbgHWMLsNRHqkNz/0IjN/HWxdn8ipOxSKP2hwBXa0x7Wln0lSz/FimktrdYJM6y2Su9Tz2IoM+Zvr9bBu4kOuLb/a4ILSCUPahMv0wH/X+DDMOjHvTVTkwS+9X9inGGfyEiGU/V80EEGY5PIHxZF5aRn0kK6oRjoTfwy5ojCI+O1+yqaz1/hMVTdhuThiAvewx4zEssMnLogyyYVbLpiqIzY4j2yfz+6NQcobr56qsvYcMTyYh1Mk9uufRCifE9tTybjb3frAUXcgPIMSvl6kEBtzCvfuulVOEX/ExImgisMw9loHkluyLqmDw/+2/laM0ZU2T5jUEOhmgO3lAkQcLIO8DHxQFP1/cyOHdSO6nDLzcxkuivv+wNx5RngrXgNrlv8Ra46bYQztuydesjB+3I63gQQX99n2jYWosQT7UsG6y/6I5Zl0jDn39AUF6hTSLTJVk/X6mDMgzEf1M+aZm0Mn+uTAR9ca3/UmnlRz3o871hbP/Djz5CFhVIZXGUfx5ARt5bLyqZZq0KHvwyjNea+Qj2DgccddDWDXuXsipg8M+7YWNtDR9iqDRt2hSvEkWUGR/dOnXCtZs3sZSUIBmm4GLil1ui0aWWB5Iz8sTCciFCSY861vBElxr2enVkJL7s5upKAf9VBAcH48s9M3GqGeO1ZWdwlj+ozwskgzwQnZ98EsuW/2lQJoGvaf/1FFy8l4RKtAqwLPXjgQrVcse1VE0RnYPwB6uvoWrlEHQjDaKjxAC6eu06idglU2DOhY8Zb1pSdI0X9YYVHfHTzlsCCnqvfaDm3vLCsPlKJhYeCIODvYNZfQVlZ+aLnsmDCPqb+kbhdrwjFeyz4e6oyHjfjHXCe7tdEEf//C/oP+KCa/+GoC8D//Tp0xFJGLLCgVcEqRhftrWxhYtVunh4323jI7JQxpaZEWJocBY7fksCrsZkYD05L6kHU0DHfzlBmH9IzH9BbFOsWP03Zad2aBniZjDQq/fD+PLd5BwspP2NHD6CunkPoEvPJ7HjRbcyE3ea7UzGX58vFIqdf61erTkvQ29VcgNpbL6TmEnpWTlIz8xGF3pb4rcsHjz/O6+l4dDxY1rXypTWwS+/jHfa+BldlPlNLSkzX8w/B/+IpBzS1rlDgd0eubk5YuHgIe0CmVr66aefPtIqm3w9YYefF17e4w674Ao90gadswou3hK7xDY++dgdU45kM/LxdpNUVPdTHNvi08je8qQ7VoSz9aLaIUs706deOrSsmIPPTilvwlJVU/kzX/Nv3oe+8V+mT7OyslcMZZA2yC5wWVp+3hU72TqJ6gAlUdlksbUPGv17vHHlA8RMmZPHj6N69eriS87/fv3117WE1qTdnrHoytnoj4fTi9AR9QV9Q/vh4HQ4LBMHbiThehRJMVNQYpqjj5M1YpIzMGrUO3h9xAiwi9TzREHlsfDWhjKR7Xun5eHVK9Xw9lvvoV+f3oiNi9NcJuPs3rTAXaK3KB416U2qVWU3o0wn9RwZCvq8TYM6dbFrhOJ8ZWjwveHBQZ8XmLvJeRi1Mhybt2wW934amcRzL0QFMq4fQKqmj7KksnoOtEXX7qNdIh1U6um/XCUP/evHU9FeWUgP33LH+wdpcaW/64N3/tc0B56OuXh9D3cN/xf0RQIqEVtpVyj+baSQ606EjvHNU1HLT4EMlpzxwdIbJLikJ+jz7/UrbZIBR4FTlvw9G6Jz0H+UFTaNRgbVL5m2N//ZQh0XY41Wkg+eTjozKTk2RbJRuYhM6lNNk+nLQ8ki7aeb43DiRiyZp9shoHx5PPfss+hBJi/SvYn567wAvEyQkZCGfmMoPNzdcZDkmZ/u1xMb+itfmIc5ZJbPBuubtm4rcip2trYCHz96+AiOHDlM/z+CqJgYOBHzw8fVAf0f8xTZurreIf/O2flvR2OLyF/Lua1VwVOwdT7p4K638Mv7ORuZp5l/vmeT9ySQdLLhRrGHOZeldewHFvR1PHI9KWn/qlUa6vorMSiFktAYyvyvx9pRgFcW4MyccsjOLYc65dNwLtIJ7x5SmGj/ZfpmBv2CdUBMmgzSy3vEwsU+BwuOEy2NVPY0wV1sXOCV+1/Q1/v9akta89WIKi4pf4YKufLDjAXvTwvGkq0nigR93qZ+7Tp4PMRdBCW1GqTEqp2dnVCd6I3TyZvVlE0fZ/lpGekYO3oMEhITywS2L7P8l55/WRid6Bv8xjKAuot1+fncG7F9x07EkXsXZ9ssoyBZULKWwk1wWfnWRYI+G8eP/eB9YmNVETpIxqid6roMF8TzbF0tMmkvrYD8IPbzsII+XxuH9l/bF8I9xq531RkvTDxrLd4EWEOWaJX0J8NC/KfyhmBoPPLwjgzeDMMU6uAol6sdtCkTL5gFjY+tjl7/rPZJCPJML/Wgb+tQG5WaL3kQz+xDO8YAgnkCEC1YJ7rSC4ZOiheGjzdG4chJhS+uHqwnE3b3rigQx6Rkoj35ujJv/TZhDS5k6vEbaeabCva8P+nr271LZ0HbfH/MGPy6YCGGjSA46hn7h8bkabMvEzt/Xi8YOyy9YGhwcfQXemthTrzu4OC/+p81aBDkITqPuTfhfEQqsZ8csfdSpF5zeWEc3+VJ7BxZ2eSzwkGfqaO8oHQkBk/Llq0fWSqmyYst2OBhBf1hNXLxZLVkgvQK2VbGzvmXAz749YaC/f8Lgj4KHLP0O2fxAqYxWSlA3DmIl0bQ/+rxdDQJTBLwzpIbNiXO9PmcFrd9tF2zzP2ycJAOcc0RgYddr9RdtIb2wRBC34VhRXx2eXsOTv0py42O5S7aclQsthZwDWe+0oidi5Km9F1YCI738fm4cVi6bCkuX7mK/pRZv0DFzKFfv4MjvT3NvcRS2y4kLheTKrwAW/rfGArcxgY/75UCKhjMsJlZ9elnnyGVmtQ0r6OU7VWtXBmbtm/Xu+teTzSmRitFFdPY4PvzzU6FubPlYoJe4xxT+3jUfi8Lud8edwH19YmhNlAR/y74j0ymZUctv5kpHyjchrN3ycZR/8k75X+/ycG+quFgr9Alio6SBH0pwxDUYhfsHQyr4sqjWtScZUpaWcyThrL58IP+nhsemHTGoUjQ5zcFvgHmYvr/5qDP2PCVixdxg6iVl+hPbuBqXjsEwxrkGIQMuCCYSIwQadPHmeS8E6lGNfL5OCuWLsWly5c1XbgMadhY2yAiKhLWRPmsRQVlfbrzUkaCt3971CjMICiIh5OTIxYvXoI1xJZZZnUEodUeLL4/9LQvZn47G0916yoWITl6+DjDhR6ayBAXlL+ZguXRCqvDmoxb5s6dqzfbl5/la91OQT6OisHsM6xPskFu26ReXSx7uZLZTVxcP2EdIE93knueP8/kQvuoBXr1+RZSNl30MnfMCfpFFgm5FtCfzwbnwt0+HzcTyV+4QTIC3JXOcvW4HOVMTXiOuJRghdOUMzJPvxE9GNU98hHkloun6iRg3lEvizP9B6K9c7+Dvg/VM+LoHccUvMMTKwK3FFAr+FMp9AJDquegX71YnIlwxSdHnGlbukuymGtSdK1oIfffGPS5KPrB+2MLMnBl4pwdbdE4yFOD55+4k4clp6hZS6W4yWySTlUdMGL5NXzctTKCPKzx9fYYjPn4M6PMD2byfDXxa+TmKcwGfYMzLH2ZLZt336I3Bt0xiYLrKh9//L5+C54Z1Avrn35w0gxPHMvGkvdm4bd5v8Jt00r8Gq/4CDf1cUR/u3w0DS98vZ9VzQm/XVLYO9xrkEJuWv5+fqhduxauXb+OhIQE4RtgDsylngOeF6LaY3gLBwxfFYdblNJ/3aMiwhNzNTTOf86T94G/tWaBZsiOXc64r4LfuKzJGMbJ0VHQP5kd9W8Z9zvov98gC51rFDYlquctMtkeC0+4YX1EOZFcysVDNmdxwZZjyuoeyVhKEtaLwiyDdx7JoM8XrjZH39I3Ft1We2uCuV4Gjx7LxDpEaa7mngcP+rJVcs9BVe9MEiJKx0GiTH19yvG/oK/nm1yvVi30auAnOPj21dsi88oesRW366+7kIx9l6PwRA0/DG/ppgkYzMnvO+s85vT1EUXZRaTJfz0+DyPeGoXnXnrJYLxQdwP7UlPQwnJWSLSxwmFHKxEsU7MLbRB1A7/Auykg5hbwyeVBnvV1xnvUAfODly1qvfkeGjVpgqE/jcGRDsZVKksjqNUMz8ZY36fRrHFzTBj6Cn6JT8c0Oo8dyVmY7GOHIGpnTaPu23PONmgbrzTtvOZshQtJGcKUnceFS5dQu2ZN3Lp9C2Hhd8EF7cbkXjV/0SKzT5Hn9TmiWbrbZGFIcx/B0OGgfiQsQ9NbwW9ln1GGz/UUdQOcpHKKcyFNnpUno/FYw0bFOr7ZJ/oQNjQV9IVAGp+XhHD435pMXvmLsUx/fBO6l5ULxfR4+1Tqyl13wR2/UFeu/LyxoN+ZePqbowt5/IWYvlLANVXILVNBny/YEqXNfYOisPCYDy7Ekm67Qz48qUGRBY38XXPh75KDlCwruNjlkXBVNnLyysGDuuCYpWNoXIt1xjv7qA1enenTxrqUzKIFYaUyrPk5/V1i+o+q4Jp6jpj5MWPSBC1BNQ7284/EIIyyxWcb+2MAySNTIl1k9F8Ug3txSYI22b9fP7PMszkjvXk7TMwnZ+dtbOnrRrrtK6Os0OFyEnqmK1kyjxrVq4kvYnR0DDUOUbMSCbK99PzzWLNunYb/LhcO3v6V/DykWpG+zcbNWPXXCvyavPW+wzz9t9hg6YLlAta5Q7DYXFrIvLPz8CKFkfeJk9+GAv2Mxr545wknfLkqBp+Hp+IrTxJbowj8KmXUERF3BbWTobRFCxZgwsSJmiBRp1ZNk+YyujeFi9wLFi6EQ34mYtMyhXmLbkOdXMyP34oXHcCy41rua2tsIL5edlBvXcbgF6wM/6LYQV9g8wprRmsxoGdRH54/uWUaHquo1PmycymBue2GqSftEE/hqHDxUGBkQ5l+IX9fHeQLWTvGgn4looZu6KNAStXbm+dudl8xfT4RS4Iy055lAAAgAElEQVT+n92SSSVQ6QgtjcFCSG/uci+VoP9p3UzU8c7Co6inrzuXHPS//epztK+pmHwMaUpc4TVRQh6hW3U7zduXrogaQztbKUh/+cUXZjfxTP32W8KP54vOT5mdJz1bHu7k17vvSjZab7yLj8hYfTdXG2kwTv/VhK9QhWic1ykoenh44qcfp+Pk6TOayxhHWX7Pgixf4uW8WKzbtPm+6/I035iIFd/8jh1bt2Dq8tmwomy+0b0UfBuVib0U2JfnkHYNeQr85WIDWzsrvHovE3F25fAb/ZvP1dvLixaydOqGThcLwDJaqPjvcjDs8sKgQZoid3G+Bxz855LJjFVuBv54QT/Wz5Dd9msZ2BAaJdRMmZorJRj+Dk3Uy8AqzjmUhW1zMuNx+1BHcSrP7dKP6RfJ9IsZ9GWmH5tihy/2eBRbbI2PX5Kg/8CM0SPOfoGU2L/xyxFHzLyln0BaEiOVRV1TEOKlfPlLY0QmO2DIDg+TQZ+PpZ3tF8X1x/2Lgj7DAu3atcOCsX2QQ3LFZ/ZuwYu1lcDDHbiRNhURei0MTnmp8HVRcPJQohA2adqsWFQ/Pk63rt2QkalkJE09HPFjci6uN3DGniB3PH4qBnUJ+x7oUA63UguzfRn81cFQPg+13RwwPzUPN6hT93kycVEPZvO89e57eHXMEGzvbVg6wtJnq+GRFIxvO1wsSC+88Dxsm/oik/B6rL+DCbl5IsMfW80F/jRlYy8ozTlzvW2xNikL0RkKzGPOMEbvNOfzrM2/Y9sWNK1EBWV7BWpgNpY/STtXJJ0lHtwMJu0TmdVz6k46rsTnYy+Z7TzqQ9tExc6wBEMhSUek48XJ9NlAJcgjEyO2uyFBld0/KLG1Bxb0oy/PRMLd2fjtpCO+v2q4a8BS0bUprTLRPFgbJzP0ALKqXVyaLewI/olMsUVatpX4e0SKDaJSrdGzRjI95Bno+Y9iP1fSBq1/U9Dn+ahepYqQW2BzFGuvYGTfOy86Qrl7kwt9zDSpWaM6ZdoeqFixIgYSxGKKVqm+VyOGDsUBCiCpqWmaBdXZ1gbvedijDomtVU7LFRnwSlcbzI9S2C1y2NHikJWg/41vCcka8GclRq77fLCdIo+PFnyFw921NeVLEswYx38xrSkGD3md4KZBCLtzB+5tg5C457bYbbCzPZaSmxxf02AKIM4FfGTdxYyvzcreBhmRCjSgb/A3y5feCA5Q13FJBnsjX7p0mYzkU4QLGS+iPm6OWP86VX8Lhmy+YwbW8itWRfSTSnL8h/XZBxH053ZMxvJzzth4T5F4edAGKv+aoP+/5lloUyUBGdmkt5Jqh3QK5BzQGde/Hm+DyFQrXEssh3Opphu05nZMEsXcF9eXp5VYydx5iD8s6Mr9twX9Vo83QyXSK+NmIB5PVnMUnHzZKdu6ZQssXLzYou8tQzoz58wRXabq4VLDFzmJGSLg8QKgLuCqt3PvUhmJW24UObaEh9ZT0XRiAQ1SdyOGT1aSoJuAX64sx5n6jhZdg/pDHPDrHbPDsmUrNfRMG2I6OXUOQtKaa+DrSrkcjXf8nDEwVikuS9hJ9+D8uazhNeHE6SG9IfBc8PVmX4hHWpjC8OHBBe3hb7xhVs3EnAvkXoiBzz2H7JwcHCLbTBns+c2OnbTWnk/+V2b6X561K5hPZZY0eLuYZBV3vpiZPr8wJWQr+L+hoK8tr6wkyfL4ksOm+XdBDm2u2NoDD/p/hTriywslz/R1G7R+bJNB2XkWhm91Q3zBrMhgXdyu3AVPJlLBNwPf7vPDPioMl5S2KYO+b43v4RrQyZzvWZnepn2r1iDyDib08MfBu7bISI7TKDGyTV5JDLFZCExCOnISOMONHlENrpQN25NAmcP+OCSTsbq+kfhJfXj/cBE56dlwItw5jYqPusVbY3CJxPdHvTUMO4LvlMhli2UWGq5Nw4KfFuDTjz7UdN26kjVhblKmODc+X/dvzsKvYQAWXFLE1p4lBo+hRc12WB3Y0+Jwp7oDKiy7i7OvBoIXlmxiRqkHwzyVQ4LRjOQnniA4rl1HBac2dzDnf93atUJQbzRJarORzpXr10TQl5pKMujPOxyDBi06Gu0LMPe4D3s7daZvbtAvDnNHBG+xaJQs6KsVOIvbjTs8uBxGNEsnm0TqoK//hVlTblEhNynqECLPD8OJcDsMPmjYwMLSBq3PGmXDzyWXGDcOJebqT22tCK9p9HeKxdVnOEibwfNsxVz0rpoGjwpvwqv6MLMmuSxvxAFh7tSvicbnivY9emPd6lUY1txBSPluuZRYROvF3GsZTc1Ta9ev12T5MiPOoezXlnx5T3Z0BwdSHoFro4sEfg7yoUOD0OD3u+KtwL6mp4BQ9BVvjZ0TUyMXEP2RJZhP93WxSKZBHfD5zYFZNqxDz8OGVsykI3fAwT+8l69YpDjzf+LAPXxG2P5SwvKnE2zl9Eot2JxORNLJCM3pujWrKJy/at7OguPtTGRVsEMkFQJ4Hzx4sZPDi95cggIDqZB9morcTvCmwnZAgD86dOiA5q1aFYHcuB9i2bJluEPSFzY2NnicqKw7du2iLuZlWLpkCZZTEZ+lrnmwfWUlKqIHuZfDy4tvYuu2bcXuFTD3uXiQ28XfWIT421OwJ8wBP11Vahhq+mVJ6ZrqoK/bzWuKuaPO9EsS9MdWIxixUbqIR741hps1vWUu6PNZP0OWrE0r5GDCCdtic/Vlo5Zs0JLwztYrnph+3t5kMdcUbbNn+Vy8UDNNrKx+9cabNcllfSOGeAY+5oKXm3li56VkkemzXeKhW2lGWRznKABNnjQJY95/v0jQaUJcc/a5lYOhjzv9/eFJkr52hOXb3c1EOXqHzSfWS97ZBAGLcOBMvxoLp/p+ZJmYibOvVESjLfHIz8hFOQdrVDgVZbB4K4/Di4s6WPLPuXu3b/8BePWtV3Gql1OxA3+zv+Px3TDlXo96+y00DnRFuyrOQuBMmpFzUOdhfTwB5RytxUIg6w6jyILwcmUPJHTwhtXUULEdL2q2Nallniieuf72KEc9EXn13GBNOvfpFezhSPPDkJEcDFexoujd8DuCknr82DENt5/BCTc3N9HD4GBnj5S0VGRmZqIR3YO29Gbw1rvvit28PexNnDx1mrD9VPp/GjYOqy5qN2725YTWfhLdlxn7oi1e6Mvacx59YQqSoxZh1RUnLA1XklOjQb+Y0E5Jgr5szDLM3DFPbG1SA6B7zQx4Bo2BTxXDPTLqe2NR0E9PvoXw488gKb0cWq8z3PKuzvQZ8dJo7OgIo/Ht0G3Q4s8y30AGcXMbtNRBnyWWf+kUT442WTge7obxx51MBn2eHGMMnsdc8vFh43+XvDLr7IQTZ5zVHWUGsjg0D5vPx2kpZjILp2/vPqJzk7PM02fPIjMrW8ghB5IO++QpU0Twl6Jobo0qILehUkS1SqesnoK7daAzsimosdVBDjFvnE4lIT/AATmHomFH+vJy5DvZ4NQTrqh3VWHz2F5Pw0xqHDJWvOXtDNUBOOP/+tvvih34dQM+F0HZ85YYmehT10awnJafIHhqoJI183VmbQ4TBWg1w+jtvDwMbOZP7Jl8XInOwKkG7sh3oEmgFNEmIhO5voQ50zOffyxOLHas5yMXCDknXJy+dfMGXiTXMN3Bi8FxkknupUfhk39XIbCi5iPcE/D91CnY8WYg1bny4WFTTjij/bAzHI0bN/3XNGZJsbXpp5yxP14JOkWCfgmYO4U2h/lGi7j3i6PP11NchU0R36hQZBiUL/JoFf7gyq6G4h/1VygFQH2jJLTNIsG+QDZBfLH4y8F/iitQirm6UgxdyufjhQaM5ytBg1uih+xQxLhKUsylN3B83yIZVlauCGmjdK8+6oPZHdt27hKv+4H0iu9Omd/FBHtMJI9UtWKmUMqkxcGD2Cq599JFdi6HeIhUjxIXb12eqyEKqC+vIkgjNhM10nMEQ8ehvCvOj6qCOtczYX8vC7caOaPS31HITclCSr+K8DyUiOwazqK5ypkZPoRztyFK0Rfr78FY8ZazZ/68brCU51jcwK8v4PO+RrYPRqtgW2oa5IJ3DpEMiPFC58vDhvoGZIbuXNUbI21z0Icsm7Y280KXFq747UQmvOhNYPKhe3BsXB5ZQfawobef3CPRsGrlh/zjStCvHJ0DB6K12u6L1cwz9y4EEoOqSkgILbpkT0kMIg70TehNLT6OtHS8vEVwP3fmDOo2aCAavniB+IkMUqROkewPQF4OdgwP0dw/Dvqrzybiqxlzi8XOKsvP/u0DTyEn+45wzbpMjDR1pOPkRl14lc9vceia5gZ9XfOUFd1SyDEuC1ejHbHmoiP+KkD8tBU2zevGLfNB34ZdV1lcqyBIy0xeYRBrSzEUN+jz59UZ+oCgPNTxzUJcujWxfawRGmdFN15Z7XWDfnGF15YUKG3+G7pyeT6mTZ6MrWv/wuDWQXiyoLuQf97+l0LFTF4Ydu/dK5qrOGjbtA9AyrLLJr/zDOsMSslAE4JoVrPuEUEYB9ydkElMkQshdgLPvhRkh3pzbyOLnLIuf1oHDU6nIc+Z7lmBcFozWhx+JM9Wt/9j7zvA46iu74/6qnfJsiTLvfdu44axwaZ3Q4BQfvSQEGoaCZCQhFBCC52EGkrAdBtjDNgYY4MLxr0X2ZZk9S6tVuV/7pt5q9F6V7urYoz/mu+TJe/OvJl58+a8+86991wWCr9hUiqyvzDCI1037QfofdcP0DSPa2ikL8AvHH6fxWUtKB1rroDIUkgy0yc76hnvHohdRQ14NiMcJdQfiv2sUFFUapwxJDO2luUdKTch2z9IC505NBZFXA38fdFeRfE45qYhtLIB9bT6Qyk41cjfOeyX7uwX2YKza2DfXoJGe/0R4asyCYTbjKgkWX1V19RgICUdsrOzVSipbJmcJORzaxUv+XxAWhyVOZOdXShFWl5bW3JcJGXpm9LZuBd+adRQ7kx1TX/DNR+bXIeJPctQVx+IiW8ZyZH+Fk+RY/xV2JRj2mzp71t1sao9eceScCxiMoenra2x+urizJBKb8JrHnX1VSPN4ZnGikBWBjLNm6UTvQqvHenM/duIGvSkts/xkJUrXXQVNeBT67Kd4ZqirbNgQwnueX8z7r37brzyyivYvXfvEaGX1mcuIBvel5x1PJNgCPB1+8udMegSwljFLpfJfQ9B79soJgFNiMJQFuDe1C8Mkuwk1rGAYMCUFGwcZFOrgC29Derw+ef34MyDdtxzGiOMKF8s+8rEI0AoP8FM1AqONVacG37eHQMf2oGwzFjlHLZG1+jrHTViOP50z7245Xe34tAMqh9mNAu0aaftXTf/Xu0uHL5rctj/TemBa8aFYeH2evSMD8IeUjHPZdqwipOXTDjioG0s5aqFK6EeSdG4aUg0pn5diK/6RGD6acmqRq0UJZdNJqVw0j4NdKQGFdShiZo9TTsr0TAqVq0aCgaEI4AmZeLCfDiKaxA6wZBQrluVj5BUigiyv6tW5x7hx/D4QppfuIK+qoWwIAff/bDR26E/ie915M7hyiD8crUxMXa0E/eCHg14a39gm8I1U2j5fnRhgaqoNW2+wUC0B/R9lWCQ87QZ9H1R2lQdTVA1LHux8D1b+q5hm+rivIC+onjcCK+5OnN1TL5b0FdttCaxrK6kxT639K/DuG52JPa+B7GZZ/0kXoLWLnLqxIm49YQYZyk9SdR64btqPPvFDiQnJigFTtdYe9f2YkaTv6cKpxq8tFYDOYLFqSnAKwDdb3wiJpRX47kxKYhfQx6fEwNSGZ1FqQI7M3IbWQxcADzsvN7YONCmuHyx9DNrGrDm/h1YOzIcp57TEyPeOoyAXpFoJEXSSD8ATkwFvjyMwInJCMplVNDJ8WqfpsExqF+ai+pLshA1/5AKqbRuYgE//q9/4c9/vRebxnK1QeCXcMm+64CnH3xGOUs1JeJ6r3OHpeGXk6OwIa8RM3oHKj78hR42rDRXLPpaJNpIqKDuMUHov6UAGXvtuCXZhotOSMGv5zdLMEdeOgABjPUWnrKRgzGwrhGB7JcGSjbUJDH5MDoQWV+X4fvZBjj0y3UgkBOB7etiBLK9ejqCm5gMFviNscIIopRzE3l6x8Zip5/Esb/iiD544/K+SjZb5Bfk9w3vFuFnN/4WZ51/3k9+TFfkfo6CHbdjc2Eo3IVr+iu/YJ00ZPI4M70R5w2uwGWLY5z6+M7ELO0nMOP/te6+VteU/Rili9fOLcC+ogicuyiyTcVTTqA88zNzaxAcQjrwhAU+P7M2g76WYvA9K9d3XX01cAm0HeHM9WrpG5jeomC6nnCaf7cE/QsYtnnOcRS2edrJJ+PCfo0qJV8Afy213weOGI3LHvkMB/KLvQK+Hm2SmZo9hfRFRKAKx+yWU4dQhiI2UGis+pQUBBHYfvVtLs7fUKUyVkV9chuB7s3wUBWeKZE6slWMiMau1GD0Id3xG+q+n/VBMS66qhd+ICXUjYAnK4DBT+xBPdUqhQ4a9VU5vp8W4/w9fGON8iUMfz0XG36WpqgjiZRxTfQS4H/19TfIeT+CxYe+wzBbFl588VX8/d578SJXN3qTSUss7AD6F6qWH8T1E7sjhOCcFReMiT0CVAbz+XaDjhJLX0fyTOOkNyItXDl8n19eiau/L8I3qaHY2DMeL5Gu0ptY+zZWEmvkZCYTFwj2zPhRVn/2sAhkfliA2hMS1MQ0aF8dbLtJfxXa4ZiUgLzkIBRTpVS2EWur8MMYw7fQs7AB5ZHGO5R2mBPq2/ta0ENzGCklVdGs9XglTDd13Bn4NaOxfupb8c5nlGpAq5E77XDi3jasjs+2Br9dFgOKmarNE+i7E1q7ZWAjLh5dhHUHYlRB9LZUzGpLYpbCtLY6crUUwyfbbbizWQPriLHS6RE8JjK3pqvfEsTd8/o6Z9SXgiozkxpx9WA6JKMmovuYp3/q7wfE0j9cUICn59HyIxWzEz0wLngbcgMy8MtX1+NggXu98OghqQqMQKtye/cQZDGTbvw7B1FYWKM0dCT5qI5O4dhvy9DIEMzMvAoVcum6SUjjdtIxP8xLJa1Th5A1JTh4ejIuX1GI39CR+ca4aPz6dCYuHaBFnhmqLHmd0NV461BU0KGqwO1QHTb3tWEQqaGtnBhGfl2hIoBGLSxG9ow4lTjluvXvkY7nXn1DOULF+SnlDtevWe1MqNKZs/sSDM+TRNXcsKmSlngTLh8dis92NWB23yCcWhGENT2NyShwrhEp86eSOuwsqldUkCRBReWUYfTa8hbCcrKfAv2p3VGZZUMQo2nCPstH0ABakOzXOk4SNZxE4zZWoYEx/+LrcLBPw0rpB2D00/50+g0YEZS6tASHZ8Qjh6sKWbFEMBeiLiVE9YfeZDKU1dHosBD8glLZsew32f6wqBhnDInFvQv2sCTjDL90lY7VwZ+/6V6lD/ba1gh8mEdC1wR410xcuf62OHG1uqadagEFVSzTWRWMirogrGOC45eUZCiUeiBs2NWJK+cTGudxk9N/fW0iHqLl0xYn7s+ZOX/HCTUIj5uFjJEP+fwo2gz6/iRoydV4U9vsDGeunPdIEDdBvx28vg7bPF5q5eqQzYcuHIp9BRU47eRpeOujpbhxaiKW1A7D7x97FeeNSceInol4jGF92ikogBh8aV9syQqFAMovvzyEQvY5BRyVfPCe+0aogSgWvFAdWj4BUyKRMykFGYx6aXz6gFOuQDj93EsMPZihX5Xg2XWFSobg6nN6oDQ0iAAWps4joZ+1e0tgy4xDEMXEvmexdbGAAxlHKSAnoCdWsUwgAQyVlM/0NVjfDBF+EyXMuyJtGDlzBg5t34pzDxzEFxysIp8g9xdweT8UmoAvKxjZrlhDq51L6/7JRqijAMl/CNia3hHKSaz+TwnUImnxn7V2nD0oDD8cqseIlQzvrGzEz/mWF9aazlpTymH9+Cg1qcRvqkLe6EjEv7hfRTmp/iDdFcAJoYarC5kYAt7ci5Jr+4LijujD72rig1HPySCaq6vyjDA1ESazrXAKvjl6RWAYHc5FayuQszYP/56XpegcnY17Kouun8iVxjtrDnao5IPPKNQJO+pwzb+zTOL3LJPorxPXGenDZ+tOUlk0d7JaEYXMLbMxYjAEdsotb80PwcbCQHzD2hMyVoTPf2FOOVJj7Lh1QTKWUnVD8/nWQujedPTbkpglXd1m0Nex+tKIL2GbvvD6VpDW9I6O4ffXmat5fYnimX+AM6m6W11py8WZa04Oho/AGIGuSVquvL6O4Ok9/cgi4J0whju1Sameddutt+Ks0ek4TKribloh4thbsqsGWX0H4J9vfYErThyCJVsLcDAvX8Wp/5XRJ3oTS1Ucp2MPlWMeP/yBFupq8tL/u72/2kUD7nRqNN/P7E/MjGYUTwKa9vFt/LBEacwvjQxFSBZ9AkzCqu5pw+9JA83ZVI37ZiXjk35xKI4NdNIY0uaQXbVoeD8blf/XBwfpTM2g5Zu8tQbfT4pygn4Co2TCaZEfYlRN/79uQeQ4Zv1x0+JoOo5efTgnFtvpzx3wURlk5bGGcfZC0yQyhDXhfwexleGmTRf1UpPJ1EMOzLc5FOCz1C/m9g/GWbT0BPRHfkj9f1bzEdD/+Su5+CMzjyUBakrPQOXArXPYceXKYjw/NArvcJLT4Z3i/JUEtEauDmQVEzOrJ8qX7FPXoKOYhm2rVaAvfZFBoD/Ic3TLr1eTrp7ohFaLL2/Erm7BOPnzUgTvrcQ2UmTjeyexwlM0Xl1TyOxro47qhayVsHBHHVbsqUQkVTh/yKk+bhKzdOTOLd9E4RCjtv1KymLftFY4Rfru3TOKWq3h4e6FrWsIQg5DoaNZ+0MKprfXiavDNcW3mNDjbJ8xos2gL2fY9uUkWvA1uPijcGyiloqnrTOcuXKuZgestt6PjNf/9NzDmPteqs+gr9uV360laT0/kS8KK3SlDXsN4QlDfO7wY3VHQzZhIX5/zQUoKirESUlFyIqqofJmT8x5eBUuJ4+9kgAyfVRfvPftVuwtqUD48GQ4ekeoKJOmvVWYS/qmksOgjI7EAykR2DU4EgcJuEJ5aHE1KTQiCVZ6E2nk39EaL0qPgX1msgLV4RQh++yRnUo2+QqCaviYVGXNi7UbwkxV0LkLOnKFX4+cmoG6tDBsliggk86RttM5CUQRAIV2GsG6vU0hgYrn19eiQI96/DNpYo2QiciyiQzyK6x+NbhXLM4sqFGa/eKD+HVMKLbTMSxUz8PfVKCW2bOz+lA6gYnHdzMT8PveoWqCkz4RoL51fgFLTEagezQnDiov/2wvJ9KxkVj43j6MWV9Dlc4sFJBvr/mOzu6ZGWgMoTHCexPfgSRuyYQgq43QeX2Qy/voQZNQ8hdqSeGEFzoQtKsKB2YlqPBOyW7eMMSIUhm+qQZNtJYy6diN4UsSwVXS3IFReODzHIzrGau4fJFTfpz3sHxnES4c0w1vrS3Aok8XHRfyC1YdfXfhmu2Nzx/D2rb3z8qnDHU0CzhxNRXSgBQzH8if99ubE1dhUCtZVPOnN6B/ikNFEcakTPT51O0Cff8jeDrJmWsitCuvn8DlxYtzC/H6hgS8m81vnVZ8+3n9Pwy2Y0hS3XETwTNu2DCUVVerEMM7Z2ciNpXL0bws9KzdhvV59eiREosnFm/FpH7dsIlWcMVJrPHGzUFwsuU70LC6APUVdoSekonAinocGBmJjIUkewhGroJqYmFPolUrE8S31fWK/48gOFVz7Ssgd11WJC7dWa1kkxsoVbwjt6XMtqwstnEVIVRI1NsH4TijuwJ3sfZlklHAx1j/DSMiINZ+Mn0NCvwtvgD9hmhrX0B9MeWdXzc177VEsvVNkhXJ8rRo5XsQi3rmu/l4cm4M3trgwPu9bPicnL6En4q+UA0H3+tFtVhNSmcondKvM3LnuVMTlEXef342Xiiuw4ekYR5jTJuOLJLwUgnBlEgbx2lpcDzVLLxmlZeQ+5dIJy03ofImGLIqeQ6y4tJtSLvisI0k6M9fewgSrfPkSq6uuEWwsEv/ZNJlXBH8+r1sqm42HTeaO2UHPkDRnnuckTsdzedf3b8e49NrcNUXUc4EL7J7GJ3YhNFpDF6gGnBmHLEhysG/PdeCbo8TV56hv2US9VhuF+jnbXlIaVv4E8HTFjkGraPT4jfvwF1mrtzYKd2aMC2rFv2SKETEUooiwfBHxuo2W+7t5/Uv6M4Inr7HhwaPJF6VfP0lDjBmsIAvv7stkfr1RWaxEkm4qj45RVEIehv67H6VUdrISBLSmAihw9Zd8pZEvcTx+xiaMLF8GyNY5jCZ+jPWzcF9ykKCsC8kBGeQ9uhNvvqb/bV4d52RcCSbRApJeKZsI1fR1Obks35khMpk3ZscrMBXOHL9f63hIxEyh/qEtXDqCvAXsrhINScoUcR0+h7Y9l46TstIV43kakAXbKm7fSiCecmiEnrZlirksL7vV2NYNYnWvVM2gvdwe4EdFfQZhCQG470hEfiYECGRRA5KJ0+nzEQ0z1VAieMITnZcNKKUvgQHB2mZJHPRoV5Ers3T83D7kFw+FEXSJAK+OM+lKlksJ4Sd9IU0sgj69tKWRYqGBfN5jJt8XEgwaCeujtxpa3y+lkTWIZf692ha+vLdd+TivSVlnUEsGp7SgLQY5vVQOTghwgEbVwaytceJ29ZwTTlvu0C/cM+rSsXOlwgeOZknZ65fmbkm2Et7mn8nBYwze9ejH8sY9oivPYJryy4Jx/VLY3ymeHS7qoNacPzNoZsDIppw77hKFSPbY/LHvryDx+w+c06YhBE5h7DBFordjARpbRPAlwQo6yYWtGMcK5PxrRD6pbo3J1g6GkV/RnRdEgnwSQLudLSG6PAJH3tjd0IUnruyB3YUNuEtctoLVh90HqmVLcXBKpa9nbSMZLceIsctYYyD99cpvtu6iSNYa+N4uoRnyMUI5Q3F2TwAACAASURBVPMcLfbxnAjutzeowiiyaWtfFDXlvMLvX7q7Bk9QT0hAXyz5ENIzMrndcKgW1WkheI2O5DUC+KIj9Mp2zGzkPj72g0yABSwen8sg5kL2r6yGfN2cPgs6nTElBjWxFHJbRclnZjZrv4W0Jaus8xsbsDIhnv3LRIWf+KblF9w5cb3G5/PevfH57ZFTlqf3a4ZrXjiSIn5fJmI+/fquSVm+FEM/j0EE98yoURGEmWOf8euJtQv0dQTPDnqnz1um42/cn99XXt8K5uLMvWlgA55kVXl5WFZn7jnpTZiUWUcJZkerXnR9NbcsTsUOKczkxpmrwN38x5/QzRcmUTKXxb1/6rz+2dOmMBwymz7UICyldZ1Lq9fTpjJIyeWL9o5QDJqakPBMUYa07a9EFB26cbQkkyjZEKlHtF/D0ti5VMCO1/TPq/rhvS125POzD5bzOklnBNpYe1ZUKsmBVw+LUvTNqC/LUN2Ponp0Igs/LuGdelNUkJskLXeXtTLIOO4r0jkvEvCFirqWvL5suo6v3K9E9Qjwz11ahgMZNqfDVcBdOPiZnxTh7fOapQ7Eyk/LKUEaAdZ1deNr90ifFNJSL+JqoICPydHKJCAy1Ffd3BefvpmNUbMNOq5pEYVe6LyVOr4PUuuolElxU1CPOIl8oqzDx5u3+3opx+R+dZXZSgxSNuHzXQG8vXy+tKtBX0+/rvH5R5ZKNPa0yimvuKAIk/7XdvkFHbkTnXIpug2+3a9n0S7Qt9eWsvDwDHXC1iJ41GBrY2buV/MKcM8XSQqUZ/dmQfLoei6V7D55ziWGtqQmVIkbfbErFo9uDj2S4tGWvFyknhD4p6coHqu+vs7MFVnT+F6X+tXxx9LOZ516Krrt3KJe/FCCSSlBbik1Qeys99rapqkaseSjYkMRwxDE2HLfa8D60gdbGVN+87l9KXcQgOeZJbxoC2PY+yWqjFtdDUv49bQcB3LSQ5C5gGGenLQC6UyWEMcmyiFoSQh9vXJeuWbZkgjAehNaReiVOIbkxAfxb35hI80Ux7+Hc3BkkKrZQhpJqnVZVxn6/DrKRsJHRVdIZxVL+5JAJslhfRghNIi0Th3HWnsmRH3NrU0CV5EiuoZFWoror1hOWiuTBV3GcFWit+djg7C3tFI9d9m+iIrFaiqn/pQ3X/l8V8CWUEkN1kbcvvtQTU8ia3KINw19q5zyKcT7T+jyaktSlpyrLZLK+rm2C/SlER3Bc/0n4VghnjkPW2sFVeQQZ2Us/i04rP//woxaDEnzXEfU9XR55UZ87JqcMCzOIR/Ld/rSXg2Y1rOqTRSPtdKf8XczxXNGN0NbX5Ij0kY8+JN9V0Qy+fxTZmNqjcHzBvMeQ3iz5bSWC2n5FxMMKwlUOuFcgDKOI9hXisJTxwh1IeAnfLZuq4ocoBUMhd753VmZWLHfwWI4wbjzw2xUm8XFVWRLiiGmJZsUW5EtggJvItIcy9c4lrRSCNvXwNaeh1TACCDNuRdxQDvO74ti0joSzaN9CNK+UDxVdEJLKKlsMinoCmDJpLum1Bnx+a73Kv+39kVbrlUmAQf7tJC1jWUbQV/MJN5/HScuVhfFAU7kVfwpq2RSj6N5wttNau+C39+NeZf5psnelms7Gsf4xeeb+vlyXVps2HVl4Mrn+0LttJaUJRDiWUPfOgm03lttjdyRVtsN+jqC556l4Zhf0Droywn9TdK6mfzXRaMMTRF3WxULo8sWydjXtzck4vkdZoUcJ41jxOYPIjZsJ73jLV7fH0kGzesfDzLLUkHrsd/dgVG1hhR1Z20C9LJpsLf+Vs/RhQ6yjc9Cv9QwFvoIxBqKrr35XTOnL/uLz8AK8L7QJnPv+CVT9A9i5X/fa3Gbky45ByfedCf2r/kGW5YswvcffNbi+1FnzUbZ4cOoZQGSnI2Gdo4AdTlzDKoYPVOeHIVy5ho4GE5aT9pHC8ZZo4ZktXF6vQH6etNgr61/+b9MLgLeHTG5enuW+0ODqZs0HG9zDPzUNyufv84S9NUWKWXpCz0JOCcD88MjqB31uaZ+jN8G+DdTO/JX80rB6Om2JGXJcW1R19TPtt2g708Ej5zUlyQt6RxdcCWFL/VbZxc5Pd7SRhHTEPP4s2K/DW9Q5e76AQ2YN6IIq7Nj8bvvbG719XXkjwZ9aUeLiPkntdws8ibX+Z/JxwevL/3x+9tvx44F76OPmSna0QAggC8WvVijkfztbaUg+5X2SMEJvaJQSWolr8KBDdsOqxWHrDY0wCf1TkfmsMGIS0tD9yEjkNirL9KHjMK3/30e7971lxa30X1YX9z84Rfqs2cvPh17VrXUELnkiYcx/PQLUJZ3CE+cfSoqDhsSFDIhnH3fYy2Ok8+GzjkTBzesU5PI/g0b1WQgoF1Gy7o8Kw7V5NX3r2oukSgNTGDoT/dW/Cayj54c5W9v/dSe5yR9vCc1HZ99vaI9zRwTx3rj8zVt4zRN/ayU5SQ7eZwnPl8mBw3urqAvnWS18tX/zYZ8LYQux2jNHX+F1vRDajfoF2e/r2Jiv9oThl+s1W5Q92OgLby+tPTGKVUslG5nxmAUFu4KxdJCnse05OV7iceff24+JPX5ss8Yp8zPnHr8/Ns1m9f5f7Wf79m5ci5Xiue2AYbi5k+d19dPbM5JJ2Hwvl2dAjRCj/hDtQif34OZpA2M47fnlnp0fgron3Pvfeg75SQ8duZMBbz/2JuNXV9/jucvu7LFYBRQFyv+okf/rYD9odknoo50kN7k+3Xvv4MrXnjLOWn0njgcJ/3iFrWLnOM3vXo495fzHNr8Pd753W047c4/ILnvQOdkMeikSTi4aZuaOOTehXLJ48AUf8Jwu3ffhwB/lYS4knN3pYI6CmVXR0XhtYWfHhdJWa3x+UdE7bADPRVB95fPd3XcCo67E1lrndrxrTyiPPe2FEO3jpd2g37bIniaLX65GG3Ve+L1H6UsgCQ8/IqF0p1gbwF9Oe4/J1WqKJ5ffZKCbUJeWsDeX6llXykeOcfpxwmvrwfFFZdcgsDVK9ocXdIaGAnwCV2RwGm5wuJAtR4jlqds7qgNoWVGnfszPHfJ+Sjc0xyzL/trS3zRP+5CfEYWBp50agtLXfYRED7nvn/iy389oCx0AfANH7+N//7ythYgLqCuLf7377oZ4y6+As9cNA9/2WgUjdGgr1cNAvqfPXI/plxxbYuJR84nk4d8v3PZEmxfvoyTwA5UVdsVmMs9Soy+J4eu1dqX83a0xS8TyeGho44LWkf6xyqy9kGurrJhgLvPUTstJgNjWLhSO4q6sex3ZLSOe2qno/j89jhxFS62VWXT+qL6UjpRdZ65EPA3Xn9eJnDZyDKc/XHcEaGb0q6A/v0T7BjXowyfbo/HgxtD3VI8l9Gh+/FBFq1WSkbGHbSX4ukb3oS/jKfq4nFSPtEA/W8I+p7DNq3P3te/BWCyg4IxiDV1A0h9SMlSAT2r5a/pn9AoG/pMGIWtn69s0fyvPv5A0TZiob95y3UtqBmxxq9742MI6NuiYzHjxjuUpf/e3Xc5Jwg5XsBZW98a2OWYNe99hOiUREX9/HFYf6a1J+IO8/wPErxlknFdPQjHLysGOb62okLRP0ufehCfPPiEum7r9xsXf45x552LftNn4fHTz2IR63SU5xcxuowSyVw+hpjI4m4COMQEq3QvdJCvz8G6n0zC9hHj8N+3327L4cfcMfuWT0NjY4Uqjyj+O9lcVTXbSu2otvhzbb96DGAmfh0zEBfsonoqI3CsoK9XCf7w+Zra8SU+X65j0ckOpMc2+C2/oB9Yh4D+3hWnqVqU3iJ4NPD7y+tLlP7paU34iJKlnoqlT2c49D0n5rPuZCSuXxblluJ55ARqlZSFtAjd9LWwijFB6IlC/c+ZEfzE2GokR5Jj7v8QotNOOuZeBn8uSIqaL3zyUfSrbqY8/Dle7ysAnk+rPT+QSoPhkYjrlgapz1q8dhUGePAZRKfGY+w5Z2DS5Tfg+3dfd4KntCnfXfTQP/H1S88paz22Wzpeunqec2KQ7+9a9YOT0tFWvewnoCyb8P1C3chkIvsPnTXDeWvCx6f27sVklyiU5uWpfWT1YP1bQF0mnPfuulV9rycNoZQK91Jn/8231KSkVwLXvPqisvxlIvhh0Seq/QMbt6gJRCapix55Vt3n6vnvOicm7cTV0Uby/9WRcYhkOcTAilLE0AmcTK2ijohGksnkcFoWPlu6tC2P+Jg6pqZ4M3I3XsrC8wG4ckWkTwJrCo/MydabqqYA+9PTK9Ev2ZxNFAJQeG1TIvHEiJSSNqijhzP61CMqtBGHyoPw311ByJcCOdzcRe2oz01nQ7NT13PXZhI8F55jvJv+VMuyttghoH9w/e2oKV2Cp74Lx9P7W88YbC10UwO6XKBW19SUj1V10xPF88GZLPjBY8/6iGX7zLvUQC3nfWoqZWYbA3DbN6xUI987I3yaeX3zY/Wd9lB4i9m/Iqsep/SsQVTi2UgZevcx9TK05WI2rl+PW667FhGlhejhY4KVWI3VDO2sIG9dGx6FKIL8+HHjcOY557QotC0yziNzWdTEklQkAF1AbtzOqJiMoQOdlIhYxHqTfWbf8lsU7d2FmrJSRd8IoGtAzRo+TFE21aXFLYDdXsUMXJOzL2DMvTB/NXyyjhBq3ASFULOeUsUxcahhHdnUCFagcjDckhpEYXV2xvvbmUlsjGfXlY+eNNIGDVXfZ4wcy+efgpUvP40vn3lFTSoyCQm18/qvblRWfm1FmfpONk1HibNZ7kdWAMuf/9cREUMro2Pw5oJPnJz7P++/H8u++golrH8QWl2hJoHuPj4j3ZcyIWeHBMOe0RPPvvjSccHna2pn2QEb/rXTiODTQ8xnakdH3/BY16gd0du5cLj7KMJfLEjBBiqB/I0V2Kb1KXU6eeUaKhld+Om2GDzAicFzqKbvfL524oqse8+Jb7Tl9e4YekfLMfjnzDXgVfP5rrx+a/r68hDdSS0/M70K/VKq8MTKZHxwiCX7uJ+rDo88hAsXJjpBX12FLrmoLXnrhMC/XUHfOMbYSb6zUjwZ4z9AcJihCfNT3ySM84knnkBwTRWqWGQkukkNW3Vb4oa0h4ahPjgUwZHRGDCgP6ZOm4YZM2e2CiLS5r9/cyuGMExwzFlzFV8umwZ44cmveO7VIyx54fPzdmxzgqJY2QKWe1evclrPur9V1A8fTHlIGBr4UxdqQzIjezIzMzF9+gyMmzDeZ6D74J35WLZsKbZv247K0hKE1NUioqGOMe60uBnv7omP11a+jiASyipjaH8nJaWpKu14lvseMWdui9WN3Edev8H4YOFCj0NJitqvWLGCK5JcNQkE2WtZRZg5CsxPkE18BtVqAAeghpm8xeyPRk5Od7NG8JQZ03/qQ9R5/Zra+fuaKKwzNOWauXz5jxlVoz+X374mZMm+r5xchm4xtcwBCkN2iQ3x4fUUVatBGDPyN+dGUcCuEb0SW+oZ6YurdQThvi8TsEioINMmbkvRFGlPZ+KKgZk27J42Pb8OsfR91dZXfe/C67e1bq4zOsfi0P314HqcNbQYh5mgVVgdjCw3OjxyDfd9lYJvilwKo7cSxSPHuLf2DdCX7XGT4jle6uZ6Gk0CgrK1p47qeaOGYjAt1KReGTj/7w8rSkQcpr0nTkFx9j7sW7fGae2LA1X49Z89/pSy8j995J8tnLhitZbSR1BEuYZa0kihCUmYeMIUDB02vF3X6O1tkn545+3/4cC+fYiut1N8rRKJFE+z0i46lHTwrDnO5sRprB28Vit/9RsvHZE3sDU0BGfefBuuvvFGb5fj/F5WaZ9/9hnW87d1692rN85h7dthI0f63NZPZUddD1eKoN9EtkFvPjlwBZNMIHbKKfAzAWUday8F0K8ZX8giONG49RsqnJr7T6eyxa8mlCIhsmXehbt+ExnlC1gLV3vK2hKqKe0+OYZikr2N+tz+aOhbr6lDQF8a3LmcA7shD3csCceiEl8pHu/F0gVTNbBKotayQ6ygxKWUFfRPpZLdzJ6+6/BsyI3Gb1dFHEHxtAB3+Y+mfzyCvrGTXN/xFsXTmS/8vJFD0LPMyLLWAChO15dvuMFJxVgt5ZWv/xdF2TnqO+0rKOQ6rjosnDRKd0yePAlXXn21z9Z7Z9ybgO0br72G1atWIqCsBPF1NS1WAmLph0XyejmB6ZWMtvJPvP7nWPHa/1qEjso1buVq6P/++mCnTl6d0RdHu83cH+5Q9LKoar7BgkmytbUsohzrSu08MqVKGZCXL0wASz0018LlvrcMqsfZwyhixy2HIeMbc8Px4a4QJNqaMJLqmtN70xigDIydsiZT3kp0UjxGVawmqh41UzsKTVqHTmdSVsaYDxAendWmru4w0NeF0t/ZxBJ0W30Fff9CN5+ayiIalHJcsCMcY7vXo3einQ7UOkTZWhbBcNcTosNzuDIMNAopLNbAeH6DgmlVbtkEe7kbb8qb3Zk///Akzkbc5IGERjXHcrfpyRynBwk43j2PUSwU+tKbleqoYERLWGQE+k0aj01LlqoYd3FmFkjET2g4aqkP07dPH5x3/vk47WzfqwUd7e4UGmv+O+9g/5bNiLDT0S90kBkRJRPAkJOmYuz5P8OuFV+iNDdXZfq6JopJpuywiy7DH1iovWtz3wPWgim/XmFUydKgr7h8heJmHVz5vwlN2oHr/M607N3JLrzPKlmN9AWuORSF93aEgCWSjXPw5+7RDszsW4I9hRG4bAn1ntiONQu3D8tW/usUYzVw+8JkfMFcv7ZSO+1NytI92GGgX5qzBAU7mNHpo+KmXIBr6KYR1cPPrb+d+wGX92rC9ROlCqtvW3FVKA6wPNmG/DB8nB2kZmkB+Q/PLMDZHzLcRztrO8ihe+txlqjlWy/7t5dU6Kr75MMWXLhYunN+c5+Kmf/yuadVcpXw2SXU/SmyRXEZm4Vz6BD+2eWX+3eyY2Rv0Tb6yz33YO+unWgqKUK8vQbdKX4mzmzN5X/+9PNHWPoy2e3tNQALPlt8jNzJsXcZOiFrbwl1mb43isD77cBtMRk0A7qAs6Z2rHe+v5hV4YrC8C4ngNtZQU808n/1aTy0lp0V9OW4p2msjs4sx2lvJSOXGNQWKWVppyP4fGmnw0Dfqrh56ns2HPBifPuTnWuleD47p8SjZe9g7Oz+4nBkl4Xi/Z0h2GImablW1BL9/YMS9WQBfdUZPjp0jX2NYWB16J6U1Iirh7Cg9nGgsd9Zr/fZp52GAVtaKjmKk1bCFn84mEf5ghBWnkrCqImTcdPNN/+olE1n9IFMAC++8AKWLlumJoCY2qpWo2+WJaRg1do1nXEpx0WbB779GRy1W/Hcxkh8VmC8lE4uX/7TBgeuOsxcEQi1M6Sbadp76LE80jr3Lo/BRlMXUoO+VtV8Zmot63zYMfuDWI+qmgYYt/5I2loT17XVDgN9aXjHN1cgoG49HlwRjldYWLu1zV3opuzvLYrHVXVTrPnccurwZBs6PLIJFWNQMsYgcAV9Hc7pVnPHjUPX1/DNGDb8yMTjR4unM1Dhr3ffja/ffA19GgyrQGibiogoNMbEY+qME390br4z7rm1NiUv4tPFi5G3by9iSAOlsl80DSRhsMX9huD9BQuO9mX9JM5n1dr5v68odmfh272GaVqAvTXZhY/PKmTsfxCCA1lZjFX4Wtv2yQqgMAzzt4dgXYWhzzOKcfuPn1qEfUXhuHgJ/Yj8sJnPt1I9rXe5NT6/PXy+Mbk4ya32P+eCHU9TeOpZr5W05EyesnO9UTx3DmWZQjpONlGH5+0t4fiCUTi+llG0xuzLMa4ibDpRS08cqoPMf9R35oSiPtJB/M6/DYfuDX0cmJZRS4fdpUge1Jze3/7ePX5aeP3ll7GYQCfbBRdccExz80ez17UzeNWqb1mz1oHExEQ89cwzx91qp6P6tGDrw6pc6+rcMDywVQJVPVv5Yro3mk5Td7r5CpOcfL9xhWeksxzmyFKc+3G8woF5WY04oUetx6hA631JtE4xIwjTY+1IZf2P+T8k4K+bGKtvnsPfLFxdKas98fn6+joU9CuLNqmsuPKaAJzwcZjXZ+sLxaMA2ARfAdUR0QF48tQCTPtfsluwtwK2N2tfg74VxL1l6Frbb0nxGCuLyXGN+OWIquNGlsHrQ+zaoasHfqQe0DLKj34fiRXU9fDG5RvArpHddO56cODKvlNY6NxGza9PDzfTRmpi4c8lPTgBsA63Cgv3EkhSWRuMeR/EI48LhbZG7dw9iCUth9YoY9LfSlmuj6dDQV8a97WoinoA7Ett2WtpBvncG8Wz/MIinPduElgBr4UWjzNTVyYKTfGYiO6aoWtV2nTn0G0B7qoNkyYy27ZOFM1/G9b+Y4zZT6Esw/Ees/8jvetdp+3qAWgHrsguXPF1pOoRv3V2rMfoWH151S2ZuY2cJKxUUYvzmP6CS3o2YmqWnRMAs/LdTAAbc6Jx+Ze2NlM7ck5dNEWkXuK6z2rXCOhw0PdXkqEZ5L3H7OvJYBaTIr6kvLI10scdxWNitZOLccftd4a1f3oqK2oNrO5y6LZraHYd3NUDnntAW/nzGZv/piU2/4gwTTfVsbyFaSpgV1apUULRCvreZJQll2h8JqOzSOvYQoxUrKe+ScILewPapJ0vxw9l2OcbZxhlLntMXIowG2tDt2PrcNDX+vrrDobi8pXN8qaerrEtFI+aKPgodIKWPBRXWQbZx52171Zn35wdWvD3Fodu8+Thm7Ufx5hT7dDtsvbbMTq7Du3qATc9UFXwLQ5vuV6Jq/1qVWQLB64y2JsZHBO4jQQoTzH7bksiCvCbVr6aBNxk7bamqJlK6uKh6dSSIv0z9e2EdkXtaP18KcuaMfKhdo+JDgd9fyQZ1POxUDwyp7pq8Vhj9jWQa9DXx3tS3vRE8Wjgl993DqtTD1SKpitwd4Zi6sgffskPtd9Wt2m9FlduX+7jogw6nPt2WfvtHqFdDXT1gEsP5Ky9AbWVq+DVyreAtTcuX2GJheJpzcrX+8rursVS9OQgNv6NfZswq28Vzv4kqs1lEeVcmtppj/SCtQs7HPSlcS217K1urgZt+e2r3LI1Zt+t8ibbchZjMZHak0NXzjstqQm/mVKIMyVZS0Df/MfVoXvEd5bztJwsjD2t1n7q4GcQmTyh6+Xt6oGuHmhnD2grv4YZ9jetjHBa+VZaR+GKicxuI3b4nSednVZLIpoNN9e/Nfh/HZcvX+vYfAH9abHGzS5jFq5OW/JHRlmOPSEqAM/M7ThqR6FTR4Zs6uep6+b6Ismg+rGNDl0BfQ248pc7ETZv1v7Pe5N/H1WIj7Ym4OltIe7VN2UItcPat0VNRPcxT7dzuHcd3tUDXT2grfylzMt5cldwh0TsmPOD6lxvXL7eVyYVb8XPjQnBVXbBdxllOZeO2mmPqqbrqOkU0NeSDIfKgjBnsRE/29rW1ph9adMXnX31oPhjTdYSyeWzBlahb5JRFEGSvC5dbOrxqJlEJ3W519p3jdvXk0/zb8Paf5TJWuGUX+2y9r2Ngq7vu3qg9R7QVr7spXV2PGXfeozLF7B2E6ljjdiRY3VqqXXflhx+cwatq+yCt+LnvlbIkvvUVbI6ImpH926ngL5IMhz49nT2XaVP1bQUKDutfc9RPLJfa8VVrA5dT+Gb1w1owNSeLLRObWzXbT6r4PybBRg0xWMAuHtuv7UsXSvwX8kCK3N61bAi07Fp7TcwCYhqUgiiNn7X1tUDx3IP/FhWvvSJZ/A3pgf5Xv4StGitWIrs5UuFLGlTUzsNTeEYeGLL0qHteU6dAvpyQVp185PtNty5wfsl+uPQlc51OnxNisdb+Oa1/Rswq0+Fkjn1tOWzQMIVS4xwKFduX0042slr7uDd2qfiJrFUq28ea9Z+RUkJDvywHE21ZUgZNB4JaT27wN/7UO3a40foAavkwo9t5Xtz4FoniWaAFxll32UXpI3OoHYUdHUGpy8NW7NzL1oU5pMAmxznzaGrwNcEZXelFF2t/RsF7Pt6BnuhdT7fHY1x6TXoyco3T36bjAU5RqipNYGryU9NnubjA/ALkWbIrD3mrP3PX3wGQTvfgC0jCxUlDvSa8TOERCUjKjULYRGRCGHFqZAwFpnXoUs/wsvedcquHpAe0OUQXbl8TyGacow/ETtSDnFCRg12F4e1kE+2ArhrjL6V1mnNypc2/HXgyjGdQe10KuhL47qwii8CbOoheXHoegrfHElpBtmkTqXsk0A3wu2j7BiVXoXIMPdynxrsnyOdIxB/cmoTbpuST5XOCNywNFqZ+l6tfZmAuJPm/zzV0k0PC8A/JxkSfD+Wtb9t916UlhQjqMGO2uJchFTkIbHocySNn47EgaOAUN5IDcu9sYhzY1ktDuWVIS9yKAs822CLjERGeiaCWJ2qa+vqgaPdA0eDy59nVsfS9ybaOetzbXh5RzCKhQHlF8LFe7LytWPXOkm0FFfzz4GrqR1Hgw2DT1rVoV3eaZa+XKWunetPopYcp3X2vZVS1OGb1/Vroi5FKV5YE49ZvWvQN5kcupkN5663PticgCco0KRTx3TUz6snlyIl2o5/rkjBkvzmyCBpo30hnMCVWQ2K2z+assslxSXYtPobFJfmIGzXSsSW74EtMRIRSWloqDmI6KzByJx6BgJZrEMskUBy+zK8pbxqIBUe4aCTm2UNUduIRnsIDpU2IbcxAd26ZyGJkr+2sC4/QIe+jV2Nue0BLZ/cWVa+jth5YWalWu1bt7qGIOzMD8fX+214bV/gERE7sq81TNM1Ykd/L9OFPw7czqJ25Ho6FfStiVoXfxSOTbXaJvY8ut1Z+1qfR4GvSTW4Fk7/5KxSxLGYgaftYEk48ipDMJbFDDbnReFmU6/DKs1wE0ufnTG4CIfKwnHN5zGerX0/QjhVJ/OaYziTPWbKLsd1vw4J/a7v1Fd8145dtkmNtgAAIABJREFU2PXJS4h37ERGRiKSRgwn4Cej6MBBlBcUoCZvP9JHzUJ0zz4IDGZvqo4Q5OcgZl0CPaE2NdZzBSYL2UYEq2DoOjTVc2JgsefGBopc0RLZX0jnVCWjlFjxKpJa+BHRCbDFp7m9P0dVMUIiEzr13rsaP356oGTvayjJftiZfVtqvuJqKFozby3/b0v2rYz9WL4GL8wpRXyEe79fpT2Y2BGJBTtDqd1v9LF24Or4/gG0g7YyRsSTlW+Arvfns+J0O2LCm9CRUTv6rJ0K+nISrcXjT8y+HOdrVS0NTqJ5cdGooiN6cy+Xaf/bHIlFecZs8fFZxQgNbsRti5NUkRWrtX85Y/YvZcy+bDqSRz0k85/2WvuzmAh2zVBDgbP7qNc6taTi8i8WImbVkxh1zZVoqK2Dg+UJszesQdHBAkSmpCAhKRWJ/QfxGqKNUNYA3p3zRmWalc1kKuX7QH4vk4L83UKDVta8TWzfwYmgFgHyFgSGcmAzhrqRP3V2SJXAQH5eUlKOA199jKwTL0LamFO8j/yuPf6/7gEphXjwu7M4jirw6tYIfJhLS1uGoICtOTyd+OkqnSyjV3/J38YathlwPWXfnpXehF9NNhG9ld4/XB7GergReJvVs9ZTO18idk5JBP44sxiT/5fQZgllOaWWUXYgCYNnLOnwMdDpoO9vzL56lq2Eb8r3nqz9908rV9rVsoll/9h30fiu1EBtDe73jbPjhF5lR1j7ErcvnL5weRu5nPsvuX4pymDl9hX2txLC6Us0z5+G1mFIUh1ERyNtxIMd/kB1g0s++whZ+99G35NPQRBLD+7dtgu5mzeh17iJSBs4UN1YQ730lekWV1hO3lHhvwH6AZwI5CWrF8tenklwMGsMS94Fj1GHyc7mj3rBSMjJbM2DqIzEBuVV4AvB0oD1tQ7kbPgc37/6PoLjMnHaA+8iMCzCcv/aFdZpXdLVcDt7oLpwP2nBUgTTzxPGesWBkUS5UEPhsjM27bzVpRC9FTtXY9ZVY8cEfPnOCvStxeU/MNGOcT3KaKwE4mCpDelxdoSyrranTWvnD0itwtrsaNyyitXfzGvxt/C5nOOB4cDcAUZNjvbKKLu75k4HfTmpllv2RZZBPRyLQ1fASYdneiqwoq39u0bU47TBJagl9XDdgkTslWWWjjwxgT+ejbx7br7a5/nVCfiAkTp6QugRDhzgMVZRNk/VteQ6W3xn/t+TU1cNSF5DP57jvglG+bXOdOp+9u6rGBi4HlnTZmHr6u9wcMsu9Oo3CD2mTEBoCMFZ+Hsh7/ljRDkYfzfwc3khdCJbba0dJXkFtLaaEJ0Qr2x/EaKyRUbzfY9Qz0YZ94GSJieTQaCwl6ZJRZoogOfifxvqarHlv49j98bdqCiqw6hzL8Kg2SdTQyUQtgSGigqAdG3HbA/s+vpjvHjbrSjPK0ZccjjSusVi4ClnYMLPfoXIRPdUXntuxuq8/duaKKwtM1rzyco3Ha7qABcr32rte8q+lcPeObVUhXfvyI/EnV9F4vIB9RjH6J4Myid72goY8n3KhzHt0tmRtjW1kzbsNUQlDm1PN7o99qiAvpZl8CdmX67WU/imFXCt3H4SSec3zixGFCN2/rQkGUtN+WWr7HIW6+M+NrtElT6zU7/j0o+TUEJu2lVvXwO/L9LLejC609zXYG/9fQWdunM72KlrJ4Wzf+8+ZO/chMac7XAc3oK02GLEp8Zj384DCAiLwfCTz0ViVg80ahpHwlA1wag+M6iaAJYwlBmqjpRN4YEc1BWV0DHOz+jwLS8vx6Hsg0hJT0fW4MGM6w9CSX4hgsPCUVpUiO5ZmYiIsBl8q6KMmOzGieTQD98ge/VKBNQ2sFh9Hc75zW8RxMmnprKavmIbfQszEMj6uL5sjXXVLMvJJbhaWrH9yHRfDvtp7MPVUZOjknMn10pBtBACfOuTTrk5MQw4iR/evRmP//xslJQWIymWzyosBDExIUhJpbUfMxCzf/8YIhI6FvjdOW+tgG+OVjVeRV9H/d9DgRTjO6OHrIqa5kFus28nUDfnvllFysL/hBItf11vrH5PJEV7ej+u1tOqFM5YN5FQ/veegBZWvj/JWNKWpnYQ1A39pi7qlMd6VEDf6tD1pWi6ekhttPYfGO/A1D6l+G5/DG5bycIFgj0Wa//z8wqxl/Uqv9wXjjdZU9cAdWMHq1O3hbWv9mmmvI1D9DEGz61P4Qvwx3CmemxiNSIoz9ARTt21637A53dfxYgbWuOhDUgIcsDGgr1htiaEcCIUuiYkrT+GzT4bcUksRqDcsiZNr7l8dT/mm8GJIIDhmQL6P6z4DlX5RRg+Zoj6vrqKlg6PSR86HIcLi2CvqkB1RTViEpJRkJONfiOH0aFLsFIhDXT00hFcV1qGPUvepdXSC7UVBXAkdMewWXPQWEPzjSuEBkc9aoscCIgbzMiirFYHem3xXtQf3ohwG/0HoaGwH6ZjuMd4BfxB4YaMxk91a6yrQsmOlbDV7EJoRBCaCKhh3cca4H+0NxPwG+iTeeqqM7F73TokxIUhg0AfHRqIanujusb+w9IQmDkboy79fYddYQvn7cpIlJrYqkan1XlrAXx/nLfSTGtWvqaRfju8HnMHFUMieG7+JFFx93qT4X0L/YgTqJ3fO7ka2cXh7VbTlLY7m9pR2NVZyVmuI2DnivMZArgLT30Xjqf3e3dfaz0ef639Piw48J8zi1DnCMANixKw30rXWLh9BfLCY8tvA8V9s/bVvs0hnLod82N1263F7punYlnFJtw8smOcuh98vBjL/nQJ6km3hIUEIYx5AcnJ0YiKtqGcUTVR7MTMXv1wwlkXICo+jlYPbSPzfpusyyD2hlhL8l1DYwPsdjvyD+XxJxfJ3ZJRZ69D9p79CAuPwNgTp6K+gc5bjv6C3HxExcahtrYK6b160foX7j+Mc0Mo6pvqULRnB0p/+BK2pN7M/i1F7LAJiM/qw/FgSmHQ4m+oKEVlPhPYMqYyQcw91dNAUKzLWcYFB0NfoxLIRpFKquCzJjDlH65H5pTLOgx4jnZDjrLDKNm4EFFRdkR274ZG9jHY33WVAahq6o6EXsylOCpbS9/Km3dchQ2LF6FHSjgyuociLt4wlCqqmpBfUq+onmReb9zESzBwtvS/0/xp09VK5m3O95cq5+1zGyPxqQ6ddgK1E/edVr4T8GVO8NV5K+2508uXqzYpIVkEv35KhaJ08spsOGch83fUccY16IidxybU4cn1odhOe8jK5ftr5VuLpXQWtaPw52iBvi6usiM/BOct8y3Jp63W/vPTazC0eyX2FEbg1qVRGMBntUocuhbAPgKs3Vj7GqDVvuaxmhnRn1kzdc1dmicF85SuSVu6Xe3Uba8uzwFSMK/cexMCy7cgRAC7oR6xCTGoYdhMcQnraoYFon+fHjjhnHmIIDiLk1XoA+Ht5fqb70kcucZM2MiJobDgMPLy8gn+DoSHh4lED/IO5iA2OhLDxo5AVBwlKxqDsHv7NkTFRKGyvAI9+/eDTXR8zHYY4IncXZtxeP0KNEZ1R9agLDqx05gBXMZJpBaRUTGciOIRzAQw2MuRv+0g0qZcRaudIbMu25qFL6H/iG6k5vg0wiJJFTlQx8mijgC5ceFnGHbWzQwhbeCgbmQYaiiCbVH8zRlPfBcy2LlCMe6vgftxtWNGI4mjOSSUCqtm8pk4tIOCeJxyUvs2Vv1HOEY81TImnNfQUFuJXZ+/iajwKmZFT0Uj/SjKV8JQ2qbqKlRVc5KOGIzw5F70jVQzS5ocpXKoN6/O5L7VOOPKyVfgtVcUo2jvVq6uunPF2avFLRzesR5Ln3sUOauWYWDfUPToE4rUDPZnQx1KCmuRk8+xUNCAGsYC9B2WDntQDNInXIT+J83zvyssR+T+cAdqSpdgU0Eo7t0YekS0juxqWPy+0TqeCqT4qpffk1399FyDMv52Xyx+vTLUbVy+XFJ7JBfkeB2bL3iQOfaZdvVjawcfNdAXEbbsVTPUtVz/SThWVHaetT+SGtSPzClSCVryHlSwMPGdnydgs+E/bSEr4E162Wenrkvsvi80TzrrtvxzsnFR8T1uQ3yvS9v0oDd9uxKLH7oDRRX55NObEGELRGlVIwHfTrBmpEVwPXpndcPU8y5EbGKSAfhmAI4z+sZc5xgwwph88uuV5aXYsmU7Qb8e4RHhKnqnlgAVEx2F/gMHwWaLQBMbOrBvL1cVUSgrKkbPPv0UV6/sRaF3aPrs2vQdDny/DMVM8Bo8bjxCWGy6sb4etvBoBcSBITYkZmax3UDkr15AUGGYJ0EkkGAdzpDSiJgYZc3v2bkfY867ALHklCVqpKmuDvaaStiDQ7DlnVdZxBqwJaeiyV6DsChOSLZYti+rF2Gk6GgmzSW4rjQUOTFWFZawH2wqi9teWUzqqQJ11WWoLCtCNScwIqxyeKcNZS2ERgd7SIIKeO0OmnTCvTOqSSKggujPCE/uwfyDGE44jNfg9QTyhzvwGGquEHlkggkkP1/P9kI4KTZylSNhrnL/9byHfevXIHP0cAyaez4vOxZNzGcIYLt1lSUIi0nCruVreL2kw9imjYlx0RkDjAmJfeSQVRlpNgH+evarWL7VFWUICQ9HbCozqUMMekiuxc5JRMZ8bXERVr31OpLTQtB74gzk7y1gH5RznwZG6LBfavIQ2nAY3XvHIK1nCiKT6MTnBF+XdwC5XPkdyg1AfmEjyiobEJ0UgeTMTPQZeyIqg5LRrd9oxGYMkhHg13jWdW/loD+sisI2QwDXBHnr382A747W8cd5K616U9K8um8jrhhXqII/7luagMV0J2kr35N8sr9WfiZtjDfnGLH5EuARkzLRr77zZ+ejBvpyUVqEzdeYffXA28jt3zGkAemxDSqRQurpKjDT9I6F5tFWuKJ5LNa+nFv7Atw6ddUOrdM8Tj+Aarv5seh25fcZqY24dJCRBShLuvAE4c5935Ys/hzL77kaM6dGISG9G5Z+dxhVBJSdB6qUNZtOmieA0gsxMdGYePppyKCjVeQUAsRRaK7kDf+EWLcCImIBU3VTBeIEoIamXEV5JSIjo2jpNxD0WQ2M4B9DYAoNIYiJpSoqnQTVJkcdbBFRavWg/BxyDgLbvm1bcGA9hd3CohHXLRNjps4kJ0/Lnv6G2toaWrIE7po6hNYfRmDlTtJHDaSoaOXRAhcV0ADSSA28h6LSdGROPxPB9QTQkEjU1dby3DVwEOj2Ln0T/QaGMYyQppmYXARlMdYlgjpQgT4dvgRiAXxlEDPkMEAsZnm4vC9yUga8qDBTfiZtEPSVyc2/m+gol/4MYBYylSpUK7JiklVCI7OW6zkBBbCtRvonGiSLmX3Z1MBMZl5bE69fHcHjgjh5yIQj99WkQ1v5RXVVGIqqU9H/zEuZ4EbfRCUBnquZpiqGSHIC2/zh62jMXg4uqOgDIeTI2BM/udwcAxFI/HOM8Xxyr/yniW021EtdVkn/Z4SWiq7iakYmQU4WTZJYZ69EcKINCePPwZ5vdqBsxzokJjUiJikG0XExCIuOJtXG8cNJSu5ThPkauBorLS7FoUN2ZekXlXKVxMkltUd39DthGlK7JfFSEhnunMrxPNnngVxTzNXg5hsUrTN/RwReN+veyrNyTcI6QjZZercVWkdb++l83DcOr0VJbSAi6U+LCmV/sVdKawLJ2xu/S+3me8E2a9l/eczlkQn76tHV6J9ShcLKUFy5MBa5NGY04MtNttfKv71vAC4fVYMQ2yD0nPiGz/3Wlh2PKuiX569StS3La1hO0AcRNg368ttfbl93hqfqWhqwZT9Xa199JgPJMjlooO4Mmuf2gQ6M62ZXEg3dx7zKSBjfHZL/ue8ubFvwH0wfF4+pp4/Gl1/uweZdhdiyr5IAVIducaGkd4KRmpaA9MHD0WdIf64EIgwgEIAnqAlYSR8EEkXEryt2jDERCA0USPySD+X1oFVO8BSap4HgKJE/TbSTJVxTO7OFSWmQYw2ugSQn+fb9h7CXln6YUDkpPTFmxgxGRbBFAmIdqaN6AkpQFKmDxmqUHNqKpJgKRNoMoDTa4XXaG7BjTzQyJzLvwC7oaaOlXMt7qEMlLdT9335MP0NPhCRK3xHAA+VVl+NJNSkc5wmDTNkIAqREFYlSk8n8KNBQ0R8KYeRYgrVMWrymgKYqGth2NakFNtEPQV9CQL20wXvnqiZAIptAoGfmsgqNklkhgP+XfVWb/JwWumqXlJTMQk11/Izgr9Yd9D8VFjViFwuDDJhzKeISIknx1HIeiia9Uw4us8irf4ToynVISyFQ8QHIc5FsabHuG9mWyoWQfAp+psC+jvvJaXl6h1yPepkMq1k+Mx5zE42MUEQOn4v8PFrt65fSX0IfUEwwacBgJu5FITQ8ls+fkwWjv0KzxrGxYth3r8L2zdnYf7ABhwodnFxt6Dd0MPs+CQOG9ES3rF6M+uJ9xvVC4iDvFeMkCSt3/S9Id21VtM7dG8zSpeaT0Ndt3EPbaB05NJYrzJdOLfaox2V0kvdt3YEYXLucPiuZb2Vo8Xd7uHw5ow7T7KiSiK3dxVEFfbkQXUrx5e/D8dAu7xSPes4+Wvuyr5lqZEngMkxsZXyabbXwXZq90x6ax2jfOI8sofUSQX2iVwOW8+jr0Q+G7xf+PqYWSRGkUfxM2nr5Py/iw7tvwERGUQwbGkPwoGUeEI0VG/JJDVQS9MMQzSiL9FQC5qCByBw1gX8n01oXgDcscrXK4Y/Kh1AfiTNXTXvKGjXyxiWOX743OG55JoZBT9ATK5r7EHfMsDnTN0BgEmDP3bsf+9csQkh0HFJ7j8QQUjy1QjPIOWXVwZ9Kgn/hng3Y8+1yAl4pKSCZeIgxYtWbfRvWfQRGnnIuJzP5LoRgZidd4kB5cQG+W/AuEiNrKQFBc44mgjii5boCacErekfCQelYFmAPIt8vnHiwcOa0wiVENYiThKxkpEuCA0nLBBC0ZGILbCDVJVFIPI6WdLD88OYdDh6nJkcSIrToA7lqCeLBQl2pyZSdUcd96ulXqec9SNc5uApoZCp/IKOrGvmZ2JnSkQH0plfV0NoPTce4C66irlEU50pGJ5H2auDKKoCgv+ztd2A/sAbRwcyuppyJPAax6OsJ8DLP8BLYvswvtPBlPuFvOW+QcsrzXPxR/WjOo/J/OTCJFbwzJ45DuT2a7+Zy5VyPZXhZDCnSSP5ERAXRhxCMMJHXSB+qqK3qfRuwe3cJcg43IKeQEyH7OWtgf8p8JGHI8N507HZnO1EIiemL0D5j9DD3+FsnYalC52a0jrLw1UAzDlO/vETruNI65iHqeE3HDIpswj1TjRj89mx/+zwZ7+QZLbTXyv959wDccQJXrJ2Uget6n0cd9LVDt6OtfasCpwwQndAlN2y19hWuGfjsBGT5s0NpHhP49Wm88fty/v4M1tBJW/7w+2vXbcCjt16L7AO7qO/ThDRaiVdeezbemr+ckTb7kBAdihhbMIYPTMPA8ZMwfOZJCCVFEWg6LQOEElD3L05O4QsMWscwCfXrJkihEF/2NPvO8rfqS3NWVbMG91GzAn8TSPdv3ogtS95CWGwCMgZPRv8x42ka1RigpyiKEIJSAL5b8gm+fG8R9mUzTE74cnHImnNoGC3qiZNH4/QrL2bboowaTFCtQS2t/VLmEbz90tvYywQ08eIo45rWcIOQ6SpCQ9N7Qm0YtxVM7ieAk5Kij3jtAtgC1uLElQlRASaBXE2Ocpm6XziwpK9COFEoDFVcvdFnsiAS8FX0Fs9Sq3ggw7zWKhbG3wLCBp0o4zSY7YmFntmvJy79zc1IiLHR+ueEw4mqlsqnQXSi/++5t7Hxu2/JyZNmUn0j92VoJMmcrLtcJigFfvK9oo/Y1UQlMUaMtDljkhcdJfWceSHjZ45CY2gMNq1Zpxy1yVwdBnMnDif6HwI5XgLpGwqi1R/E/uCEx1VERUU9StUPfR288UTSO/GZvTBkaCaSuqfDURGCyPRRSGCdhtY2K49vTcJyDj09DPlb0zy+Rut4ismXinZ/nlyNId1MJ585svVob/WCzS91iGZHWPlaQvloWPnqVT1a0TvWjtSSy+2x9j0pcCoA1/Bkoq6upXtEoRUL2Gvg11avnhm80Twa7/R5jRe+eRKxAr/+2xu/L9o8qUOe9pnf30f65IsP38fOdSvQOyUIM+cMw+rl67B08de0LB2kSoIwbFA3zDj3AvQcMUo5LpWVLy+Syb9bfW4GZDmv3FjFqP8aFrwzCUY+USsCvS+pBWURC/CIIiETVUiJ5OzYxKzsD1HmsGHYiadj8MQRygmrAaiyuBwVBYXYtn03Xn7xPWT1G4bJJ81ijoGIutUjhGGou3cfwHdfLcMffn8Z0+OZEUyfQA1XC9UExWomeL3x4rtMHqLExgljUVZKh2RwGCnxCDgYdtpIJHbQ8g6mD0KscgHcSoayHjhIeenkBAX+wfxx8JpqamqRX1DMCKUIRkwyOojWudyenRE1dvof5N7VZCKAzYlTJhfxKTRwX/lMuH2h1dQYMGYDNcGoa6D1roBLWaxN6nrUY5CVArtx8rRR+MVvL6eVHa7YoCDSURXlZaxtEIrH/jkfW7bvUysrOZfQCrKakfPXU1epTq5BTqeehuGXkXMJ7aPGsty0KVwjoNkktBCPaayrwQ2/OB/btx3AipUblaM6OoLXRcSP5gQSTsntcAJ/BH/CafHb+FsmK5l0HFxx1fDcwQL6DOntNmAA+gzMoF8nEdXIwojpcxEd71lcr708vpP2kUnOBBjXaB01xuUf1efGSNWTx/WstXHWkFIV8CFiav/9Pk4paQ6n3ySaC8NUBkXYZPZTxzf/quWz+ZyO7Dw+cteC5/4oaUqLTiufwoV9TljE3BqjiFNnbj8K6Lff2m/m+KVzmqtoGQamsvrNB6wB1tXa9+rUlYY5uE271snve0vaMiYJN3V1BQRMK1Oa9gT8mt8Xh07ayCf94vc/ffkx9ImnoFxKEqqKC7Fm6TIc2n0QFWV1mDR1PCaeOhdJ6d1VdIfGaXMsW0Z1szXYYvkjlqO6aGNK0Js0JVyyog5MukjRHWrVQCAlAO78YQ1WL/gQe/PtGDFtOs6+9GxUVtcw4qSW11mKg9t24vDhPDp6w9G9/wkYd/IFdObyzbO+bfz7jSf/hv7d5DWjI5nJYNUE/cLDh9mOHYVlDlxw819oIdtRxciVYDpp40g3COetN6E0aqtJl4iFz8/zsvchq08vTiqke8TxaYZx5hzMpeM7SjmslQ4R76O6vAT52TvpeDbojPjEOB4XQjcBlUW5grGx6EyA4lxI8fDcct4GgmptDZftMgmwo4ROkklTJhc7aZsSJrfJhGAnb//eC08TUO045ZwTOaGFUtuGqzHSUYWFOZzEanG4PAqTZp+pJhq5dvGjSGiuPIuK0hKUswpaDc8l39sY3STnl4lO7rWWE5mcM5wrPDv3CWPGdExCAt56+SXkHdiLUYy1H3fOZaTf4lHAa/p+1WqsWrESObkHlD8oQix9gn8EuyKUL1toCFdA/K0mf/ZbEM+R0j0V3XtkIKjGgeQhUzDtohuQkJrqEbs6jMfnGazRNzrBXDt2nZOBWhkZm3X/GYlNuHUyc0eYoS/b9wdjcA+TOvO50tObVTrZenx7lTSlrZcnNWJ0Rp1K0kzuf4PH/urIL34U0JcbaKu1L8d60tuX75pB3ugmK7j66tSV45TF7+TpjbY0LdRaGKexn2nt+8jvW9sWfv/RCSz+EtrEDNazkTL0bp+e91dff4Xk4hXo3jNOKWoGEpDKS4qwa/1q5JBTT0ofgLEzTkE8LdsmCTekJW4s9I0fPUmavdYM607TSPP8BmOhjHlaj8pRKOBimdDEClT2JjtKgGH792uw8M13kFtMUGLi1eAhAxEdTiuRoZGl1PwXWB4yajR6DBmHAePPYLhaD7f3XJKXjaf+cB1S40MxePRIlDCsMocUVvaefMy++ApMO+9qn/rqWNxpx7ef45N/P8yJKgo9+vdETHyieo7fLfsG9QxfPf+GO9FjUMcmaJUV5GDHyiXoNWICkrIYAspNRxUJ3VRRVoJrL7oIu7ZsRASteeK/KHHwdwDCxC/OeV10nEI4SYlbfMqM2Rh28nm8zhEsvZnRajdbefybyOOLuKEaavrlM0dmW3h8T7SONK0B3/q36HE9OqNZS7+IETp/Wx6Hb1g/wjURy9V5q/rM5KL8tfLnxAfgwVmGlk+PiUuPipWvsObHoHfkxG2x9tV44MjQwms6okc+9ybGJvt0JM3jcxhnK/y+XJOelJx+Bn42mnlJvx1j8I3C88VmntXqCyRffvDoXzB2cD1jpmONyA7hnMmVi8XdRPBY8L8PMfHkM2h9JSunq8F7a9A3mlekgwJqcxqQJbEAuyquooaLmh4M7tv4rlFoHLWPfEauWWgD4dPF0hXLmcfs2bINn773scoZyK9glBKjh4b0SUNIYy0SkuMxcNQYJPfoifCYnkjud4IKp/S0leYfwrtP/QP7t66no1OoDmDeTXdg6LQ56vp+ytue77/Ce4/dx1UGHbhcAezbfQiTZ8/B+FMvRLc+/oXyeu0HU2bB236lhYdx8tixzF+ooB+DgE+LS0Bf/pagpRA+A2G7zr/oYlzy+38w4kgc6a1vVh7/vtVR+J4BSj4DvoxTbYRbaB2rZa+tfU+0jlydnlz0BCFN3j3agSm9yhXdIzH5X+yMxT3rDdSwFjuX41s6b5urYqm3pHmR0GpHPDmmCdN625VxlzbsHm/d1mHf/2igL3ewZSl5WxTCX25fjvXV2pf+b82p21aaRz1cE2N0NJDrZ05+vw3Af1FGI87tZ8Tv+6LG+b8nH8Lu1Z8ikXHX3RKjGeceTA2cUC71+VKyl9fRYhw1/UT0GTVChQ02ysgU8KaXUkBdkTYC1gRvcaDKhCA/alpQPHinz5koAAAgAElEQVRLQJd4dIlgqWUcv/ohXSN8tiRR2fl3Hbl0oTUkhr+wsJT8eRGjQhgbT644o+9AnHnhJYoeiYsX3r4WhTkFTDgai/RBkpTiHbwlNl6sUqE4QsnvB0pQ/nGwbV35BdZ/+Skpoxj0Hz2BVvh40jFHZicfrVsVX8TLj/4D//jr3xW3T4UPAj4pHlorQvHUcpU3fux43PPi64iME12n1jd3PL5bwGcz7hy3beXx5apck7DkM9PP7hRiOy+jCVeNId1jFmSSrP4/fhWF7YyYen9OJc78JKpDQjStkgsZYz5AeHSWt67rsO9/VNDvCGtfAMKT9LL0UmtO3SNCN01HmDrOxB13NI+eKHzl963tOeHMPJc2CtxZ/Df1rVcF1X117P7nuRew/NOPUJh3CDZa0U0ERLG+Hcwy7Z0SiZGjB2HC3Nnkven0U7LKYrFIaKL8QSekJOBITI2APC31WiZMCR9s508ls1XFkSn/ryaoO8SxSVDXsfYS7hjK1YXiucmFCx8unR8oJiGbP5BTggMHCpWFdNIZZ+C6ux5Q2aMHN39Fp2ko8vMr0Xf8HMo3e+aBO2zUH+MNKXkGjusQ248gtOambypJE/7jd7diwQcfIi2R8pN81uUVlcrZH0/fygPPPoNBE0/02qtWwP8uJwz/YMlST4CvaR1rpI4T8F0A3BOPr4wa86rcAb628rUVL7vKGyD6XX+eWoneSYbRJU5eyeq3BTdg1ntxTuetXJsRn6/j9L12gXMHLax2tK18uYAfFfTlArS176sQm5qdTeR0TdiS73x16up2dKindthafQBOIFY9pXl647l54vdH853oE9uId8yMwtYcuzLijWh4945dOc8Do2vRM5YcfSsRPTKgrde9OzsHlZTBLSirVABdtW8T9m9ag7Jd32LWBWfQ4pE48UYVmdJgxpJLKKA4KSUiRsIEawTYKWfgoOhXPfdRdA4nESmtKNLJEZRliGD8eDgTeML5/xBm0Kr+ZxKT3cwErayU5CkKwEUwHpXhh3bGYSf3HISZ513CsL5my8ZRU8Wszs4rxuH7q9i1p6ceKMo9iCvOOpOTf7F69gf37sWUqSfidw89hIx+3qknq5CaFEX58waKARIxTTpcndbJiqhIG+N9O0Iu2dxRAFuDtnxk5fGn0Tm7gVpbxdpPYKFb3NE6mhoSwFdtKXoTeHxyHSb2NIX8+f+/Mjb/3dz2J2JZrfzOllxw9zx/dNDX1v6hsiDMWey7drhrwpZrCKfcrNWp2xaaR7ehQNmC9K5hnNriv6Z/Pc4cVIJdXBLe/g1BzJycPDp2VZutA38M+dO7htco4G9Lxq5+6KWlFbjztAmIS41Bd/6UV9QqS76OlIwUP5ciJ0p904ghVHH8kVERiKeomoipxcTFKimGYBuTokTUjKkkNeTnRWahkuJpotYZGdkNQVljGNtdhyjSLen9BpILrqG1H0bHXg8kpmXBxqxc5+Yjr9wFxcdGD8gKcA3LcEq0UY/+Q9CN9ZWbo6w8X6M1UscK+M4YAQ+A35qujjEhGOe0gn96WBP+dXIxSmpCcPMX0aBKhE+0jgZ8DfryW1YAt1I++Weji7ApJwqXfxHe7hBNaVdLLnS2sJqnJ/Kjg75cmLb2H1wRjldyfPSCyEP30amrwNvA1yMydTVgy3dek7ZM4HcN45SP7xlbi0lZhlWwmmnad68hf632N0HdU0SPuY+mfdyFclqBvy2hnPrhL/v4Izzz0N208mmNlxSjmtx+WmIEM0DDqKVD7R7GVMcmJlAuN1EJrNlp4cukILH+Iqtcwfj3eoZHilhYcPxQpPalyFh0D3Tv05sTQ7xK+oqKjWZoYAh//3g8tKfB3vX50e8BK+BLxq27SB13Fn5rCVhWoHe19l+aVY6MOCMiRqJwrlkcBypAt4gKcrX2DYBvaeVr5203UgEfzyvAtR8k41tq8KnXVdE6bXPeHk1htWMa9Ntj7cuNtabL45qp6wn4XWkeDdZqwmiF34/j4uSf05sHmhX0TTzvEOCXjN3fjTJCOdsD/AepkLhj105s+fQdfP7he5gybRiGjxvEJKBqyjaQsxfenrovtVWUNyipVM7SaAp/RaT3oVNxCgZPORGhjCG32RilwYksitRNiNKe6dq6euDIHshZewNLYq6CAP5f10ZiO2lyK4/vDfD9cdz+fmQdZvYtQUl1KN7eFIPByfVYsjcUywqPjOVXAQrmyT0BvtyNFDs/pY/jiLq3QgA1R/D4/uS1fPLREFY7pkFfLk7H7bfH2pfh1BaaR4EzR6KKYNHOXItT1wr8hvVuzAJDoppwN3U8EiJb6nis3B+Lv6wVUDS63Rrl4xrR45wYzMnF1bFrPd4K/P7E8Lt7+FXVtbj/uisZ+vgNBo/sjxpWxBLtloY6TipMLsoaNA59xk9Ht8wMDBw+TE184nhVDtqurasHfOgBayy+O8CXJgxO341Usvml5vxdM27layuPP69HA64ZX4gqOl3/siwB3+r6GeZ+ciJreGazE7eZWdDOXp3O15Ex+XK9J1DL6Jm5xirkx+Dy9SM7JugduZiOsfZbZuq6xu7LeVxpHvnMl2xddSyBT/P7J3drwrVjSxFnZvJZ34F3NibihZ2iD8PNnEz0BKAdu8a1sDVzRrDSO96AX2v0tBf4v9+0FU//9mbsY5nDXtRLmX3hZZg662SkimBW19bVA+3oAQ340sQj6yLxdYmhf+RT8pUV8OUYN9y9FfDHxTbhzzMLldLEs6sT8cEhkUM2Ll6fT0ofyva3HyiJIa+lFyvfs2xy22gdObfOvhVRxYyRD7Wjd9t36DED+nIb2tr3J5JHjQ8TMVuL3feV5vEljPNaanacO7REKUE6GlhEpCSc4V1mxQdezwtrkvBOtqFG2RnAP4vFma8dZpyvvcAvyo9btu9EN6bMpzJbt2vr6oH29kDB1odRkf+aaubZDZEsOtJ2wNcWurtIHWmfKtB44ZRiZXytzo7Fb79lgRoXwJf9XppViV6J1VjIIud/M4uce6J1rIDvTlCtLSGaWmOnoSkcWWPfPKpx+a7P85gC/bbG7Wvg15a9rzRPW/j9+8ZR1KuX4bAtZYTAc2vicGJPO8ZlMq3Q3P66LAXLipp1e1qGfho7ebP4vUX1dCTwt/cl7zq+qwd0D1gt/M4GfJkIXjiRJToJ5rLZHYH4ZHscHttiUJAa/G8Y0ICLRhapz+SdF+D/MzNt9eaqrdPRtI44b5+f6VBFnY6mxo6nUXlMgb5c5L5VF6tiCp9st+HODb6/TC1j973TPPqRe9LmacHv8zIS6bB9nANMiiTLJs6i+1fEYS25w3vHNk8E8t28D1JRKjHIpl+gRcauHnl+Ar+aKDQHJIOXf1uBX8K/Uob+zS+BNt97t2vPrh5ovQckSqd45+OoLHpf7dhRgC9teZJYuJc1KLQBZr26zXlRuPErRj7Q4p/BVfHvphWrr9/fHIdlB4MwPb0Bj20zgg+SGBJ9mBLYTqERHqM5/Y6QWpBz6BDNo6WX722sHnOgX1m0iWUVjVqxdywJx6IS/0I45ThXmqf5M1OB0wRPX/l9kVr98/TmwgtlEgP8WTwOsCi0NPXY1GZtbvn/rPmpR6hzdhbwT45vwjWDq9sd1eNtoHR939UDnnrAGpbpCfB9jtJRKO9dKvny3g24hNb7yv0xGNqt2qmSqa8xr8yGv3wdg7/NLFHfeaJ1/jSSWe+9KnDxR/EE/2bA7yhax5qIldz/IVr6s370gXTMgb70SN6WhxQn6G/Clhovlth9TzSPP/z+aWnisC1zRugcJH//qy+jVeyvduy+MpsrADM2OK/chp8vZmFrS/SPNXpnMi2PXjGNeH2vyPAaz98fqkft72Lxu4Zzpg65n6Xu3CtV/ugjrusCjqsecI3DlyidHWRbjnDYqpfTS5SOj4AvuwnobyoKxBqutDNZBfO+qc2rcN3BIpom4mkil/zL5YYQnDsef/HZpSinzML1n0U5a9+2VydfX4MWVWsKHYn+k186Jp79MQn6dpbL27fqVIJhtV9ibBr05beV33fV5rECp1V7f3R0AHowDHNBnoGqf6AVcFL/MmrKGAu+vUURuHVZlKJu9CYA/LIF9LM5KVz9uZGY5A74E3hhr52ej/e2JOA/jPDpDOD3VavnmBiBXRfxk+0B0dIp2PYbynQccsbhdzTgm3OF6iNNwVgLoqjPzWXEX8bVUSXTEqtp9ux7GxPw8CYqsjYLPagYe52AteD0cqTG2DH/Bzp5NwZ51NaR5nxV0JR9rdLJR1tUrbVBdUyCvlxwac4SFOy4XV37xR+FYxNV7nzdWtPmcRfGqfl9Af2nz8jHir2x6B7jUN5+vW3OjSZPaApgWax4Af0vLsh3DqddBZG4nhNDa8VXfjfSjol0/D64Mgkr6fDtKOBnaVPcNcK7Vo+v/di1X1cPeOoBq3iaSCs8v83W4Ra+BnoBWm+Ar1cWl2Q14opxJU5DTV+/yChcs8x4f60a+f8YK4Yd/RFVobhpUSw2Gy47Z9ZtW5OwpA1dBjE65VJ0G2xg2bGwHbOgL51zYM31Kptv3cFQXL7Sv6xPT2Gc0q6rKJt8pvn9K3s14YqxJWpZqLfV5A1vYzUdq+Wuo2uyuGp86cx8574yYdwtiVlmm/Lbqsoplv7DzOBNj61R0T/3fp2AHSKd3wFUj5xLirBYgT++520+6fEfC4Ox6xp+Gj1QVfAtLfw7GBdfAa2lw3K5HUrpSE9YY/FVzyh6yPmquC2IIt9PYsXB3045MmlSeP5buQrfJYXlud9sKkH/8UQjqufBrxLxnhsxtbaEZ0p7N2QF4MbxUgc6Aj0nLjxqBVJ8GUHHNOjXVOzH3u/mMR6+Fv5m6mqw9RbG6Y7fv2VQIy4aZQwG0dO+bIlhubtG9MgHpzJJ6zfTCpx9/cOhaNz2DS0KF1VOAX4N+Jr/l4PKCfy3L03AoVr/gV/do3lmK88vwH9dfwfGdaOnmZtYGsmDbvNlPHTt09UDrfZA8c5nuAp/Vu1zzAC++SJYM27lXXuE1bC0PLK+KbHoP90erfxiM/pUKlrn9bWJeHhroEcxNTnWH1rHqq9ztIqd+zNsj2nQlxsp2PG0GmTlNQG4aFEYDlj4dG836hrGKbDtK7//0AQHpvYpxf1Lk/ExiyBbhdmsFv8lPRtx7QSKe5jbl7vi8JfvQ49Q5cywMWtwSrPDV5K6NuZGISnSocrOXfNFjFOgTZry5NxVA1CfzKSZPGXwXtmzAXN7GetV0frocvB6GzFd33vqAXHY5m/6vVp5y7Y024Z/0SfVTLsYR7Y3Skfa8MvCdwP41kSuv4+vU++xp23Vvlj84utQFx6/7Vm3ch6tlf9jqWh6G8XHPOjLDexccT4Lse7yO3ZfjQcTId1p73vi92XgymHvzK1Uvy9aFG0MaBNkrRb/jUz8mGcmfsg+r6xNwst7lMCCE/gHRzbhvhkliDElG4TWeeCbeKwrMy5u7P9r7zrArKiu/1m2F9i+7EqTZgOUIgqCBUVErFFjjSXGxPLXaNRYYlRMNNF8amKJNbF3RcGogNhAUBABUVRAQIpI3WV7L/9z7n135s59017Zt29373yfsm/mtjlz53fPnHvO72AY+fJK/jf7v2Tq4f3yE3a0DXIQFysjefbQ74no0nnJMO7SSRu8+UNuh54lx3jNC31dS8CQgGzOIeK0F1ZnwDyMsrX10GETNjwvHaqq0iv8EZOWp2Dk+5Xv5zEXaUuOW9YXH6aaAYvOiY3bq5Ee+Rcj9mCaR2OJYnU2l6XDqZgJS/XHj8SOL2/ediS/jtv07RSgX7lzMez47jJ2H6H67nPQ5CKw8993A/7eycgMeFgdXLogzZJfVzb13DGmiTH7ieOJLwrg1U18/4Hm4yHMvrjH4Oj5qTwdPYB6QUXgi8Uu+xbVVcE/FOCn+jL490GXtmuG8w1eOrS5x+2V0NdkCajmnFA2bKkd+UvA/AoI9sMXZcWmLRGoXTSmjFGd0CHTJBvtKnQLdpw6rCyWO7kY7eyHmq7XFbXJcO7/cmA7+ubz7FecLpne2nDt+NTXjCNbYJ+ippjnvQ1l1nYK0KcbEr77a3cmw+nzzRBqvzcbiv8+tekUscvAXNL4H5hYD6P6mhQMN88rgsW4BhDoTundBtdOKDUmLtn7//g5RgpKbah/hx3ERQ25mHvo8sUDtbnH73zp7uXszDnPbUhiSUn4BLb840+7D9SzY8yUo24vxWREvzywlPFarUSvm6qGHnBAUT1sr0qGazDnhvyFYbJlBpOoCcAXX+6HowI2HYO1aCG5+f18mI/vqRPgs9fJv8MgE4aIvE1IyIJ+h74TV5u38nzuNKBPvvtblpyIn3HVIfvuC2Clf/1u7FJZp4hdGajJZjhxkGkznPRaEZPvxUNwM/ggrqkQJ8ictTnwEOYEFe2qYB+Jxh/Ae/5cFeBn/UkmH23ukae//ttOAn7MOQH8DiB/5OYcBtD4n2DMpLy0t32SA98hr6Aw6ZCn3I/k8KCwbtJPlRuftYcXBODTWkUJUWaeUQqvfZVr2bhVc92y1yhEwJfNOvESees0uzsN6NMNyIRsl36QFpLvvgBZ+tcrcMstYpfqExWzaO9hSdNvbEmEY2fkw00jmmHSkArm9kn83v9dlgszke5VTbko2rDLxOVH42eAbkPPbHH/DDx51dxz9QH1MDDXNPfkDrpY8/Z08zWAtPvKza9avHO8zDkcrAP7UQGXSv5yyJu67uYcAfj07wMTa2BYcTX8d2kBvLSxh6MN3w83vgz41PapvQHG9W2GG5cmRXXjVvbW6YhE56FO204F+nRzkfjuBwO/mXTFuhiYHD1yxK5KzkZ1XptWCXtlk/qBZHFlGfBTeTJq/pyFkzh6/v5pLiwN5FaWN3c5YPND1vL9aPyirqudnwp5aP2yuYfy7+YO/IPe5A31Deoi5Um7L/3hryy6lg7yzjHMOZLW6+SdQ3XCsd/L9XIS2+Dlk3ejp14SnDk7J6qAb8ecKdvxw8mCJR69oFqA5CHQf8x/4tasI8bb6UCffPe3Lj+PmXneWJUOd3wf4ndYAGRJAG4buwxYxQYw1eEYajGVkMb/wpRqg9qVbJBi40nm6JHbCgv4AwBu/OMUyMUKSF5AUj3mAhp46vLiNQG9e84ZWg+9s7ixltzMCva9WXP3dBEw97qNxurNULr+Iagr/4AV3V2bBM+vToVFmPQkHO8casPvhq11oWiDQ3Ixycnkncwcet28AviWghbxkMexN5p4xvdGk2lzAizbnQAb0CNZTWquavhegB/Jxq0IwqJxlox4ATdwh3uJvMOvdzrQJ4kJMw/9Pf2TdJixKzzgt7PvU5terpwyaL42tRr6BeiWxdP86qeecDVuOAlNW5y35NqVANpJ46ci4lpE5h7WV8D3X5pyYjyUiGJaSSucPpTTTpBrZ6/icyFvKPeY0kfXlAB55lRuf4lF1tLx3oZ0lvyHbdZ6aPdsSrmYc+i63YatmgyFdRxoZwSy2T5wPI9uJ7Pogg29cC62QVFmC2SltkBJzwbISrMG6pBy9fiXWfBBIA+uE+BTm3aumQLw+f2E9pzl9Ie5/a+DgkHnh9ZAB5XulKBPshLePBS0FY59n801BMJIgX/eL/YYE5FY/Wavzob7MYmDQRoRAFvxfL2AX4wrFDs/1fEbzGWUlSacGBO5dl6yTyMML+Q5f8nkkz/0VsgsPLSDpqfutj0kQKacsg0PsLwVdKzalQIvrUuB1YJqSvXMMYDZxnbPrnlr91TMDfCFr8Frx5sU5n7vnb6wiUZhJtIoUDtqjlsB6NH01CE7/r8mcPfMjk5/6FdOolynBX26AZFwJRI3TgZugQVApmKm83YcPaqNf/E5u5hSRN4G/1maC6//xDdsafIJSmU3jV+AsB1tgxfws8ls/M8K/LxdHJlq7gmUN9oOzATV5PPbQEAXXaZJnT/4Km3yCfXtirPyaqITp0CrAI7z0UuBVhw8zYQjwW6b3to9teFGnnbRoFa48GAzwt2vCHdXp8AJb2fHBPBpTCLqltIfDhw/O+7t+LIcOzXok31/05dnIzjXRWzfD8WVUwb+L87dBeUY6PGX+bmwJOC56UTZEK6553Dk4J/QpxHu+RpVcQHSEtjLwC8WEcsmLyur2Pol8Je/amWTzxn9TL9+YfLp1f8s7eXjF4nipJzwygnJlKMAPoG9FbCt2r18TdXohdlEBntRXmj4cqTtOQPa4NyR+AWdGsy58jOSpu2oSmE2/byMZtS0zdzU9y9Ajx9Uuuxy3FJ/3LxjBl/xRSz0h3R6YQJMP4rTm8Rr1K3bXXVq0KcbkymYwyFlY9MgMPPCAf5TSwD+t90UsezO6UTSJvOFupl79kdu/8tG1cL+vTkdxBTMyOXk3WOsATabvHwhMLV+ab3gA3ex91OClnMGmyYfDf6hg0RH1bADeyJJe3FdGtJ+BEblYcrhwOhfu5eB1I85RwZ7sRCIL4jT+rZBdmobpCW1wcbyHowDS920pcxXJxzAI+If+awAnv6RygSmNd6bWDai4YtPrVImrMcn1yOlSltc5LsNZ251etCnm5ZJ2W74JA0WVYe+fEcC/HZePWIxCQf4D8ANrUsR7PcprDW8gYiv56z38tgzDgJ+CcXdtH5X8JcXC2kmiUXp2MI2OGWg6eUjwD+rZJo2+4Tz5rVjHTuw31GdCDM3pMFc5Mzhk8gcgOyGyYHXh+0+0IbTZq0K/lPQ4+b78gTYhN7Ndtq9XZQtjcuNWoGu90ZN7fHjqpjb9Jtf58GdmARF9G0F/MhI1IS0nh3fCqP74p4XumcOnfBGOz7F9mu6S4A+iWftZxdBQuNXQPb9axYlhsTGKcTrl5VTBG9RPS8//lBMPTnY2E2YZH1knyoD7MXYvsNkz1cvyrT49nMQ54fs3cMmvfE/OR6Av26cDo4K2Zt8RHt2Zh8C/8OKm4zNXipLASk5e/9ag3/7vae+Wib3y4otr0PN7lmGR44vsKfWw7TdU1Uncw5dm5jfBheP4uyyGzHz3G8+Qo2G6kiTK1zAF5u2Lx5TC/v2roGnv8iHh37oYXDnsCke4NQRmj4/50ucQYUEzQLZ8Qcc/Aqk9xwQXkMdXKvLgD7RNPz4+fHMvj97TRrc8HV4ku0I4M9FbeXaUY0wcq9qw465A3PtFveqNxSy17/OhyfXJlkTudC7KnkHyR5DBuYLtYqBPZU3T3iZfOzAn53DJkZhRsiTB5hmHw3+4c23aNQisC/f+DRUl840miOPnJkbU4LMOAzf5U7dwD5Q2FrefrPWDvzvGFMPB/etgtRkk91y7ppcuHslpyMRdexoFbw0fAH4RyGfzp3HlmHmq2Q48d2etoAfCWumGKdMsxCPHPmhzKMuA/p00zIbZ7j2fTbPbWz8djz80dD4bxvdBOP6V0FmYNOK2ARf/DobcpF///zR3IuBglXOe7cQKqV9LacoXgHUJum+Ves3gLydwT89f6KO7g3lTQyjbNW2D6GudGHMwd4O4GXtmQD70SOrYWhhDWzDjdcFGzPh612JcDzuDY3Gr9gTZuaHDPgX7N0Ge+oTLG6ZUzHz1R8xQ1Z2RhOz5/93Q0IQvUKkrJk0UHLPfPLoJsx2h/EC+FVbMmJ6GE8rfqp0KdAnsQr7Pv192ez0sOz7sQD+K/ZrgUmUuQcDTuigYJRPf+yFHjq0nQzw8BG1jIOEjk1I7/CbD/Gz2CYbl7wpbEnniPWCArqoMcl2z+8zdM2f1ZPmsJPmT3b/zIJTIKv3VEjPGxY/s74Tj4Ry01bvmAN1ez42KBPodlbtRM1+U3iaPdV3TobChWVnuxfnVe8c0vAnDKxASpJ0uPqjnizYS5hzhuE0/iYQaRuKhn8SUiPfeORu+H57JvywOwWGFjTCkMI6xm/1zre5cPuKYD6daAG+8MenREQlIx/tVO6ZdlO9y4E+3eRPX13Pwsq3ViRiRqrksOz77QX8VyLYHzu0CvKzeACUOD5alwt3LE82bPT/O6XMMPVQ3t1bv0y1JWwT45R9+umcq60/EvAP1FX9/MV99Eev0il9WmA0BngJage6Ri9MVtE0DPQ6Qtv+Q1x0aGO26ud3oXrne0ZAFTVBfvbzt6TBwp2JPLBKsVV7mXEsYM8mkmr6kRaDAPCrAK/+Jv6cZ6eVQVKPVrj94wLGO+Vmv2fTKdCvl0nntoNa4MRhZYb0Gpt7wOsrc+F+KdVhtHjxRSdi45bokouHP9YpaBa8pleXBH2y729eejY65m7HUO5U+L9lkmHbSyLK9Ui9ehadVcrSIm6rTIIxfWtYTk5xrN2ZCZ9tToczRpQzgP8Wy13xaQacN8CagnH6x0XwKVp66CWWmTplcBc5eG8ZWwsPrsiAzdiNxTVUAnrjT8XezxcQZ81faSL4q0GR3eF5bTAyvwWO6s8J6cRBwV4ZeRN1wnaPuVixZRbUli00eHFEcSJDW7E7ET5Ffhx2xBDsqTtbV0w2jjY4k+buIbtZ0pMz3kODu3T42bCl4m5cOo9MaIBDB1QC+evf9klPWI4MEqqXTjQ0fBqHHIDV98AnuwTgs3e4DQ+PudcpL1eXroJt3/yKjT2SjV02l138+Om66d9vsnPSebL5H1uIdscJ3O4oju04YZ9Z0RP9+3n07ngkmroRy5D2T9fqUIMZmM9j4inS96RZeQbRm3hYdjTNT02uZF4S5N754soceHsrbtVKG73UnpuXj3Gd3bMC/my2mOeCllHVbCTNGuL2GY8LgOr1Q+afjNxjIDV7JKTnjur2XwC0IVu3ZwU0VHwFtXs+NDxwSJTkX//+llRYUpYQlMiEzVH1LbXZoKUiloSBNpq9XEaUlbV51XbPupUCtyii9oIxfC9q6eZecMNiZEgT/QYGaUeLTGVksGd1AuMTm7ZsCuI5Zz98KtkmceyoQvH/+/b9E+CM4TwAK9758f3fFS/ZZUGfbk4mZguXkYTXSOMAACAASURBVFMINBLgL0LQe+RYTsxG2v2FH2YEpV/MxzIPH4OubQp527foqnnlAp5tiw6nYK77xtcyV0+icxa5eL/fkQX3fZnJtH5WV7QhGpOQ286/n4p5gT+bRPKsU8CftSFd74vmn4PzWmFK/waL+YeKENdPWq+x3WYRILNNzc4FDOTrK5dabPQkD3K3/PTnVPh0Rw/YIqyBHlo91RN+9oEXnEk/FLAX5VWAD4qsZQ+XB26JOXBccRvccPgu44nvqU2BenREoKMBOXJSA+kPW1px0xVfqu+2p8HfVib5YsqkNtoz8EoM+oK9ElBR44Df2T115FdT/N2lQZ9uUgb+SDx62PyOQOOnd/Vh/DQdVlwLZ7+dB7ub2wzgF23Tq/HQxDoYiW5u4pi5Kg8eQAI3+ZCBn85TvasOaIYPNyfBdgT4qw9qgIP2qmEmI9ogXrSpJ9z3TYq91k8NhAj+vE9rhK/SDB+uxwKwL65lo/NbYT9M5iJI3uT7pH2AtF5jIC0HvwTyRnd6+gcC+bqy5VBfTiC/zGKfF/dNG7Kry5NgeWmPIAI0UcZOq6dr7Q32vI/AIWn3YvoI2/0b00IjTdtRmQrXfZgN6+txAQlTu4+ELVOeczLg5+x1KWr5l1veva7wo8uDPj0k2aOno4H/FKRtWIw84AT64pCpG+4fTzbLQNYVLHDRrCKeIk4x0zDglUy6po8+P9kfXT6vP7gWBqOZiPyky2pS4OkVOfD+joSwwJ8DPT9C1v6lBcDSjvIGke//ftnui0BSWh9IzRgMyVlDoUdSVtwygBKTZWtzNTRV/wANteuhuX6rPcijP/1qNN2sruhh+tQzIVuF4wfomZhlygQDoIM3aOmSrP3LdnpxzdaUw8Zm1e5ZeWmAx2Fu6OsPL2WbuX6PLUiRfMbsLEt6Q34/3uacaAE+USX/4yhOsdAVXDOdZN8tQJ9ufts30w1/5o4GfoGBdpm4ZpxQYWz2Uiau8+dlOdI0MwCVtHS7jd7/TKoykrxQeWHy2YILia29XwFoo3mPTV8O5oE3X7L9K82Z89CuPWWWUj7ffXNaYf/cJiO1o91EJrNQUmo/SM4YAolJmZCaMwqS00vafY+AbPBNddugEYG9uX4HNNWug+aGLUFmGnnMZJv/viwZ1iDIf1Npb5+XsFpBfi5fWaPnwCjOS8UDj0INrOL1zUOAfQ5+TN4xrgYeXZHJ8tK6mXLkZ+rEnUPJTv58WA1kI/99UcAtmepRStGqukQowP0rdSG77r0iWFiONvnA8DoK8NtSRsI+hz1jN9W6xLluA/r0tATwEwd/uBw9xgsZoqmH6gnzkBN1w8ieCfDoiTyJBB2UjOX3C9ODwNlL66e6pJWfgoRVV4037auiXdnkI8YV5Okjv9kELKKysoNrp/3z/u0XAKVZ8yWyWQTY2AIlaDN4MJqD+me2Qgb+TSahwvSWoH0Bp7eSTEWJSdmWy2KRcKpTX7nScqmlucJWW7erT/b4XQhupMXXonvJ5poesA7BtFIgmoP7hJNGT33YgX2QLh0i2IuxT0Ht/LqJO5kTwJNf5sA83EdwM+Ww8UiDVf381c1a9twD5cWG7WE5CXDbkXsMJ4fFG7Phys9S2JBiCfgyN35nSXnoNGf9nO9WoE8CiTXwC1Clf70ieP86uhkm78sZA+m4+5NCeBc9fAQeWjRzF3OPAEuyreZlmvEA89fnQE5aC+yHdLRk8tlZlQrPr8xmJh8xThn86Zzq7cNeSDFAH9o/byPEBcDSCe9MHZcMjrQ3UIhsjP2QlbQgrRUK0lth715NuKfhgKx+3gyfZYjuYHddD9hdj5ut1QmwqyEB1qLjlRMYq83ajtDQ3IN8pOy1+sADD14weG9eZhwqcwRy5Nx6FFc4KAL8ddxLemY9zlgPU47RfqBz2eOHTqlmI9UdkxSdB4/fDalJrUCgf9UiDvpeHjrRMulYAD+xGPqPfaXTB195Td1uB/rMh3/ZJQBN6zABc3to/ByxkCXHsH27uXSKF/XyIchiOazSAOnq+iSY+lae8XVgy9YZAMcgoEas+AsSt1FUpDgI4M+dy/2mpxajL/WYcsPL54ddmeghxImwGH7YbMCqvD6BrnkFB+2ft2W9aCwArF4wqNk0Z53DNlXU+1cnPcmY9gvkIxODiGiRcDsIxGtarB1ukLR1W4u1xzrjBvI0FlWbZ/KwM98w4Qael80Nq2Ozs9nzts3K75+xw9ISBQzeuSLZlhWTj9Ucg/gtAq3k3/S3mzvmWydwlsyXluXDvav505RBXcigmY0kcj58+SZFUnMiUetKvvhu87rbgT4JQwX+s+ekhh21K4QbrmcP1acvgIm5CXDLEaZmThtbZ87JCnLtdNL6qR0BfhTqft+xqD1JRFf/+LQQ3sevBgG0ZMP999HlbP9g5daecP1i7haq0jqo5+zAn72UQhAhLAC8bQl1HBYBS/tuszmkgl4NOVz3+fHgWkwKjXEDeQGclpG4AL1aXvWzl6/bbdLO/sWuIHZX1WXY0keY2j1VE9NkFM7Ve6fwlKO3zC2E2ejib9Xi2zD4ioM9e7z4jxmMFeYzDFTrqsFXXlLplqBPQpGBPxI6ZlnAoQA/1VPNPfSpe9ekcrbJternLPjtfEyuzsqZ5hcBwuw1UDXygMnniaNqYKiUUYhe3N9jpK8AIhHYdeOBjTBh70q46L18KMdMRHTYafl258Q4+JtonWZOC4BcR/0CEC1YFgHWtv3XgNyjdwmv1yC8677wX4l9tAN5Dma8NbevBz/mG7kNO2B388j5zzHVsHeeSJRryoSSj//+456wB9HWTbtXFxlZu3/gsAb417I0WIdumfS8hPnmzanV0D+vDpZv6QWXLEh1BPxogj3dmQB8+rsr+uK7zehuC/okFEq3uHX5eWi2rI6Ih18WsBfwU1k3c48I5CrKaoDfvFPAfJfFoWblEngr2/qnYXDMjUeam7cU0Xvl3DzYYrI/GOD/51FNsL4sEV7dzBNPqGaS/VELG4E0CvN+TsRFgY8i7AVADFaZjdYvC2f4DmcxCA/Kw6xlE9juBPCeIM8EHZC3Ohy7zVMsY6fVU1XVrCPOGc1KLp4vTqmEEjSzEK13dnoTIzMTBwX93bXATAkqt+vHdv8cct4X92yE/36ZDR/hF+dlw1pgbD9OS7K5LB0uRy+17Ri07kapwOUW5vORqnVnwA/Mva5Jw+B3ahBdw/ZVlxnAf/p8DoCRHHbAT+35sfNTORrB3w5uhoLMZrhsAQ9jF4cAfvotJ2gRv2eeYA2MmbM6z2DupDKqiyc7x2aCansHyME0dU8ctwcyU1pgKWpiD6xMNcBf9GfUl8boZAJi3YhyDvgetGHrQ9MPWhBkgfmo7/qsPV4PN2A3btViznHoTQIzJ42eatrZ6plcbRYDFSCtUblmCkTxKO4/rA4OwqjupZuzYfXuZDhzRJnFREgKxMOLc7lzAfYnwF7u38l2P5TSDCIRG5lx5CmwDOfVb+enGmDP2zJNOlrDd52dYV3s1pq+kJgM/ETQdvfKhKjZ+KkPu9y71vN8JKq5hwFrAChlsOZlg80xfz+kASYOMjdvd+Dm7ZmzuZui3WavuH+L2Ye9dSYiU703Typl0b1NGEa/bncGvPdDhuHxw8Zos/Er2va1ALA+neev3Uatk3korLcgCpWEeUZuyjU0yQ3kxYPHf+3acNPqmSgdvgbY2CTNXhY5uV/+dWwjTBxYDgt/zIFblqbA6X3a4PJxpRY7P/nZv/lNDvx7daJjKkPqRkTWikdL3wzX7tcK544uZcMgk9GjX2TBHPwotdPuxX1Ey35PXjp3jg2kOsTGu5tJR56XGvQD0iDg3/rN7xAca6Nm6pEB0Qn4qYyTuYeuyT799NsuoIvOj+gJ8M/jSi2f5PfML4T3aPNWvHlUX376SntsvHIZBP+pLLrSjB2oa+JfQmXIqXLRB72CXSn9LADSeJTh2PL4qGXk356eOxFq+nZgLvfvK+bUC+CF4AMNRwr01Iyq1cvnVLAX9yNAn/I3XIDmFjqOR3Ph7w6usLj+0vlP0f335i+Ee6X128SOSoE9cix2CvLil2Jg4IKAZ7KXOUfUc5sDXtdkt0zy0tlr2D+hV9E4r2pd9roGfenRqqae+5chb00YSdbV2RKquYfqh6r1vzLVStZGxG6XfJRpG9hF7duBpZ3p56HDzWQuVI/c+O7+ir/s4nDS9MV5x/7kRlw0fdtLHbV76wYFil3G3YPHbMgN5O1Az0vTNx+MuTmsissukvaKfVrgrJGlQJHgFyLoC7v9OGSBvWvy7iDPnjU7MuGij0wyQDvtXh6/k+89lWkPd0xqVwX87uKW6TZNNegr0mGUzN9dz7j4o+HHrwIj/Y621n8p+vhfNJbT2dJBn+BXz86HVZihSLzsKuWCHRmaqC/A/4BMgPuPM192iuS96L082CP5rgfb4Hkr9iYZU9hOGrrlvA9g9yziWcDt9Qhcc0FvX/uKSiGnrwMnMKdRhAP0DHCl21OBXm6XPvHO3bsVLj10N1CMyLSZeawmy/A2CDO8SXkgZImRa/GN6NlDDgfCFGlu/wZH1lJdJ+3ees3Hc/EoQlw6145phn2KmpBuufv44XtJToO+jYRUP/77l6TBjF2+Xm8veTsydVJFYaeWzT10Xtb6xW/y8nlmWgV8tyMDxqHbZXKAspaur/ipF1z1aVpQYBfvQwFkF3PMnWOtAV4iWMe00wdv/soC8NL0va6LtoIWiGgAueeTCqOAT3C3gC0Bs8PU8rMIGKO0Yb0U17zAXpQbh7F79xzHTXmvfJUPxw4xM7zRl+MTyzPhioOrYVCB1a2zojYZ/r04B2Ztt0bSquAvpXh23KylvqPhoSOTpzW05sHeBz3YZZKghDEzLVU06DtIkIB/56qboL56MSsRKUmbHRgKwmQOftYoXjqj2voFaAvgPwu5dc4bWWXRwsjD4hykbiafanHI5hdHzZ8PwaKhv4MpG0XCdtrEvXZuASPjctv4dQRqvOC24Svfm1sb6uNytelHa3FwWe/92PTVMl4gbwd89v77pvkm8Pgs4vEL9rL75YKzzf0baow2XF9YmQWztplNP3N0LezbGyeCcpCv/VurU2F/zJdQiBxJN6J5VNxrLDZrxXBkwO8OXDoOEOZ4WoO+h8Rkds5nV6TDveuiq/FbwZ0DPwdAjlh+tP5r0CvipGEVzMNGDurii4OJfE7gT+VUs4qw7wrxiMhM1YsoaAFg6GP2SUk1Tt23BnqmynofZQRLhM0VKTBrXQpbSNzAW/4iCBprqDO+HcvbAbOb1iqXtyvXnkBPYrDztZ97KnerFNndZLBndQIP/CHMDXEIpi1UD3ry5NN/z4Ici2cOmxYOrpj8WnQejAZ8bzlq0PeWEWz/7l6o2vkCKxlp6kW1O3mTl6750fo5mJuaM/3eF/2gbx5XCwcU18CzX+bDE+utaq4T+LMXTmpLgO9LGKjTJ9fMbTv9oyKYb24bsNtwWgDoGnn9XDSq0kKrS5z+ZWgKSEN6iMLMBuYDTl8QP+xCN9B16TAXmR3lw8szx5SX/UP0U9/H47ePkg1U9AtWXgBPzRGT6CkDMKl8ST0UY1T2DZ/kwqY6Ew0Nc48yaDuNnooYfbr48Ku+9i9MwahczMFA3jk3fJFs9CT6kO3205VE5bvQRXjeWszWJiUqZ/NLAnv+O3pUCrIo5AQoxKpaMvLRLk+e5mf+qmU06PuUmpyBK1q+/KJrWZOVN3npuhrQJQOdCCOT65+LJp9fjqhhCSnsgFkGf9GWAEexTJxQYo3qJUK2Sz7GXV0Cegd5yQsA0fReO8H07xZ0vXMl7p9+GHN2xYH1cGBJlREARP3833yT+M0J1KMF5j4fve9ioWr6fVAGx/dvhoE5zdAvpwFy0xuZLDaWZsD96MNOG/Hi8PK+EeXsgF5eAFRCNAHCov5r0yoZ+RkxXv7h8xRDsxdgT+VkL5z3Ty2H5tYeBtjTddmUw38ztcKGRM23aD0LyoDflROgeArCRwEN+j6EJIoQ8O9cfw8kJlCu22S4ZlFixEFccveRaP22iwGeFGChauVU3i66l84/diS6aZaYiPMqbuo9skaKVHbZ/J2K5hwZ8ClH6j0Lc+ALM2bMsnCMxc3DvxxteghtrUhHd8Fg/3+nx6SafuRy0VocvOz2frR9uzb+PKoR+vZqhGL0jCHTnDieW14Az64PHr2TRi+Dusxfp24EC4VfNuuIPkUk7T/HN6JjQAXL5Xz+B5yvScwhO6+csbhGLyUvMUl75wtJ+xGlqXNBTmKuAd8b0DToe8vIUkIN4oqWL7/oxF7rZ6+RReunM37s/WwxCAH81SCv0uoU+MU7OcFeP2LAygLwzORqGCCRdr26Ek1Na4OpLWQT7jkDWuG3h5i2I9o/uHoh/7KwO2Q4HJPTBtccUgE3L8iGn+qjtXPrf1JcuX8TzPwxifXttTiwVm0oGc7G+/+ddP/EenrtZ5xsL1SglxcAL61epUygugL06WvjrLn8GTi5YLJZ6Qj2fOYFX/cvW6+Sw9Gkeceh3CWTjp5Fv4LiA9DdWh+uEtCgH8YEkYO4ounLLw/Fr8mH6kQT/EnLH76XqeUv3JCNkZfIfhgYnJsHzjQyCx1hkr3RgnHGe5zDXz7svjoeR2bQIYWmR8iN84pgWblUyyGy9uphzXDS/qUYU5EMFyOxnCCGo5qqrkxcQn8cw/cptldjEnnMbtWACvb2Wkx8grnoBdOoOl4nMKfEI7dP2gk/Bb5OLPWC2DWtrarL05zTzK8dioi96AOrmYtqW6Nsre25afVUUv4asQN7sbgQ59Mx++xBufSACa/lG51QfRX86aKbKcd6PYwXzaXKVKQiv3UCz2eLudSg9z5/gZy9Jke3ky7amgb9MB8suXRu++pyI31eND175CG5mXyonOzlw3+bte38+1mZQBEqKgNwQVICvHAipq9DhkU66pFy4Q9z8+HrwBqg2v7VBYC0/IG4CSgO4nC5FTlcvA4aww0HNsFxUtYwIv26aUlqUB5VaksG8/sCJGF0/qfydEYN4XZcNLgFJvavteQNJmqJJZt7YcIQ77EabSOo//3QBhjbn9utng+YZILcM10GI2vx8hcSbW5PmVHAajrZ6OVrAtBl8w1dV81ObmAvvganj2qG4/fnHAkfrs2FVhzAF1uTYAb64MttOvncizLW614zILTr1w9JgAtH1fFKyUMw+cl9kN5zQGiNdOPSGvQjePgE/LtW3wl15R+wVpb/lAJ/Xopp8+QZH0H7oqqXyYfKhQL+VF5w+rCXNNDRg4c1wqFozxXHBiRXExws6ljsFoDnj7XysV/1bhFfMKSFyMnOfih+ENw9xfQRJ7K4c+dYc9qqoiSg+quSIYxMI38ImEbcbPqzA5o120N4H4mLPA47bX8GMprmZvB0lPRVc+bs4K8a0aybmYbK/GOcuYDQ7wcXFcKMn4LNVaFo9NSOHdDTeZUygZfF4KyApk+/m3HxWb87He5YmAFrAhjbkWCvkqaR/T5v6DXaQ8dr8irXNeiHKDC74vIGL5l7ohnBK/fnBf4y86Q18Iu3YuftQ+femlaFfvTNkIzmD5lD/bHPcUNxowk8dp4/1K4A15knWmmdD3+1yNn/XlkIcnHAb55mgj7xv/zaxsShmoYeVLiBaDwUOXwXpvlzO14IcMfL9vNQpsJYXI/kRYrq0v7FY/KGt48GBYgTo+XvJ5imMTKr3bI01WLSiUSjF0BP/8pferLJphifwZPHVeFC1gTrdqXDg0vTYRmavdgCIN2LuUnLW2tPu73oVva/b2pJg+KhN0Fe/1N9SFgXUSWgQT9Kc4Jx9qyeznLv0vHGKtSQvo9SxIkyxlDt/ezVVMw+6rnrMMDr0H51MKiQm2eIf+WYN3ODfPHFUOwWgM8wmlPcsdgAVvtx0r7V/QAy1ZzvQwN/6Tie+EM+yCz13IpceHWTs67/0S/5WBf9mA23fZnqOQtUTV0wUnJnRH5QNPQF71qjoeWG3WzyVG7eGaWQkshhmNxXf/OhuZntZbqhOqFq9WJsBNq9EfBnnFbK2FP/sbBnEAsmlbUDezofLfpjp4dw+YAEuOIQ/qnRCANgwIi7NKWC54x1LqBBPwLh2VWVA7nIrfP2JUmwSsp+Fc3unOz91Ifs3y/6tNvwpWvygjAG9w+vOrgO9iuuxRD8NLj7syxYUdXmCP5UXywAs04yNX1KxP6Ld7MNTV82VNhx7giyLzFWwfTolnSdyj6N+wiU4o8WmXwMaBIH2cXvml8QFFAmrn9yJgf9Z78sgGc2hO7cKWzwn6E/+2GSSUxw0Vues8vaLy8EsnmMFt3jkfTMK6jLDejZsw0MxMntki7TMlOCk6Mvxg186VOzl8Ge/vbjthrq3Cdzzk0HtcERg3jat/ScyVC435+1OSdUQSrlNehHKEC76uU/fwDb19zK/PnJ3PP48jR47ufYaP0miAsaB2Q/VDxfVPDnwM0PAcgU4XvZQY0I9m3wh8V8g9PN55+uP3lkncXz5/J3iuAbBBEnYjV5IfibkgDm221ZcMUCk7bX9jFhA+8iPxD5uN+zoBBO26/WkhuY6ACunodRrdYPAdbUxwj6dDyxpABedvkiEP3K4DscF8Z/T9vJ2EzPmpUPD00yaa0JrM9Hbb/MZl/HDcBpdvwNCe4OH2zuqdz9SSG8s81q13cCeRqnHDUrPy8rAYaV+VLcn2qiYfUNX3s2M4J4dHgZ2ycT8UmZIZMay+1/HRQMOj/idnUD7Ll273SJ7TUJKP/ulm9uh4TGr1gXRN/w0HcQ9U1eMX7V5MNeU4YXJvjzcyaI+LX7Uz1jUQh0aLcAXI0morNH8cxIdLyyIh8exgxL6qEuAvk4kMenVFiI4x5YWAhvbA3eyFR18vlncfC+6f0iWIMLzKOTK6FYMvfQF8Alc3MsIDwe+eGFPZ7qfR5I6OF3LtyGuYXJrZFFKmPOAjUvMbGc/n6RmeZSBUYZJ+WF4HykNr5snBmv8AF6z0xfluxotqHxhgL0VN7N7VLcf0eCPY2BomsvHc3dMRMSsqB4+GPanON3cvoop0Hfh5AiKbJr7aNQ/vPjrImtFYnw189SopKYxWlMzuBPNYIJ3eisHfjTebuNX8v5wCAENBclJ8Arp5SynLp0ENf6mXNMf3N1H0Dcw4WDMDgLedzFQRr6ibMQmZX21Xsm8BZUwBfOKoINqNEPRKx9aIrpdkp1iCnyPGl/QAbpI3Gz2c9hZ4Yhl8bpgQ3jpybVGF8Z9AVwzZx89pUjH0HunIqWTIvfLNxrEAdFxV74YfDXDgH97SNbYFN5D3gaN9rtTDfsaQfat9P06bq6OcvPGREZMdXsqW/VnJOWNQ6Kht+tzTl+JmgIZTTohyCscItW7lwMO9ZMZ4lZ6HjkC8wPuqmdvosDg1TpCawmHXfTDzWh1ve7AJyN3D/XHbHbsCXP+T4X7vhKLCvBEqSFYMYJVi2fvIZe2Ijpzm0CbGVN/zxJMz7iFRO8JxW0wS2TyoxNUeqVvGH+hEFmdND+AWnUzG4+Ky8kE8Vw9O585AQOzFeQ+SoQw3Ai0k/ceJTpfUNsp5cGzFNuJhDVXDMDPamI+4YOGt+xb6GvvDRVxJ+PHc7t3L/7NHgT2g7sVYpj8SREOkjBj0Pn7cq2lxlHjIM2a381gmv3dOTsdSkU7nN5uK+cruciAQ36MZoesfLpt7sdN1dPKq/SOfNz/LADXq8F4FaJfZE8aRb92Av+tTIZdjdbFzoKBntgkjUpx7vf5cLfsazXQeO6ZHAr/BozhtHacJgE+gwMh7TChQdbaUHf+iYP7v82CcRiQVQD5wdywXr1J7T0O0Y3wWQ07ZCnzoqtWdA7qwnSkSSNjn65AWf2QGO3fVAIH+0yVy43e7zo/xEE81F9Tcril5fnwz9X97DsZ1+7fyucMrwcvtiMdA1LuKwi1epFG+pWRHuDPdnufzcCmUX78k14YscsGHqLNud4TcgIrmvQj0B44VTdveF52LP5PlaVNnnfWp0WNY5+r/GEY/qhNsNZAC7euw3OOrDS8KghE8vKbWnw7vpk5pEyZUAzjB9Qa7hbkklk3mqMiv3a+auAxiKbiP5ycCMcPbScacRT3+Lp/eTjTwc1w7QDyoxTtAA9tiQPijHBB+09/FyRBr+czSN4/YKbmouYIH0rttOItAWEzORJJA7VvOX0fGRN/oZhLXD6QeaYiY56zpqe0De7BfLSm7H9esZ3Twd9Tfz643SLnV6+l2AA54tuR5pwhAzIlHPW3mZkLaUzLBhwhd6s9XqJo3Bdg34UhBhqE+TTv+fHh42sXGTr/xfSFczZ074mHzFOv6YfKm/n+cPPB9+13RfAtbi5O2lwTZAvvahNzaxB2/VzKzNgXsA6YsfNYydjoRUTeJ/+njWqViwO9x9Wb0n2QRr6prI0xiIq0kr6fX4UQPb6qaUsgI2+Gp5anRT09fLa1GqLxv/3jwvh7QCFgerrLz9tIc7DMaj33mmmmUgdW0NTD9hWmQbbq5Lh1W9TYH6An8if+YY9Ocsi1550CU5yJd6ca8Y2Qh9cyOggV8z8wVdpKgW/EzHCchr0IxRgJNUpkrfsx3uReJEbhomn/9Fve7SbX786VjvwtwK6s+cPlZN1cq9F4CgEs0HIiFmS1QoDcjnUfLUtBR5fZ64e6pKnrivqYvCPQ5rQxbGc8c+f874zK+eLx9ZY8roS/3tSj9agRCFez/KPqIWfdmCZYWu3K39yMcDNk0zQ3lGZCie/08vCIaTel7zROhUpd+6cYiWt21aZgkCfbOHAob6jAfRyO173H+l1daO2CQpgr31u0kRpkQo2xPoa9EMUWLSLk61/z4b/GJm5qH0ib3t1Y1u7uXfa3UOkC4C8CNgtAHRddt50KqOWY4uQzYAJON86gW96yjTAVNTuS0HVwKncEgysugYThfg9nj2mFml8a2DNjky43KodHwAACllJREFUQPGqkcc443irtr9gXQ78MZCFSvWkkfu+FW3bJxxQDqsx2f2aXSkwY10irJZiDGQTlB/TjWjb3gff711Hp5y6UUs0yLmDLtGeOdERb0itaNAPSVztV1j16yeTz5PI+jhjV2xMPvKd2dn+GfgaKqr7F4C8AFjrWeWnevC7LQTqYlCEnxkvYAQwMYKSL/7xb7uTsxHNwNPTKqBAitp9GeMI/oWp/cThJukhGKz21EnctDNjZR7c862TAyqmPERt/09HW000CzD94Ntrk+GTQEyAlerYfl65gTzVsHreWJdH56+A9pvDdi2rG7XEilmy33S9URvbx2DpTYN+BwrfrmuK5v157d2Avi7sMjF3PvENesBUdyz4OwO5dQHgIB9s8PcyBQlZBIdyWfcPiBTsRSR2I/Cl1ILi2IB5dteXpcLNy5IsXxSyjAm4HzquHPIyuafIo58hBYNEKOc2Fe49lJuS6Lh/QQG8ZMOAKeqTiWb65FJmQmIbveU8UIvE8tSXPQ2aYrU/vyBP9eTNWNFOvAA9jUdv1MYZsEjD0aAfh8+GTD6Vm1+GXVueheRE/n1PBG5P/RBbk48sGjstPNirR472NRcpu0XAeRFxfiDqgrAPYukg3L8tyeJ7BVmpbfDASkyO4kJtTXsL04/Zw6gbbp1XCLOd90zZQH6FcQcnH1ALgwtqLTw2BOSnvGtPyUwmnP1wbLJpJlSAZ4uEFCxvgjxdsW7G8kXAevj1RmqP6U8RtecO1xu17SHbaLSpQT8aUmynNsjkU7r+IYOvv715fPzehtsCwCBJMQNxgPdeBNSFwNqW39FZ9w7sao0mUrmxdczd0eu4eGAb9Ecvk01obttWnQDveiwSbuBuB85yeS+Q5wsBr2G3rnUk0NOYVLCHxGIoHHy93qj1mmQxvq5BP8YCD6c7NaKXTD4vI6dNrFw8vcbs7yuAQZalKb8Lgajk5MHvtRfgNf5wrzuBrJ8cOjLAcxCXZWMukN4mn3BHH716KtgTX052yXnQq/85eqM2emKOWksa9KMmyvZviHh8Kra9aLh4EnXzrB+S2o3BM5w7cgJgZ5u+80JA/TuZhtzG5h7eFc5dOWvXXq25gzu7Q6MJL4Bny2bst3Ycb1EFe8pVm7fX+RrsvSZFB1/XoN/BDyDU7oWLZ8XON5Grnkd/kqfPS6tS4OOdHWfzd7oPP4sAB3e1BbvNYHvEC2dhCFXuTuVVUBflrJq7FdztwNvp6yCeQF7cmwb7aM2ejmlHg37HyD3iXsVmb9nPzxvgL2gdYu3jH+rNuJljVC3dvqxNOLA0CNlsFOrYvMoHg7lcI3hRsgNtN/NPPIK8E9hT2sLCfhdqzd5r0sTZdQ36cfZAwhkORfZu3/Cw4eZJ4L9ocyo8tzYhZtG94YzbApfuOG6J/g2lnnVcHp0Yhf3bUCKz60cqtdjU15p9bOQcq1406MdK0jHoh8B/908zjcQt1CUlb5m1AReBDvDzj8Yth7pJ2172fNNsE9pdxbPm7nUnGuy9JNQ5r2vQ75zPzXXU5O2zfd1jFvCPN4+faIk91EUhWv2KdjozqNvJgiJoJ/dDFtTBJre99saJ9qzp2PY06Hes/Nu1d2LzrPr5DagunWn0Q+D/4abEuPL4aVch6MY9JUDRsyf2QbDfuxm5hZqM8knJfSCr8ERts/eUYOcqoEG/cz2vsEZLQV7lG5+GytL3LZu+ZPf/aEtC3Pj7h3VzulLYEji9MAEOLWmD4/e1Zo7Pyj8VMntPhV5F48JuW1eMXwlo0I/fZxP1kQmPn+pd70Bz01ajfXL5/HxLCnywBTqt7T/qwuqiDQ5H/qGpfQEmDzJpEtitIhFafr9fQWbRUTqgqos+e9Mk2SYRfHTxm9W3Z0qA7P41O+ZYtH+6qheArjlLaFP2mAFmWkK6S8pWldP7dMhCrT4rf3jXvHF9V0ES0Jq+nhRAzJ51pQuhYvdcSEww87xSxO8HG5Pg0x2Ymq/evxujFml8SIA2ZU8Z1AYT+jcYCcdpZJSpKiNvIuT1PzU+BqpHEVMJaNCPqbjjvzNaAKp3zjFI3sSI9QIQ/8+ONmQPwVSEZKc/sLjJSEfIRo7kZzm9T4Gskmk6LWH8P8p2HaEG/XYVb+dtnOz/NTs/gdqyhbYLAHH+LMf83foLoGOfMeWbHZ4PML6P1fOGRkWulpl5k/WmbMc+orjrXYN+3D2S+BuQ2wJAewBfb0+GJdsS4AtM7L7FD8Vk/N1ipxkRbcSOzgPU5lthZEmjxWxDN5Gctj+k9RoDGQUTtfdNp3mqsR2oBv3YyrvT90bunzW7FkBd2SKor14cdD9kBvp6ZxKswsRfHZHqsdMLWLkBYbIZjtm4xvdTPG6wbENrHuQXHgGp2SO1501Xe/jtdD8a9NtJsN2lWfICqt29EOorl0FT/fe2i8D6PYm4CCTA+krtEuo1L0iT74u5XchkM6LQ6m0j6qZljYP0vAmQlj1Ke914CVRfD5KABn09KaImATID1ZV9CfXlXzkuAtQZRQVvKKeFAGBNVffcFyANfljPBBjYC2Bgdhv0zmyF0X157l71ECabtJyROgtV1GZr921Ig373ffbtfue0CDRUroaG8hXQULse/14Cra2I8jYHmYW2V/eAHyt6wE5ME7ATPUfjJTNYpIKizVYC96IMzOmb0wJD8puCbPFyH6TJJ2cMgZSsIZCeO0p720T6AHR9iwQ06OsJEVMJ0J5AQ9UP0FT9A34NrITG2m8dFwIaGG0U76jC/2poMeDUyKtK+ZC/rYqPjWPyh++ZDJCJ2vvAbD42Ms307tlidZtUJF2fsD8U5O0LSam9ITVnFCSnl2iAj+ls7J6dadDvns89ru5afBE012+HlvptbDFobthioYpwGzDlD1hXiqgbOOQFQpz7sQKgJgTPIrKpq0dRBjfD0JGV0mYhJ3MbH5lnktL6QGrGYEjOGoogX6xt8XE1A7vXYDTod6/n3enulphCW1uqoRG/DNqaq3Ex2IH/bWf3Yec91BE3SGyUSanIR4wHmWUSkzIhMa0EUjKHaHDviAei+3SVgAZ9PUE6vQTEwiBuRCwQ8o3R10MohwBvuU5CUhba2YcapzQLZSgS1WXjRQIa9OPlSehxaAloCWgJxEACGvRjIGTdhZaAloCWQLxIQIN+vDwJPQ4tAS0BLYEYSECDfgyErLvQEtAS0BKIFwlo0I+XJ6HHoSWgJaAlEAMJaNCPgZB1F1oCWgJaAvEiAQ368fIk9Di0BLQEtARiIAEN+jEQsu5CS0BLQEsgXiSgQT9enoQeh5aAloCWQAwkoEE/BkLWXWgJaAloCcSLBDTox8uT0OPQEtAS0BKIgQQ06MdAyLoLLQEtAS2BeJGABv14eRJ6HFoCWgJaAjGQgAb9GAhZd6EloCWgJRAvEtCgHy9PQo9DS0BLQEsgBhLQoB8DIesutAS0BLQE4kUCGvTj5UnocWgJaAloCcRAAhr0YyBk3YWWgJaAlkC8SECDfrw8CT0OLQEtAS2BGEhAg34MhKy70BLQEtASiBcJ/D+CwPzKTYn0sgAAAABJRU5ErkJggg==",
        url: "https://www.parliament.go.ke/the-national-assembly/",
    },
    {
        name: "Parliament of Kenya: The Senate",
        logo: "https://www.parliament.go.ke/sites/default/files/senate_logo.gif",
        url: "https://www.parliament.go.ke/the-senate/",
    },
    {
        name: "Department of Criminal Investigations",
        logo: "https://dci.ecitizen.go.ke/files/local-download?name=DCI-Logo.jpg&path=063dae540ea70feba651b03954f7725eb3cf35fc.jpg",
        url: "https://www.dci.go.ke/",
    },
    {
        name: "National Intelligence Service (Kenya)",
        logo: "https://upload.wikimedia.org/wikipedia/commons/a/a5/NSIS_Emblem.png",
        url: "https://nis.go.ke/",
    },
    {
        name: "Integrated Population Registration Service",
        logo: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDndhIW0MuB-e93-I6_fZ7oaEwqxcvBSWjtg&s",
        url: "https://usajili.go.ke/",
    },
    {
        name: "Office of the Registrar of Political Parties",
        logo: "https://orpp.or.ke/wp-content/uploads/2025/01/logo_orpp-png.avif",
        url: "https://orpp.or.ke/",
    },
    {
        name: "eCitizen",
        logo: "https://accounts.ecitizen.go.ke/en/images/logo.svg",
        url: "https://www.ecitizen.go.ke/",
    },
    {
        name: "Safaricom",
        logo: "https://www.safaricom.co.ke/images/safaricom-logo-green.png",
        url: "https://www.safaricom.co.ke/",
    },
];

const animatedStats = reactive({
    users: 0,
    services: 0,
    departments: 0,
    activities: 0,
});
let slideInterval;
let animationFrame;

// Animate counter values
const animateCounter = () => {
    const duration = 2000; // 2 seconds
    const startTime = Date.now();

    const startValues = {
        users: 0,
        services: 0,
        departments: 0,
        activities: 0,
    };

    const targetValues = {
        users: props.stats.users || 0,
        services: props.stats.services || 0,
        departments: props.stats.departments || 0,
        activities: props.stats.activities || 0,
    };

    const easeOutQuart = (t) => 1 - Math.pow(1 - t, 4);

    const updateCounter = () => {
        const currentTime = Date.now();
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        Object.keys(animatedStats).forEach((key) => {
            const start = startValues[key];
            const target = targetValues[key];
            const current = Math.floor(
                easeOutQuart(progress) * (target - start) + start
            );
            animatedStats[key] = current;
        });

        if (progress < 1) {
            animationFrame = requestAnimationFrame(updateCounter);
        }
    };

    // Start the animation
    animationFrame = requestAnimationFrame(updateCounter);
};

// Reactive state
const services = reactive({
    data: props.initialServices.data || [],
    current_page: props.initialServices.current_page || 1,
    last_page: props.initialServices.last_page || 1,
    per_page: 6, // Fixed at 6 items per page
    total: props.initialServices.total || 0,
    from: props.initialServices.from || 0,
    to: props.initialServices.to || 0,
    prev_page_url: props.initialServices.prev_page_url || null,
    next_page_url: props.initialServices.next_page_url || null,
    loading: false,
});

const departments = reactive({
    data: props.initialDepartments.data || [],
    current_page: props.initialDepartments.current_page || 1,
    last_page: props.initialDepartments.last_page || 1,
    per_page: 6, // Fixed at 6 items per page
    total: props.initialDepartments.total || 0,
    from: props.initialDepartments.from || 0,
    to: props.initialDepartments.to || 0,
    prev_page_url: props.initialDepartments.prev_page_url || null,
    next_page_url: props.initialDepartments.next_page_url || null,
    loading: false,
});

// Auto slide hero
const startSlideShow = () => {
    slideInterval = setInterval(() => {
        activeSlide.value = (activeSlide.value + 1) % props.heroSlides.length;
    }, 5000);
};

// Scroll to counter
const scrollPartners = (direction) => {
    if (!partnersContainer.value) return;

    const container = partnersContainer.value;
    const itemWidth = 192; // 48 (w-48) * 4 (default scale)
    const scrollAmount = itemWidth * 4; // Scroll 4 items at a time
    const maxScroll = container.scrollWidth - container.clientWidth;

    if (direction === "left") {
        const newScrollLeft = Math.max(0, container.scrollLeft - scrollAmount);
        smoothScroll(container, newScrollLeft, 500);
        currentPartnerSlide.value = Math.max(0, currentPartnerSlide.value - 1);
    } else {
        const newScrollLeft = Math.min(
            maxScroll,
            container.scrollLeft + scrollAmount
        );
        smoothScroll(container, newScrollLeft, 500);
        currentPartnerSlide.value = Math.min(
            Math.ceil(partners.length / 4) - 1,
            currentPartnerSlide.value + 1
        );
    }

    // Reset auto-slide timer
    resetAutoSlide();
};

const smoothScroll = (element, to, duration) => {
    const start = element.scrollLeft;
    const change = to - start;
    const startTime = performance.now();

    const animateScroll = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeInOutQuad =
            progress < 0.5
                ? 2 * progress * progress
                : 1 - Math.pow(-2 * progress + 2, 2) / 2;

        element.scrollLeft = start + change * easeInOutQuad;

        if (progress < 1) {
            requestAnimationFrame(animateScroll);
        }
    };

    requestAnimationFrame(animateScroll);
};

const startAutoSlide = () => {
    if (autoSlideInterval.value) clearInterval(autoSlideInterval.value);

    autoSlideInterval.value = setInterval(() => {
        if (!isHovering.value) {
            const container = partnersContainer.value;
            if (!container) return;

            const maxScroll = container.scrollWidth - container.clientWidth;
            const isAtEnd = container.scrollLeft >= maxScroll - 10; // 10px threshold

            if (isAtEnd) {
                // If at the end, scroll back to the start
                smoothScroll(container, 0, 1000);
                currentPartnerSlide.value = 0;
            } else {
                // Otherwise, scroll to the next set of items
                const itemWidth = 192; // 48 (w-48) * 4
                const scrollAmount = itemWidth * 4;
                const newScrollLeft = Math.min(
                    maxScroll,
                    container.scrollLeft + scrollAmount
                );
                smoothScroll(container, newScrollLeft, 1000);
                currentPartnerSlide.value = Math.min(
                    Math.ceil(partners.length / 4) - 1,
                    currentPartnerSlide.value + 1
                );
            }
        }
    }, 4000); // Change slide every 4 seconds
};

const resetAutoSlide = () => {
    if (autoSlideInterval.value) {
        clearInterval(autoSlideInterval.value);
        startAutoSlide();
    }
};

const goToPartnerSlide = (index) => {
    if (!partnersContainer.value) return;

    const container = partnersContainer.value;
    const itemWidth = 192; // 48 (w-48) * 4
    container.scrollLeft = index * (itemWidth * 4);
    currentPartnerSlide.value = index;
};

const scrollToCounter = () => {
    counterSection.value.scrollIntoView({ behavior: "smooth" });
};

// Helper function to safely log objects
const safeLog = (message, data) => {
    try {
        // Create a safe copy of the data that can be logged
        const safeData = {};
        if (data && typeof data === "object") {
            Object.keys(data).forEach((key) => {
                try {
                    // Convert to string to avoid cross-origin issues
                    safeData[key] =
                        typeof data[key] === "object"
                            ? JSON.parse(JSON.stringify(data[key]))
                            : data[key];
                } catch (e) {
                    safeData[key] = "[Unserializable Data]";
                }
            });
        }
        console.log(message, safeData);
    } catch (e) {
        console.log(message, "[Logging error]");
    }
};

// Fetch data function
const fetchData = async (type, page = 1) => {
    const state = type === "services" ? services : departments;
    const otherType = type === "services" ? "departments" : "services";
    const otherState = type === "services" ? departments : services;

    safeLog(`Fetching ${type} page ${page}`, {
        current_page: state.current_page,
        last_page: state.last_page,
        total: state.total,
    });

    state.loading = true;
    try {
        // Get current page from URL or state
        const currentPage = page || state.current_page || 1;
        const otherPage =
            new URLSearchParams(window.location.search).get(
                `${otherType}_page`
            ) ||
            otherState.current_page ||
            1;

        // Prepare query with both page parameters
        const query = {};
        query[`${type}_page`] = currentPage;
        query[`${otherType}_page`] = otherPage;

        safeLog("Making request with query:", query);
        state.data = [];

        await router.get(route("frontend.welcome"), query, {
            preserveState: true,
            preserveScroll: true,
            only: [
                type === "services" ? "initialServices" : "initialDepartments",
            ],
            onSuccess: (page) => {
                console.log(`${type} page loaded successfully`);
                const data =
                    type === "services"
                        ? page.props.initialServices
                        : page.props.initialDepartments;

                if (data && data.data) {
                    state.data = Array.isArray(data.data) ? [...data.data] : [];
                    state.current_page = data.current_page || 1;
                    state.last_page = data.last_page || 1;
                    state.per_page = data.per_page || 6;
                    state.total = data.total || 0;
                    state.from = data.from || 0;
                    state.to = data.to || 0;
                    state.prev_page_url = data.prev_page_url || null;
                    state.next_page_url = data.next_page_url || null;

                    // Update URL without page reload
                    const url = new URL(window.location);
                    url.searchParams.set(`${type}_page`, state.current_page);
                    window.history.replaceState({}, "", url);

                    safeLog(`Updated ${type} state`, {
                        current_page: state.current_page,
                        last_page: state.last_page,
                        total: state.total,
                        data_length: state.data.length,
                        has_prev: !!state.prev_page_url,
                        has_next: !!state.next_page_url,
                    });
                }
            },
            onError: (errors) => {
                console.error(`Error loading ${type} page ${page}:`, errors);
            },
            onFinish: () => {
                state.loading = false;
            },
        });
    } catch (error) {
        console.error(`Error in fetchData for ${type} page ${page}:`, error);
        state.loading = false;
    }
};

// Refs for section elements
const servicesSection = ref(null);
const departmentsSection = ref(null);

// Go to page
const goToPage = async (page, type) => {
    const state = type === "services" ? services : departments;
    safeLog(`goToPage called for ${type} page ${page}`, {
        currentPage: state.current_page,
        lastPage: state.last_page,
    });

    // Validate page number
    if (page < 1 || page > state.last_page) {
        console.warn(
            `Invalid page number: ${page}. Must be between 1 and ${state.last_page}`
        );
        return;
    }

    if (page === state.current_page) {
        console.log(`Already on page ${page}, skipping navigation`);
        return;
    }

    try {
        console.log(`Initiating fetch for ${type} page ${page}`);

        // Update URL immediately for better UX
        const url = new URL(window.location);
        url.searchParams.set(`${type}_page`, page);
        window.history.pushState({}, "", url);

        await fetchData(type, page);

        // Scroll to the section after the data is loaded
        nextTick(() => {
            const section =
                type === "services"
                    ? servicesSection.value
                    : departmentsSection.value;
            if (section) {
                console.log(`Scrolling to ${type} section`);
                window.scrollTo({
                    top: section.offsetTop - 20,
                    behavior: "smooth",
                });
            } else {
                console.warn(`Could not find section for type: ${type}`);
            }
        });
    } catch (error) {
        console.error(`Error in goToPage for ${type} page ${page}:`, error);
    }
};

// Watch for changes in initial props
watch(
    () => props.initialServices,
    (newVal) => {
        if (newVal) {
            Object.assign(services, {
                data: newVal.data,
                current_page: newVal.current_page,
                last_page: newVal.last_page,
                total: newVal.total,
                from: newVal.from,
                to: newVal.to,
                prev_page_url: newVal.prev_page_url,
                next_page_url: newVal.next_page_url,
            });
        }
    },
    { immediate: true }
);

watch(
    () => props.initialDepartments,
    (newVal) => {
        if (newVal) {
            Object.assign(departments, {
                data: newVal.data,
                current_page: newVal.current_page,
                last_page: newVal.last_page,
                total: newVal.total,
                from: newVal.from,
                to: newVal.to,
                prev_page_url: newVal.prev_page_url,
                next_page_url: newVal.next_page_url,
            });
        }
    },
    { immediate: true }
);

// On mounted
onMounted(() => {
    // Initialize from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const servicesPage = parseInt(urlParams.get("services_page")) || 1;
    const departmentsPage = parseInt(urlParams.get("departments_page")) || 1;

    // Update services state if needed
    if (servicesPage !== services.current_page) {
        services.current_page = servicesPage;
        // Only fetch if we need to load a different page than the initial props
        if (servicesPage > 1) {
            fetchData("services", servicesPage);
        }
    }

    // Update departments state if needed
    if (departmentsPage !== departments.current_page) {
        departments.current_page = departmentsPage;
        // Only fetch if we need to load a different page than the initial props
        if (departmentsPage > 1) {
            fetchData("departments", departmentsPage);
        }
    }

    // Ensure URL has both parameters
    if (!urlParams.has("services_page") || !urlParams.has("departments_page")) {
        const url = new URL(window.location);
        if (!urlParams.has("services_page")) {
            url.searchParams.set("services_page", services.current_page);
        }
        if (!urlParams.has("departments_page")) {
            url.searchParams.set("departments_page", departments.current_page);
        }
        window.history.replaceState({}, "", url);
    }

    startSlideShow();
    startAutoSlide(); // Start the auto-slide functionality

    // Start counter animation when component mounts
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                animateCounter();
                observer.disconnect();
            }
        },
        { threshold: 0.1 }
    );

    if (counterSection.value) {
        observer.observe(counterSection.value);
    }

    // Cleanup function
    return () => {
        if (slideInterval) clearInterval(slideInterval);
        if (animationFrame) cancelAnimationFrame(animationFrame);
        if (autoSlideInterval.value) clearInterval(autoSlideInterval.value);
        if (counterSection.value) {
            observer.unobserve(counterSection.value);
        }
        observer.disconnect();
    };
});

// Cleanup on unmount
onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
    if (animationFrame) cancelAnimationFrame(animationFrame);
    if (autoSlideInterval.value) clearInterval(autoSlideInterval.value);
    if (animationFrame.value) cancelAnimationFrame(animationFrame.value);
});
</script>

<style scoped>
/* Custom scrollbar for partners */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Partner logo hover effect */
.partner-logo {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.partner-logo:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Animations */
.animate-bounce {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%,
    20%,
    50%,
    80%,
    100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Card Description */
.description-text {
    color: #4b5563; /* text-gray-600 */
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.5;
    max-height: 4.5em; /* 3 lines * 1.5 line-height */
}

.dark .description-text {
    color: #d1d5db; /* dark:text-gray-300 */
}
</style>
