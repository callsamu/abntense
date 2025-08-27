import forms from '@tailwindcss/forms'

const defaultPreset = {
    theme: {
        extend: {
            fontfamily: {
                sans: ['figtree'],
            },
        },
    },
    plugins: [forms],
}

export default defaultPreset;
