<script lang="ts" setup>
import {useLayoutStore} from "../stores/layout";
import {computed, onMounted} from "vue";
import CalculationForm from "./CalculationForm.vue";
import CalculationResult from "./CalculationResult.vue";
import {useCalculationStore} from "../stores/calculation";

const layoutStore = useLayoutStore();
const calculationStore = useCalculationStore();

const onToggleTheme = () => {
    if (layoutStore.isDarkTheme) {
        layoutStore.changeTheme('light');
        document.querySelector("html").classList.remove('dark');
    } else {
        layoutStore.changeTheme('dark');
        document.querySelector("html").classList.add('dark');
    }
};

onMounted(() => {
    if (layoutStore.isDarkTheme) {
        document.querySelector("html").classList.add('dark');
    }
});
</script>

<template>
    <div class="bg-secondary-100 dark:bg-secondary-950 p-5 md:p-12 w-screen min-h-screen h-full min-w-[375px]">
        <div class="bg-white dark:bg-secondary-700 p-4 md:p-6 shadow rounded-2xl flex flex-col gap-4 xl:max-w-7xl mx-auto relative">
            <div class="flex flex-row items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col gap-2">
                        <div class="text-lg md:text-2xl font-semibold leading-tight text-secondary-900 dark:text-secondary-100">Калькулятор стоимости</div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex flex-row justify-between toggle">
                        <label for="dark-toggle" class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="dark-mode" id="dark-toggle" class="checkbox hidden" :checked="layoutStore.isDarkTheme" @click="onToggleTheme">
                                <div class="block border-[1px] dark:border-white border-secondary-900 w-10 h-5 md:w-14 md:h-8 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 dark:bg-white bg-secondary-800 w-3 h-3 md:w-6 md:h-6 rounded-full transition"></div>
                            </div>
                            <div class="ml-3 font-medium">
                                Темная тема
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-1">
                <CalculationResult v-if="calculationStore.hasCalculation" />
                <CalculationForm v-else />
            </div>
        </div>
    </div>
</template>

<style scoped>
input:checked ~ .dot {
    transform: translateX(100%);
    background: var(--color-primary-500);
}
</style>
