/**
 * Tailwind Configuration
 */

//  Load our settings
const gumboSettings = require('./resources/js-build/branding')
const defaultTheme = require('tailwindcss/defaultTheme')

// Build configs
module.exports = {
  plugins: gumboSettings.plugins,

  mode: 'jit',

  purge: {
    content: [
      'app/**/*.php',
      'config/**/*.php',
      'resources/views/**/*.blade.php',
      'resources/assets/html/**/*.html',
      'resources/js/**/*.js'
    ]
  },

  theme: {
    extend: {
      fontFamily: {
        title: ['Poppins', ...defaultTheme.fontFamily.sans]
      },
      fontSize: {
        huge: '8rem'
      },
      colors: gumboSettings.colors,
      objectPosition: {
        center: 'center',
        top: 'top'
      },
      width: {
        screen: '100vw'
      }
    }
  }
}
