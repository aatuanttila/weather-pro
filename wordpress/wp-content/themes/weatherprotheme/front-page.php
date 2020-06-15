<?php get_header(); ?>

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

                $weather_now = round($response["currently"]["temperature"], 0);
                $weather_now_summary = $response["currently"]["summary"];

                $weather_tmrw =  round($response["daily"]["data"][1]["temperatureHigh"], 0);
                $weather_tmrw_summary = $response["daily"]["data"][1]["summary"];

                $weather_day_after_tmrw = round($response["daily"]["data"][2]["temperatureHigh"], 0);
                $weather_day_after_tmrw_summary = $response["daily"]["data"][2]["summary"];

                // +-sign if temperature positive
                $weather_now = sprintf("%+d",$weather_now);
                $weather_tmrw = sprintf("%+d",$weather_tmrw);
                $weather_day_after_tmrw = sprintf("%+d",$weather_day_after_tmrw);

                echo "<div>";
                    echo "<h3>" . get_the_title() . "</h3>";
                    echo "<div class='weather_container'>";
                        echo "
                            <p>" . date("l") . "<br>" . $weather_now_summary . "<br>" . $weather_now  . "</p>
                            <p>" . date("l", strtotime("+1day")) . " <br>$weather_tmrw_summary<br>" . $weather_tmrw . "</p>
                            <p>" . date("l", strtotime("+2day")) . " <br>$weather_day_after_tmrw_summary<br>" . $weather_day_after_tmrw . "</p>"
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

<section class="info">
    <?php

        $field = get_field('title');
        echo $field;

    ?>
    <h4>Hello world</h4>
    <h4><?php the_field('catchphrase'); ?></h4>
    <h3><?php the_field('subtitle'); ?></h3>

</section>

<?php if ( ! dynamic_sidebar( 'footer-area' ) ) : ?>
  <section class="footer">
      <h2>Contact us</h2>
      <p>Weather Pro</p>
      <p>Randomstreet 9</p>
      <p>40100 Jyväskylä</p>
  </section>
<?php endif; ?>

<?php get_footer(); ?>