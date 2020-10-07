module.exports = {
  purge: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue'
  ],
  theme: {
    colors: {
      transparent: 'transparent',
      white: '#ffffff',
      black: '#000000',
      gray: {
        50: '#f9fafb',
        100: '#f4f5f7',
        200: '#e5e7eb',
        300: '#d2d6dc',
        350: '#b9bec7',
        400: '#9fa6b2',
        450: '#858c99',
        500: '#6b7280',
        600: '#4b5563',
        700: '#374151',
        800: '#252f3f',
        900: '#161e2e'
      },
      'cool-gray': {
        50: '#fbfdfe',
        100: '#f1f5f9',
        200: '#e2e8f0',
        300: '#cfd8e3',
        400: '#97a6ba',
        500: '#64748b',
        600: '#475569',
        700: '#364152',
        800: '#27303f',
        900: '#1a202e'
      },
      red: {
        50: '#fdf2f2',
        100: '#fde8e8',
        200: '#fbd5d5',
        300: '#f8b4b4',
        400: '#f98080',
        500: '#f05252',
        600: '#e02424',
        700: '#c81e1e',
        800: '#9b1c1c',
        900: '#771d1d'
      },
      yellow: {
        kox: '#ffcc00',
        ciul: '#f6e05e',
        50: '#fdfdea',
        100: '#fdf6b2',
        200: '#fce96a',
        300: '#faca15',
        400: '#e3a008',
        500: '#c27803',
        600: '#9f580a',
        700: '#8e4b10',
        800: '#723b13',
        900: '#633112'
      },
      green: {
        50: '#f3faf7',
        100: '#def7ec',
        200: '#bcf0da',
        300: '#84e1bc',
        400: '#31c48d',
        500: '#0e9f6e',
        600: '#057a55',
        700: '#046c4e',
        800: '#03543f',
        900: '#014737'
      },
      blue: {
        50: '#ebf5ff',
        100: '#e1effe',
        200: '#c3ddfd',
        300: '#a4cafe',
        400: '#76a9fa',
        500: '#3f83f8',
        600: '#1c64f2',
        700: '#1a56db',
        800: '#1e429f',
        900: '#233876'
      }
    },
    extend: {
      fontSize: { '2xs': '0.7rem' },
      spacing: { 4.5: '1.125rem' }
    }
  },
  variants: {
    opacity: ({ after }) => after(['group-hover'])
  },
  plugins: [
    require('@tailwindcss/ui')
  ],
  experimental: {
    applyComplexClasses: true,
    darkModeVariant: false,
    defaultLineHeights: true,
    extendedFontSizeScale: true,
    extendedSpacingScale: true,
    uniformColorPalette: false
  },
  future: {
    removeDeprecatedGapUtilities: true,
    purgeLayersByDefault: true
  }
}
