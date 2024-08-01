const rootElement = document.documentElement;
const currentDomain = window.location.hostname;
//检查暗色模式设置
const autoDark = localStorage.getItem("isDarkMode");
//监听页面滚动
const scrollTotal = rootElement.scrollHeight - rootElement.clientHeight;
//自动深色模式
if (autoDark === "true") {
    rootElement.classList.add("dark");
} else {
    rootElement.classList.remove("dark");
}
//启动动作
document.addEventListener("DOMContentLoaded", function () {
    //操作滚动后样式
    document.addEventListener("scroll", headerScroll);
    document.addEventListener("scroll", buttonScroll);
    //操作导航栏菜单
    let menuBtn = document.getElementById("nav-drawer-btn");
    let drawerMenu = document.querySelector(".nav-drawer-menu");

    menuBtn.addEventListener("click", function () {
        drawerMenu.classList.toggle("show");
    });
});
//深色模式切换器
function switchDarkMode() {
    let switchDark = localStorage.getItem("isDarkMode");
    if (switchDark === "true") {
        localStorage.setItem("isDarkMode", "false");
        rootElement.classList.remove("dark");
    } else {
        localStorage.setItem("isDarkMode", "true");
        rootElement.classList.add("dark");
    }
}
//按钮函数
function buttonScroll() {
    if (rootElement.scrollTop / scrollTotal > 0) {
        document.querySelector(".scroll-button").classList.add("scroll-show");
    } else {
        document.querySelector(".scroll-button").classList.remove("scroll-show");
    }
}
//导航函数
function headerScroll() {
    try {
        if (rootElement.scrollTop / scrollTotal > 0.1) {
            document.querySelector(".sticky-top").classList.add("scrolled");
        } else {
            document.querySelector(".sticky-top").classList.remove("scrolled");
        }
    } catch (error) {
        // error
    }
}
//返回顶部动作
function switchToTop() {
    rootElement.scrollTo({
        top: 0,
        behavior: "smooth",
    });
}
//跳转指定id位置
function switchTo(id) {
    if (id !== null && id !== undefined) {
        rootElement.querySelector("#" + id).scrollIntoView({
            block: "start",
            behavior: "smooth",
        });
    }
}
//返回上一页
function backButton() {
    window.history.back();
}
//导航栏菜单抽屉
function navDrawer() {
    let menuBtn = self;
    let drawerMenu = document.querySelector(".nav-drawer-menu");

    menuBtn.addEventListener("click", function () {
        drawerMenu.classList.toggle("show");
    });
}
