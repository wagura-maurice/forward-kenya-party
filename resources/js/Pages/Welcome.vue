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
                    <h2
                        class="text-3xl font-bold text-gray-800 dark:text-white mb-3"
                    >
                        Unity in Diversity for a Better Kenya
                    </h2>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <p
                        class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto"
                    >
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
                    <h2
                        class="text-3xl font-bold text-gray-800 dark:text-white mb-3"
                    >
                        Building Kenya's Future Together
                    </h2>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <p
                        class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto"
                    >
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
                    <h2
                        class="text-3xl font-bold text-gray-800 dark:text-white mb-3"
                    >
                        Our Pillars of Progress
                    </h2>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <p
                        class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto"
                    >
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
                    <h2
                        class="text-3xl font-bold text-gray-800 dark:text-white mb-3"
                    >
                        Our Leadership Structure
                    </h2>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <p
                        class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto"
                    >
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
                    <h2
                        class="text-3xl font-bold text-gray-800 dark:text-white mb-3"
                    >
                        Our Strategic Alliances
                    </h2>
                    <div
                        class="w-20 h-1 bg-gradient-to-r from-green-500 to-blue-500 mx-auto mb-4"
                    ></div>
                    <p
                        class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto"
                    >
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
    // {
    //     name: "Vision 2030",
    //     logo: "https://vision2030.go.ke/wp-content/uploads/2020/02/v2030logo.jpg",
    //     url: "https://vision2030.go.ke/",
    // },
    // {
    //     name: "Government Delivery Unit",
    //     logo: "https://www.delivery.go.ke/img/gduLogo.png",
    //     url: "https://www.delivery.go.ke/",
    // },
    // {
    //     name: "Forward Kenya Party",
    //     logo: "https://www.egpkenya.go.ke/assets/images/kenya-logo.png",
    //     url: "https://www.egpkenya.go.ke/",
    // },
    // {
    //     name: "Public Procurement Regulatory Authority",
    //     logo: "https://ppra.go.ke/wp-content/uploads/2017/05/logo-site.png",
    //     url: "https://ppra.go.ke/",
    // },
    // {
    //     name: "Business Registration Service",
    //     logo: "https://brs.go.ke/wp-content/uploads/2022/10/BRS-Logo-Edited.png",
    //     url: "https://brs.go.ke/",
    // },
    // {
    //     name: "AGPO",
    //     logo: "https://agpo.go.ke/assets/2.0/images/agpo_logo.png",
    //     url: "https://agpo.go.ke/",
    // },
    // {
    //     name: "KRA",
    //     logo: "https://www.kra.go.ke/templates/kra/images/kra/logoKRA.webp",
    //     url: "https://www.kra.go.ke/",
    // },
    // {
    //     name: "eCitizen",
    //     logo: "https://accounts.ecitizen.go.ke/en/images/logo.svg",
    //     url: "https://www.ecitizen.go.ke/",
    // },
    // {
    //     name: "Safaricom",
    //     logo: "https://www.safaricom.co.ke/images/safaricom-logo-green.png",
    //     url: "https://www.safaricom.co.ke/",
    // },
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
