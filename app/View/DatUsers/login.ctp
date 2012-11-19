<h2>Users login ビュー</h2>
<div class="users form">
    <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('DatUser');?>
    <fieldset>
        <legend>
            <?php echo __('ユーザ名とパスワードを入力してください。'); ?>
        </legend>
        <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('ログイン'));?>
    </form>
</div>
<?php echo $this->element('all_links'); ?>