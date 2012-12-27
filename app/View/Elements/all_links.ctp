<div class="actions">
    <h3>リンク</h3>
    <ul>
        <?php
        echo "<li>" . $this->Html->link('会員登録', array('controller' => 'DatUsers', 'action' => 'add')) . "</li>";
		echo "<li>" . $this->Html->link('ログイン', array('controller' => 'DatUsers', 'action' => 'login')) . "</li>";
        echo "<li>" . $this->Html->link('ログアウト', array('controller' => 'DatUsers', 'action' => 'logout')) . "</li>";
        echo "<li>" . $this->Html->link('管理画面', array('controller' => 'DatUsers', 'action' => 'index')) . "</li>";
        ?>
    </ul>
</div>