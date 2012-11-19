<div class="actions">
    <h3>リンク</h3>
    <ul>
        <?php
        echo "<li>" . $this->Html->link('ログイン', array('controller' => 'DatUsers', 'action' => 'login')) . "</li>";
        echo "<li>" . $this->Html->link('ログアウト', array('controller' => 'DatUsers', 'action' => 'logout')) . "</li>";
        echo "<li>" . $this->Html->link('メイン', array('controller' => 'DatUsers', 'action' => 'index')) . "</li>";
        echo "<li>" . $this->Html->link('DatAlbums', array('controller' => 'DatAlbums', 'action' => 'index')) . "</li>";
        echo "<li>" . $this->Html->link('DatPhotos', array('controller' => 'DatPhotos', 'action' => 'index')) . "</li>";
        echo "<li>" . $this->Html->link('Api Test', 'http://'. $_SERVER["HTTP_HOST"]. '/Phorest/api_test.php') . "</li>";
        ?>
    </ul>
</div>