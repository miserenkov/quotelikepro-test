<script lang="ts" setup>
import FormField from "./FormField.vue";
import {onMounted, ref, Ref, defineModel, defineProps} from "vue";
import {useCalculationStore} from "../stores/calculation";

defineProps({
    errors: {
        type: [String, Array],
        default: () => [],
    },
});

const modelValue = defineModel<number|null>();
const loading: Ref<boolean> = ref(true);
const calculationStore = useCalculationStore();

onMounted(async () => {
    if (calculationStore.locations.length > 0) {
        loading.value = false;

        return;
    }

    await calculationStore.fetchLocations();

    loading.value = false;
});
</script>

<template>
    <FormField
        label-for="location"
        label="Локация"
        :required="true"
        :errors="errors"
        :loading="loading"
    >
        <select name="location" class="custom-input" v-model="modelValue">
            <option :value="null">Выберите локацию</option>
            <option
                v-for="location in calculationStore.locations"
                :key="location.id"
                :value="location.id"
            >
                {{ location.name }}
            </option>
        </select>
    </FormField>
</template>

<style scoped>

</style>
