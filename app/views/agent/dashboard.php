<section id="sec-dashboard">
    <div class="container">
    <?php
        $html = '';
        if (isset($dashboard)) {
            $html .= '<div class="table-grid">';
            $html .= '<div id="grid"></div>';
            $html .= '</div>';
        }
        echo $html;
    ?>
    </div>
</section>
