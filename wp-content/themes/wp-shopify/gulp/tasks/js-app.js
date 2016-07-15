////////
// JS //
////////

import gulp from 'gulp';
import config from '../config';
import browserify from 'browserify';
import babelify from 'babelify';
import uglify from 'gulp-uglify';
import source from 'vinyl-source-stream';
import buffer from 'vinyl-buffer';
import sourcemaps from 'gulp-sourcemaps';
import rename from "gulp-rename";

gulp.task('js-app', () => {
  return browserify({
    entries: [config.files.jsEntry],
    extensions: ['.js'],
    debug: true
  })
  .external(config.libs)
  .transform(babelify)
  .bundle()
  .pipe(source(config.names.js))
  .pipe(buffer())
  .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(rename(config.names.js))
  .pipe(sourcemaps.write(config.folders.js))
  .pipe(gulp.dest(config.folders.js))
  .pipe(config.bs.stream());

});
