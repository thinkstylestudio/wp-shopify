////////////
// Server //
////////////

import gulp from 'gulp';
import config from '../config';

gulp.task('reload', function() {
  return config.bs.reload();
});

gulp.task('server', () => {

  config.bs.init({
    proxy: config.serverName
  });

  gulp.watch(config.files.css, gulp.series('css'));
  gulp.watch(config.files.js, gulp.series('js'));
  gulp.watch(config.files.html).on('change', config.bs.reload);

});
