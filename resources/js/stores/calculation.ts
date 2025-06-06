/**
 * Created by WebStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 21:59
 */
import {defineStore} from "pinia";
import {
    CalculationStoreState,
    CalculationStoreGetters,
    CalculationStoreActions,
    PriceCalculationResult, CalculationFormData, PriceCalculation
} from "../types";
import {useToast} from "vue-toastification";
import {AxiosError} from "axios";

export const useCalculationStore = defineStore<'calculation', CalculationStoreState, CalculationStoreGetters, CalculationStoreActions>('calculation', {
    state: (): CalculationStoreState => ({
        categories: [],
        locations: [],

        lastCalculation: null,
    }),
    actions: {
        async fetchCategories(): Promise<void> {
            try {
                const response = await window.axios.get('/api/categories');

                if (response.status === 200 && response.data.data && response.data.data.length > 0) {
                    this.categories = response.data.data;
                } else {
                    throw new Error('Could not get categories from response');
                }
            } catch (e) {
                console.error(e);

                useToast().error('Не удалось получить список категорий!');
            }
        },

        async fetchLocations(): Promise<void> {
            try {
                const response = await window.axios.get('/api/locations');

                if (response.status === 200 && response.data.data && response.data.data.length > 0) {
                    this.locations = response.data.data;
                } else {
                    throw new Error('Could not get locations from response');
                }
            } catch (e) {
                console.error(e);

                useToast().error('Не удалось получить список локаций!');
            }
        },

        async calculate(data: CalculationFormData): Promise<PriceCalculationResult> {
            try {
                const response = await window.axios.post('/api/price-calculation/calculate', data);

                if (response.status === 200 && response.data.data) {
                    return {
                        success: true,
                        data: response.data.data,
                    }
                } else {
                    throw new Error(`Could not get calculation results from response`);
                }
            } catch (e) {
                if (e instanceof AxiosError && e.status === 422) {
                    return {success: false, errors: e.response.data.errors};
                }

                return {success: false, message: 'Ошибка получения расчета!'}
            }
        },

        storeLastCalculation(data: PriceCalculation): void {
            this.lastCalculation = data;
        },

        resetCalculation(): void {
            this.lastCalculation = null;
        }
    },
    getters: {
        hasCalculation: state => state.lastCalculation !== null,
    },

    persist: {
        pick: ['lastCalculation']
    }
});
