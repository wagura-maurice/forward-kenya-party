<!-- resources/js/Layouts/AppLayout.vue -->
<script setup>
import { ref, computed, watch, onUnmounted } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const showingNavigationDropdown = ref(false);
const activeDepartment = ref(null);

// Desktop dropdown state
const isPlatformDropdownOpen = ref(false);

// Mobile dropdown state
const isMobilePlatformDropdownOpen = ref(false);

// Use live data from the backend
const navigationData = computed(() => ({
    departments: usePage().props.navigation?.departments || []
}));

const props = defineProps({
    title: String,
    navigation: {
        type: Object,
        default: () => ({
            departments: [],
        }),
    },
});

// Get authenticated user from Inertia
const user = computed(() => usePage().props.auth?.user || null);

// Check if user has administrator role
const isAdmin = computed(() => {
    return Array.isArray(user.value?.roles) && user.value.roles.includes('administrator');
});

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};

const setActiveDepartment = (departmentId) => {
    // Only update if we're not already on this department
    if (activeDepartment.value !== departmentId) {
        activeDepartment.value = departmentId;
    }
    // Keep the dropdown open when changing departments
    isPlatformDropdownOpen.value = true;
};

const resetActiveDepartment = () => {
    activeDepartment.value = null;
};

// Track if mouse is over dropdown or panel
const isMouseOverDropdown = ref(false);
const isMouseOverPanel = ref(false);
const dropdownTimeout = ref(null);

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    const dropdown = document.querySelector("[data-platform-dropdown]");
    const dropdownPanel = document.querySelector("[data-dropdown-panel]");
    const target = event.target;

    // Only close if click is outside both dropdown and panel
    if (
        dropdown &&
        dropdownPanel &&
        !dropdown.contains(target) &&
        !dropdownPanel.contains(target)
    ) {
        isPlatformDropdownOpen.value = false;
        activeDepartment.value = null; // Reset active department when closing
    }
};

// Handle dropdown hover states
const onDropdownEnter = () => {
    clearTimeout(dropdownTimeout.value);
    isMouseOverDropdown.value = true;
    isPlatformDropdownOpen.value = true;
};

const onPanelEnter = () => {
    clearTimeout(dropdownTimeout.value);
    isMouseOverPanel.value = true;
};

const onDropdownLeave = () => {
    isMouseOverDropdown.value = false;
    // Only start close timeout if not over panel
    if (!isMouseOverPanel.value) {
        dropdownTimeout.value = setTimeout(() => {
            if (!isMouseOverPanel.value) {
                isPlatformDropdownOpen.value = false;
                activeDepartment.value = null;
            }
        }, 300);
    }
};

const onPanelLeave = () => {
    isMouseOverPanel.value = false;
    // Only start close timeout if not over dropdown
    if (!isMouseOverDropdown.value) {
        dropdownTimeout.value = setTimeout(() => {
            if (!isMouseOverDropdown.value) {
                isPlatformDropdownOpen.value = false;
                activeDepartment.value = null;
            }
        }, 300);
    }
};

// Add click outside listener when dropdown is open
watch(isPlatformDropdownOpen, (isOpen) => {
    if (isOpen) {
        document.addEventListener("click", handleClickOutside);
    } else {
        document.removeEventListener("click", handleClickOutside);
    }
});

