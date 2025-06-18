import Prism from 'prismjs';

//导入CSS和插件
//import 'prismjs/themes/prism.css';
//import 'prismjs/themes/prism-twilight.css';
import 'prismjs/themes/prism-tomorrow.css';
import 'prismjs/plugins/line-numbers/prism-line-numbers.css';
import 'prismjs/plugins/line-numbers/prism-line-numbers';

//导入常用语言支持
import 'prismjs/components/prism-markup';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-typescript';
import 'prismjs/components/prism-css';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-ruby';
import 'prismjs/components/prism-python';
import 'prismjs/components/prism-java';
import 'prismjs/components/prism-c';
import 'prismjs/components/prism-cpp';
import 'prismjs/components/prism-csharp';

export default class PrismJSPlugin {

    constructor(container?: string | Element) {
        this.init(container);
    }

    init(container?: string | Element) {
        // 添加行号支持
        this.addLineNumbersClass(container);
        this.highlightAll();
    }

    // 为所有代码块添加行号类
    addLineNumbersClass(container?: string | Element) {
        //定位根元素
        const root = container
            ? (typeof container === 'string' ? document.querySelector(container) : container)
            : document;

        if (!root) return;

        const codeBlocks = root.querySelectorAll('pre:not(.no-line-numbers)');

        codeBlocks.forEach(block => block.classList.add('line-numbers'));
    }

    highlightAll() {
        // 应用Prism代码高亮
        Prism.highlightAll();
    }
}

export function initPrism(selector: string = '.article-content'): void {

    //只在文章页面触发初始化
    if (document.getElementsByClassName(selector)) {
        new PrismJSPlugin();
    }
}