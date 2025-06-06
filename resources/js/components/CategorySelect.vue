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
    if (calculationStore.categories.length > 0) {
        loading.value = false;

        return;
    }

    await calculationStore.fetchCategories();

    loading.value = false;
});
</script>

<template>
    <FormField
        label-for="category"
        label="Категория"
        :required="true"
        :errors="errors"
        :loading="loading"
    >
        <select name="category" class="custom-input" v-model="modelValue">
            <option :value="null">Выберите категорию</option>
            <option
                v-for="category in calculationStore.categories"
                :key="category.id"
                :value="category.id"
            >
                {{ category.name }}
            </option>
        </select>
    </FormField>
</template>

<style scoped>

</style>
