import { Config } from 'ziggy-js';

declare global {
    interface Window {
        Ziggy: Config;
    }
}

declare function route(name?: string, params?: any, absolute?: boolean): string;
declare function route(): {
    current(name?: string): boolean;
}; 