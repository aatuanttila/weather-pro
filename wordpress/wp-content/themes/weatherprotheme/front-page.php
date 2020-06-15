<?php get_header(); ?>

<header>
    <h1><?php the_title(); ?></h1>
    
    <div>

        <?php 
        /* if (have_posts()) : while(have_posts()) : the_post();
            the_content(); 
            endwhile; 
            endif; */ 
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

                $weather_now = $response["currently"]["temperature"];
                $weather_tmrw =  $response["daily"]["data"][1]["temperatureHigh"];
                $weather_day_after_tmrw = $response["daily"]["data"][2]["temperatureHigh"];

                echo "<div>";
                    echo "<h3>" . get_the_title() . "</h3>";
                    echo "<div class='weather_container'>";
                        echo "<p>Nyt: " . $weather_now  . "</p></p>Huomenna: " . $weather_tmrw . "</p><p>Ylih.: " . $weather_day_after_tmrw . "</p>";
                    echo "</div>";
                echo "</div>";
                
            }
        }
        ?>

    </div>
</header>
<?php get_footer(); ?>