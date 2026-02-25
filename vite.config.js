import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
<<<<<<< HEAD
                'resources/sass/app.scss',
=======
                'resources/css/app.css',
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
<<<<<<< HEAD
});
=======
});
>>>>>>> 04aac4a680a6a495057e4e49de8b3b64be28f879
