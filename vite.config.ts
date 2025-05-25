// *** 配置参考来源 ***
//https://github.com/andrefelipe/vite-php-setup
//https://cn.vite.dev/guide/backend-integration
//https://cn.vuejs.org/guide/scaling-up/tooling#note-on-in-browser-template-compilation

//如需支持旧版浏览器，可能需要 @vitejs/plugin-legacy

import { defineConfig } from 'vite'
import { resolve } from 'node:path'

import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload'

export default defineConfig({
    //插件列表
    plugins: [
        vue(),
        tailwindcss(),
        liveReload([
            __dirname + '/**/*.php',
            __dirname + '/src/**/*.vue',
            __dirname + '/src/**/*.js'
        ])
    ],
    //基本
    root: '',
    base: process.env.NODE_ENV === 'development'
        ? '/'
        : '/build/',
    //服务器选项
    server: {
        //允许跨域请求
        cors: true,
        //或从自定义主机加载脚本
        /*
        cors: {
            origin: 'http://local.test',
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
    },
    //构建选项
    build: {
        //生产文件的输出目录
        outDir: './build',
        emptyOutDir: true,

        //指定esbuild版本
        //target: 'es2018',
        //清单文件，用于PHP解析以找到带hash的文件
        manifest: true,

        // 是否启用压缩
        write: true,
        minify: true,

        //rollup入口
        //e.g: https://rollupjs.org/configuration-options/
        rollupOptions: {
            input: resolve(__dirname, 'src/main.js'),
            output: {
                //定义输出文件名
                entryFileNames: 'assets/[name].[hash].js',
                chunkFileNames: 'assets/[name].[hash].js',
                assetFileNames: 'assets/[name].[hash].[ext]',

                manualChunks(id) {
                    // all third-party code will be in vendor chunk
                    if (id.includes('node_modules')) {
                        return 'vendor'
                    }
                    // example on how to create another chunk
                    // if (id.includes('src/'components')) {
                    //   return 'components'
                    // }
                    console.log(id)
                },
            },
        },
    },
    //加载模板编译器
    resolve: {
        alias: {
            //'@': resolve(__dirname, 'src'),
            vue: 'vue/dist/vue.esm-bundler.js'
        }
    }
})