// Cleanup event listeners when component is unmounted
onUnmounted(() => {
    clearTimeout(dropdownTimeout.value);
    document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('frontend.welcome')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('frontend.about-us')"
                                    :active="route().current('frontend.about-us')"
                                >
                                    About Us
                                </NavLink>
                                <!-- Platform Dropdown - Desktop -->
                                <div
                                    class="hidden sm:flex sm:items-center sm:ml-4 relative"
                                    data-platform-dropdown
                                    @mouseenter="onDropdownEnter"
                                    @mouseleave="onDropdownLeave"
                                >
                                    <!-- <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                    >
                                        Our Work
                                        <i
                                            class="fas fa-chevron-down ml-2 -mr-0.5 h-4 w-4"
                                        ></i>
                                    </button> -->

                                    <!-- Desktop Dropdown Panel -->
                                    <div
                                        v-show="isPlatformDropdownOpen"
                                        data-dropdown-panel
                                        class="absolute left-0 top-full mt-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                        @mouseenter="onPanelEnter"
                                        @mouseleave="onPanelLeave"
                                    >
                                        <div class="py-1">
                                            <div
                                                v-for="department in navigationData.departments"
                                                :key="`dept-${department.id}`"
                                                class="relative group"
                                                @mouseenter="
                                                    setActiveDepartment(
                                                        department.id
                                                    )
                                                "
                                                @mouseleave="
                                                    resetActiveDepartment()
                                                "
                                            >
                                                <Link
                                                    :href="department.url"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex justify-between items-center"
                                                    @click="
                                                        isPlatformDropdownOpen = false
                                                    "
                                                >
                                                    {{ department.name }}
                                                    <i
                                                        v-if="
                                                            department.services
                                                                .length
                                                        "
                                                        class="fas fa-chevron-right text-xs"
                                                    ></i>
                                                </Link>

                                                <!-- Services Submenu -->
                                                <div
                                                    v-if="
                                                        activeDepartment ===
                                                            department.id &&
                                                        department.services
                                                            .length
                                                    "
                                                    class="absolute left-full top-0 ml-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                                    @mouseenter="onPanelEnter"
                                                    @mouseleave="onPanelLeave"
                                                >
                                                    <div class="py-1">
                                                        <template v-if="department.services.length">
                                                            <Link
                                                                v-for="service in department.services"
                                                                :key="`service-${service.id}`"
                                                                :href="service.url"
                                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                                @click="
                                                                    isPlatformDropdownOpen = false
                                                                "
                                                            >
                                                                {{ service.name }}
                                                            </Link>
                                                        </template>
                                                        <div v-else class="px-4 py-2 text-sm text-gray-500 italic">
                                                            No services available
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    !navigationData.departments
                                                        .length
                                                "
                                                class="px-4 py-2 text-sm text-gray-500 italic"
                                            >
                                                No departments available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ms-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown
                                    v-if="$page.props.jetstream.hasTeamFeatures"
                                    align="right"
                                    width="60"
                                >
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.auth.user
                                                        .current_team.name
                                                }}

                                                <svg
                                                    class="ms-2 -me-0.5 size-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Wing Management -->
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                Manage Wing
                                            </div>

                                            <!-- Wing Settings -->
                                            <DropdownLink
                                                :href="
                                                    route(
                                                        'teams.show',
                                                        $page.props.auth.user
                                                            .current_team
                                                    )
                                                "
                                            >
                                                Wing Settings
                                            </DropdownLink>

                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .canCreateTeams
                                                "
                                                :href="route('teams.create')"
                                            >
                                                New Party Wing
                                            </DropdownLink>

                                            <!-- Wing Switcher -->
                                            <template
                                                v-if="
                                                    $page.props.auth.user
                                                        .all_teams.length > 1
                                                "
                                            >
                                                <div
                                                    class="border-t border-gray-200"
                                                />

                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    Switch Wings
                                                </div>

                                                <template
                                                    v-for="team in $page.props
                                                        .auth.user.all_teams"
                                                    :key="team.id"
                                                >
                                                    <form
                                                        @submit.prevent="
                                                            switchToTeam(team)
                                                        "
                                                    >
                                                        <DropdownLink
                                                            as="button"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <svg
                                                                    v-if="
                                                                        team.id ==
                                                                        $page
                                                                            .props
                                                                            .auth
                                                                            .user
                                                                            .current_team_id
                                                                    "
                                                                    class="me-2 size-5 text-green-400"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke-width="1.5"
                                                                    stroke="currentColor"
                                                                >
                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                    />
                                                                </svg>

                                                                <div>
                                                                    {{
                                                                        team.name
                                                                    }}
                                                                </div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                        >
                                            <img
                                                class="size-8 rounded-full object-cover"
                                                :src="
                                                    $page.props.auth.user
                                                        .profile_photo_path
                                                "
                                                :alt="
                                                    $page.props.auth.user.name
                                                "
                                            />
                                        </button>

                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150 capitalize"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 size-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Manage Account
                                        </div>

                                        <DropdownLink
                                            :href="route('profile')"
                                        >
                                            My Profile
                                        </DropdownLink>

                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            Account Settings
                                        </DropdownLink>

                                        <DropdownLink
                                            v-if="
                                                $page.props.jetstream
                                                    .hasApiFeatures
                                            "
                                            :href="route('api-tokens.index')"
                                        >
                                            Developer Access
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />
                                        
                                        <!-- System Settings -->
                                        <DropdownLink
                                            :href="route('settings')"
                                            v-if="isAdmin"
                                        >
                                            System Settings
                                        </DropdownLink>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Sign Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                            >
                                <svg
                                    class="size-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('frontend.about-us')"
                            :active="route().current('frontend.about-us')"
                        >
                            About Us
                        </ResponsiveNavLink>

                        <!-- Mobile Platform Dropdown -->
                        <div class="pt-2 pb-3 space-y-1">
                            <!-- <button
                                @click="
                                    isMobilePlatformDropdownOpen =
                                        !isMobilePlatformDropdownOpen
                                "
                                class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md"
                            >
                                <span>Our Work</span>
                                <i
                                    class="fas transition-transform duration-200"
                                    :class="
                                        isMobilePlatformDropdownOpen
                                            ? 'fa-chevron-up'
                                            : 'fa-chevron-down'
                                    "
                                ></i>
                            </button> -->

                            <div
                                v-show="isMobilePlatformDropdownOpen"
                                class="pl-4 space-y-1"
                            >
                                <div class="mb-2">
                                    <div class="text-green-600">
                                        Party Departments ({{
                                            navigationData.departments
                                                ?.length || 0
                                        }})
                                        <span
                                            v-if="
                                                !navigationData.departments
                                                    ?.length
                                            "
                                            class="text-yellow-600 text-xs"
                                        >
                                            (No departments available)
                                        </span>
                                    </div>
                                </div>
                                <div
                                    v-for="department in navigationData.departments"
                                    :key="`mob-dept-${department.id}`"
                                    class="space-y-1"
                                >
                                    <div class="flex items-center w-full">
                                        <div class="flex-1">
                                            <Link
                                                :href="department.url"
                                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md font-medium"
                                                @click="isMobilePlatformDropdownOpen = false"
                                            >
                                                {{ department.name }}
                                            </Link>
                                        </div>
                                        <button
                                            v-if="department.services.length"
                                            @click.stop="
                                                setActiveDepartment(
                                                    activeDepartment === department.id
                                                        ? null
                                                        : department.id
                                                )
                                            "
                                            class="px-3 py-2 text-gray-400 hover:text-gray-500"
                                            aria-label="Toggle services"
                                        >
                                            <i
                                                class="fas fa-chevron-down text-xs transition-transform duration-200"
                                                :class="{
                                                    'rotate-180': activeDepartment === department.id
                                                }"
                                            ></i>
                                        </button>
                                    </div>

                                    <!-- Mobile Services Submenu -->
                                    <div
                                        v-if="
                                            activeDepartment ===
                                                department.id &&
                                            department.services.length
                                        "
                                        class="pl-4"
                                    >
                                        <Link
                                            v-for="service in department.services"
                                            :key="`mob-service-${service.id}`"
                                            :href="service.url"
                                            class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md"
                                            @click="
                                                isMobilePlatformDropdownOpen = false
                                            "
                                        >
                                            {{ service.name }}
                                        </Link>
                                        <!-- <div
                                            class="px-3 py-2 text-sm text-gray-500 italic"
                                        >
                                            No services available
                                        </div> -->
                                    </div>
                                    <div
                                        v-if="activeDepartment === department.id && !department.services.length"
                                        class="pl-8 py-2 text-sm text-gray-500 italic"
                                    >
                                        No services available
                                    </div>
                                </div>
                                <div
                                    v-if="!navigationData.departments?.length"
                                    class="px-3 py-2 text-sm text-gray-500 italic"
                                >
                                    No departments available
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div
                                v-if="
                                    $page.props.jetstream.managesProfilePhotos
                                "
                                class="shrink-0 me-3"
                            >
                                <img
                                    class="size-10 rounded-full object-cover"
                                    :src="
                                        $page.props.auth.user.profile_photo_path
                                    "
                                    :alt="$page.props.auth.user.name"
                                />
                            </div>

                            <div>
                                <div
                                    class="font-medium text-base text-gray-800 capitalize"
                                >
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('profile')"
                                :active="route().current('profile')"
                            >
                                My Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink
                                :href="route('profile.show')"
                                :active="route().current('profile.show')"
                            >
                                Account Settings
                            </ResponsiveNavLink>

                            <ResponsiveNavLink
                                v-if="$page.props.jetstream.hasApiFeatures"
                                :href="route('api-tokens.index')"
                                :active="route().current('api-tokens.index')"
                            >
                                Developer Access
                            </ResponsiveNavLink>

                            <ResponsiveNavLink
                                :href="route('settings')"
                                :active="route().current('settings')"
                                v-if="props.role === 'administrator'"
                            >
                                System Settings
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Sign Out
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template
                                v-if="$page.props.jetstream.hasTeamFeatures"
                            >
                                <div class="border-t border-gray-200" />

                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Manage Party Wing
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink
                                    :href="
                                        route(
                                            'teams.show',
                                            $page.props.auth.user.current_team
                                        )
                                    "
                                    :active="route().current('teams.show')"
                                >
                                    Wing Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink
                                    v-if="$page.props.jetstream.canCreateTeams"
                                    :href="route('teams.create')"
                                    :active="route().current('teams.create')"
                                >
                                    Create New Wing
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template
                                    v-if="
                                        $page.props.auth.user.all_teams.length >
                                        1
                                    "
                                >
                                    <div class="border-t border-gray-200" />

                                    <div
                                        class="block px-4 py-2 text-xs text-gray-400"
                                    >
                                        Switch Wings
                                    </div>

                                    <template
                                        v-for="team in $page.props.auth.user
                                            .all_teams"
                                        :key="team.id"
                                    >
                                        <form
                                            @submit.prevent="switchToTeam(team)"
                                        >
                                            <ResponsiveNavLink as="button">
                                                <div class="flex items-center">
                                                    <svg
                                                        v-if="
                                                            team.id ==
                                                            $page.props.auth
                                                                .user
                                                                .current_team_id
                                                        "
                                                        class="me-2 size-5 text-green-400"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
