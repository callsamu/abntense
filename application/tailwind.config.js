import { join, dirname } from 'node:path';
import defaultPreset from '@monorepo/ui/theme';


const ui = (path_) =>
    join(dirname(require.resolve('@monorepo/ui')), path_);


/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        ui('src/**/*.vue'),
    ],

    presets: [defaultPreset],
};
