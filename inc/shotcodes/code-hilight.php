<?php

if (!defined('ABSPATH')) {
    exit;
}

if (is_admin()) {
    AYA_Shortcode::shortcode_register('highlight-content', array(
        'id' => 'sc-prism-highlight',
        'title' => '代码高亮',
        'note' => '使用 Prism.js 进行代码高亮',
        'template' => '[pre_code {{attributes}}]<pre><code> {{content}} </code></pre>[/pre_code]',
        'field_build' => array(
            [
                'id' => 'content',
                'type' => 'textarea',
                'label' => '代码内容',
                'desc' => '代码块需要包含<pre><code> </code></pre>标签内才能工作',
                'default' => '',
            ],
            [
                'id' => 'language',
                'type'  => 'select',
                'label' => '代码语言',
                'desc'  => '设置高亮代码的语言格式',
                'sub' => [
                    'none' => 'Plain text',
                    'markup' => 'HTML/XML',
                    'clike' => 'C-like',
                    'css' => 'CSS',
                    'javascript' => 'JavaScript',
                    'c' => 'C',
                    'csharp' => 'C#',
                    'cpp' => 'C++',
                    'php' => 'PHP',
                    'python' => 'Python',
                    'java' => 'Java',
                    'ruby' => 'Ruby',
                    'json' => 'JSON',
                    'yaml' => 'YAML',
                    'sql' => 'SQL',
                ],
                'default' => 'markup',
            ],
            [
                'id' => 'theme',
                'type'  => 'select',
                'label' => '颜色主题',
                'desc'  => '设置高亮代码块的颜色主题',
                'sub' => [
                    'default' => 'Prism Default',
                    'okaidia' => 'Okaidia',
                    'solarizedlight' => 'Solarized Light',
                    'tomorrow' => 'Tomorrow Night',
                ],
                'default' => 'markup',
            ],
            [
                'id' => 'line_number',
                'type'  => 'checkbox',
                'label' => '显示行号',
                'desc' => '展示文本内容时是否显示行号',
                'default' => true,
            ]
        )
    ));
}

add_shortcode('pre_code', 'aya_plugin_prismjs_process_pre');
//AIYA-CMS 短代码组件：剪贴板功能卡片
function aya_plugin_prismjs_process_pre($atts = array(), $content = '')
{
    $atts = shortcode_atts(
        array(
            'theme' => 'okaidia',
            'language' => 'markup',
            'line_number' => true
        ),
        $atts
    );

    //储存一个id用于多次调用
    static $prism_box = 0;
    $prism_box++;

    if ($prism_box == 1) {
        //静态文件地址
        $load = '';
        $prism_plugin_uri = AYA_URI . '/assets/prismjs';
        //是否包含语言包
        if (in_array($atts['language'], ['none', 'markup', 'clike', 'css', 'javascript'])) {
            $load .= '<script src="' . $prism_plugin_uri . '/prism.slim.js"></script>' . PHP_EOL;
        } else {
            $load .= '<script src="' . $prism_plugin_uri . '/prism.js"></script>' . PHP_EOL;
        }
        $load .= '<script type="text/javascript"> Prism.highlightAll(); //PrismJS </script>' . PHP_EOL;
        //切换主题样式
        if (in_array($atts['theme'], ['default', 'okaidia', 'solarizedlight', 'tomorrow'])) {
            $load .= '<link rel="stylesheet" href="' . $prism_plugin_uri . '/css/prism.' . $atts['theme'] . '.css">' . PHP_EOL;
        } else {
            $load .= '<link rel="stylesheet" href="' . $prism_plugin_uri . '/css/prism.okaidia.css">' . PHP_EOL;
        }
        //引入JS
        add_filter('aya_int_add_scripts', function ($strings) use ($load) {
            return $strings . $load;
        });
    }

    //显示行号
    $lin_cl = ($atts['line_number'] == 'true' || $atts['line_number'] == 'on' || $atts['line_number'] == true) ? 'line-numbers' : '';
    $lan_cl = ' language-' . $atts['language'];

    $content = trim($content);

    //找到<pre>标签
    $start_pos = strpos($content, '<pre><code>');

    if ($start_pos === false) {
        $content = '<pre><code>' . $content . '</code></pre>';
    }

    //标签添加class
    $content = str_replace('<pre><code>', '<pre class="' . $lin_cl . '"><code class="' . $lan_cl . '">', $content);
    $content = trim($content);

    return $content;
}
