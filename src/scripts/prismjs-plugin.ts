import Prism from 'prismjs';
//导入插件
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/normalize-whitespace/prism-normalize-whitespace';
import 'prismjs/plugins/toolbar/prism-toolbar';
import 'prismjs/plugins/show-language/prism-show-language';
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

export function initPrism() {
    const container = document.querySelector('.article-content') as HTMLElement;

    //检查PrismJS是否需要初始化
    if (!container || container.dataset.prismInitialized === "done") {
        return;
    }

    const codeBlocks = document.querySelectorAll('pre code');

    if (codeBlocks.length === 0) {
        return;
    }

    //标记已初始化
    container.dataset.prismInitialized = "done";

    codeBlocks.forEach(block => {
        //添加行号支持
        const preElement = block.parentElement;
        //为 pre 标签添加 line-numbers 样式类
        if (preElement && !preElement.classList.contains('line-numbers')) {
            preElement.classList.add('line-numbers');
        }
    });

    Prism.highlightAll();
    //完成时添加一条Log
    console.log('PrismJS code highlight is activated.');
}
