//https://github.com/andrefelipe/vite-php-setup
//https://cn.vite.dev/guide/backend-integration
//https://cn.vuejs.org/guide/scaling-up/tooling#note-on-in-browser-template-compilation

import path, { resolve } from 'node:path'
import type { Plugin } from 'vite';
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
//import legacy from '@vitejs/plugin-legacy'
//import liveReload from 'vite-plugin-live-reload'

// 检查调试
const debugMode = process.argv.includes('--debug');

// 捕获PHP文件刷新
function fullReloadPhp(): Plugin {
  return {
    name: 'full-reload-php',
    apply: 'serve',
    configureServer(server) {
      const globs = [
        //resolve(__dirname, '**/*.php'),
        resolve(__dirname, 'inc/**/*.php'),
        resolve(__dirname, 'templates/**/*.php'),
        resolve(__dirname, 'template-parts/**/*.php'),
      ];

      server.watcher.add(globs);

      server.watcher.on('change', (file) => {
        if (file.endsWith('.php')) {
          server.ws.send({ type: 'full-reload', path: '*' });
        }
      });
    },
  };
}

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    fullReloadPhp(),
  ],
  root: '',
  //差异编译文件路径
  base: process.env.NODE_ENV === 'development'
    ? '/'
    : './',
  //服务器选项
  server: {
    //允许跨域请求
    cors: true,
    //或从自定义主机加载脚本
    /*
    cors: {
        origin: 'http://local.host',
    },
    */
    //固定的端口以匹配PHP接入
    /*
    host: '0.0.0.0',
    */
    strictPort: true,
    port: 5173,

    //本地使用HTTPS
    //e.g: https://github.com/FiloSottile/mkcert
    /*
    https: {
      key: fs.readFileSync('localhost-key.pem'),
      cert: fs.readFileSync('localhost.pem'),
    },
    */
    //HMR连接
    hmr: {
      host: 'localhost',
      //port: 443
    },
  },//构建选项
  build: {
    //生产文件的输出目录
    outDir: './build',
    emptyOutDir: true,
    //指定esbuild版本
    //target: 'ES2020',
    //启用清单文件，用于PHP解析以找到带hash的文件
    manifest: true,

    write: true,
    //是否启用压缩
    minify: !debugMode,
    //是否生成sourcemap
    sourcemap: debugMode,
    //调整chunk警告
    chunkSizeWarningLimit: 1024,

    //rollup入口
    //e.g: https://rollupjs.org/configuration-options/
    rollupOptions: {
      input: resolve(__dirname, 'src/main.tsx'),
      output: {
        //定义输出文件名
        entryFileNames: 'assets/[name].[hash].js',
        chunkFileNames: 'assets/[name].[hash].js',
        assetFileNames: () => {
          return `assets/[name].[hash].[ext]`;
        },

        manualChunks(id) {
          // all third-party code will be in vendor chunk
          if (id.includes('node_modules')) {
            return 'vendor'
          }
          // example on how to create another chunk
          // if (id.includes('src/'components')) {
          //   return 'components'
          // }
          //console.log(id)
        },
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
});
