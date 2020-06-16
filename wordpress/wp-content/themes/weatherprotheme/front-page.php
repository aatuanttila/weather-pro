<?php get_header(); ?>

<header>
    <h1><?php the_title(); ?></h1>
    
    <div>

        <?php 
            if (get_fields()) {
                $catchphrase = get_field('catchphrase');
                $title = get_field('title');
                $windy_text = get_field('windy_text');
                $text_field = get_field('text_field');
                $button = get_field('button');
                $background_image = get_field('background_image');
            } else {
                echo "Install Advanced Custom Fields Plugin.";
                die();
            }
        ?>

        <?php 
        $args = array(
            'post_type' => 'sijainnit',
        );

        $the_query = new WP_Query($args);

        if ( $the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $post_id = get_the_ID();
                
                $coordinates = get_post_meta($post_id, 'sijainti_koordinaatti', true);

                $url = 'https://api.darksky.net/forecast/06b9f4153248185764afc5486d67eece/'. $coordinates .'?units=si';

                $response = file_get_contents($url);
                $response = json_decode($response, true);

                $weather_now = round($response["currently"]["temperature"], 0);
                $weather_now_icon = $response["currently"]["icon"];
                $weather_now_icon_img = "<img src='wp-content/themes/weatherprotheme/weather-icons/" . $weather_now_icon . ".svg' alt='$weather_now_icon'>";

                $weather_tmrw =  round($response["daily"]["data"][1]["temperatureHigh"], 0);
                $weather_tmrw_icon = $response["daily"]["data"][1]["icon"];
                $weather_tmrw_icon_img = "<img src='wp-content/themes/weatherprotheme/weather-icons/" . $weather_tmrw_icon . ".svg' alt='$weather_tmrw_icon'>";

                $weather_day_after_tmrw = round($response["daily"]["data"][2]["temperatureHigh"], 0);
                $weather_day_after_tmrw_icon = $response["daily"]["data"][2]["icon"];
                $weather_day_after_tmrw_icon_img= "<img src='wp-content/themes/weatherprotheme/weather-icons/" . $weather_day_after_tmrw_icon . ".svg' alt='$$weather_day_after_tmrw_icon'>";


                // +-sign if temperature positive
                $weather_now = sprintf("%+d",$weather_now);
                $weather_tmrw = sprintf("%+d",$weather_tmrw);
                $weather_day_after_tmrw = sprintf("%+d",$weather_day_after_tmrw);

                echo "<div>";
                    echo "<h3>" . get_the_title() . "</h3>";
                    echo "<div class='weather_container'>";
                        echo "
                            <p>" . date("l") . "<br>$weather_now_icon_img<br>" . $weather_now  . "</p>
                            <p>" . date("l", strtotime("+1day")) . " <br>$weather_tmrw_icon_img<br>" . $weather_tmrw . "</p>
                            <p>" . date("l", strtotime("+2day")) . " <br>$weather_day_after_tmrw_icon_img<br>" . $weather_day_after_tmrw . "</p>"
                        ;
                    echo "</div>";
                echo "</div>";
                
            }
        } else {
            echo "<p>Käy lisäämässä uusi 'Sijainnit'-custom post type.</p>";
        }
        ?>
    </div>
</header>

<section class="info-section">
    <div>
        <h3><?php echo $catchphrase; ?></h3>
        <h2><?php echo $title; ?></h2>
        <p><?php echo $text_field; ?></p>
        <a
        role="button"
        href="<?php echo $button['url']; ?>"
        target="<?php echo $button['target']; ?>"
        >
        <?php echo $button['title']; ?>
        </a>
    </div>
</section>

<section class="windy-section" style="background-image: url(<?php echo $background_image['url'];  ?>)">
    <div>    
        <h1><?php echo $windy_text; ?></h1>
        <a
        role="button"
        href="<?php echo $button['url']; ?>"
        target="<?php echo $button['target']; ?>"
        >
        <?php echo $button['title']; ?>
        </a>
    </div>
</section>

<?php get_footer(); ?>