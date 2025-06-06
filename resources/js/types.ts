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

export type APIValidationErrors = Record<string, string[]>;

export interface CategoryItem {
    id: number;
    name: string;
}

export interface LocationItem {
    id: number;
    name: string;
}

export interface CalculationFormData {
    basePrice?: number|null;
    quantity?: number|null;
    categoryId?: number|null;
    locationId?: number|null;
}

export interface ProductItem {
    name: string;
    price: number;
    quantity: number;
    total: number;
}

export interface AppliedRule {
    id: string;
    label: string;
    percent: number;
    value: number;
    newPrice: number;
}

export interface PriceCalculation {
    number: string;
    product: ProductItem;
    appliedRules: Array<AppliedRule>;
    total: number;
    currency: string;
}

export interface PriceCalculationResult {
    success: boolean;
    message?: string;
    errors?: APIValidationErrors;
    data?: PriceCalculation;
}

export interface CalculationStoreState {
    categories: Array<CategoryItem>;
    locations: Array<LocationItem>;

    lastCalculation: PriceCalculation|null;
}

export interface CalculationStoreGetters extends _GettersTree<CalculationStoreState> {
    hasCalculation: (state: CalculationStoreState) => boolean;
}

export interface CalculationStoreActions {
    fetchCategories: () => Promise<void>;

    fetchLocations: () => Promise<void>;

    calculate: (data: CalculationFormData) => Promise<PriceCalculationResult>;

    storeLastCalculation: (data: PriceCalculation) => void;

    resetCalculation: () => void;
}
