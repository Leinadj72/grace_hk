const { src, dest, watch, series } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const prefix = require("gulp-autoprefixer");
const minify = require("gulp-clean-css");
const terser = require("gulp-terser");
const babel = require("gulp-babel");
const browserSync = require("browser-sync").create();
const purgecss = require("gulp-purgecss");

//create function

//scss
function compileScss() {
  return src("src/scss/styles.scss")
    .pipe(sass())
    .pipe(purgecss({ content: ["*.php"] }))
    .pipe(prefix("last 2 version"))
    .pipe(minify())
    .pipe(dest("public/css"))
    .pipe(browserSync.stream());
}

//js
function jsMin() {
  return src("src/js/app.js")
    .pipe(
      babel({
        presets: ["@babel/env"],
      })
    )
    .pipe(terser())
    .pipe(dest("public/js"))
    .pipe(browserSync.stream());
}

function bootstrapJs() {
  return src([
    "node_modules/bootstrap/dist/js/bootstrap.bundle.min.js",
    "node_modules/axios/dist/axios.min.js",
  ])
    .pipe(dest("public/js"))
    .pipe(browserSync.stream());
}

//create watchtask
function watchTask() {
  watch(["src/scss/*.scss", "*.php"], compileScss);
  watch("src/js/*.js", jsMin);
  watch("node_modules/bootstrap/dist/js/bootstrap.bundle.min.js", bootstrapJs);
}

//export
exports.default = series(compileScss, jsMin, bootstrapJs, watchTask);
