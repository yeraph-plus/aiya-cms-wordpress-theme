<?php
/**
 * 工单页面 待施工
 */
get_header(); ?>

<main class="container pt-6" xmlns="http://www.w3.org/1999/html">
    <?php
        if( isset($_POST['submitForm']) && $_POST['submitForm'] == 'send') {
            global $wpdb;
            $currentUrl = bloginfo('name');   // 注意修改此处的链接地址

            $last_post = $wpdb->get_var("SELECT `post_date` FROM `$wpdb->posts` ORDER BY `post_date` DESC LIMIT 1");

            // 博客当前最新文章发布时间与要投稿的文章至少间隔120秒。
            // 可自行修改时间间隔，修改下面代码中的120即可
            // 相比Cookie来验证两次投稿的时间差，读数据库的方式更加安全
            if ( (date_i18n('U') - strtotime($last_post)) < 120 ) {
                wp_die('您投稿也太勤快了吧，先歇会儿！<a href="'.$currentUrl.'">点此返回</a>');
            }

            // 表单变量初始化
            $name = isset( $_POST['submitName'] ) ? trim(htmlspecialchars($_POST['submitName'], ENT_QUOTES)) : '';
            $email =  isset( $_POST['submitEmail'] ) ? trim(htmlspecialchars($_POST['submitEmail'], ENT_QUOTES)) : '';
            $blog =  isset( $_POST['submitBlog'] ) ? trim(htmlspecialchars($_POST['submitBlog'], ENT_QUOTES)) : '';
            $title =  isset( $_POST['submitTitle'] ) ? trim(htmlspecialchars($_POST['submitTitle'], ENT_QUOTES)) : '';
            $category =  isset( $_POST['cat'] ) ? (int)$_POST['cat'] : 0;
            $content =  isset( $_POST['submitContent'] ) ? trim(htmlspecialchars($_POST['submitContent'], ENT_QUOTES)) : '';

            // 表单项数据验证
            if ( empty($name) || mb_strlen($name) > 20 ) {
                wp_die('昵称必须填写，且长度不得超过20字。<a href="'.$currentUrl.'">点此返回</a>');
            }

            if ( empty($email) || strlen($email) > 60 || !preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
                wp_die('Email必须填写，且长度不得超过60字，必须符合Email格式。<a href="'.$currentUrl.'">点此返回</a>');
            }

            if ( empty($title) || mb_strlen($title) > 100 ) {
                wp_die('标题必须填写，且长度不得超过100字。<a href="'.$currentUrl.'">点此返回</a>');
            }

            if ( empty($content) || mb_strlen($content) > 5000 || mb_strlen($content) < 100) {
                wp_die('内容必须填写，且长度不得超过5000字，不得少于100字。<a href="'.$currentUrl.'">点此返回</a>');
            }

            $post_content = '昵称: '.$name.'<br />Email: '.$email.'<br />blog: '.$blog.'<br />内容:<br />'.$content;

            $tougao = array(
                'post_title' => $title,
                'post_content' => $post_content,
                'post_category' => array($category)
            );


            // 将文章插入数据库
            $status = wp_insert_post( $tougao );

            if ($status != 0) {
                // 投稿成功给博主发送邮件
                // somebody#example.com替换博主邮箱
                // My subject替换为邮件标题，content替换为邮件内容
                wp_mail("somebody#example.com","My subject","content");
                wp_die('投稿成功！感谢投稿！<a href="'.$currentUrl.'">点此返回</a>', '投稿成功');
            }
            else {
                wp_die('投稿失败！<a href="'.$currentUrl.'">点此返回</a>');
            }
        }
        the_content();
    ?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="form-submit-wrap">
                <?php if (have_posts()): ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <h3 class="form-submit-title"><?php the_title(); ?></h3>
                        <div class="form-submit-description"><?php the_content(); ?></div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <form class="form-submit" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; $current_user = wp_get_current_user(); ?>">
                    <div class="form-group">
                        <label for="submitName">昵称:*</label>
                        <input type="text" size="40" value="<?php if ( 0 != $current_user->ID ) echo $current_user->user_login; ?>" id="submitName" name="submitName" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="submitEmail">E-Mail:*</label>
                        <input type="text" size="40" value="<?php if ( 0 != $current_user->ID ) echo $current_user->user_email; ?>" id="submitEmail" name="submitEmail" class="form-control" />
                    </div class="form-group">

                    <div class="form-group">
                        <label for="submitBlog">您的博客:</label>
                        <input type="text" size="40" value="<?php if ( 0 != $current_user->ID ) echo $current_user->user_url; ?>" id="submitBlog" name="submitBlog" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="submitTitle">文章标题:*</label>
                        <input type="text" size="40" value="" id="submitTitle" name="submitTitle" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="tougaocategorg">分类:*</label>
                        <?php wp_dropdown_categories('class=form-control&hide_empty=0&id=tougaocategorg&show_count=1&hierarchical=1'); ?>
                    </div>

                    <div class="form-group">
                        <label style="vertical-align:top" for="submitContent">文章内容:*</label>
                        <textarea id="submitContent" name="submitContent" class="form-control"></textarea>
                    </div>

                    <div class="form-group text-center">
                        <input type="hidden" value="send" name="submitForm" />
                        <input type="submit" class="btn btn-geek" value="提交" />
                        <input type="reset" class="btn btn-geek" value="重填" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
