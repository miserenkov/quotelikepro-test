/**
 * Created by WebStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 20:27
 */

import { defineStore } from 'pinia';
import {LayoutStoreActions, LayoutStoreGetters, LayoutStoreState, LayoutTheme} from "../types";

export const useLayoutStore = defineStore<'layout', LayoutStoreState, LayoutStoreGetters, LayoutStoreActions>('layout', {
    state: (): LayoutStoreState => ({
        theme: 'light',
    }),
    actions: {
        changeTheme(theme: LayoutTheme) {
            this.theme = theme;
        }
    },
    getters: {
        isDarkTheme: (state) => state.theme === 'dark',
    },

    persist: true,
});
