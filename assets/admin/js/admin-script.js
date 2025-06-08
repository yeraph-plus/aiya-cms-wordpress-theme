/**
 * HUB 帖子管理后台 JavaScript
 */
(function($) {
    'use strict';
    
    // 通用消息提示函数
    function showNotice(message, type = 'success') {
        const notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap.hub-admin h1').after(notice);
        
        // 自动消失
        setTimeout(function() {
            notice.fadeOut('slow', function() {
                notice.remove();
            });
        }, 3000);
    }
    
    // 错误处理函数
    function handleApiError(error) {
        console.error('API错误:', error);
        let message = '操作失败';
        
        if (error.responseJSON && error.responseJSON.message) {
            message = error.responseJSON.message;
        }
        
        showNotice(message, 'error');
    }
    
    // 格式化日期时间
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('zh-CN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // 文档加载完成后执行
    $(document).ready(function() {
        // 关闭所有模态框的通用方法
        $(window).click(function(e) {
            if ($(e.target).hasClass('hub-modal')) {
                $('.hub-modal').hide();
            }
        });
        
        // 上下文感知导航
        function highlightCurrentNav() {
            const currentPage = window.location.href;
            $('.hub-admin-tabs a').each(function() {
                if (currentPage.indexOf($(this).attr('href')) !== -1) {
                    $(this).addClass('active');
                }
            });
        }
        
        highlightCurrentNav();
    });
    
})(jQuery);