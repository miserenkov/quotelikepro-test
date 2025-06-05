/**
 * Created by WebStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 20:27
 */
import {_GettersTree} from "pinia";

export type LayoutTheme = 'light' | 'dark';

export interface LayoutStoreState {
    theme: LayoutTheme;
}

export interface LayoutStoreGetters extends _GettersTree<LayoutStoreState> {
    isDarkTheme: (state: LayoutStoreState) => boolean;
}

export interface LayoutStoreActions {
    changeTheme(theme: LayoutTheme): void;
}
