//初始化方法
class AIYA_INIT {
    //初始化全部组件
    firstInit() {
        this.infoTheme();
        this.loadPjax();
        this.OP_RootOut();
        this.loadLozad();
        this.loadMasonry();
        this.loadViewer();
        this.loadHighlight();
    }
    reInit() {
        this.loadLozad();
        this.loadViewer();
        this.loadHighlight();
    }
    //LozadJS
    loadLozad() {
        if (document.getElementsByClassName("lozad") !== null) {
            const lozad_js = lozad(".lozad", {
                //load: function (el) {},
                //loaded: function (el) {},
                rootMargin: "0px", // CSS Margin
                threshold: 0.5, // ratio of element convergence
                enableAutoReload: true, // it will reload the new image when validating attributes changes
            });
            //Load
            lozad_js.observe();
            //Log
            console.log("Loaded: lozad.js", lozad_js);
        }
    }
    //ViewerJS
    loadViewer() {
        if (document.getElementById("entry-main") !== null) {
            const viewer_js = new Viewer(document.getElementById("entry-main"), {
                navbar: false,
                toolbar: false,
                url: "src",
            });
            //Log
            console.log("Loaded: viewer.js", viewer_js);
        }
    }
    //highlightJS
    loadHighlight() {
        document.querySelectorAll("pre code").forEach((el) => {
            hljs.highlightAll();
        });
    }
    //masonryJS
    loadMasonry() {
        if (document.getElementById("post-loop-content") !== null && document.getElementsByClassName("loop-waterfall-mode") !== null) {
            const msnry_js = new Masonry(document.querySelector(".loop-waterfall-mode"), {
                percentPosition: true,
            });
            //Load
            msnry_js.layout();
            //Log
            console.log("Loaded: masonry.js", msnry_js);
        }
    }
    //pjaxJS
    loadPjax() {
        const pjax_js = new Pjax({
            elements: 'a[href]:not([href^="#"]):not([href="javascript:void(0)"])', // 拦截正常带链接的 a 标签
            selectors: ["#pjax-wrapper", "#aside-wrapper", "title"], // 根据实际需要确认重载区域
            cacheBust: true,
        });
        //Log
        console.log("Document initialized:", pjax_js);
    }
    //Loading
    OP_RootIn() {
        let loading = document.getElementById("loading");
        //in
        if (loading !== null) {
            this.Loading_Opacity(loading, "in", true);
        }
    }
    OP_RootOut() {
        let loading = document.getElementById("loading");
        //out
        if (loading !== null) {
            this.Loading_Opacity(loading, "out", true);
        }
    }
    OP_ContainerIn() {
        let wrapper = document.getElementById("pjax-wrapper");
        //in
        this.Loading_Opacity(wrapper, "in", false);
    }
    OP_ContainerOut() {
        let wrapper = document.getElementById("pjax-wrapper");
        //out
        this.Loading_Opacity(wrapper, "out", false);
    }
    //Info
    infoTheme() {
        console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
    }
    //demo
    /*
    ajax({
      type: "GET",
      url: "",
      data: {},
      success: function (res) {},
    });
    */
    //封装好的ajax方法
    Ajax(option) {
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let res = JOSN.parse(this.responseText);
                option.success(res); //这里调用success函数
            }
        };
        //开始处理data数据转为字符串形式
        let arr = [];
        for (let i in option.data) {
            arr.push(key + "=" + option.data[key]); //option.data[key]表示对象的值：['bookname=aaa', 'author=bbb', ...]
        }
        let querystring = arr.join("&"); //把数组转化为字符串：bookname=aaa&author=bbb&publiser=ccc

        let method = option.type.toUpperCase(); //把请求方式转成大写，识别 post / POST
        //判断请求
        if (option.type === "GET") {
            xhr.open("GET", option.url + "?" + querystring);
            xhr.send();
        } else if (option.type === "POST") {
            xhr.open("POST", option.url);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(querystring);
        }
    }
    //动画计算器
    Loading_Opacity(loading, fade, none) {
        let speed = 10;
        if (fade === "in") {
            let city = 0;
            let wall = setInterval(function () {
                city += 0.1;
                loading.style.opacity = city;
                if (city >= 10) {
                    clearInterval(wall);
                    none ? loading.classList.remove("d-none") : null;
                }
            }, speed);
        }
        if (fade === "out") {
            let city = 1;
            let wall = setInterval(function () {
                city -= 0.1;
                loading.style.opacity = city;
                if (city <= 0) {
                    clearInterval(wall);
                    none ? loading.classList.add("d-none") : null;
                }
            }, speed);
        }
    }
}

//IF Loaded
document.addEventListener("DOMContentLoaded", function () {
    //Init
    init_self = new AIYA_INIT();
    init_self.firstInit();
    init_self.ajaxNextPage();
});
//PJAX: Send
document.addEventListener("pjax:send", function () {
    //BackToTop
    window.switchToTop();
    //fadeTo
    init_self.OP_ContainerOut();
    //Log
    console.log("Event: pjax:send", arguments);
});
//PJAX: Complete
document.addEventListener("pjax:complete", function () {
    //fadeTo
    init_self.OP_ContainerIn();
    //Log
    console.log("Event: pjax:complete", arguments);
});
//PJAX: Error
document.addEventListener("pjax:error", function () {
    //Log
    console.log("Event: pjax:error", arguments);
});
//PJAX: Success
document.addEventListener("pjax:success", function () {
    //Promise
    async function loadCallback() {
        await init_self.reInit();
        //timeout
        setTimeout(function () {
            init_self.loadMasonry();
        }, 1000);
    }
    loadCallback();
    //url without
    let urlWithoutParam = window.location.pathname;
    window.history.replaceState({}, document.title, urlWithoutParam);
    //Log
    console.log("Event: pjax:success", arguments);
});
//IF Window resize
window.addEventListener("resize", function () {
    init_self.loadMasonry();
});
