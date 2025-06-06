<script lang="ts" setup>

import {useCalculationStore} from "../stores/calculation";
import {ref} from "vue";

const calculationStore = useCalculationStore();

const calculation = ref(calculationStore.lastCalculation);

const formatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: calculation.value.currency,
});

const percentFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'percent',
});

</script>

<template>
    <div class="flex flex-col gap-4 w-full h-full">
        <div class="lg:flex-1 w-full rounded-3xl border border-secondary">
            <div class="px-6 pt-6 pb-6 flex justify-between gap-4">
                <div class="flex flex-col gap-6">
                    <div class="inline-flex items-center gap-2">
                        <span class="text-xl font-semibold">Первая Счетоводческая Компания</span>
                    </div>
                    <p class="text-sm text-secondary-500 dark:text-secondary-400">Украина</p>
                </div>
                <div class="inline-flex flex-col items-end justify-between">
                    <span class="text-lg md:text-2xl font-medium text-right">Счет</span>
                    <span class="text-base md:text-lg text-secondary-500 dark:text-secondary-400 text-right">#{{ calculation.number }}</span>
                </div>
            </div>

            <div class="flex items-center gap-6 py-2 px-6 bg-secondary-100 dark:bg-secondary-800">
                <div class="flex-1 font-medium">Продукт</div>
                <div class="flex-1 flex items-center gap-6">
                    <span class="flex-1 font-medium text-center">Кол-во</span>
                    <span class="flex-1 font-medium text-right">Цена за ед.</span>
                    <span class="flex-1 font-medium text-right">Итого</span>
                </div>
            </div>
            <div class="flex items-center gap-6 py-4 px-6">
                <div class="flex-1 line-clamp-1">{{ calculation.product.name }}</div>
                <div class="flex-1 flex items-center gap-6">
                    <span class="flex-1 text-center">{{ calculation.product.quantity }}</span>
                    <span class="flex-1 text-right">{{ formatter.format(calculation.product.price) }}</span>
                    <span class="flex-1 text-right">{{ formatter.format(calculation.product.total) }}</span>
                </div>
            </div>

            <div class="p-6 flex flex-col items-end gap-4 justify-start">
                <div class="w-full md:w-1/2 flex flex-col gap-4">
                    <table class="min-w-full">
                        <thead class="bg-secondary-100 dark:bg-secondary-800">
                        </thead>
                        <tbody>
                        <tr v-for="rule of calculation.appliedRules" :key="rule.id" class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium line-clamp-1">
                                {{ rule.label }}
                            </td>
                            <td class="pl-6 py-4 text-right font-medium">
                                {{ formatter.format(rule.value) }}
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="px-6 py-4 font-bold line-clamp-1">
                                Итого
                            </td>
                            <td class="pl-6 py-4 text-right font-medium">
                                {{ formatter.format(calculation.total) }}
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="w-full mx-auto md:max-w-64 flex items-center justify-between gap-8">
            <button class="btn btn-success w-full" @click="calculationStore.resetCalculation">
                Посчитать еще
            </button>
        </div>
    </div>
</template>

<style scoped>

</style>
