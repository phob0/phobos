// Configuration for your app
// https://quasar.dev/quasar-cli/quasar-conf-js

module.exports = function(ctx) {
  return {
    // app boot file (/src/boot)
    // --> boot files are part of "main.js"
    boot: [
      'i18n',
      'axios',
      'backend',
      'console',
      // icons
      'fontawesome-pro',
      // fonts
      'open-sans',
      'source-serif',
      // misc
      'vue-filters'
    ],

    css: [
      'app.styl'
    ],

    extras: [],

    framework: {
      cssAddon: true,
      iconSet: 'fontawesome-v5-pro',
      // lang: 'de', // Quasar language

      all: 'auto',

      components: [],

      directives: [],

      // Quasar plugins
      plugins: [
        'Dialog',
        'Loading',
        'LocalStorage',
        'Notify',
        'Cookies'
      ]
    },

    supportIE: true,

    build: {
      distDir: (ctx.prod && ctx.mode.spa) ? '../public' : undefined,
      scopeHoisting: true,
      vueRouterMode: 'history',
      // vueCompiler: true,
      // gzip: true,
      // analyze: true,
      // extractCSS: ctx.prod,
      extendWebpack(cfg) {
        cfg.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /node_modules/,
          options: {
            formatter: require('eslint').CLIEngine.getFormatter('stylish')
          }
        })

        if (ctx.prod && ctx.mode.spa) {
          const CopyWebpackPlugin = require('copy-webpack-plugin')
          cfg.plugins.push(
            new CopyWebpackPlugin([
              { from: 'src/public', to: cfg.output.path }
            ])
          )

          const CreateSymlinkPlugin = require('create-symlink-webpack-plugin')
          cfg.plugins.push(
            new CreateSymlinkPlugin([
              { origin: '../storage/app/public', symlink: 'storage' }
            ])
          )
        }
      }
    },

    devServer: {
      // https: true,
      port: 8081,
      open: false
      // proxy: {
      //   '/api': {
      //     target: 'http://esp-dyadic.loc',
      //     changeOrigin: true,
      //     pathRewrite: {
      //       '^/api': ''
      //     }
      //   }
      // }
    },

    // animations: 'all', // --- includes all animations
    animations: [
    ],

    ssr: {
      pwa: false
    },

    pwa: {
      // workboxPluginMode: 'InjectManifest',
      // workboxOptions: {}, // only for NON InjectManifest
      manifest: {
        // name: 'Phobo Broth',
        // short_name: 'Broth',
        // description: 'A Quasar Framework app',
        display: 'standalone',
        orientation: 'portrait',
        background_color: '#fff',
        theme_color: '#027be3',
        icons: [
          {
            'src': 'statics/icons/icon-128x128.png',
            'sizes': '128x128',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-192x192.png',
            'sizes': '192x192',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-256x256.png',
            'sizes': '256x256',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-384x384.png',
            'sizes': '384x384',
            'type': 'image/png'
          },
          {
            'src': 'statics/icons/icon-512x512.png',
            'sizes': '512x512',
            'type': 'image/png'
          }
        ]
      }
    },

    cordova: {
      // id: 'com.phobo.spa.broth',
      // noIosLegacyBuildFlag: true, // uncomment only if you know what you are doing
    },

    electron: {
      // bundler: 'builder', // or 'packager'

      extendWebpack(cfg) {
        // do something with Electron main process Webpack cfg
        // chainWebpack also available besides this extendWebpack
      },

      packager: {
        // https://github.com/electron-userland/electron-packager/blob/master/docs/api.md#options

        // OS X / Mac App Store
        // appBundleId: '',
        // appCategoryType: '',
        // osxSign: '',
        // protocol: 'myapp://path',

        // Windows only
        // win32metadata: { ... }
      },

      builder: {
        // https://www.electron.build/configuration/configuration

        // appId: 'chr-b2b'
      }
    }
  }
}
