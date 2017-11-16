<section id="sec-dashboard">
    <div class="container">
    <?php
        $html = '';
        if (isset($dashboard)) {
            $html .= '<div id="dashboard">'. base64_decode($dashboard[0]['dashboard']) .'</div>';
        }
        echo $html;
    ?>
    </div>
    <script>
        <?php if (isset($agent_contract_count)) : ?>
        var contract_count = <?php echo $agent_contract_count; ?>;
        if(typeof contract_count != 'undefined' && contract_count == 0){
            alert('聯絡人資料尚未填寫，請盡速填寫！\n謝謝！');
        }
        <?php endif; ?>
    </script>
</section>
