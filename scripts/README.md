 zh_CN.po转台湾繁体（ s2tw ）：
`composer run @php scripts/opencc-po.php --in=languages/zh_CN.po --out=languages/zh_TW.po --strategy=s2tw --lang=zh_TW`

zh_CN.po转香港繁体（ s2hk ）：
`composer run @php scripts/opencc-po.php --in=languages/zh_CN.po --out=languages/zh_HK.po --strategy=s2hk --lang=zh_HK`

从空的zh_CN.po文件填充msgstr为msgid：
`composer run @php scripts/po-fill-msgstr-from-msgid.php --in=languages/zh_CN.po`

批量读取src/components目录下的所有tsx文件，生成json文件：
`composer run @php scripts/i18n-json-components.php --source=languages --components=src/components --domain=aiya-cms`