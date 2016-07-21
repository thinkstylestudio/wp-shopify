import gulp from "gulp";

gulp.task('default',
  gulp.series('server', gulp.parallel('js', 'css'))
);
