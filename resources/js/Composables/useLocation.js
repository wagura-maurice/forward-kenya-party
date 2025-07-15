import { reactive, computed } from "vue";

export default function useLocation(locationData) {
    const state = reactive({
        selectedCounty: null,
        selectedSubCounty: null,
        selectedConstituency: null,
        selectedWard: null,
    });

    // Computed properties for filtered data
    const filteredSubCounties = computed(() => {
        if (!state.selectedCounty) return [];
        return locationData.subCounties.filter(
            (subCounty) => subCounty.county_id == state.selectedCounty
        );
    });

    const filteredConstituencies = computed(() => {
        if (!state.selectedCounty) return [];
        return locationData.constituencies.filter(
            (constituency) => constituency.county_id == state.selectedCounty
        );
    });

    const filteredWards = computed(() => {
        if (!state.selectedCounty) return [];

        return locationData.wards.filter((ward) => {
            const matchesCounty = ward.county_id == state.selectedCounty;
            const matchesConstituency =
                !state.selectedConstituency ||
                ward.constituency_id == state.selectedConstituency;
            return matchesCounty && matchesConstituency;
        });
    });

    // Reset dependent fields when parent changes
    const resetDependentFields = (field) => {
        const resetMap = {
            county: () => {
                state.selectedSubCounty = null;
                state.selectedConstituency = null;
                state.selectedWard = null;
            },
            constituency: () => {
                state.selectedWard = null;
            },
        };

        if (field in resetMap) {
            resetMap[field]();
        }
    };

    // Watch for changes to update form values
    const formValues = computed(() => ({
        county_id: state.selectedCounty,
        sub_county_id: state.selectedSubCounty,
        constituency_id: state.selectedConstituency,
        ward_id: state.selectedWard,
    }));

    return {
        state,
        filteredSubCounties,
        filteredConstituencies,
        filteredWards,
        resetDependentFields,
        formValues,
    };
}
