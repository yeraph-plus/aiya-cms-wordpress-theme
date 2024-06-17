class AIYA_JQUERY {
    Once() {
        // 初始化一些基本的事件
    }
    loadHitokoto() {
        setTimeout(() => {
            $(".widget-hitokoto").each((_, v) => {
                const el = $(v);
                const api = el.attr("data-api") || "https://v1.hitokoto.cn/";
                $.get(
                    api,
                    (res) => {
                        el.find(".t").text(res.hitokoto ?? res.content ?? "无内容");
                        el.find(".f").text(res.from);
                        el.find(".fb").removeClass("d-none");
                    },
                    "json"
                ).fail((err) => {
                    console.error(err);
                    el.find(".t").text("加载失败：" + err.responseText || err);
                    el.remove(".fb");
                });
            });
        }, 300);
    }
jQuery(document).ready(function ($){
    //初始化toolTip
    $('[data-bs-toggle="tooltip"]').tooltip();
    //初始化Highlight
    $('.entry-main pre').each(function(){
        hljs.initHighlightingOnLoad();
        hljs.initLineNumbersOnLoad();
    });
    $('code.hljs').each(function(i, block){
        hljs.highlightBlock(block);
        hljs.lineNumbersBlock(block);
    });
    //点击事件
    $('div.read-more a').click(function (){
        $this = $(this);
        $this.addClass('loading').text("加载中...");
        var href = $this.attr('href');
        if (href != undefined) {
            $.ajax({
                url: href,
                type: 'get',
                dataType:'html',
                error: function (request) {
                    $('div.read-more a').text("没有更多了");
                },
                success: function (data) {
                    $this.removeClass('loading').text("加载更多");
                    var $res = $(data).find('div.post-loop');
                    $('div.post-list').append($res);
                    var $newhref = $(data).find('div.read-more a').attr('href');
                    if( $newhref != undefined ){
                        $("div.read-more a").attr("href",$newhref);
                    }else{
                        $("div.read-more a").hide();
                    }
                }
            });
        }
        return false;
    });
    //爱发电组件
    $("#afdian-check").click(function (){
        var $order = $("#afdian-order").val();
        var $back = $("#afdian-aaaa").val();
        if ($order != undefined) {
            $.ajax({
                url: aya_ajax.url,
                type: "POST",
                dataType: "HTML",
                data: {
                    action: "afdian_check",
                    "afdian_order": $order ,
                },
                success: function($msg){
                    $("#info-back").html($msg);
                    $("#info-back").addClass('tips-box');
                    $("#aaaa-back").attr('href', $back+'/?afor='+$order);
                }
            });
        }
    });
    $("#down-clip").click(function (){
        var copy_text = $(".conter")[0].innerText
        copyText(copy_text);
        $("div.down-concent").html('<i class="bi bi-clipboard-check"></i> 已复制');
    });
    $("#specsZan").click(function (){
        if ($(this).hasClass('done')) {
            $(this).removeClass('done');
            var id = $(this).data("id"),
                action = $(this).data('action'),
                rateHolder = $(this).children('.count');
            var ajax_data = {
                action: "sub_zan",
                um_id: id,
                um_action: action
            };
            $.post("/wp-admin/admin-ajax.php", ajax_data,
                function (data) {
                    $(rateHolder).html(data);
                });
            return false;
        } else {
            $(this).addClass('done');
            var id = $(this).data("id"),
                action = $(this).data('action'),
                rateHolder = $(this).children('.count');
            var ajax_data = {
                action: "add_zan",
                um_id: id,
                um_action: action
            };
            $.post("/wp-admin/admin-ajax.php", ajax_data,
                function (data) {
                    $(rateHolder).html(data);
                });
            return false;
        }
    });
});

//ajax提交评论
jQuery(document).ready(function ($) { 
    var __cancel = jQuery('#cancel-comment-reply-link'),
        __cancel_text = __cancel.text(),
        __list = 'comment-list';//your comment wrapprer
    jQuery(document).on("submit", "#commentform", function () {
        $.ajax({
            url: ajaxcomment.ajax_url,
            data: jQuery(this).serialize() + "&action=ajax_comment",
            type: jQuery(this).attr('method'),
            beforeSend: faAjax.createButterbar("提交中..."),
            error: function (request) {
                var t = faAjax;
                t.createButterbar(request.responseText);
            },
            success: function (data) {
                jQuery('textarea').each(function () {
                    this.value = ''
                });
                var t = faAjax,
                    cancel = t.I('cancel-comment-reply-link'),
                    temp = t.I('wp-temp-form-div'),
                    respond = t.I(t.respondId),
                    post = t.I('comment_post_ID').value,
                    parent = t.I('comment_parent').value;
                if (parent != '0') {
                    jQuery('#respond').before('<ol class="children">' + data + '</ol>');
                } else if (!jQuery('.' + __list).length) {
                    if (ajaxcomment.formpostion == 'bottom') {
                        jQuery('#respond').before('<ol class="' + __list + '">' + data + '</ol>');
                    } else {
                        jQuery('#respond').after('<ol class="' + __list + '">' + data + '</ol>');
                    }
                } else {
                    if (ajaxcomment.order == 'asc') {
                        jQuery('.' + __list).append(data); // your comments wrapper
                    } else {
                        jQuery('.' + __list).prepend(data); // your comments wrapper
                    }
                }
                t.createButterbar("提交成功");
                cancel.style.display = 'none';
                cancel.onclick = null;
                t.I('comment_parent').value = '0';
                if (temp && respond) {
                    temp.parentNode.insertBefore(respond, temp);
                    temp.parentNode.removeChild(temp)
                }
            }
        });
        return false;
    });
    faAjax = {
        I: function (e) {
            return document.getElementById(e);
        },
        clearButterbar: function (e) {
            if (jQuery(".butterBar").length > 0) {
                jQuery(".butterBar").remove();
            }
        },
        createButterbar: function (message) {
            var t = this;
            t.clearButterbar();
            jQuery("body").append('<div class="butterBar"><p class="butterBar-message">' + message + '</p></div>');
            setTimeout("jQuery('.butterBar').remove()", 3000);
        }
    };
});
}

