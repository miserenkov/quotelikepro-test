<script lang="ts" setup>

import {computed, defineProps} from "vue";

const props = defineProps({
    labelFor: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: false,
    },
    placeholder: {
        type: String,
        required: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: [String, Array],
        default: () => [],
    },
});

const errorList = computed(() =>
    Array.isArray(props.errors) ? props.errors : [props.errors]
);
</script>

<template>
    <div class="form-field flex flex-col gap-y-1">
        <label v-if="label && label.length > 0" :for="labelFor" class="block text-sm font-medium">
            {{ label }}

            <span v-if="required" class="text-danger-500">*</span>
        </label>

        <div>
            <div class="custom-input animate-pulse h-[42px]" v-if="loading">

            </div>

            <slot v-else />
        </div>

        <span v-for="(error, index) in errorList" :key="index" class="flex items-center font-medium tracking-wide text-danger-500 text-xs ml-1">
            {{ error }}
		</span>
    </div>
</template>

<style scoped>

</style>
