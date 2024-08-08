//前端依赖库文件自动打包脚本
const fs = require('fs');
const path = require('path');

// 要合并的.min.js文件的数组
const min_js_files = [
  './node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
  './node_modules/pjax/pjax.min.js',
  './node_modules/lozad/dist/lozad.min.js',
  './node_modules/masonry-layout/dist/masonry.pkgd.min.js',
  './node_modules/viewerjs/dist/viewer.min.js',
  './node_modules/clipboard/dist/clipboard.min.js',
];
const min_css_files = [
  './node_modules/bootstrap/dist/css/bootstrap.min.css',
  './node_modules/bootstrap-icons/font/bootstrap-icons.min.css',
  './node_modules/viewerjs/dist/viewer.min.css'
];

//输出路径
const output_js_file = './lib.merged.js';
const output_css = './lib.merged.css';
//可写流
const output_js = fs.createWriteStream(output_js_file);
const output_css_file = fs.createWriteStream(output_css);

//写入
min_js_files.forEach(file => {
  const file_path = path.resolve(__dirname, file);
  const file_content = fs.readFileSync(file_path, 'utf8');
  output_js.write(file_content + '\n');
});
min_css_files.forEach(file => {
  const file_path = path.resolve(__dirname, file);
  const file_content = fs.readFileSync(file_path, 'utf8');
  output_css_file.write(file_content + '\n');
});

//关闭
output_js.end();
output_css_file.end();

//复制其他文件
const other_files_dir = [
  './node_modules/bootstrap-icons/font/fonts',
];
//循环目录数组
other_files_dir.forEach(dir => {
  const dir_path = path.resolve(__dirname, dir);
  const files = fs.readdirSync(dir_path);
  files.forEach(file => {
    const file_path = path.resolve(dir_path, file);
    fs.copyFileSync(file_path, path.resolve(__dirname, file));
  })
});

console.log('file created:', output_js_file);
console.log('file created:', output_css);