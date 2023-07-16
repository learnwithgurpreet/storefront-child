const gulp = require("gulp");
const clean = require("gulp-clean");
const stripCssComments = require("gulp-strip-css-comments");
const cleanCSS = require("gulp-clean-css");
const zip = require("gulp-zip");

// WP write header and footer
const fs = require("fs");
const header = require("gulp-header");
const footer = require("gulp-footer");

// Package.JSON contents
const pkg = JSON.parse(fs.readFileSync("./package.json"));

function css() {
  const cssbanner = [
    "/*",
    "Theme Name:          " + pkg.theme_name,
    "Description:         " + pkg.description,
    "Theme URI:           " + pkg.theme_uri,
    "Template:            " + pkg.template,
    "Author:              " + pkg.author,
    "Author URI:          " + pkg.authoruri,
    "Version:             " + pkg.version,
    "License:             " + pkg.license,
    "Text Domain:         " + pkg.textDomain,
    "Requires PHP:        " + pkg.theme_requires_php_version,
    "*/",
    "",
  ].join("\n");

  return gulp
    .src("src/style.css") // Change this to your own stylesheet
    .pipe(stripCssComments({ preserve: false }))
    .pipe(cleanCSS(require("./configs/clean-css.js")))
    .pipe(
      header(cssbanner, {
        pkg: pkg,
      })
    )
    .pipe(footer("\n"))
    .on("error", console.error.bind(console))
    .pipe(gulp.dest("./dist"));
}

function cleanDestination() {
  return gulp
    .src("./dist", { read: false, allowEmpty: true })
    .pipe(clean({ force: true }));
}

function copy() {
  var files = ["./src/*.*", "!./src/style.css"];
  return gulp.src(files).pipe(gulp.dest("./dist/"));
}

async function writeUpdate() {
  const contents = {
    download_link: `https://learnwithgurpreet.github.io/storefront-child/storefront-child.${pkg.version}.zip`,
    new_version: pkg.version,
  };
  const jsonString = JSON.stringify(contents);
  await fs.writeFileSync("./update.json", jsonString);
}

gulp.task("zip", () =>
  gulp
    .src("./dist/**/*")
    .pipe(zip(`storefront-child.${pkg.version}.zip`))
    .pipe(gulp.dest("./"))
);

gulp.task("build", gulp.series(cleanDestination, css, copy, writeUpdate));
