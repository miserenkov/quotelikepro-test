<script lang="ts" setup>

import CategorySelect from "./CategorySelect.vue";
import {onMounted, ref} from "vue";
import {APIValidationErrors, CalculationFormData} from "../types";
import FormField from "./FormField.vue";
import LocationSelect from "./LocationSelect.vue";
import {useCalculationStore} from "../stores/calculation";
import {useToast} from "vue-toastification";

const newCalculationForm = ref<CalculationFormData>({});
const formErrors = ref<APIValidationErrors>({});

const loading = ref<boolean>(false);
const loadingTimeout = ref<NodeJS.Timeout|null>(null);
const calculationStore = useCalculationStore();
const toast = useToast();

const resetForm = () => {
    formErrors.value = {};
    newCalculationForm.value = {
        basePrice: null,
        quantity: null,
        categoryId: null,
        locationId: null,
    }
};

const setLoading = (value: boolean) => {
    clearTimeout(loadingTimeout.value);

    loadingTimeout.value = setTimeout(() => loading.value = value, 100);
}

const calculate = async () => {
    formErrors.value = {};

    setLoading(true);

    const result = await calculationStore.calculate(newCalculationForm.value);

    setLoading(false);

    if (result.success === true) {
        calculationStore.storeLastCalculation(result.data);

        return;
    }

    if (result.errors) {
        formErrors.value = result.errors;
    }

    if (result.message) {
        toast.error(result.message);
    }
}

onMounted(() => {
    resetForm();
});
</script>

<template>
    <div class="w-full h-full">
        <div class="absolute w-full h-full top-0 left-0" v-show="loading">
            <div class="absolute bg-secondary-50/70 w-full h-full rounded-2xl z-0"></div>
            <div class="absolute top-1/2 left-1/2 -translate-1/2 z-10 w-12 h-12 rounded-full animate-spin border-2 border-solid border-primary-500 border-t-transparent shadow-md"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
            <div class="col-span-1">
                <FormField
                    label-for="basePrice"
                    label="Базовая цена продукта"
                    :required="true"
                    :errors="formErrors.basePrice"
                >
                    <input
                        type="number"
                        class="custom-input"
                        name="basePrice"
                        placeholder="Введите базовую цену продукта"
                        v-model="newCalculationForm.basePrice"
                        min="0.01"
                        max="999999999.99"
                    />
                </FormField>
            </div>

            <div class="col-span-1">
                <FormField
                    label-for="quantity"
                    label="Количество единиц продукта"
                    :required="true"
                    :errors="formErrors.quantity"
                >
                    <input
                        type="number"
                        class="custom-input"
                        name="quantity"
                        placeholder="Введите количество единиц продукта"
                        v-model="newCalculationForm.quantity"
                        min="1"
                        max="999999999.99"
                    />
                </FormField>
            </div>

            <div class="col-span-1">
                <CategorySelect v-model="newCalculationForm.categoryId" :errors="formErrors.categoryId"></CategorySelect>
            </div>

            <div class="col-span-1">
                <LocationSelect v-model="newCalculationForm.locationId" :errors="formErrors.locationId"></LocationSelect>
            </div>

            <div class="col-span-full">
                <div class="w-full mx-auto md:max-w-96 flex items-center justify-between gap-8">
                    <button class="btn btn-secondary w-1/2" @click="resetForm">
                        Сбросить
                    </button>
                    <button class="btn btn-primary w-1/2" @click="calculate">
                        Рассчитать
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
