<?php if(isset($_SESSION['message'])){ ?>
    <div class="notification <?php echo clean($_SESSION['message_type']); ?>">
        <?php echo clean($_SESSION['message']); ?>
    </div>

    <?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
<?php } ?>
