////////////
// Config //
////////////

import browserSync from 'browser-sync';

const config = {
  files: {
    js: [
      './assets/js/app.js'
    ],
    jsEntry: './assets/js/app.js',
    css: './assets/css/scss/**/*.scss',
    cssEntry: './assets/css/scss/app.scss',
    all: './**/*',
    html: './index.html',
    filtered: [
      './fonts/**',
      './img/**',
      './meta/**',
      './js/slick.min.js',
      './js/bootstrap.min.js',
      './js/jquery-1.9.1.min.js',
      './js/html5shiv.js',
      './js/app.min.js',
      './css/vendor/slick.min.css',
      './css/vendor/bootstrap.min.css',
      './css/style.css',
      './index.html'
    ]
  },
  folders: {
    css: './assets/css',
    js: './assets/js',
    build: './assets/build'
  },
  names: {
    jsVendor: 'vendor.min.js',
    js: 'app.min.js',
    css: 'app.min.css'
  },
  libs: [],
  bs: browserSync.create(),
  serverName: "wpshop.dev"
};

export default config;