jQuery(() => {
    if (window.$ === undefined) {
        window.$ = jQuery;
    }
    //主题函数初始化
    window.InitOnce = new AIYA_JQUERY();
});

jQuery(document).ready(function ($){
  //初始化toolTip
  $('[data-bs-toggle="tooltip"]').tooltip();
  //初始化Highlight
  $('.entry-main pre').each(function(){
      hljs.initHighlightingOnLoad();
      hljs.initLineNumbersOnLoad();
  });
  $('code.hljs').each(function(i, block){
      hljs.highlightBlock(block);
      hljs.lineNumbersBlock(block);
  });
  //点击事件
  $('div.read-more a').click(function (){
      $this = $(this);
      $this.addClass('loading').text("加载中...");
      var href = $this.attr('href');
      if (href != undefined) {
          $.ajax({
              url: href,
              type: 'get',
              dataType:'html',
              error: function (request) {
                  $('div.read-more a').text("没有更多了");
              },
              success: function (data) {
                  $this.removeClass('loading').text("加载更多");
                  var $res = $(data).find('div.post-loop');
                  $('div.post-list').append($res);
                  var $newhref = $(data).find('div.read-more a').attr('href');
                  if( $newhref != undefined ){
                      $("div.read-more a").attr("href",$newhref);
                  }else{
                      $("div.read-more a").hide();
                  }
              }
          });
      }
      return false;
  });
  //爱发电组件
  $("#afdian-check").click(function (){
      var $order = $("#afdian-order").val();
      var $back = $("#afdian-aaaa").val();
      if ($order != undefined) {
          $.ajax({
              url: aya_ajax.url,
              type: "POST",
              dataType: "HTML",
              data: {
                  action: "afdian_check",
                  "afdian_order": $order ,
              },
              success: function($msg){
                  $("#info-back").html($msg);
                  $("#info-back").addClass('tips-box');
                  $("#aaaa-back").attr('href', $back+'/?afor='+$order);
              }
          });
      }
  });
  $("#down-clip").click(function (){
      var copy_text = $(".conter")[0].innerText
      copyText(copy_text);
      $("div.down-concent").html('<i class="bi bi-clipboard-check"></i> 已复制');
  });
  $("#specsZan").click(function (){
      if ($(this).hasClass('done')) {
          $(this).removeClass('done');
          var id = $(this).data("id"),
              action = $(this).data('action'),
              rateHolder = $(this).children('.count');
          var ajax_data = {
              action: "sub_zan",
              um_id: id,
              um_action: action
          };
          $.post("/wp-admin/admin-ajax.php", ajax_data,
              function (data) {
                  $(rateHolder).html(data);
              });
          return false;
      } else {
          $(this).addClass('done');
          var id = $(this).data("id"),
              action = $(this).data('action'),
              rateHolder = $(this).children('.count');
          var ajax_data = {
              action: "add_zan",
              um_id: id,
              um_action: action
          };
          $.post("/wp-admin/admin-ajax.php", ajax_data,
              function (data) {
                  $(rateHolder).html(data);
              });
          return false;
      }
  });
});

