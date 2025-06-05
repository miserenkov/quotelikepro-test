/**
 * Created by WebStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 21:52
 */

import type axios from 'axios';

export {};

declare global {
    interface Window {
        axios: axios;
    }
}
