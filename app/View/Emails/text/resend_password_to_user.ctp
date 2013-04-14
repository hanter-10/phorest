パスワード再発行のメッセージとなります。
以下の対応を行い、新しいパスワードを設定してください。

□発行パスワード
<?php echo h($password); ?>


下記URL画面にて、上記発行パスワードを入力して、新たにパスワードを登録してください。
<?php echo $this->Html->url('/', true) . 'reset_password/'. $username; ?>