//ajax提交评论
jQuery(document).ready(function ($) { 
  var __cancel = jQuery('#cancel-comment-reply-link'),
      __cancel_text = __cancel.text(),
      __list = 'comment-list';//your comment wrapprer
  jQuery(document).on("submit", "#commentform", function () {
      $.ajax({
          url: ajaxcomment.ajax_url,
          data: jQuery(this).serialize() + "&action=ajax_comment",
          type: jQuery(this).attr('method'),
          beforeSend: faAjax.createButterbar("提交中..."),
          error: function (request) {
              var t = faAjax;
              t.createButterbar(request.responseText);
          },
          success: function (data) {
              jQuery('textarea').each(function () {
                  this.value = ''
              });
              var t = faAjax,
                  cancel = t.I('cancel-comment-reply-link'),
                  temp = t.I('wp-temp-form-div'),
                  respond = t.I(t.respondId),
                  post = t.I('comment_post_ID').value,
                  parent = t.I('comment_parent').value;
              if (parent != '0') {
                  jQuery('#respond').before('<ol class="children">' + data + '</ol>');
              } else if (!jQuery('.' + __list).length) {
                  if (ajaxcomment.formpostion == 'bottom') {
                      jQuery('#respond').before('<ol class="' + __list + '">' + data + '</ol>');
                  } else {
                      jQuery('#respond').after('<ol class="' + __list + '">' + data + '</ol>');
                  }
              } else {
                  if (ajaxcomment.order == 'asc') {
                      jQuery('.' + __list).append(data); // your comments wrapper
                  } else {
                      jQuery('.' + __list).prepend(data); // your comments wrapper
                  }
              }
              t.createButterbar("提交成功");
              cancel.style.display = 'none';
              cancel.onclick = null;
              t.I('comment_parent').value = '0';
              if (temp && respond) {
                  temp.parentNode.insertBefore(respond, temp);
                  temp.parentNode.removeChild(temp)
              }
          }
      });
      return false;
  });
  faAjax = {
      I: function (e) {
          return document.getElementById(e);
      },
      clearButterbar: function (e) {
          if (jQuery(".butterBar").length > 0) {
              jQuery(".butterBar").remove();
          }
      },
      createButterbar: function (message) {
          var t = this;
          t.clearButterbar();
          jQuery("body").append('<div class="butterBar"><p class="butterBar-message">' + message + '</p></div>');
          setTimeout("jQuery('.butterBar').remove()", 3000);
      }
  };
});