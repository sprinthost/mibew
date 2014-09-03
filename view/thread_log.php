<?php
/*
 * This file is part of Mibew Messenger project.
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * All rights reserved. The contents of this file are subject to the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and
 * is available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Alternatively, the contents of this file may be used under the terms of
 * the GNU General Public License Version 2 or later (the "GPL"), in which case
 * the provisions of the GPL are applicable instead of those above. If you wish
 * to allow use of your version of this file only under the terms of the GPL, and
 * not to allow others to use your version of this file under the terms of the
 * EPL, indicate your decision by deleting the provisions above and replace them
 * with the notice and other provisions required by the GPL.
 * 
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

$page['title'] = getlocal("thread.chat_log");

function tpl_content() { global $page, $webimroot, $errors;
$chatthread = $page['thread'];
?>

<?php echo getlocal("thread.intro") ?>

<br/><br/>

    <?php
    $check_admin=check_login();

    $linkid = connect();
    $agentId=operator_by_id_($chatthread['agentId'], $linkid);
    mysql_close($linkid);

    $worker_id=$agentId['vcemail'];

    if ($check_admin['iperm']=='65535') {
        ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript">
            $j = jQuery.noConflict();

            var quality;

            $j(document).ready(function() {
               load_score();
                $j('.fine').prop('checked',false);
            });

            function check_score(obj, score) {
                $j(".score_button").css({'border':'2px solid #fff'});
                $j(obj).css({'border':'2px solid #fc6000'});
                quality=score;
            }

            function score(obj, message_id, admin_id) {

                var fine = $j(obj).parent().children('.fine').prop('checked');

                var comment_admin = document.getElementById('comment_admin');

                var load_comm = $j.when($j.ajax({
                    url: 'http://feedback.from.sh/action/get_comment.php',
                    type: 'POST',
                    crossDomain: true,
                    data: {id_score: message_id, type: 'chat'},
                    cache: false,
                    success: function(response){
                        if (response!='' && (comment_admin.value=='' || comment_admin.value=='Введите комментарий')) {
                            comment_admin.value = response;
                        }
                    },
                    error: function() {
                        if (comment_admin.value=='' || comment_admin.value=='Введите комментарий') {
                            comment_admin.value = 'Введите комментарий';
                        }
                    }
                }));

                load_comm.done(function() {
                    comment_admin = document.getElementById('comment_admin').value;
                    if (comment_admin=='Введите комментарий') {
                        comment_admin='';
                    }

                    if (fine===true) {
                        var f=1;
                    }
                    else {
                        var f=0;
                    }

                    $j.ajax({
                        url: 'http://feedback.from.sh/action/score_action.php',
                        type: 'POST',
                        crossDomain: true,
                        data: {admin: 'true', quality: quality, id_score: message_id, comment_admin: comment_admin, type: 'chat', admin_id: admin_id, worker_id: '<?php echo $worker_id;?>', date_add: '<?php echo $chatthread['created'];?>', fine: f},
                        cache: false,
                        success: function(response){
                            if (response.message=="Спасибо, Ваше мнение очень важно для нас") {
                                $j("#score_button_"+quality).css({'border':'2px solid green'});
                            }
                        }
                    });
                });
            }

            function blur_comment(obj) {
                if (obj.value=='' || obj.value=='Введите комментарий'){
                    obj.value='Введите комментарий';
                }
                obj.style.width='140px';
            }

            function load_comment(obj, mess_id) {
                if (obj.style.width!='600px') {
                    obj.style.width='600px';
                    if (obj.value=='' || obj.value=='Введите комментарий') {
                        $j.ajax({
                            url: 'http://feedback.from.sh/action/get_comment.php',
                            type: 'POST',
                            crossDomain: true,
                            data: {id_score: mess_id, type: 'chat'},
                            cache: false,
                            success: function(response){
                                obj.value = response;
                            },
                            error: function() {
                                obj.value = '';
                            }
                        });
                    }
                }
            }

            function load_score() {

                $j.ajax({
                    url: 'http://feedback.from.sh/action/get_score.php',
                    type: 'POST',
                    crossDomain: true,
                    data: {id_score: '<?php echo $_GET['threadid'];?>', type: 'chat'},
                    cache: false,
                    success: function(response){
                        $j("#score_button_"+response).css({'border':'2px solid #fc6000'});
                    },
                    error: function() {
                        //obj.value = '';
                    }
                });
            }

        </script>
        <div style="position: fixed; right: 0px; top: 80px; padding: 20px; background: #fff; box-shadow: 0 0 4px 1px rgba(0,0,0,0.6); text-align: center;">
            <button type="button" style="margin-left: 2px; border: 2px solid #fff;" id="score_button_1" class="score_button" onclick="check_score(this, '1');" title=":)">:)</button>

            <button type="button" style="margin-left: 2px; border: 2px solid #fff;" id="score_button_0" class="score_button" onclick="check_score(this, '0');" title=":|">:|</button>

            <button type="button" style="margin-left: 2px; border: 2px solid #fff;" id="score_button_-1" class="score_button" onclick="check_score(this, '-1');" title=":(">:(</button>
            <br>

            <input id="comment_admin" style="margin: 10px; width: 140px; height: 14px; padding: 2px; resize: none;" onClick="load_comment(this, '<?php echo $_GET['threadid'];?>');" onBlur="blur_comment(this);" value="Введите комментарий">
            <br>
            <button type="button" style="" onClick="score(this, '<?php echo $_GET['threadid'];?>', '<?php echo $check_admin['operatorid'];?>')" title="submit">submit</button>

            &nbsp &nbsp &nbsp

            Штраф?<input type="checkbox" class="fine">


        </div>
    <?php
    }
    ?>

<div class="logpane">
<div class="header">

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_name") ?>:
		</div> 
		<div class="wvalue">
			<?php echo topage(htmlspecialchars($chatthread['userName'])) ?>
		</div>
		<br clear="all"/>
		
		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_host") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_user_addr(topage($chatthread['remote'])) ?>
		</div>
		<br clear="all"/>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_browser") ?>:
		</div>
		<div class="wvalue">
			<?php echo get_useragent_version(topage($chatthread['userAgent'])) ?>
		</div>
		<br clear="all"/>

		<?php if( $chatthread['groupName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_group") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(htmlspecialchars($chatthread['groupName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<?php if( $chatthread['agentName'] ) { ?>
			<div class="wlabel">
				<?php echo getlocal("page.analysis.search.head_operator") ?>:
			</div>
			<div class="wvalue">
				<?php echo topage(htmlspecialchars($chatthread['agentName'])) ?>
			</div>
			<br clear="all"/>
		<?php } ?>

		<div class="wlabel">
			<?php echo getlocal("page.analysis.search.head_time") ?>:
		</div>
		<div class="wvalue">
			<?php echo date_diff_to_text($chatthread['modified']-$chatthread['created']) ?> 
				(<?php echo date_to_text($chatthread['created']) ?>)
		</div>
		<br clear="all"/>
</div>

<div class="message">
<?php 
	foreach( $page['threadMessages'] as $message ) {
		echo $message;
	}
?>
</div>
</div>

<br />
<a href="<?php echo $webimroot ?>/operator/history.php">
	<?php echo getlocal("thread.back_to_search") ?></a>
<br />


<?php 
} /* content */

require_once('inc_main.php');
?>